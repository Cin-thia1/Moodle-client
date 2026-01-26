<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Services\MoodleCourseService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use App\Models\Category;
use App\Models\Competency;

class CourseController extends Controller
{
    protected $moodleCourseService;

    public function __construct(MoodleCourseService $moodleCourseService)
    {
        $this->moodleCourseService = $moodleCourseService;
    }


    public function index(Request $request)
{
    $search = $request->get('search');
    $user = Auth::user();

    if ($user->hasRole('ROLE_TEACHER')) {
        // Pour un enseignant : affiche SEULEMENT les cours qu'il enseigne
        $courses = Course::where('teacher_id', $user->id)
            ->when($search, function ($query, $search) {
                return $query->where('fullname', 'like', '%' . $search . '%');
            })
            ->with('teacher')
            ->get();

        return view('courses.index', compact('courses'));
    }

    elseif ($user->hasRole('ROLE_STUDENT')) {
        // Pour un étudiant : ses cours inscrits + les cours disponibles

        // 1. Cours inscrits (via la table participants)
        $enrolledCourses = $user->courses()
            ->when($search, function ($query, $search) {
                return $query->where('fullname', 'like', '%' . $search . '%');
            })
            ->with('teacher')
            ->get();

        // 2. Cours disponibles (tous les autres)
        $availableCourses = Course::whereNotIn('id', $enrolledCourses->pluck('id'))
            ->when($search, function ($query, $search) {
                return $query->where('fullname', 'like', '%' . $search . '%');
            })
            ->with('teacher')
            ->get();

        return view('courses.index', compact('enrolledCourses', 'availableCourses'));
    }

    else {
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
        try{
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

        // If the creator is a teacher, set them as the course teacher
        $user = Auth::user();
        if ($user && $user->hasRole('ROLE_TEACHER')) {
            $validated['teacher_id'] = $user->id;
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('courses/images', 'public');
            $validated['image'] = $imagePath;
        }

        // Create the course in the database
        $course = Course::create($validated);

        // Log the action for Moodle synchronization
        $this->moodleCourseService->logCourseCreation($course);

        return redirect()->route('courses.show', $course)->with('success', 'Course created successfully!');
    }

    public function show(Course $course)
    {
        $user = Auth::user();
        
        // For teachers: show dashboard with management tools
        if ($user->hasRole('ROLE_TEACHER') && $course->teacher_id === $user->id) {
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
        
        // For students: show course content
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
        return view('courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'fullname' => 'required|string|max:255',
            'shortname' => 'required|string|max:255',
            'summary' => 'nullable|string',
            'numsections' => 'required|integer',
            'startdate' => 'nullable|date',
            'enddate' => 'nullable|date|after_or_equal:startdate',
            'teacher_id' => 'nullable|exists:users,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image upload - delete old image if new one is uploaded
        if ($request->hasFile('image')) {
            if ($course->image && \Storage::disk('public')->exists($course->image)) {
                \Storage::disk('public')->delete($course->image);
            }
            $imagePath = $request->file('image')->store('courses/images', 'public');
            $validated['image'] = $imagePath;
        }

        $course->update($validated);

        // Log the action for synchronization
        $this->moodleCourseService->logCourseUpdate($course);

        return redirect()->route('courses.index')->with('success', 'Course updated successfully!');
    }

    public function destroy(Course $course)
    {
        // Delete associated image if exists
        if ($course->image && \Storage::disk('public')->exists($course->image)) {
            \Storage::disk('public')->delete($course->image);
        }

        // Log the action for synchronization before deleting
        $this->moodleCourseService->logCourseDeletion($course);

        $course->delete();

        return redirect()->route('courses.index')->with('success', 'Course deleted successfully!');
    }
}
