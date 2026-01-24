<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\GradeItem;
use App\Models\Submission;
use App\Models\User;
use App\Models\Course;
use App\Services\MoodleGradeService;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    protected MoodleGradeService $gradeService;

    public function __construct(MoodleGradeService $gradeService)
    {
        $this->gradeService = $gradeService;
    }

    // ============ MÉTHODES LEGACY (anciennes) ============

    public function index()
    {
        $grades = Grade::with(['submission', 'teacher'])->get();
        return view('grades.index', compact('grades'));
    }

    public function create()
    {
        $submissions = Submission::all();
        $teachers = User::where('role', 'teacher')->get();
        return view('grades.create', compact('submissions', 'teachers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'grade' => 'required|integer|min:0|max:100',
            'comment' => 'nullable|string',
            'submission_id' => 'required|exists:submissions,id',
            'teacher_id' => 'required|exists:users,id',
        ]);

        Grade::create($validated);
        return redirect()->route('grades.index')->with('success', 'Grade ajouté avec succès.');
    }

    public function show(Grade $grade)
    {
        return view('grades.show', compact('grade'));
    }

    public function edit(Grade $grade)
    {
        $submissions = Submission::all();
        $teachers = User::where('role', 'teacher')->get();
        return view('grades.edit', compact('grade', 'submissions', 'teachers'));
    }

    public function update(Request $request, Grade $grade)
    {
        $validated = $request->validate([
            'grade' => 'required|integer|min:0|max:100',
            'comment' => 'nullable|string',
            'submission_id' => 'required|exists:submissions,id',
            'teacher_id' => 'required|exists:users,id',
        ]);

        $grade->update($validated);
        return redirect()->route('grades.index')->with('success', 'Grade mis à jour avec succès.');
    }

    public function destroy(Grade $grade)
    {
        $grade->delete();
        return redirect()->route('grades.index')->with('success', 'Grade supprimé avec succès.');
    }

    // ============ NOUVELLES MÉTHODES (Section Notes) ============

    /**
     * Affiche la liste des critères d'évaluation d'un cours
     */
    public function courseItems(Course $course)
    {
        $this->authorize('view_grades');
        
        $gradeItems = $this->gradeService->getCourseGradeItems($course->id);
        return view('grades.course-items', compact('course', 'gradeItems'));
    }

    /**
     * Affiche les notes d'un utilisateur
     */
    public function userGrades(Course $course)
    {
        $user = auth()->user();
        
        $this->authorize('view_own_grades');

        $grades = $this->gradeService->getUserGrades($course->id, $user->id);
        return view('grades.user', compact('course', 'user', 'grades'));
    }

    /**
     * Affiche le tableau récapitulatif des notes du cours
     */
    public function courseGradebook(Course $course)
    {
        $this->authorize('view_grades');

        $participants = $course->participants()->active()->with('user')->get();
        $gradeItems = $this->gradeService->getCourseGradeItems($course->id);

        return view('grades.gradebook', compact('course', 'participants', 'gradeItems'));
    }

    /**
     * Affiche les statistiques pour un critère d'évaluation
     */
    public function itemStatistics(Course $course, GradeItem $gradeItem)
    {
        $this->authorize('view_grades');

        $statistics = $this->gradeService->getGradeStatistics($gradeItem->id);
        return view('grades.statistics', compact('course', 'gradeItem', 'statistics'));
    }

    /**
     * Synchronise les critères d'évaluation depuis Moodle
     */
    public function syncItems(Course $course)
    {
        $this->authorize('view_grades');

        $result = $this->gradeService->syncCourseGradeItems($course->id);

        return redirect()->route('grades.courseItems', $course)
            ->with('success', "Synchronisation complétée: {$result['synced']} critères synchronisés");
    }

    /**
     * Synchronise les notes d'un utilisateur depuis Moodle
     */
    public function syncUserGrades(Course $course, int $userId)
    {
        $this->authorize('view_grades');

        $result = $this->gradeService->syncUserGrades($course->id, $userId);

        return redirect()->route('grades.userGrades', [$course, $userId])
            ->with('success', "Synchronisation complétée: {$result['synced']} notes synchronisées");
    }
}

