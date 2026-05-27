<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Course;
use App\Repositories\SectionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SectionController extends Controller
{
    protected SectionRepository $sectionRepository;

    public function __construct(SectionRepository $sectionRepository)
    {
        $this->sectionRepository = $sectionRepository;
    }

    /**
     * Liste les sections d'un cours
     */
    public function index(Course $course)
    {
        // Seul l'enseignant du cours ou un manager peut voir la liste complète
        if ((Auth::user()->hasRole('ROLE_TEACHER') && $course->teacher_id === Auth::id()) || Auth::user()->hasRole('ROLE_MANAGER')) {
            $sections = $this->sectionRepository->getByCourseId($course->id);
            return view('sections.index', compact('course', 'sections'));
        }

        // Les étudiants voient aussi les sections
        $sections = $this->sectionRepository->getByCourseId($course->id);
        return view('sections.student-view', compact('course', 'sections'));
    }

    /**
     * Affiche le formulaire de création (enseignant seulement)
     */
    public function create(Course $course)
    {
        // Vérifier que l'utilisateur est l'enseignant du cours
        if (!Auth::user()->hasRole(['ROLE_TEACHER', 'ROLE_ADMIN', 'ROLE_MANAGER'])) {
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
        if (!Auth::user()->hasRole(['ROLE_TEACHER', 'ROLE_ADMIN', 'ROLE_MANAGER'])) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'summary' => 'nullable|string',
        ]);

        // Déterminer la position automatiquement
        $lastSection = $course->sections()->orderBy('position', 'desc')->first();
        $validated['position'] = $lastSection ? $lastSection->position + 1 : 0;
        $validated['course_id'] = $course->id;

        // Créer la section via le Repository (enqueue automatiquement la sync)
        $section = $this->sectionRepository->create($validated);

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
        if (!Auth::user()->hasRole(['ROLE_TEACHER', 'ROLE_ADMIN', 'ROLE_MANAGER'])) {
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
        if (!Auth::user()->hasRole(['ROLE_TEACHER', 'ROLE_ADMIN', 'ROLE_MANAGER'])) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'summary' => 'nullable|string',
            'visible' => 'nullable|boolean',
            'updated_at' => 'nullable|string',
        ]);

        if ($request->filled('updated_at') && $section->updated_at) {
            $submittedUpdatedAt = \Carbon\Carbon::parse($request->updated_at);
            if (!$section->updated_at->eq($submittedUpdatedAt)) {
                return back()
                    ->withInput()
                    ->withErrors(['updated_at' => 'Ce contenu a été modifié par un autre utilisateur entre temps. Veuillez recharger la page et réessayer.']);
            }
        }

        // Mettre à jour la section via le Repository (enqueue automatiquement la sync)
        $section = $this->sectionRepository->update($section, $validated);

        return redirect()->route('courses.teacher-dashboard', $course)->with('success', 'Section mise à jour avec succès');
    }

    /**
     * Supprime une section
     */
    public function destroy(Course $course, Section $section)
    {
        // Vérifier que l'utilisateur est l'enseignant du cours
        if (!Auth::user()->hasRole(['ROLE_TEACHER', 'ROLE_ADMIN', 'ROLE_MANAGER'])) {
            abort(403, 'Unauthorized action.');
        }

        // Supprimer la section via le Repository (enqueue automatiquement la sync)
        $this->sectionRepository->delete($section);

        return redirect()->route('courses.teacher-dashboard', $course)->with('success', 'Section supprimée avec succès');
    }

    /**
     * Réorganise les positions des sections (drag & drop)
     */
    public function reorder(Request $request, Course $course)
    {
        // Vérifier que l'utilisateur est l'enseignant du cours
        if (!Auth::user()->hasRole(['ROLE_TEACHER', 'ROLE_ADMIN', 'ROLE_MANAGER'])) {
            abort(403, 'Unauthorized action.');
        }

        $positions = $request->validate([
            'positions' => 'required|array',
            'positions.*' => 'integer',
        ])['positions'];

        // Réorganiser les positions via le Repository
        $this->sectionRepository->reorder($positions);

        return response()->json(['success' => true, 'message' => 'Sections réorganisées avec succès']);
    }
}

