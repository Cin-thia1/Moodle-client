<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Course;
use App\Services\MoodleSectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SectionController extends Controller
{
    protected $moodleSectionService;

    public function __construct(MoodleSectionService $moodleSectionService)
    {
        $this->moodleSectionService = $moodleSectionService;
    }

    /**
     * Liste les sections d'un cours
     */
    public function index(Course $course)
    {
        // Seul l'enseignant du cours peut voir la liste complète
        if (Auth::user()->hasRole('ROLE_TEACHER') && $course->teacher_id === Auth::id()) {
            $sections = $course->sections()->orderBy('id')->get();
            return view('sections.index', compact('course', 'sections'));
        }

        // Les étudiants voient aussi les sections
        $sections = $course->sections()->orderBy('id')->get();
        return view('sections.student-view', compact('course', 'sections'));
    }

    /**
     * Affiche le formulaire de création (enseignant seulement)
     */
    public function create(Course $course)
    {
        // Vérifier que l'utilisateur est l'enseignant du cours
        if (!Auth::user()->hasRole('ROLE_TEACHER') || $course->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        return view('sections.create', compact('course'));
    }

    /**
     * Crée une nouvelle section
     */
    public function store(Request $request, Course $course)
    {
        // Vérifier que l'utilisateur est l'enseignant du cours
        if (!Auth::user()->hasRole('ROLE_TEACHER') || $course->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $section = $course->sections()->create($validated);

        // Log the action for synchronization
        $this->moodleSectionService->logSectionCreation($section);

        return redirect()->route('courses.teacher-dashboard', $course)->with('success', 'Section créée avec succès');
    }

    /**
     * Affiche une section spécifique
     */
    public function show(Course $course, Section $section)
    {
        $section->load('course', 'modules');
        return view('sections.show', compact('course', 'section'));
    }

    /**
     * Affiche le formulaire de modification (enseignant seulement)
     */
    public function edit(Course $course, Section $section)
    {
        // Vérifier que l'utilisateur est l'enseignant du cours
        if (!Auth::user()->hasRole('ROLE_TEACHER') || $course->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        return view('sections.edit', compact('course', 'section'));
    }

    /**
     * Met à jour une section
     */
    public function update(Request $request, Course $course, Section $section)
    {
        // Vérifier que l'utilisateur est l'enseignant du cours
        if (!Auth::user()->hasRole('ROLE_TEACHER') || $course->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $section->update($validated);

        // Log the action for synchronization
        $this->moodleSectionService->logSectionUpdate($section);

        return redirect()->route('courses.teacher-dashboard', $course)->with('success', 'Section mise à jour avec succès');
    }

    /**
     * Supprime une section
     */
    public function destroy(Course $course, Section $section)
    {
        // Vérifier que l'utilisateur est l'enseignant du cours
        if (!Auth::user()->hasRole('ROLE_TEACHER') || $course->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Log the action for synchronization before deleting
        $this->moodleSectionService->logSectionDeletion($section);

        $section->delete();

        return redirect()->route('courses.teacher-dashboard', $course)->with('success', 'Section supprimée avec succès');
    }
}
