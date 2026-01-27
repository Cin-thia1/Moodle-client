<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Course;
use App\Services\MoodleAnnouncementService;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    protected MoodleAnnouncementService $announcementService;

    public function __construct(MoodleAnnouncementService $announcementService)
    {
        $this->announcementService = $announcementService;
    }

    /**
     * Affiche la liste des annonces d'un cours
     */
    public function index(Course $course)
    {
        $this->authorize('view_announcements');
        
        $announcements = $this->announcementService->getCourseAnnouncements($course->id);
        return view('announcements.index', compact('course', 'announcements'));
    }

    /**
     * Affiche le formulaire de création
     */
    public function create(Course $course)
    {
        if (!auth()->user()->hasRole(['ROLE_TEACHER', 'ROLE_ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }
        return view('announcements.create', compact('course'));
    }

    /**
     * Sauvegarde une nouvelle annonce
     */
    public function store(Request $request, Course $course)
    {
        if (!auth()->user()->hasRole(['ROLE_TEACHER', 'ROLE_ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $announcement = $this->announcementService->createAnnouncement(
            $course->id,
            auth()->id(),
            $validated['subject'],
            $validated['message']
        );

        return redirect()->route('announcements.index', $course)->with('success', 'Annonce créée avec succès');
    }

    /**
     * Affiche le formulaire de modification
     */
    public function edit(Course $course, Announcement $announcement)
    {
        if (!auth()->user()->hasRole(['ROLE_TEACHER', 'ROLE_ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }
        return view('announcements.edit', compact('course', 'announcement'));
    }

    /**
     * Met à jour une annonce
     */
    public function update(Request $request, Course $course, Announcement $announcement)
    {
        if (!auth()->user()->hasRole(['ROLE_TEACHER', 'ROLE_ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $this->announcementService->updateAnnouncement($announcement->id, $validated);

        return redirect()->route('announcements.index', $course)->with('success', 'Annonce mise à jour avec succès');
    }

    /**
     * Supprime une annonce
     */
    public function destroy(Course $course, Announcement $announcement)
    {
        if (!auth()->user()->hasRole(['ROLE_TEACHER', 'ROLE_ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }

        $this->announcementService->deleteAnnouncement($announcement->id);

        return redirect()->route('announcements.index', $course)->with('success', 'Annonce supprimée avec succès');
    }

    /**
     * Synchronise les annonces depuis Moodle
     */
    public function sync(Course $course)
    {
        if (!auth()->user()->hasRole(['ROLE_TEACHER', 'ROLE_ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }

        $result = $this->announcementService->syncCourseAnnouncements($course->id);

        return redirect()->route('announcements.index', $course)
            ->with('success', "Synchronisation complétée: {$result['synced']} annonces synchronisées");
    }
}
