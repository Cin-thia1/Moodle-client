<?php

namespace App\Http\Controllers;
use App\Models\Course;
use App\Models\Module;
use App\Models\Submission;
use App\Models\Section;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    /**
     * FRONT ONLY : Sidebar cours + devoirs (mock)
     */
    
   public function index(Request $request)
{
    $user = Auth::user();
    if (!$user) abort(403, 'Utilisateur non authentifié.');

    // ✅ Cours accessibles (pivot OU prof du cours)
    $courses = Course::query()
        ->whereHas('users', fn($q) => $q->where('users.id', $user->id))
        ->orWhere('teacher_id', $user->id)
        ->orderBy('fullname')
        ->get();

    $selectedCourseId = (int) $request->query('course_id', 0);

    // ✅ Forcer un course_id valide
    if ($courses->isNotEmpty()) {
        if ($selectedCourseId === 0 || !$courses->pluck('id')->contains($selectedCourseId)) {
            $selectedCourseId = (int) $courses->first()->id;
        }
    } else {
        $selectedCourseId = 0;
    }

    // ✅ Est prof de CE cours ?
    $isTeacher = false;
    if ($selectedCourseId) {
        $isTeacher = Course::query()
            ->where('id', $selectedCourseId)
            ->where('teacher_id', $user->id)
            ->exists();
    }

    // ✅ Sections du cours sélectionné
    $sectionIds = collect();
    if ($selectedCourseId) {
        $sectionIds = Section::query()
            ->where('course_id', $selectedCourseId)
            ->pluck('id');
    }

    // ✅ Devoirs du cours sélectionné seulement
    $assignments = collect();

if ($selectedCourseId) {
    $assignments = Module::query()
        ->where('modname', 'assign')
        ->whereHas('section', function ($q) use ($selectedCourseId) {
            $q->where('course_id', $selectedCourseId);
        })
        ->orderByDesc('duedate')
        ->get();
}



    // ✅ Submissions de l'élève (si pas prof du cours)
    $mySubs = collect();
    if (!$isTeacher && $assignments->isNotEmpty()) {
        $mySubs = Submission::query()
            ->where('user_id', $user->id)
            ->whereIn('module_id', $assignments->pluck('id'))
            ->get()
            ->keyBy('module_id');
    }

    return view('assignments.index', [
        'courses' => $courses,
        'selectedCourseId' => $selectedCourseId,
        'assignments' => $assignments,
        'mySubs' => $mySubs,
        'isTeacher' => $isTeacher,
    ]);
}



       public function show($id)
{
    $user = Auth::user();
    if (!$user) abort(403, 'Utilisateur non authentifié.');

    // 1) Module (devoir)
    $module = Module::with(['section.course'])
        ->where('modname', 'assign')
        ->findOrFail($id);

    if (!$module->section || !$module->section->course) {
        abort(404, 'Module mal configuré.');
    }

    $course = $module->section->course;

    // 2) ✅ Vérifier accès au cours (pivot OU prof du cours)
    $hasPivotAccess = $course->users()->where('users.id', $user->id)->exists();
    $isTeacherOfCourse = ((int)$course->teacher_id === (int)$user->id);

    if (!$hasPivotAccess && !$isTeacherOfCourse) {
        abort(403, 'Vous n’avez pas accès à ce cours.');
    }

    // 3) Sidebar cours (cours accessibles : pivot OU prof)
    $courses = Course::query()
        ->whereHas('users', fn($q) => $q->where('users.id', $user->id))
        ->orWhere('teacher_id', $user->id)
        ->orderBy('fullname')
        ->get();

    // 4) Devoirs du cours
    $assignments = Module::where('modname', 'assign')
        ->whereHas('section', fn($q) => $q->where('course_id', $course->id))
        ->orderByDesc('duedate')
        ->get();

    // 5) Étudiants du cours (tous les users du cours sauf le user courant)
    // ⚠️ On ne doit montrer la liste+notes que si c'est le prof du cours
    $students = collect();
    $subByUser = collect();

    if ($isTeacherOfCourse) {
        $students = $course->users()
            ->where('users.id', '!=', $user->id)
            ->orderBy('name')
            ->get();

        $submissions = Submission::where('module_id', $module->id)->get();

        $tmp = [];
        foreach ($students as $student) {
            $submission = $submissions->where('user_id', $student->id)->first();

            $tmp[$student->id] = $submission ? (object)[
                'status' => $submission->status,
                'file' => $submission->file_path,
                'content' => $submission->content,
                'submitted_at' => $submission->submitted_at,
                'grade' => $submission->grade,
            ] : null;
        }

        $subByUser = collect($tmp);
    }

    // 6) Submission de l'utilisateur courant (utile pour élève)
    $mySubmission = Submission::query()
        ->where('module_id', $module->id)
        ->where('user_id', $user->id)
        ->first();

    return view('assignments.show', [
        'courses' => $courses,
        'assignments' => $assignments,
        'module' => $module,
        'students' => $students,
        'subByUser' => $subByUser,
        'mySubmission' => $mySubmission,
        'isTeacher' => $isTeacherOfCourse,
    ]);
}




    

    public function create(Request $request)
    {
        $user = Auth::user();

        // ✅ Cours liés au user via pivot ou dont il est le prof
        $courses = Course::query()
            ->whereHas('users', fn($q) => $q->where('users.id', $user->id))
            ->orWhere('teacher_id', $user->id)
            ->orderBy('fullname')
            ->get();

        if ($courses->isEmpty()) {
            return view('assignments.create', [
                'courses' => $courses,
                'selectedCourseId' => 0,
                'sections' => collect(),
            ]);
        }

        $selectedCourseId = (int) $request->query('course_id', $courses->first()->id);

        // ✅ Sécurité : course_id doit être dans la liste de mes cours
        if (!$courses->pluck('id')->contains($selectedCourseId)) {
            $selectedCourseId = (int) $courses->first()->id;
        }

        // ✅ Sections du cours sélectionné
        $sections = Section::query()
            ->where('course_id', $selectedCourseId)
            ->orderBy('name')
            ->get();

        return view('assignments.create', compact('courses', 'selectedCourseId', 'sections'));
    }


public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'course_id'   => 'required|integer',
            'section_id'  => 'required|integer',
            'name'        => 'required|string|max:255',
            'intro'       => 'nullable|string',
            'activity'    => 'nullable|string',
            'duedate'     => 'nullable|date',
            'grade'       => 'required|numeric|min:0|max:100',
            'pdf'         => 'nullable|file|mimes:pdf|max:10240',
        ]);

        // ✅ Vérifier les permissions (Prof ou Admin)
        if (!$user->hasRole(['ROLE_TEACHER', 'ROLE_ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }

        $course = Course::findOrFail($validated['course_id']);

        // ✅ Vérifier que la section appartient au cours
        $section = Section::query()
            ->where('course_id', $course->id)
            ->findOrFail($validated['section_id']);

        $data = [
            'name'            => $validated['name'],
            'modplural'       => 'Devoirs',
            'downloadcontent' => 0,
            'modname'         => 'assign',
            'section_id'      => $section->id,
            'intro'           => $validated['intro'] ?? null,
            'activity'        => $validated['activity'] ?? null,
            'duedate'         => $validated['duedate'] ?? null,
            'grade'           => $validated['grade'],
        ];

        // ✅ Upload PDF
        if ($request->hasFile('pdf')) {
            $file = $request->file('pdf');

            $filename = time() . '_' . $file->getClientOriginalName();
            $destinationPath = public_path('images/pdf');

            if (!is_dir($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $filename);

            $data['pdf_filename'] = $filename;
            $data['pdf_url'] = '/images/pdf/' . $filename;
            $data['file_path'] = 'images/pdf/' . $filename;
        }

        $module = Module::create($data);
   // Création automatique d'un événement calendrier pour ce devoir
\App\Models\Event::create([
    'title'        => 'Devoir : ' . $module->name,
    'date'         => $module->duedate,
    'type'         => 'cours',                    // valeur autorisée dans votre enum
    'course_id'    => $validated['course_id'],
    'description'  => $module->activity ?? $module->intro ?? 'Rendre le devoir avant la date limite',
]);
        return redirect()
            ->route('assignments.show', $module->id)
            ->with('success', 'Devoir ajouté avec succès.');
    }
   
 public function saveGrade(Request $request, $moduleId, $studentId)
{
    $user = Auth::user();
    if (!$user) abort(403, 'Utilisateur non authentifié.');

    $validated = $request->validate([
        'grade' => 'required|integer|min:0|max:100',
    ]);

    $module = Module::query()
        ->where('modname', 'assign')
        ->findOrFail($moduleId);

    $section = Section::query()->find($module->section_id);
    if (!$section) abort(404, "Section introuvable pour ce devoir.");

    $course = Course::query()->find($section->course_id);
    if (!$course) abort(404, "Cours introuvable pour ce devoir.");

    // Autorisation via pivot ou prof
    if ($course->teacher_id !== $user->id && !$course->users()->where('users.id', $user->id)->exists()) {
        abort(403, "Accès refusé.");
    }

    // ✅ IMPORTANT : module_id (et pas assignment_id)
    $submission = Submission::query()
        ->where('module_id', $module->id)
        ->where('user_id', $studentId)
        ->first();

    if (!$submission) {
        return back()->withErrors(['grade' => "Impossible de noter : l'élève n'a pas soumis."]);
    }

    $submission->grade = $validated['grade'];
    $submission->status = 'graded';
    $submission->graded_at = now();
    $submission->graded_by = $user->id;
    $submission->save();

    return back()->with('success', 'Note enregistrée.');
}


public function gradebook($courseId)
{
    $user = Auth::user();
    if (!$user) abort(403, 'Utilisateur non authentifié.');

    // 1) Cours accessible via pivot ou prof
    $course = Course::query()
        ->where(function ($q) use ($user) {
            $q->whereHas('users', fn($sub) => $sub->where('users.id', $user->id))
              ->orWhere('teacher_id', $user->id);
        })
        ->where('id', $courseId)
        ->firstOrFail();

    // Sidebar cours
    $courses = Course::query()
        ->whereHas('users', fn($q) => $q->where('users.id', $user->id))
        ->orWhere('teacher_id', $user->id)
        ->orderBy('fullname')
        ->get();

    // Sections du cours
    $sectionIds = Section::query()
        ->where('course_id', $course->id)
        ->pluck('id');

    // Devoirs du cours (modules assign)
    $assignments = Module::query()
        ->where('modname', 'assign')
        ->whereIn('section_id', $sectionIds)
        ->orderBy('name')
        ->get();

    // Étudiants = users du cours sauf l'utilisateur courant
    $students = $course->users()
        ->where('users.id', '!=', $user->id)
        ->orderBy('name')
        ->get();

    $assignmentIds = $assignments->pluck('id');

    // Toutes les soumissions pour ces devoirs et ces étudiants
    // IMPORTANT : chez toi c'est submissions.module_id (pas assignment_id)
    $subs = Submission::query()
        ->whereIn('module_id', $assignmentIds)
        ->whereIn('user_id', $students->pluck('id'))
        ->get()
        ->groupBy('user_id');

    // matrix[user_id][module_id] = grade
    // canGrade[user_id][module_id] = true/false (soumis ?)
    $matrix = [];
    $canGrade = [];

    foreach ($students as $student) {
        $matrix[$student->id] = [];
        $canGrade[$student->id] = [];

        $studentSubs = $subs->get($student->id, collect());

        foreach ($assignments as $a) {
            $one = $studentSubs->firstWhere('module_id', $a->id);

            $matrix[$student->id][$a->id] = $one ? $one->grade : null;

            // ✅ autoriser la note seulement si submission existe ET status = submitted/graded
            $canGrade[$student->id][$a->id] = $one && in_array($one->status, ['submitted', 'graded'], true);
        }
    }

    return view('assignments.gradebook', [
        'course' => $course,
        'courses' => $courses,
        'assignments' => $assignments,
        'students' => $students,
        'matrix' => $matrix,
        'canGrade' => $canGrade,
    ]);
}


public function saveGradebook(Request $request, $courseId)
{
    $user = Auth::user();
    if (!$user) abort(403, 'Utilisateur non authentifié.');

    // Cours accessible via pivot ou prof
    $course = Course::query()
        ->where(function ($q) use ($user) {
            $q->whereHas('users', fn($sub) => $sub->where('users.id', $user->id))
              ->orWhere('teacher_id', $user->id);
        })
        ->where('id', $courseId)
        ->firstOrFail();

    // Devoirs du cours
    $sectionIds = Section::query()
        ->where('course_id', $course->id)
        ->pluck('id');

    $assignments = Module::query()
        ->where('modname', 'assign')
        ->whereIn('section_id', $sectionIds)
        ->get();

    $assignmentIds = $assignments->pluck('id')->all();

    // Étudiants du cours (sauf user)
    $students = $course->users()
        ->where('users.id', '!=', $user->id)
        ->get();

    $studentIds = $students->pluck('id')->all();

    // Input : grades[studentId][moduleId] = grade
    $grades = $request->input('grades', []);

    foreach ($grades as $studentId => $byModule) {
        if (!in_array((int)$studentId, $studentIds, true)) continue;

        foreach ($byModule as $moduleId => $grade) {
            if (!in_array((int)$moduleId, $assignmentIds, true)) continue;

            // Champ vide => on ne change rien
            if ($grade === null || $grade === '') continue;

            $gradeInt = (int) $grade;
            if ($gradeInt < 0) $gradeInt = 0;
            if ($gradeInt > 100) $gradeInt = 100;

            // ✅ IMPORTANT : submissions.module_id
            $submission = Submission::query()
                ->where('module_id', $moduleId)
                ->where('user_id', $studentId)
                ->first();

            // ✅ Interdire de noter si pas soumis (pas de submission) OU status pas submitted/graded
            if (!$submission || !in_array($submission->status, ['submitted', 'graded'], true)) {
                continue;
            }

            $submission->grade = $gradeInt;
            $submission->status = 'graded';
            $submission->graded_at = now();
            $submission->graded_by = $user->id;
            $submission->save();
        }
    }

    return back()->with('success', 'Carnet de notes enregistré.');
}

public function submit(Request $request, $moduleId)
{
    $user = Auth::user();
    if (!$user) abort(403, 'Utilisateur non authentifié.');

    // ✅ Récupération explicite du module
    $module = Module::where('modname', 'assign')->findOrFail($moduleId);

    $request->validate([
        'pdf' => 'required|file|mimes:pdf|max:10240',
        'content' => 'nullable|string',
    ]);

    // ✅ Création ou mise à jour submission
    $submission = Submission::firstOrNew([
        'module_id' => $module->id,
        'user_id' => $user->id,
    ]);

    $file = $request->file('pdf');
    $filename = time().'_'.$file->getClientOriginalName();
    $destination = public_path('submissions');

    if (!is_dir($destination)) {
        mkdir($destination, 0755, true);
    }

    $file->move($destination, $filename);

    $submission->file_path = 'submissions/'.$filename;
    $submission->content = $request->input('content');
    $submission->status = 'submitted';
    $submission->submitted_at = now();
    $submission->save();

    return back()->with('success', 'Devoir remis avec succès.');
}

}
