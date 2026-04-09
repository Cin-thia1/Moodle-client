<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\Course;
use App\Repositories\ParticipantRepository;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    protected ParticipantRepository $repository;

    public function __construct(ParticipantRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Affiche la liste des participants d'un cours
     */
    public function index(Course $course)
    {
        $this->authorize('manage', $course);
        
        $participants = $this->repository->getByCourseId($course->id);
        return view('participants.index', compact('course', 'participants'));
    }

    /**
     * Affiche le formulaire d'enrôlement
     */
    public function create(Course $course)
    {
        $this->authorize('manage', $course);
        return view('participants.create', compact('course'));
    }

    /**
     * Enrôle un utilisateur à un cours
     */
    public function store(Request $request, Course $course)
    {
        $this->authorize('manage', $course);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:' . implode(',', [
                Participant::ROLE_TEACHER,
                Participant::ROLE_STUDENT,
                Participant::ROLE_USER,
            ]),
        ]);

        $participant = $this->repository->enroll([
            'course_id' => $course->id,
            'user_id' => $validated['user_id'],
            'role' => $validated['role'],
            'status' => 1,
        ]);

        return redirect()->route('participants.index', $course)
            ->with('success', 'Utilisateur enrôlé avec succès');
    }

    /**
     * Affiche le formulaire de modification du rôle
     */
    public function edit(Course $course, Participant $participant)
    {
        $this->authorize('manage', $course);
        return view('participants.edit', compact('course', 'participant'));
    }

    /**
     * Met à jour le rôle d'un participant
     */
    public function update(Request $request, Course $course, Participant $participant)
    {
        $this->authorize('manage', $course);

        $validated = $request->validate([
            'role' => 'required|in:' . implode(',', [
                Participant::ROLE_TEACHER,
                Participant::ROLE_STUDENT,
                Participant::ROLE_USER,
            ]),
        ]);

        $this->repository->update($participant, [
            'role' => $validated['role'],
        ]);

        return redirect()->route('participants.index', $course)
            ->with('success', 'Rôle mis à jour avec succès');
    }

    /**
     * Désenrôle un utilisateur
     */
    public function destroy(Course $course, Participant $participant)
    {
        $this->authorize('manage', $course);

        $this->repository->unenroll($participant);

        return redirect()->route('participants.index', $course)
            ->with('success', 'Utilisateur désenrôlé avec succès');
    }

    /**
     * Affiche les participants par rôle
     */
    public function byRole(Course $course, string $role)
    {
        $this->authorize('manage', $course);

        $participants = $this->repository->getByCourseIdAndRole($course->id, $role);
        return view('participants.by-role', compact('course', 'participants', 'role'));
    }
}

