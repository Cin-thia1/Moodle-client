<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\Course;
use App\Services\MoodleParticipantService;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    protected MoodleParticipantService $participantService;

    public function __construct(MoodleParticipantService $participantService)
    {
        $this->participantService = $participantService;
    }

    /**
     * Affiche la liste des participants d'un cours
     */
    public function index(Course $course)
    {
        $this->authorize('view_participants');
        
        $participants = $this->participantService->getCourseParticipants($course->id);
        return view('participants.index', compact('course', 'participants'));
    }

    /**
     * Affiche le formulaire d'enrôlement
     */
    public function create(Course $course)
    {
        $this->authorize('enrol_user');
        return view('participants.create', compact('course'));
    }

    /**
     * Enrôle un utilisateur à un cours
     */
    public function store(Request $request, Course $course)
    {
        $this->authorize('enrol_user');

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:' . implode(',', [
                Participant::ROLE_TEACHER,
                Participant::ROLE_STUDENT,
                Participant::ROLE_USER,
            ]),
        ]);

        $participant = $this->participantService->enrollUser(
            $course->id,
            $validated['user_id'],
            $validated['role']
        );

        return redirect()->route('participants.index', $course)->with('success', 'Utilisateur enrôlé avec succès');
    }

    /**
     * Affiche le formulaire de modification du rôle
     */
    public function edit(Course $course, Participant $participant)
    {
        $this->authorize('manage_participants');
        return view('participants.edit', compact('course', 'participant'));
    }

    /**
     * Met à jour le rôle d'un participant
     */
    public function update(Request $request, Course $course, Participant $participant)
    {
        $this->authorize('manage_participants');

        $validated = $request->validate([
            'role' => 'required|in:' . implode(',', [
                Participant::ROLE_TEACHER,
                Participant::ROLE_STUDENT,
                Participant::ROLE_USER,
            ]),
        ]);

        $this->participantService->changeRole($course->id, $participant->user_id, $validated['role']);

        return redirect()->route('participants.index', $course)->with('success', 'Rôle mis à jour avec succès');
    }

    /**
     * Désenrôle un utilisateur
     */
    public function destroy(Course $course, Participant $participant)
    {
        $this->authorize('unenrol_user');

        $this->participantService->unenrollUser($course->id, $participant->user_id);

        return redirect()->route('participants.index', $course)->with('success', 'Utilisateur désenrôlé avec succès');
    }

    /**
     * Synchronise les participants depuis Moodle
     */
    public function sync(Course $course)
    {
        $this->authorize('manage_participants');

        $result = $this->participantService->syncCourseParticipants($course->id);

        return redirect()->route('participants.index', $course)
            ->with('success', "Synchronisation complétée: {$result['synced']} participants synchronisés");
    }

    /**
     * Affiche les participants par rôle
     */
    public function byRole(Course $course, string $role)
    {
        $this->authorize('view_participants');

        $participants = $this->participantService->getParticipantsByRole($course->id, $role);
        return view('participants.by-role', compact('course', 'participants', 'role'));
    }
}
