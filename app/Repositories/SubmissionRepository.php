<?php

namespace App\Repositories;

use App\Models\Submission;
use Illuminate\Support\Facades\DB;

/**
 * Repository pour gestion locale des soumissions de devoirs avec sync.
 */
class SubmissionRepository
{
    /**
     * Enregistre une soumission localement et enqueue la sync.
     */
    public function submit(int $moduleId, int $userId, array $data): Submission
    {
        $submission = Submission::create([
            'module_id' => $moduleId,
            'user_id' => $userId,
            'status' => 'submitted',
            'content' => $data['content'] ?? null,
            'file_path' => $data['file_path'] ?? null,
            'attempt_number' => $data['attempt_number'] ?? 1,
            'submitted_at' => $data['submitted_at'] ?? now(),
            'sync_status' => 'pending',
            'sync_action' => 'create',
            'dirty' => 1,
        ]);

        // Enqueue l'opération de soumission
        DB::table('sync_queue')->updateOrInsert(
            [
                'operation' => 'CREATE',
                'entity_type' => 'submissions',
                'entity_id' => $submission->id,
                'status' => 'pending',
            ],
            [
                'payload' => json_encode([
                    'module_id' => $moduleId,
                    'user_id' => $userId,
                    'content' => substr($submission->content ?? '', 0, 100),
                    'file_path' => $submission->file_path,
                    'attempt_number' => $submission->attempt_number,
                ]),
                'created_at' => now(),
            ]
        );

        return $submission;
    }

    /**
     * Met à jour une soumission localement et enqueue la sync.
     */
    public function update(Submission $submission, array $data): Submission
    {
        $oldValues = $submission->only(['status', 'content', 'file_path']);

        $submission->update([
            'status' => $data['status'] ?? $submission->status,
            'content' => $data['content'] ?? $submission->content,
            'file_path' => $data['file_path'] ?? $submission->file_path,
            'sync_status' => 'pending',
            'sync_action' => 'update',
            'dirty' => 1,
        ]);

        // Enqueue l'opération de mise à jour
        DB::table('sync_queue')->updateOrInsert(
            [
                'operation' => 'UPDATE',
                'entity_type' => 'submissions',
                'entity_id' => $submission->id,
                'status' => 'pending',
            ],
            [
                'payload' => json_encode([
                    'status' => $submission->status,
                    'old_values' => $oldValues,
                ]),
                'created_at' => now(),
            ]
        );

        return $submission;
    }

    /**
     * Récupère une soumission par ID.
     */
    public function getById(int $id): ?Submission
    {
        return Submission::find($id);
    }

    /**
     * Récupère les soumissions d'un module.
     */
    public function getByModuleId(int $moduleId)
    {
        return Submission::where('module_id', $moduleId)
            ->with('user', 'module')
            ->get();
    }

    /**
     * Récupère les soumissions d'un utilisateur pour un module.
     */
    public function getByModuleAndUser(int $moduleId, int $userId)
    {
        return Submission::where('module_id', $moduleId)
            ->where('user_id', $userId)
            ->with('user', 'module')
            ->get();
    }

    /**
     * Récupère les soumissions en attente de sync.
     */
    public function getPending()
    {
        return Submission::pending()->get();
    }

    /**
     * Récupère les soumissions avec conflits.
     */
    public function getConflicts()
    {
        return Submission::where('sync_status', 'conflict')->get();
    }

    /**
     * Supprime une soumission.
     */
    public function delete(Submission $submission): void
    {
        $submission->delete();
    }
}
