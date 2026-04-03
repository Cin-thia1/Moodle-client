<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Module;
use App\Models\Section;
use App\Models\QuizQuestion;
use App\Models\QuizAnswer;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    private function accessibleCourses($user)
    {
        return Course::query()
            ->whereHas('users', fn($q) => $q->where('users.id', $user->id))
            ->orWhere('teacher_id', $user->id)
            ->orderBy('fullname')
            ->get();
    }

    // ─────────────────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────────────────

    public function create(Request $request)
    {
        $user    = Auth::user();
        $courses = $this->accessibleCourses($user);

        if ($courses->isEmpty()) {
            return view('quiz.create', [
                'courses'          => $courses,
                'selectedCourseId' => 0,
                'sections'         => collect(),
            ]);
        }

        $selectedCourseId = (int) $request->query('course_id', $courses->first()->id);
        if (!$courses->pluck('id')->contains($selectedCourseId)) {
            $selectedCourseId = (int) $courses->first()->id;
        }

        $sections = Section::where('course_id', $selectedCourseId)->orderBy('name')->get();

        return view('quiz.create', compact('courses', 'selectedCourseId', 'sections'));
    }

    // ─────────────────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'course_id'                        => 'required|integer',
            'section_id'                       => 'required|integer',
            'name'                             => 'required|string|max:255',
            'intro'                            => 'nullable|string',
            'timeopen'                         => 'nullable|date',
            'timeclose'                        => 'nullable|date',
            'timelimit'                        => 'nullable|integer|min:1',
            'grade'                            => 'required|numeric|min:0|max:100',
            'attempts'                         => 'required|integer|min:0',
            'grademethod'                      => 'required|integer|in:0,1,2,3',
            'shuffleanswers'                   => 'nullable|boolean',
            'questions'                        => 'nullable|array',
            'questions.*.qtype'                => 'required|in:multichoice,truefalse',
            'questions.*.questiontext'         => 'required|string',
            'questions.*.defaultmark'          => 'required|integer|min:1',
            'questions.*.correct_answer'       => 'nullable|integer',
            'questions.*.correct_index'        => 'nullable|integer|min:0',
            'questions.*.answers'              => 'nullable|array',
            'questions.*.answers.*.answer'     => 'nullable|string',
            'questions.*.answers.*.fraction'   => 'nullable|numeric',
        ]);

        $course = Course::query()
            ->where(function ($q) use ($user) {
                $q->whereHas('users', fn($sub) => $sub->where('users.id', $user->id))
                  ->orWhere('teacher_id', $user->id);
            })
            ->where('id', $validated['course_id'])
            ->firstOrFail();

        $section = Section::where('course_id', $course->id)
            ->findOrFail($validated['section_id']);

        // ✅ Parse des dates avec timezone app (datetime-local attendu) puis conversion UTC
        $timeopen = !empty($validated['timeopen'])
            ? \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['timeopen'], config('app.timezone'))->utc()
            : null;

        $timeclose = !empty($validated['timeclose'])
            ? \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['timeclose'], config('app.timezone'))->utc()
            : null;

        $createdModule = null;

        DB::transaction(function () use ($validated, $section, $course, $request, $timeopen, $timeclose, &$createdModule) {

            $module = Module::create([
                'name'             => $validated['name'],
                'modname'          => 'quiz',
                'modplural'        => 'Quiz',
                'downloadcontent'  => 0,
                'file_path'        => '',
                'section_id'       => $section->id,
                'intro'            => $validated['intro'] ?? null,
                'grade'            => $validated['grade'],
                'timeopen'         => $timeopen,
                'timeclose'        => $timeclose,
                'timelimit'        => $validated['timelimit'] ? $validated['timelimit'] * 60 : null,
                'attempts'         => $validated['attempts'],
                'grademethod'      => $validated['grademethod'],
                'shuffleanswers'   => $request->boolean('shuffleanswers'),
                'questionsperpage' => 0,
            ]);

            foreach ($validated['questions'] ?? [] as $slot => $qData) {
                $question = QuizQuestion::create([
                    'module_id'    => $module->id,
                    'qtype'        => $qData['qtype'],
                    'questiontext' => $qData['questiontext'],
                    'defaultmark'  => $qData['defaultmark'],
                    'slot'         => $slot + 1,
                ]);

                if ($qData['qtype'] === 'truefalse') {
                    $correctIndex = (int)($qData['correct_answer'] ?? 0);
                    QuizAnswer::create(['question_id' => $question->id, 'answer' => 'Vrai', 'fraction' => $correctIndex === 0 ? 1.0 : 0.0]);
                    QuizAnswer::create(['question_id' => $question->id, 'answer' => 'Faux',  'fraction' => $correctIndex === 1 ? 1.0 : 0.0]);
                } else {
                    // ✅ FIX QCM : utiliser correct_index envoyé par le formulaire corrigé
                    $correctIndex = isset($qData['correct_index']) ? (int)$qData['correct_index'] : null;

                    foreach ($qData['answers'] ?? [] as $aIdx => $aData) {
                        if (empty($aData['answer'])) continue;

                        $fraction = ($correctIndex !== null)
                            ? (($aIdx == $correctIndex) ? 1.0 : 0.0)
                            : (float)($aData['fraction'] ?? 0);

                        QuizAnswer::create([
                            'question_id' => $question->id,
                            'answer'      => $aData['answer'],
                            'fraction'    => $fraction,
                        ]);
                    }
                }
            }

            \App\Models\Event::create([
                'title'       => 'Quiz : ' . $module->name,
                'date'        => $timeclose ?? $timeopen,
                'type'        => 'cours',
                'course_id'   => $course->id,
                'module_id'   => $module->id,
                'description' => $module->intro ?? 'Date de fermeture du quiz',
            ]);

            $createdModule = $module;
        });

        /*return redirect()
            ->route('quiz.show', $createdModule->id)
            ->with('success', 'Quiz créé avec succès.');*/
            if (!$createdModule) {
    return back()->withErrors('Échec création quiz');
}
return redirect()->route('quiz.show', $createdModule->id)
    ->with('success', 'Quiz créé avec succès.');
    }

    // ─────────────────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────────────────

    public function show($id)
    {
        $user = Auth::user();

        $module = Module::with(['section.course', 'quizQuestions.answers'])
            ->where('modname', 'quiz')
            ->findOrFail($id);

        $course = $module->section->course;

        $hasPivotAccess    = $course->users()->where('users.id', $user->id)->exists();
        $isTeacherOfCourse = (int)$course->teacher_id === (int)$user->id;

        if (!$hasPivotAccess && !$isTeacherOfCourse) {
            abort(403, 'Accès refusé.');
        }

        $courses = $this->accessibleCourses($user);

        $quizzes = Module::where('modname', 'quiz')
            ->whereHas('section', fn($q) => $q->where('course_id', $course->id))
            ->orderByDesc('timeclose')
            ->get();

        $students = collect();
        $results  = collect();

        if ($isTeacherOfCourse) {
            $students = $course->users()
                ->where('users.id', '!=', $user->id)
                ->orderBy('name')
                ->get();

            foreach ($students as $student) {
                $attempts = QuizAttempt::where('module_id', $module->id)
                    ->where('user_id', $student->id)
                    ->orderBy('attempt')
                    ->get();

                $bestAttempt = $attempts->where('state', 'finished')
                    ->sortByDesc('sumgrades')
                    ->first();

                $results[$student->id] = [
                    'attempts' => $attempts,
                    'best'     => $bestAttempt,
                    'grade'    => $bestAttempt ? $bestAttempt->gradeOutOf($module->grade) : null,
                ];
            }
        }

        $myAttempts    = collect();
        $myLastAttempt = null;
        $canAttempt    = false;
        $statusMessage = null;

        if (!$isTeacherOfCourse) {
            $myAttempts = QuizAttempt::where('module_id', $module->id)
                ->where('user_id', $user->id)
                ->orderBy('attempt')
                ->get();

            $myLastAttempt = $myAttempts->last();
            $maxAttempts   = (int) $module->attempts;
            $doneCount     = $myAttempts->where('state', 'finished')->count();
            $hasInProgress = $myAttempts->contains('state', 'inprogress');

            // ✅ Comparaison timezone-safe : tout en UTC
            $now       = now()->utc();
            $timeopen  = $module->timeopen  ? \Carbon\Carbon::parse($module->timeopen)->utc()  : null;
            $timeclose = $module->timeclose ? \Carbon\Carbon::parse($module->timeclose)->utc() : null;

            $notOpenYet    = $timeopen  && $now->lt($timeopen);
            $alreadyClosed = $timeclose && $now->gt($timeclose);
            $maxReached    = $maxAttempts > 0 && $doneCount >= $maxAttempts;

            $canAttempt = !$hasInProgress && !$notOpenYet && !$alreadyClosed && !$maxReached;

            if ($hasInProgress) {
                $statusMessage = null;
            } elseif ($notOpenYet) {
                $statusMessage = 'Ce quiz n\'est pas encore ouvert. Ouverture le '
                    . $timeopen->setTimezone(config('app.timezone'))->format('d/m/Y à H:i') . '.';
            } elseif ($alreadyClosed) {
                $statusMessage = 'Ce quiz est fermé depuis le '
                    . $timeclose->setTimezone(config('app.timezone'))->format('d/m/Y à H:i') . '.';
            } elseif ($maxReached) {
                $statusMessage = 'Vous avez atteint le nombre maximum de tentatives (' . $maxAttempts . ').';
            }
        }

        return view('quiz.show', [
            'module'        => $module,
            'courses'       => $courses,
            'quizzes'       => $quizzes,
            'isTeacher'     => $isTeacherOfCourse,
            'students'      => $students,
            'results'       => $results,
            'myAttempts'    => $myAttempts,
            'myLastAttempt' => $myLastAttempt,
            'canAttempt'    => $canAttempt,
            'statusMessage' => $statusMessage,
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // ATTEMPT
    // ─────────────────────────────────────────────────────────

    public function attempt($id)
    {
        $user   = Auth::user();
        $module = Module::with(['quizQuestions.answers'])
            ->where('modname', 'quiz')
            ->findOrFail($id);

        $course = $module->section->course;

        if ((int)$course->teacher_id === (int)$user->id) {
            return redirect()->route('quiz.show', $id)
                ->withErrors('Les enseignants ne peuvent pas passer le quiz.');
        }

        $doneCount   = QuizAttempt::where('module_id', $id)
            ->where('user_id', $user->id)
            ->where('state', 'finished')
            ->count();

        $maxAttempts = (int)$module->attempts;
        if ($maxAttempts > 0 && $doneCount >= $maxAttempts) {
            return redirect()->route('quiz.show', $id)
                ->withErrors('Vous avez atteint le nombre maximum de tentatives.');
        }

        $attempt = QuizAttempt::firstOrCreate(
            ['module_id' => $module->id, 'user_id' => $user->id, 'state' => 'inprogress'],
            ['attempt' => $doneCount + 1, 'timestart' => now()]
        );

        $questions = $module->quizQuestions;

        if ($module->shuffleanswers) {
            $questions = $questions->map(function ($q) {
                $q->setRelation('answers', $q->answers->shuffle());
                return $q;
            });
        }

        // Charger les réponses données (peut y en avoir plusieurs par question avec checkboxes)
        $attemptAnswers = QuizAttemptAnswer::where('attempt_id', $attempt->id)->get();
        $givenAnswers = [];
        foreach ($attemptAnswers as $aa) {
            if (!isset($givenAnswers[$aa->question_id])) {
                $givenAnswers[$aa->question_id] = [];
            }
            $givenAnswers[$aa->question_id][] = $aa->answer_id;
        }

        return view('quiz.attempt', compact('module', 'attempt', 'questions', 'givenAnswers'));
    }

    // ─────────────────────────────────────────────────────────
    // SUBMIT
    // ─────────────────────────────────────────────────────────

    public function submitAttempt(Request $request, $id)
    {
        $user   = Auth::user();
        $module = Module::with(['quizQuestions.answers'])
            ->where('modname', 'quiz')
            ->findOrFail($id);

        $attempt = QuizAttempt::where('module_id', $module->id)
            ->where('user_id', $user->id)
            ->where('state', 'inprogress')
            ->firstOrFail();

        DB::transaction(function () use ($request, $module, $attempt) {
            $sumgrades = 0;

            foreach ($module->quizQuestions as $question) {
                // Récupérer les réponses (peut être un array pour checkboxes ou une valeur pour radios)
                $answers = $request->input('answers.' . $question->id);
                $answerIds = is_array($answers) ? $answers : ($answers ? [$answers] : []);

                if (!empty($answerIds)) {
                    // Pour les questions à choix multiples : scoring "tout ou rien"
                    $correctAnswers = $question->answers->where('fraction', '>', 0)->pluck('id')->toArray();
                    $wrongAnswers = $question->answers->where('fraction', '=', 0)->pluck('id')->toArray();
                    
                    $hasAllCorrect = count(array_intersect($answerIds, $correctAnswers)) === count($correctAnswers);
                    $hasNoWrong = count(array_intersect($answerIds, $wrongAnswers)) === 0;
                    
                    $questionScore = ($hasAllCorrect && $hasNoWrong) ? 1.0 : 0.0;
                    $sumgrades += $questionScore * $question->defaultmark;

                    // Enregistrer chaque réponse cochée
                    foreach ($answerIds as $answerId) {
                        $answer = QuizAnswer::find($answerId);
                        if ($answer) {
                            $fraction = (float)$answer->fraction;
                            QuizAttemptAnswer::updateOrCreate(
                                ['attempt_id' => $attempt->id, 'question_id' => $question->id, 'answer_id' => $answerId],
                                ['fraction' => $fraction]
                            );
                        }
                    }
                }
            }

            $attempt->sumgrades  = $sumgrades;
            $attempt->state      = 'finished';
            $attempt->timefinish = now();
            $attempt->save();
        });

        return redirect()
            ->route('quiz.result', [$id, $attempt->id])
            ->with('success', 'Quiz soumis avec succès.');
    }

    // ─────────────────────────────────────────────────────────
    // RESULT
    // ─────────────────────────────────────────────────────────

    public function result($moduleId, $attemptId)
    {
        $user   = Auth::user();
        $module = Module::with(['quizQuestions.answers'])
            ->where('modname', 'quiz')
            ->findOrFail($moduleId);

        $attempt = QuizAttempt::with(['answers.answer', 'answers.question'])
            ->where('module_id', $module->id)
            ->findOrFail($attemptId);

        $isTeacher = (int)$module->section->course->teacher_id === (int)$user->id;
        if (!$isTeacher && (int)$attempt->user_id !== (int)$user->id) {
            abort(403);
        }

        $grade = $attempt->gradeOutOf($module->grade);

        $canAttempt = false;
        if (!$isTeacher) {
            $doneCount   = QuizAttempt::where('module_id', $module->id)
                ->where('user_id', $user->id)
                ->where('state', 'finished')
                ->count();
            $maxAttempts = (int) $module->attempts;
            $now         = now()->utc();
            $timeopen    = $module->timeopen  ? \Carbon\Carbon::parse($module->timeopen)->utc()  : null;
            $timeclose   = $module->timeclose ? \Carbon\Carbon::parse($module->timeclose)->utc() : null;
            $isOpen      = (!$timeopen  || $now->gte($timeopen))
                        && (!$timeclose || $now->lte($timeclose));
            $canAttempt  = $isOpen && ($maxAttempts === 0 || $doneCount < $maxAttempts);
        }

        return view('quiz.result', compact('module', 'attempt', 'grade', 'canAttempt'));
    }

    // ─────────────────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────────────────

    public function edit(Request $request, $id)
    {
        $user   = Auth::user();
        $module = Module::with(['section.course', 'quizQuestions.answers'])
            ->where('modname', 'quiz')
            ->findOrFail($id);

        $course = $module->section->course;

        if ((int)$course->teacher_id !== (int)$user->id) {
            abort(403, 'Accès refusé.');
        }

        $courses          = $this->accessibleCourses($user);
        $selectedCourseId = $course->id;
        $sections         = Section::where('course_id', $course->id)->orderBy('name')->get();

        return view('quiz.edit', compact('module', 'courses', 'sections', 'selectedCourseId'));
    }

    // ─────────────────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────────────────

    public function update(Request $request, $id)
    {
        $user   = Auth::user();
        $module = Module::with(['section.course'])
            ->where('modname', 'quiz')
            ->findOrFail($id);

        $course = $module->section->course;

        if ((int)$course->teacher_id !== (int)$user->id) {
            abort(403, 'Accès refusé.');
        }

        $validated = $request->validate([
            'section_id'                       => 'required|integer',
            'name'                             => 'required|string|max:255',
            'intro'                            => 'nullable|string',
            'timeopen'                         => 'nullable|date',
            'timeclose'                        => 'nullable|date',
            'timelimit'                        => 'nullable|integer|min:1',
            'grade'                            => 'required|numeric|min:0|max:100',
            'attempts'                         => 'required|integer|min:0',
            'grademethod'                      => 'required|integer|in:0,1,2,3',
            'shuffleanswers'                   => 'nullable|boolean',
            'questions'                        => 'nullable|array',
            'questions.*.qtype'                => 'required|in:multichoice,truefalse',
            'questions.*.questiontext'         => 'required|string',
            'questions.*.defaultmark'          => 'required|integer|min:1',
            'questions.*.correct_answer'       => 'nullable|integer',
            'questions.*.correct_index'        => 'nullable|integer|min:0',
            'questions.*.answers'              => 'nullable|array',
            'questions.*.answers.*.answer'     => 'nullable|string',
            'questions.*.answers.*.fraction'   => 'nullable|numeric',
        ]);

        $section = Section::where('course_id', $course->id)->findOrFail($validated['section_id']);

        $timeopen = !empty($validated['timeopen'])
            ? \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['timeopen'], config('app.timezone'))->utc()
            : null;

        $timeclose = !empty($validated['timeclose'])
            ? \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['timeclose'], config('app.timezone'))->utc()
            : null;

        DB::transaction(function () use ($validated, $module, $section, $request, $timeopen, $timeclose) {

            $module->update([
                'name'           => $validated['name'],
                'intro'          => $validated['intro'] ?? null,
                'section_id'     => $section->id,
                'grade'          => $validated['grade'],
                'timeopen'       => $timeopen,
                'timeclose'      => $timeclose,
                'timelimit'      => $validated['timelimit'] ? $validated['timelimit'] * 60 : null,
                'attempts'       => $validated['attempts'],
                'grademethod'    => $validated['grademethod'],
                'shuffleanswers' => $request->boolean('shuffleanswers'),
            ]);

            $module->quizQuestions()->delete();

            foreach ($validated['questions'] ?? [] as $slot => $qData) {
                $question = QuizQuestion::create([
                    'module_id'    => $module->id,
                    'qtype'        => $qData['qtype'],
                    'questiontext' => $qData['questiontext'],
                    'defaultmark'  => $qData['defaultmark'],
                    'slot'         => $slot + 1,
                ]);

                if ($qData['qtype'] === 'truefalse') {
                    $correctIndex = (int)($qData['correct_answer'] ?? 0);
                    QuizAnswer::create(['question_id' => $question->id, 'answer' => 'Vrai', 'fraction' => $correctIndex === 0 ? 1.0 : 0.0]);
                    QuizAnswer::create(['question_id' => $question->id, 'answer' => 'Faux',  'fraction' => $correctIndex === 1 ? 1.0 : 0.0]);
                } else {
                    $correctIndex = isset($qData['correct_index']) ? (int)$qData['correct_index'] : null;

                    foreach ($qData['answers'] ?? [] as $aIdx => $aData) {
                        if (empty($aData['answer'])) continue;

                        $fraction = ($correctIndex !== null)
                            ? (($aIdx == $correctIndex) ? 1.0 : 0.0)
                            : (float)($aData['fraction'] ?? 0);

                        QuizAnswer::create([
                            'question_id' => $question->id,
                            'answer'      => $aData['answer'],
                            'fraction'    => $fraction,
                        ]);
                    }
                }
            }

            $event = \App\Models\Event::where('module_id', $module->id)->first();
            if ($event) {
                $event->title = 'Quiz : ' . $module->name;
                $event->date  = $timeclose ?? $timeopen;
                $event->save();
            }
        });

        return redirect()->route('quiz.show', $module->id)->with('success', 'Quiz modifié avec succès.');
    }

    // ─────────────────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────────────────

    public function destroy($id)
    {
        $user   = Auth::user();
        $module = Module::with(['section.course'])
            ->where('modname', 'quiz')
            ->findOrFail($id);

        $course = $module->section->course;

        if ((int)$course->teacher_id !== (int)$user->id) {
            abort(403, 'Accès refusé.');
        }

        $courseId = $course->id;

        \App\Models\Event::where('module_id', $module->id)->delete();
        $module->delete();

        return redirect()
            ->route('assignments.index', ['course_id' => $courseId])
            ->with('success', 'Quiz supprimé avec succès.');
    }
}