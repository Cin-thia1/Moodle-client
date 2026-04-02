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
    // ─────────────────────────────────────────────────────────
    // HELPER : cours accessibles par l'utilisateur
    // ─────────────────────────────────────────────────────────

    private function accessibleCourses($user)
    {
        return Course::query()
            ->whereHas('users', fn($q) => $q->where('users.id', $user->id))
            ->orWhere('teacher_id', $user->id)
            ->orderBy('fullname')
            ->get();
    }

    // ─────────────────────────────────────────────────────────
    // CREATE — Formulaire de création d'un quiz
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
    // STORE — Enregistrement du quiz + questions
    // ─────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'course_id'        => 'required|integer',
            'section_id'       => 'required|integer',
            'name'             => 'required|string|max:255',
            'intro'            => 'nullable|string',
            'timeopen'         => 'nullable|date',
            'timeclose'        => 'nullable|date|after_or_equal:timeopen',
            'timelimit'        => 'nullable|integer|min:1',    // en minutes dans le form, converti en secondes
            'grade'            => 'required|numeric|min:0|max:100',
            'attempts'         => 'required|integer|min:0',
            'grademethod'      => 'required|integer|in:0,1,2,3',
            'shuffleanswers'   => 'nullable|boolean',
            // Questions
            'questions'                        => 'nullable|array',
            'questions.*.qtype'                => 'required|in:multichoice,truefalse',
            'questions.*.questiontext'         => 'required|string',
            'questions.*.defaultmark'          => 'required|integer|min:1',
            'questions.*.answers'              => 'nullable|array',
            'questions.*.answers.*.answer'     => 'required|string',
            'questions.*.answers.*.fraction'   => 'required|numeric|in:0,1',
            'questions.*.correct_answer'       => 'nullable|integer',  // index pour truefalse
        ]);

        // Vérifier accès au cours
        $course = Course::query()
            ->where(function ($q) use ($user) {
                $q->whereHas('users', fn($sub) => $sub->where('users.id', $user->id))
                  ->orWhere('teacher_id', $user->id);
            })
            ->where('id', $validated['course_id'])
            ->firstOrFail();

        $section = Section::where('course_id', $course->id)
            ->findOrFail($validated['section_id']);

        DB::transaction(function () use ($validated, $section, $course, $request) {

            // Créer le module quiz
            $module = Module::create([
                'name'             => $validated['name'],
                'modname'          => 'quiz',
                'modplural'        => 'Quiz',
                'downloadcontent'  => 0,
                'file_path'        => '',
                'section_id'       => $section->id,
                'intro'            => $validated['intro'] ?? null,
                'grade'            => $validated['grade'],
                'timeopen'         => $validated['timeopen'] ?? null,
                'timeclose'        => $validated['timeclose'] ?? null,
                // Convertir minutes → secondes
                'timelimit'        => $validated['timelimit'] ? $validated['timelimit'] * 60 : null,
                'attempts'         => $validated['attempts'],
                'grademethod'      => $validated['grademethod'],
                'shuffleanswers'   => $request->boolean('shuffleanswers'),
                'questionsperpage' => 0,
            ]);

            // Créer les questions et réponses
            $questions = $validated['questions'] ?? [];
            foreach ($questions as $slot => $qData) {
                $question = QuizQuestion::create([
                    'module_id'    => $module->id,
                    'qtype'        => $qData['qtype'],
                    'questiontext' => $qData['questiontext'],
                    'defaultmark'  => $qData['defaultmark'],
                    'slot'         => $slot + 1,
                ]);

                if ($qData['qtype'] === 'truefalse') {
                    // Vrai/Faux : 2 réponses fixes
                    $correctIndex = (int)($qData['correct_answer'] ?? 0);
                    QuizAnswer::create([
                        'question_id' => $question->id,
                        'answer'      => 'Vrai',
                        'fraction'    => $correctIndex === 0 ? 1.0 : 0.0,
                    ]);
                    QuizAnswer::create([
                        'question_id' => $question->id,
                        'answer'      => 'Faux',
                        'fraction'    => $correctIndex === 1 ? 1.0 : 0.0,
                    ]);
                } else {
                    // QCM : réponses libres
                    foreach ($qData['answers'] as $aData) {
                        QuizAnswer::create([
                            'question_id' => $question->id,
                            'answer'      => $aData['answer'],
                            'fraction'    => (float)$aData['fraction'],
                        ]);
                    }
                }
            }

            // Événement calendrier
            \App\Models\Event::create([
                'title'       => 'Quiz : ' . $module->name,
                'date'        => $module->timeclose ?? $module->timeopen,
                'type'        => 'cours',
                'course_id'   => $course->id,
                'module_id'   => $module->id,
                'description' => $module->intro ?? 'Date de fermeture du quiz',
            ]);

            return $module;
        });

        // Récupérer le module créé pour la redirection
        $module = Module::where('modname', 'quiz')
            ->where('section_id', $section->id)
            ->where('name', $validated['name'])
            ->latest()
            ->first();

        return redirect()
            ->route('quiz.show', $module->id)
            ->with('success', 'Quiz créé avec succès.');
    }

    // ─────────────────────────────────────────────────────────
    // SHOW — Vue du quiz (prof: stats | élève: passer le quiz)
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

    // ── VUE ENSEIGNANT ──────────────────────────────────────
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

    // ── VUE ÉLÈVE ───────────────────────────────────────────
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

        // Parse explicite Carbon pour éviter les bugs de comparaison string/date
        $timeopen  = $module->timeopen  ? \Carbon\Carbon::parse($module->timeopen)  : null;
        $timeclose = $module->timeclose ? \Carbon\Carbon::parse($module->timeclose) : null;

        $notOpenYet    = $timeopen  && now()->lt($timeopen);
        $alreadyClosed = $timeclose && now()->gt($timeclose);
        $maxReached    = $maxAttempts > 0 && $doneCount >= $maxAttempts;

        $canAttempt = !$hasInProgress && !$notOpenYet && !$alreadyClosed && !$maxReached;

        if ($hasInProgress) {
            $statusMessage = null; // Le bouton "Reprendre" s'affiche dans le tableau
        } elseif ($notOpenYet) {
            $statusMessage = 'Ce quiz n\'est pas encore ouvert. Ouverture le '
                . $timeopen->format('d/m/Y à H:i') . '.';
        } elseif ($alreadyClosed) {
            $statusMessage = 'Ce quiz est fermé depuis le '
                . $timeclose->format('d/m/Y à H:i') . '.';
        } elseif ($maxReached) {
            $statusMessage = 'Vous avez atteint le nombre maximum de tentatives ('
                . $maxAttempts . ').';
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
    // ATTEMPT — Démarre ou reprend une tentative
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

        // Vérifier limite de tentatives
        $doneCount   = QuizAttempt::where('module_id', $id)
            ->where('user_id', $user->id)
            ->where('state', 'finished')
            ->count();

        $maxAttempts = (int)$module->attempts;
        if ($maxAttempts > 0 && $doneCount >= $maxAttempts) {
            return redirect()->route('quiz.show', $id)
                ->withErrors('Vous avez atteint le nombre maximum de tentatives.');
        }

        // Reprendre tentative en cours ou en créer une
        $attempt = QuizAttempt::firstOrCreate(
            [
                'module_id' => $module->id,
                'user_id'   => $user->id,
                'state'     => 'inprogress',
            ],
            [
                'attempt'   => $doneCount + 1,
                'timestart' => now(),
            ]
        );

        $questions = $module->quizQuestions;

        if ($module->shuffleanswers) {
            $questions = $questions->map(function ($q) {
                $q->setRelation('answers', $q->answers->shuffle());
                return $q;
            });
        }

        // Réponses déjà données dans cette tentative
        $givenAnswers = QuizAttemptAnswer::where('attempt_id', $attempt->id)
            ->get()
            ->keyBy('question_id');

        return view('quiz.attempt', [
            'module'       => $module,
            'attempt'      => $attempt,
            'questions'    => $questions,
            'givenAnswers' => $givenAnswers,
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // SUBMIT — Soumet la tentative
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
                $answerId = $request->input('answers.' . $question->id);

                if ($answerId) {
                    $answer   = QuizAnswer::find($answerId);
                    $fraction = $answer ? (float)$answer->fraction : 0;
                    $points   = $fraction * $question->defaultmark;
                    $sumgrades += $points;

                    QuizAttemptAnswer::updateOrCreate(
                        [
                            'attempt_id'  => $attempt->id,
                            'question_id' => $question->id,
                        ],
                        [
                            'answer_id' => $answerId,
                            'fraction'  => $fraction,
                        ]
                    );
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
    // RESULT — Résultat d'une tentative
    // ─────────────────────────────────────────────────────────

    public function result($moduleId, $attemptId)
    {
        $user    = Auth::user();
        $module  = Module::with(['quizQuestions.answers'])
            ->where('modname', 'quiz')
            ->findOrFail($moduleId);

        $attempt = QuizAttempt::with(['answers.answer', 'answers.question'])
            ->where('module_id', $module->id)
            ->findOrFail($attemptId);

        // Seul l'auteur de la tentative ou le prof peut voir
        $isTeacher = (int)$module->section->course->teacher_id === (int)$user->id;
        if (!$isTeacher && (int)$attempt->user_id !== (int)$user->id) {
            abort(403);
        }

        $grade = $attempt->gradeOutOf($module->grade);

        return view('quiz.result', [
            'module'  => $module,
            'attempt' => $attempt,
            'grade'   => $grade,
        ]);
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
            'section_id'       => 'required|integer',
            'name'             => 'required|string|max:255',
            'intro'            => 'nullable|string',
            'timeopen'         => 'nullable|date',
            'timeclose'        => 'nullable|date',
            'timelimit'        => 'nullable|integer|min:1',
            'grade'            => 'required|numeric|min:0|max:100',
            'attempts'         => 'required|integer|min:0',
            'grademethod'      => 'required|integer|in:0,1,2,3',
            'shuffleanswers'   => 'nullable|boolean',
            'questions'                        => 'nullable|array',
            'questions.*.qtype'                => 'required|in:multichoice,truefalse',
            'questions.*.questiontext'         => 'required|string',
            'questions.*.defaultmark'          => 'required|integer|min:1',
            'questions.*.answers'              => 'nullable|array',
            'questions.*.answers.*.answer'     => 'required|string',
            'questions.*.answers.*.fraction'   => 'required|numeric|in:0,1',
            'questions.*.correct_answer'       => 'nullable|integer',
        ]);

        $section = Section::where('course_id', $course->id)
            ->findOrFail($validated['section_id']);

        DB::transaction(function () use ($validated, $module, $section, $request) {

            $module->update([
                'name'           => $validated['name'],
                'intro'          => $validated['intro'] ?? null,
                'section_id'     => $section->id,
                'grade'          => $validated['grade'],
                'timeopen'       => $validated['timeopen'] ?? null,
                'timeclose'      => $validated['timeclose'] ?? null,
                'timelimit'      => $validated['timelimit'] ? $validated['timelimit'] * 60 : null,
                'attempts'       => $validated['attempts'],
                'grademethod'    => $validated['grademethod'],
                'shuffleanswers' => $request->boolean('shuffleanswers'),
            ]);

            // Supprimer les anciennes questions et les recréer
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
                    foreach ($qData['answers'] as $aData) {
                        QuizAnswer::create([
                            'question_id' => $question->id,
                            'answer'      => $aData['answer'],
                            'fraction'    => (float)$aData['fraction'],
                        ]);
                    }
                }
            }

            // Mettre à jour l'événement calendrier
            $event = \App\Models\Event::where('module_id', $module->id)->first();
            if ($event) {
                $event->title = 'Quiz : ' . $module->name;
                $event->date  = $module->timeclose ?? $module->timeopen;
                $event->save();
            }
        });

        return redirect()
            ->route('quiz.show', $module->id)
            ->with('success', 'Quiz modifié avec succès.');
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

        // Suppression en cascade via FK (questions → answers, attempts → attempt_answers)
        // + événements calendrier
        \App\Models\Event::where('module_id', $module->id)->delete();
        $module->delete();

        return redirect()
            ->route('assignments.index', ['course_id' => $courseId])
            ->with('success', 'Quiz supprimé avec succès.');
    }
}