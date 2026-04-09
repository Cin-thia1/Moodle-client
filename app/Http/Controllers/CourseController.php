<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Repositories\CourseRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use App\Models\Category;
use App\Models\Competency;

class CourseController extends Controller
{
    protected CourseRepository $courseRepository;

    public function __construct(CourseRepository $courseRepository)
    {
        $this->courseRepository = $courseRepository;
    }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $user = Auth::user();

        if ($user->hasRole('ROLE_TEACHER')) {
            // Pour un enseignant : affiche ses cours + tous les cours du système
            $courses = Course::where('teacher_id', $user->id)
                ->when($search, function ($query, $search) {
                    return $query->where('fullname', 'like', '%' . $search . '%');
                })
                ->with('teacher')
                ->get();

            // Récupérer tous les cours du système pour la section "Tous les cours"
            $allCourses = Course::when($search, function ($query, $search) {
                return $query->where('fullname', 'like', '%' . $search . '%');
            })
                ->with('teacher')
                ->get();

            return view('courses.index', compact('courses', 'allCourses'));
        } elseif ($user->hasRole('ROLE_STUDENT')) {
            // Pour un étudiant : ses cours inscrits + les cours disponibles
            $enrolledCourses = $user->courses()
                ->when($search, function ($query, $search) {
                    return $query->where('fullname', 'like', '%' . $search . '%');
                })
                ->with('teacher')
                ->get();

            $availableCourses = Course::whereNotIn('id', $enrolledCourses->pluck('id'))
                ->when($search, function ($query, $search) {
                    return $query->where('fullname', 'like', '%' . $search . '%');
                })
                ->with('teacher')
                ->get();

            return view('courses.index', compact('enrolledCourses', 'availableCourses'));
        } else {
            // Admin ou autre rôle : tous les cours
            $courses = Course::when($search, function ($query, $search) {
                return $query->where('fullname', 'like', '%' . $search . '%');
            })->with('teacher')->get();

            return view('courses.index', compact('courses'));
        }
    }

    public function create()
    {
        $categories = Category::all();
        return view('courses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'fullname' => 'required|string|max:255',
                'shortname' => 'required|string|max:255',
                'summary' => 'nullable|string',
                'numsections' => 'required|integer',
                'category_id' => 'required|exists:categories,id',
                'startdate' => 'required|date',
                'enddate' => 'nullable|date|after_or_equal:startdate',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
        } catch (ValidationException $e) {
            return redirect()->route('courses.create')->with('error', 'Course not created ! Check parameters');
        }

        // Si le créateur est un enseignant, le définir comme enseignant du cours
        $user = Auth::user();
        if ($user && $user->hasRole('ROLE_TEACHER')) {
            $validated['teacher_id'] = $user->id;
        }

        // Gestion du téléchargement d'image
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('courses/images', 'public');
            $validated['image'] = $imagePath;
        }

        // Créer le cours via le Repository (enqueue automatiquement la sync)
        $course = $this->courseRepository->create($validated);

        return redirect()->route('courses.show', $course)->with('success', 'Course created successfully!');
    }

    public function show(Course $course)
    {
        $user = Auth::user();

        // Pour les enseignants : afficher le tableau de bord avec les outils de gestion
        if ($user->hasRole(['ROLE_TEACHER', 'ROLE_ADMIN'])) {
            $participants = $course->participants()->with('user')->get();
            $announcements = $course->announcements()->latest('published_at')->get();
            $documents = $course->documents()->get();
            $gradeItems = $course->gradeItems()->get();
            $competencies = $course->competencies()->get();
            $availableCompetencies = Competency::whereNotIn('id', $competencies->pluck('id'))
                ->orderBy('shortname')
                ->get();
            $sections = $course->sections()->get();

            return view('courses.teacher-dashboard', compact('course', 'participants', 'announcements', 'documents', 'gradeItems', 'competencies', 'availableCompetencies', 'sections'));
        }

        // Pour les étudiants : afficher le contenu du cours
        $course->load(['sections.modules', 'documents', 'competencies']);

        // Récupérer les compétences validées par l'étudiant pour ce cours
        $userCompletedCompetencyIds = [];
        if ($user) {
            $userCompletedCompetencyIds = \App\Models\UserCompetency::where('user_id', $user->id)
                ->whereIn('competency_id', $course->competencies->pluck('id'))
                ->where('proficiency', 1) // 1 = Complété
                ->pluck('competency_id')
                ->toArray();
        }

        return view('courses.show', compact('course', 'userCompletedCompetencyIds'));
    }

    public function edit(Course $course)
    {
        if (!Auth::user()->hasRole(['ROLE_TEACHER', 'ROLE_ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }
        $categories = Category::all();
        return view('courses.edit', compact('course', 'categories'));
    }

    public function update(Request $request, Course $course)
    {
        if (!Auth::user()->hasRole(['ROLE_TEACHER', 'ROLE_ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'fullname' => 'required|string|max:255',
            'shortname' => 'required|string|max:255',
            'summary' => 'nullable|string',
            'numsections' => 'required|integer',
            'startdate' => 'nullable|date',
            'enddate' => 'nullable|date|after_or_equal:startdate',
            'teacher_id' => 'nullable|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Gestion du téléchargement d'image - supprimer l'ancienne si nouvelledelle transmise
        if ($request->hasFile('image')) {
            if ($course->image && \Storage::disk('public')->exists($course->image)) {
                \Storage::disk('public')->delete($course->image);
            }
            $imagePath = $request->file('image')->store('courses/images', 'public');
            $validated['image'] = $imagePath;
        }

        // Mettre à jour le cours via le Repository (enqueue automatiquement la sync)
        $course = $this->courseRepository->update($course, $validated);

        return redirect()->route('courses.show', $course)->withFragment('settings')->with('success', 'Course updated successfully!');
    }

    public function destroy(Course $course)
    {
        if (!Auth::user()->hasRole(['ROLE_TEACHER', 'ROLE_ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }

        // Supprimer l'image associée si elle existe
        if ($course->image && \Storage::disk('public')->exists($course->image)) {
            \Storage::disk('public')->delete($course->image);
        }

        // Supprimer le cours via le Repository (enqueue automatiquement la sync)
        $this->courseRepository->delete($course);

        return redirect()->route('courses.index')->with('success', 'Course deleted successfully!');
    }
}

