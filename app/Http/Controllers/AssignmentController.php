<?php

namespace App\Http\Controllers;
use App\Models\Course;
use App\Models\Module;
use App\Models\Submission;
use App\Models\Section;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    /**
     * FRONT ONLY : Sidebar cours + devoirs (mock)
     */
    /*public function __construct()
    {
        $this->middleware('auth');
    }*/
    public function index(Request $request)
    {
        $user = Auth::user();

        // ✅ Cours liés au user via pivot course_user
        $courses = Course::query()
            ->whereHas('users', fn($q) => $q->where('users.id', $user->id))
            ->orderBy('fullname')
            ->get();

        $selectedCourseId = (int) $request->query('course_id', 0);

        if ($courses->isNotEmpty()) {
            if ($selectedCourseId === 0 || !$courses->pluck('id')->contains($selectedCourseId)) {
                $selectedCourseId = (int) $courses->first()->id;
            }
        } else {
            $selectedCourseId = 0;
        }

        // ✅ Sections du cours sélectionné
        $sectionIds = collect();
        if ($selectedCourseId) {
            $sectionIds = Section::query()
                ->where('course_id', $selectedCourseId)
                ->pluck('id');
        }

        // ✅ Devoirs (modules assign)
        $assignments = Module::query()
            ->where('modname', 'assign')
            ->when($sectionIds->isNotEmpty(), fn($q) => $q->whereIn('section_id', $sectionIds))
            ->orderByDesc('duedate')
            ->get();

        return view('assignments.index', [
            'courses' => $courses,
            'selectedCourseId' => $selectedCourseId,
            'assignments' => $assignments,
        ]);
    }

    /**
     * FRONT ONLY : Détail devoir + tableau élèves (mock)
     */
    /*public function show($id)
    {
        // Mock cours
        $courses = collect([
            (object)['id' => 1, 'fullname' => 'Mathématiques'],
            (object)['id' => 2, 'fullname' => 'Informatique'],
            (object)['id' => 3, 'fullname' => 'Physique'],
        ]);

        // Mock devoir courant
        $module = (object)[
            'id' => (int)$id,
            'course_id' => 1,
            'name' => 'Devoir 1 - Algèbre',
            'description' => 'Résoudre les exercices 1 à 5. Joindre un PDF.',
            'due_date' => '2026-01-20 23:59',
            'max_grade' => 20,
        ];

        // Mock liste devoirs du cours (menu constant)
        $assignments = collect([
            (object)['id' => 10, 'course_id' => 1, 'name' => 'Devoir 1 - Algèbre'],
            (object)['id' => 11, 'course_id' => 1, 'name' => 'Devoir 2 - Fonctions'],
        ]);

        // Mock étudiants du cours
        $students = collect([
            (object)['id' => 101, 'name' => 'Alice N.', 'email' => 'alice@test.com'],
            (object)['id' => 102, 'name' => 'Bruno K.', 'email' => 'bruno@test.com'],
            (object)['id' => 103, 'name' => 'Carla P.', 'email' => 'carla@test.com'],
        ]);

        // Mock soumissions indexées par student_id
        $subByUser = collect([
            101 => (object)['status' => 'submitted', 'file' => 'devoir_alice.pdf', 'comment' => 'Voici mon devoir', 'grade' => 16],
            102 => null,
            103 => (object)['status' => 'graded', 'file' => 'devoir_carla.pdf', 'comment' => null, 'grade' => 18],
        ]);

        return view('assignments.show', compact('courses', 'assignments', 'module', 'students', 'subByUser'));
    }*/
       public function show($id)
{
      $user = \Illuminate\Support\Facades\Auth::user();

    if (!$user) {
        abort(403, 'Utilisateur non authentifié.');
    }

    // 1️⃣ Récupérer le module (devoir)
    $module = Module::with(['section.course'])
        ->where('modname', 'assign')
        ->findOrFail($id);

    if (!$module->section || !$module->section->course) {
        abort(404, 'Module mal configuré.');
    }

    $course = $module->section->course;

    // 2️⃣ Vérifier accès au cours
    if (!$course->users()->where('users.id', $user->id)->exists()) {
        abort(403, 'Vous n’avez pas accès à ce cours.');
    }

    // 3️⃣ Tous les cours du prof
    $courses = Course::whereHas('users', function ($q) use ($user) {
        $q->where('users.id', $user->id);
    })->orderBy('fullname')->get();

    // 4️⃣ Tous les devoirs du cours
    $assignments = Module::where('modname', 'assign')
        ->whereHas('section', function ($q) use ($course) {
            $q->where('course_id', $course->id);
        })
        ->orderByDesc('duedate')
        ->get();

    // 5️⃣ Étudiants (tous les users du cours sauf le prof)
    $students = $course->users()
        ->where('users.id', '!=', $user->id)
        ->orderBy('name')
        ->get();

    // 6️⃣ Charger toutes les soumissions du module
    $submissions = Submission::where('module_id', $module->id)->get();

    $subByUser = [];

    foreach ($students as $student) {
        $submission = $submissions->where('user_id', $student->id)->first();

        if ($submission) {
            $subByUser[$student->id] = (object)[
                'status' => $submission->status,
                'file' => $submission->file_path,
                'content' => $submission->content,
                'submitted_at' => $submission->submitted_at,
                'grade' => $submission->grade,
            ];
        } else {
            $subByUser[$student->id] = null;
        }
    }

    return view('assignments.show', [
        'courses' => $courses,
        'assignments' => $assignments,
        'module' => $module,
        'students' => $students,
        'subByUser' => collect($subByUser),
    ]);
}



    

    public function create(Request $request)
    {
        $user = Auth::user();

        // ✅ Cours liés au user via pivot
        $courses = Course::query()
            ->whereHas('users', fn($q) => $q->where('users.id', $user->id))
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

        // ✅ Vérifier que le cours appartient au user via pivot
        $course = Course::query()
            ->whereHas('users', fn($q) => $q->where('users.id', $user->id))
            ->where('id', $validated['course_id'])
            ->firstOrFail();

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

        return redirect()
            ->route('assignments.show', $module->id)
            ->with('success', 'Devoir ajouté avec succès.');
    }
   
   /* public function saveGrade(Request $request, $moduleId, $studentId)
    {
        $teacher = Auth::user();
        if (!$teacher) abort(403, 'Utilisateur non authentifié.');
        if (!$teacher->hasRole('ROLE_TEACHER')) abort(403, "Accès refusé.");

        $validated = $request->validate([
            'grade' => 'required|integer|min:0|max:100',
        ]);

        $module = Module::with(['section.course'])
            ->where('modname', 'assign')
            ->findOrFail($moduleId);

        $course = $module->section?->course;
        if (!$course) abort(404, "Cours introuvable.");

        if (!$course->users()->where('users.id', $teacher->id)->exists()) {
            abort(403, "Vous n’avez pas accès à ce cours.");
        }

        $submission = Submission::query()
            ->where('assignment_id', $module->id)
            ->where('user_id', $studentId)
            ->first();

        if (!$submission) {
            return back()->withErrors(['grade' => "Impossible de noter : l'élève n'a pas soumis."]);
        }

        $submission->grade = $validated['grade'];
        $submission->status = 'graded';
        $submission->graded_at = now();
        $submission->graded_by = $teacher->id;
        $submission->save();

        return back()->with('success', 'Note enregistrée.');
    }

    // ✅ CARNET DE NOTES (vrai) : élèves × devoirs
    public function gradebook($courseId)
    {
        $user = Auth::user();
        if (!$user) abort(403, 'Utilisateur non authentifié.');
        if (!$user->hasRole('ROLE_TEACHER')) abort(403, "Accès refusé.");

        // cours accessible via pivot
        $course = Course::query()
            ->whereHas('users', fn($q) => $q->where('users.id', $user->id))
            ->where('id', $courseId)
            ->firstOrFail();

        // cours sidebar
        $courses = Course::whereHas('users', fn($q) => $q->where('users.id', $user->id))
            ->orderBy('fullname')
            ->get();

        // devoirs du cours
        $sectionIds = Section::where('course_id', $course->id)->pluck('id');

        $assignments = Module::query()
            ->where('modname', 'assign')
            ->whereIn('section_id', $sectionIds)
            ->orderBy('name')
            ->get();

        // étudiants = users du cours sauf prof actuel
        $students = $course->users()
            ->where('users.id', '!=', $user->id)
            ->orderBy('name')
            ->get();

        // toutes les notes du cours (soumissions graded ou pas)
        $assignmentIds = $assignments->pluck('id');

        $subs = Submission::query()
            ->whereIn('assignment_id', $assignmentIds)
            ->whereIn('user_id', $students->pluck('id'))
            ->get()
            ->groupBy(fn($s) => $s->user_id);

        // matrix[user_id][assignment_id] = grade
        $matrix = [];
        foreach ($students as $student) {
            $matrix[$student->id] = [];
            foreach ($assignments as $a) {
                $studentSubs = $subs->get($student->id, collect());
                $one = $studentSubs->firstWhere('assignment_id', $a->id);
                $matrix[$student->id][$a->id] = $one?->grade; // null si pas de note
            }
        }

        return view('assignments.gradebook', [
            'course' => $course,
            'courses' => $courses,
            'assignments' => $assignments,
            'students' => $students,
            'matrix' => $matrix,
        ]);
    }*/
}
