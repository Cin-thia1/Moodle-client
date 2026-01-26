<?php

namespace App\Http\Controllers;

use App\Models\Competency;
use App\Models\Course;
use App\Services\MoodleCompetencyService;
use Illuminate\Http\Request;

class CompetencyController extends Controller
{
    protected MoodleCompetencyService $competencyService;

    public function __construct(MoodleCompetencyService $competencyService)
    {
        $this->competencyService = $competencyService;
    }

    /**
     * Affiche la liste des compétences d'un cours
     */
    public function index()
    {
        $this->authorize('view_competencies');
        
        $competencies = Competency::all();
        return view('competencies.index', ['competencies' => $competencies, 'course' => null]);
    }

    /**
     * Affiche les compétences d'un utilisateur
     */
    public function userCompetencies(int $userId)
    {
        $user = \App\Models\User::findOrFail($userId);
        
        if (auth()->id() === $userId) {
            $this->authorize('view_own_competencies');
        } else {
            $this->authorize('view_competencies');
        }

        $competencies = $this->competencyService->getUserCompetencies($userId);
        return view('competencies.user-competencies', compact('user', 'competencies'));
    }

    /**
     * Affiche les compétences complétées d'un utilisateur
     */
    public function userCompletedCompetencies(int $userId)
    {
        $user = \App\Models\User::findOrFail($userId);
        
        if (auth()->id() === $userId) {
            $this->authorize('view_own_competencies');
        } else {
            $this->authorize('view_competencies');
        }

        $competencies = $this->competencyService->getUserCompletedCompetencies($userId);
        return view('competencies.user-completed', compact('user', 'competencies'));
    }

    /**
     * Affiche le formulaire de création de compétence
     */
    public function create(Request $request)
    {
        $this->authorize('manage_competencies');
        $courseId = $request->query('course_id');
        $course = $courseId ? Course::find($courseId) : null;
        return view('competencies.create', compact('course'));
    }

    /**
     * Affiche le formulaire d'édition d'une compétence
     */
    public function edit(Competency $competency)
    {
        $this->authorize('manage_competencies');
        return view('competencies.edit', compact('competency'));
    }

    /**
     * Sauvegarde une nouvelle compétence
     */
    public function store(Request $request)
    {
        $this->authorize('manage_competencies');

        $validated = $request->validate([
            'shortname' => 'required|string|unique:competencies',
            'idnumber' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'boolean',
            'course_id' => 'nullable|exists:courses,id',
        ]);

        $competency = $this->competencyService->createCompetency($validated);

        // Si un cours est spécifié, on associe la compétence à ce cours
        if (!empty($validated['course_id'])) {
            $course = Course::find($validated['course_id']);
            if ($course) {
                $course->competencies()->syncWithoutDetaching([
                    $competency->id => [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                ]);
                return redirect()->route('courses.show', $course)->withFragment('competencies')->with('success', 'Compétence créée et associée au cours avec succès');
            }
        }

        return redirect()->route('competencies.index')->with('success', 'Compétence créée avec succès');
    }

    /**
     * Met à jour une compétence
     */
    public function update(Request $request, Competency $competency)
    {
        $this->authorize('manage_competencies');

        $validated = $request->validate([
            'shortname' => 'required|string|unique:competencies,shortname,' . $competency->id,
            'idnumber' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'boolean',
        ]);

        $competency->update($validated);

        return redirect()->route('competencies.index')->with('success', 'Compétence mise à jour avec succès');
    }

    /**
     * Affiche les compétences d'un cours
     */
    public function courseCompetencies(Course $course)
    {
        $competencies = $course->competencies()->get();
        return view('competencies.index', compact('course', 'competencies'));
    }

    /**
     * Affiche les statistiques pour une compétence
     */
    public function statistics(Competency $competency)
    {
        $this->authorize('view_competencies');

        $stats = $this->competencyService->getCompetencyStatistics($competency->id);
        return view('competencies.statistics', compact('competency', 'stats'));
    }

    /**
     * Marque une compétence comme complète pour un utilisateur
     */
    public function markComplete(int $userId, int $competencyId)
    {
        $this->authorize('mark_competency_complete');

        $this->competencyService->updateUserCompetency($userId, $competencyId, [
            'proficiency' => \App\Models\UserCompetency::PROFICIENCY_COMPLETE,
            'reviewed_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Compétence marquée comme complète');
    }

    /**
     * Synchronise les compétences d'un cours depuis Moodle
     */
    public function syncCourse(Course $course)
    {
        $this->authorize('manage_competencies');

        $result = $this->competencyService->syncCourseCompetencies($course->id);

        return redirect()->route('competencies.course', $course)
            ->with('success', "Synchronisation complétée: {$result['synced']} compétences synchronisées");
    }

    /**
     * Synchronise les compétences d'un utilisateur depuis Moodle
     */
    public function syncUser(int $userId)
    {
        $this->authorize('manage_competencies');

        $result = $this->competencyService->syncUserCompetencies($userId);

        return redirect()->route('competencies.userCompetencies', $userId)
            ->with('success', "Synchronisation complétée: {$result['synced']} compétences synchronisées");
    }
}
