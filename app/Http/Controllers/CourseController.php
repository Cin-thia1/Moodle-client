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

        $courses = Course::when($search, function ($query, $search) {
            return $query->where('fullname', 'like', '%' . $search . '%');
        })->with('teacher')->get();

        return view('courses.index', compact('courses'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('courses.create', compact('categories'));
    }

 /**
 * Store a newly created course in storage.
 * Automatically assigns the logged-in user as teacher.
 */
public function store(Request $request)
{
    try {
        $validated = $request->validate([
            'fullname'    => 'required|string|max:255',
            'shortname'   => 'required|string|max:255',
            'summary'     => 'nullable|string',
            'numsections' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'startdate'   => 'required|date',
            'enddate'     => 'nullable|date|after_or_equal:startdate',
        ]);
    } catch (ValidationException $e) {
        return redirect()->route('courses.create')
            ->withErrors($e->errors())
            ->withInput();
    }
    // Automatically assign the current logged-in user as the teacher
    $validated['teacher_id'] = Auth::id();

    // Create the course in the database
    $course = Course::create($validated);

    // Log the action for Moodle synchronization
    $this->moodleCourseService->logCourseCreation($course);

    // Redirect back with success message
    return redirect()->route('courses.index')
        ->with('success', 'Cours créé avec succès !');
}
    public function show(Course $course)
    {
        $course->load('sections.modules');
        return view('courses.show', compact('course'));
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
        ]);

        $course->update($validated);

        // Log the action for synchronization
        $this->moodleCourseService->logCourseUpdate($course);

        return redirect()->route('courses.index')->with('success', 'Course updated successfully!');
    }

    public function destroy(Course $course)
    {
        // Log the action for synchronization before deleting
        $this->moodleCourseService->logCourseDeletion($course);

        $course->delete();

        return redirect()->route('courses.index')->with('success', 'Course deleted successfully!');
    }
}
