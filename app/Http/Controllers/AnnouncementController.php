<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Course;
use App\Repositories\AnnouncementRepository;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    protected AnnouncementRepository $repository;

    public function __construct(AnnouncementRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Affiche la liste des annonces d'un cours
     */
    public function index(Course $course)
    {
        $announcements = $this->repository->getVisibleByCourseId($course->id);
        return view('announcements.index', compact('course', 'announcements'));
    }

    /**
     * Affiche le formulaire de création
     */
    public function create(Course $course)
    {
        // Vérifier que l'utilisateur peut gérer ce cours
        $this->authorize('manage', $course);
        return view('announcements.create', compact('course'));
    }

    /**
     * Sauvegarde une nouvelle annonce
     */
    public function store(Request $request, Course $course)
    {
        // Vérifier que l'utilisateur peut gérer ce cours
        $this->authorize('manage', $course);

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $announcement = $this->repository->create($course->id, array_merge($validated, [
            'user_id' => auth()->id(),
            'status' => 1,
        ]));

        return redirect()->route('announcements.index', $course)
            ->with('success', 'Annonce créée avec succès et enqueued pour synchronisation');
    }

    /**
     * Affiche le formulaire de modification
     */
    public function edit(Course $course, Announcement $announcement)
    {
        // Vérifier que l'utilisateur peut gérer ce cours
        $this->authorize('manage', $course);
        return view('announcements.edit', compact('course', 'announcement'));
    }

    /**
     * Met à jour une annonce
     */
    public function update(Request $request, Course $course, Announcement $announcement)
    {
        // Vérifier que l'utilisateur peut gérer ce cours
        $this->authorize('manage', $course);

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $this->repository->update($announcement, $validated);

        return redirect()->route('announcements.index', $course)
            ->with('success', 'Annonce mise à jour avec succès et enqueued pour synchronisation');
    }

    /**
     * Supprime une annonce
     */
    public function destroy(Course $course, Announcement $announcement)
    {
        // Vérifier que l'utilisateur peut gérer ce cours
        $this->authorize('manage', $course);

        $this->repository->delete($announcement);

        return redirect()->route('announcements.index', $course)
            ->with('success', 'Annonce supprimée (soft-deleted) et enqueued pour synchronisation');
    }
}

