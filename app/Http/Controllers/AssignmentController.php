<?php

namespace App\Http\Controllers;
use App\Models\Course;
use App\Models\Module;
use App\Models\Section;
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

    // 1) Cours de l'enseignant connecté
    $courses = Course::query()
        ->where('teacher_id', $user->id)
        ->orderBy('fullname')
        ->get();

    $selectedCourseId = (int) $request->query('course_id', 0);

    if ($selectedCourseId === 0 && $courses->count() > 0) {
        $selectedCourseId = (int) $courses->first()->id;
    }

    // 2) Récupérer les sections du cours sélectionné
    $sectionIds = collect();

    if ($selectedCourseId) {
        $selectedCourse = Course::with('sections:id,course_id')
            ->find($selectedCourseId);

        // Sécurité : si le cours n'appartient pas au prof, on vide
        if (!$selectedCourse || (int)$selectedCourse->teacher_id !== (int)$user->id) {
            $selectedCourseId = 0;
            $sectionIds = collect();
        } else {
            $sectionIds = $selectedCourse->sections->pluck('id');
        }
    }

    // 3) Modules de type devoir (assign) dans les sections du cours
    $assignments = Module::query()
        ->where('modname', 'assign')
        ->when($sectionIds->isNotEmpty(), fn($q) => $q->whereIn('section_id', $sectionIds))
        ->orderByDesc('duedate') // ou created_at si tu as, mais toi tu as duedate
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
    public function show($id)
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
    }

    /**
     * FRONT ONLY : Carnet de notes (mock)
     */
    public function gradebook($courseId)
    {
        $course = (object)['id' => (int)$courseId, 'fullname' => 'Mathématiques'];

        $assignments = collect([
            (object)['id' => 10, 'name' => 'Devoir 1'],
            (object)['id' => 11, 'name' => 'Devoir 2'],
        ]);

        $students = collect([
            (object)['id' => 101, 'name' => 'Alice N.'],
            (object)['id' => 102, 'name' => 'Bruno K.'],
            (object)['id' => 103, 'name' => 'Carla P.'],
        ]);

        $matrix = [
            101 => [10 => 16, 11 => 14],
            102 => [10 => null, 11 => 12],
            103 => [10 => 18, 11 => 19],
        ];

        return view('assignments.gradebook', compact('course', 'assignments', 'students', 'matrix'));
    }

    public function create(Request $request)
{
    $user = Auth::user();

    // Cours du prof connecté
    $courses = Course::where('teacher_id', $user->id)
        ->orderBy('fullname')
        ->get();

    $selectedCourseId = (int) $request->query('course_id', 0);
    if ($selectedCourseId === 0 && $courses->count() > 0) {
        $selectedCourseId = (int) $courses->first()->id;
    }

    // Sections du cours sélectionné (pour remplir le select)
    $sections = collect();
    if ($selectedCourseId) {
        $course = Course::where('teacher_id', $user->id)->find($selectedCourseId);
        if ($course) {
            $sections = Section::where('course_id', $course->id)
                ->orderBy('name')
                ->get();
        }
    }

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
        'pdf'         => 'nullable|file|mimes:pdf|max:10240', // 10MB
    ]);

    // ✅ Sécurité: vérifier que le cours appartient bien à ce prof
    $course = Course::where('teacher_id', $user->id)->findOrFail($validated['course_id']);

    // ✅ Sécurité: vérifier que la section appartient bien au cours
    $section = Section::where('course_id', $course->id)->findOrFail($validated['section_id']);

    $data = [
        'name'        => $validated['name'],
        'modplural' => 'Devoirs',     // ou "Assignments"
        'downloadcontent' => 0,       // ou false

        'modname'     => 'assign',
        'section_id'  => $section->id,
        'intro'       => $validated['intro'] ?? null,
        'activity'    => $validated['activity'] ?? null,
        'duedate'     => $validated['duedate'] ?? null,
        'grade'       => $validated['grade'],
    ];

    // Upload PDF (énoncé)
    /*if ($request->hasFile('pdf')) {
        $path = $request->file('pdf')->store('assignments', 'public');

        // tu as dans Module: pdf_filename / pdf_url + file_path
        $data['file_path'] = $path;
        $data['pdf_filename'] = $request->file('pdf')->getClientOriginalName();
        $data['pdf_url'] = Storage::disk('public')->url($path);
    }*/
        if ($request->hasFile('pdf')) {

    $file = $request->file('pdf');

    // Nom unique pour éviter les collisions
    $filename = time().'_'.$file->getClientOriginalName();

    // Destination : public/images/pdf
    $destinationPath = public_path('images/pdf');

    // Déplacement réel du fichier
    $file->move($destinationPath, $filename);

    // Sauvegarde en base
    $data['pdf_filename'] = $filename;
    $data['pdf_url'] = '/images/pdf/' . $filename;

    // Optionnel : si tu veux garder file_path cohérent
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
}
