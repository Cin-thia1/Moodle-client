<?php

namespace App\Repositories;

use App\Models\Participant;
use Illuminate\Support\Facades\DB;

/**
 * Repository pour gestion locale des participants (inscriptions) avec sync.
 * Chaque action insère dans sync_queue pour synchronisation future.
 */
class ParticipantRepository
{
    /**
     * Enrôle un utilisateur localement et enqueue la sync.
     */
    public function enroll(array $data): Participant
    {
        $participant = Participant::create([
            'course_id' => $data['course_id'],
            'user_id' => $data['user_id'],
            'role' => $data['role'] ?? 'ROLE_STUDENT',
            'status' => $data['status'] ?? 1,
            'enrolled_at' => $data['enrolled_at'] ?? now(),
            'moodle_enrolment_id' => $data['moodle_enrolment_id'] ?? null,
            'sync_status' => 'pending',
            'sync_action' => 'create',
            'dirty' => 1,
        ]);

        // Enqueue l'opération d'enrôlement
        DB::table('sync_queue')->insert([
            'operation' => 'CREATE',
            'entity_type' => 'participants',
            'entity_id' => $participant->id,
            'payload' => json_encode([
                'course_id' => $participant->course_id,
                'user_id' => $participant->user_id,
                'role' => $participant->role,
            ]),
            'status' => 'pending',
            'created_at' => now(),
        ]);

        return $participant;
    }

    /**
     * Mets à jour un participant localement et enqueue la sync.
     */
    public function update(Participant $participant, array $data): Participant
    {
        $oldValues = $participant->only(['role', 'status']);

        $participant->update([
            'role' => $data['role'] ?? $participant->role,
            'status' => $data['status'] ?? $participant->status,
            'sync_status' => 'pending',
            'sync_action' => 'update',
            'dirty' => 1,
        ]);

        // Enqueue l'opération de mise à jour
        DB::table('sync_queue')->insert([
            'operation' => 'UPDATE',
            'entity_type' => 'participants',
            'entity_id' => $participant->id,
            'payload' => json_encode([
                'role' => $participant->role,
                'status' => $participant->status,
                'old_values' => $oldValues,
            ]),
            'status' => 'pending',
            'created_at' => now(),
        ]);

        return $participant;
    }

    /**
     * Désenrôle un utilisateur et enqueue la sync.
     */
    public function unenroll(Participant $participant): void
    {
        $participant->update([
            'status' => 0,
            'unenrolled_at' => now(),
            'sync_status' => 'pending',
            'sync_action' => 'delete',
            'dirty' => 1,
        ]);

        // Enqueue l'opération de désenrôlement
        DB::table('sync_queue')->insert([
            'operation' => 'DELETE',
            'entity_type' => 'participants',
            'entity_id' => $participant->id,
            'payload' => json_encode([
                'course_id' => $participant->course_id,
                'user_id' => $participant->user_id,
                'role' => $participant->role,
                'moodle_enrolment_id' => $participant->moodle_enrolment_id,
            ]),
            'status' => 'pending',
            'created_at' => now(),
        ]);
    }

    /**
     * Récupère tous les participants d'un cours.
     */
    public function getByCourseId(int $courseId)
    {
        return Participant::where('course_id', $courseId)
            ->with('user')
            ->orderBy('role')
            ->get();
    }

    /**
     * Récupère les participants d'un cours par rôle.
     */
    public function getByCourseIdAndRole(int $courseId, string $role)
    {
        return Participant::where('course_id', $courseId)
            ->where('role', $role)
            ->with('user')
            ->get();
    }

    /**
     * Récupère un participant par ID.
     */
    public function getById(int $id): ?Participant
    {
        return Participant::find($id);
    }

    /**
     * Récupère les participants en attente de sync.
     */
    public function getPending()
    {
        return Participant::pending()->get();
    }

    /**
     * Récupère les participants avec conflits.
     */
    public function getConflicts()
    {
        return Participant::where('sync_status', 'conflict')->get();
    }

    /**
     * Supprime un participant complètement (ne l'enrôle pas, le supprime).
     */
    public function delete(Participant $participant): void
    {
        $participant->delete();
    }
}
