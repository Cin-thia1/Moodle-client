<?php

namespace App\Repositories;

use App\Models\Grade;
use Illuminate\Support\Facades\DB;

/**
 * Repository pour gestion locale des grades avec sync.
 * Chaque action insère dans sync_queue pour synchronisation future.
 */
class GradeRepository
{
    /**
     * Crée/enregistre un grade localement et enqueue la sync.
     */
    public function grade(int $submissionId, int $teacherId, array $data): Grade
    {
        $grade = Grade::create([
            'submission_id' => $submissionId,
            'teacher_id' => $teacherId,
            'grade' => $data['grade'] ?? null,
            'comment' => $data['comment'] ?? null,
            'sync_status' => 'pending',
            'sync_action' => 'create',
            'dirty' => 1,
        ]);

        // Enqueue l'opération de notation
        DB::table('sync_queue')->updateOrInsert(
            [
                'operation' => 'CREATE',
                'entity_type' => 'grades',
                'entity_id' => $grade->id,
                'status' => 'pending',
            ],
            [
                'payload' => json_encode([
                    'submission_id' => $submissionId,
                    'teacher_id' => $teacherId,
                    'grade' => $grade->grade,
                    'comment' => $grade->comment,
                ]),
                'created_at' => now(),
            ]
        );

        return $grade;
    }

    /**
     * Met à jour un grade localement et enqueue la sync.
     */
    public function update(Grade $grade, array $data): Grade
    {
        $oldValues = $grade->only(['grade', 'comment']);

        $grade->update([
            'grade' => $data['grade'] ?? $grade->grade,
            'comment' => $data['comment'] ?? $grade->comment,
            'sync_status' => 'pending',
            'sync_action' => 'update',
            'dirty' => 1,
        ]);

        // Enqueue l'opération de mise à jour
        DB::table('sync_queue')->updateOrInsert(
            [
                'operation' => 'UPDATE',
                'entity_type' => 'grades',
                'entity_id' => $grade->id,
                'status' => 'pending',
            ],
            [
                'payload' => json_encode([
                    'grade' => $grade->grade,
                    'comment' => $grade->comment,
                    'old_values' => $oldValues,
                ]),
                'created_at' => now(),
            ]
        );

        return $grade;
    }

    /**
     * Récupère un grade par ID.
     */
    public function getById(int $id): ?Grade
    {
        return Grade::find($id);
    }

    /**
     * Récupère les grades d'une soumission.
     */
    public function getBySubmissionId(int $submissionId)
    {
        return Grade::where('submission_id', $submissionId)
            ->with('teacher', 'submission')
            ->get();
    }

    /**
     * Récupère les grades en attente de sync.
     */
    public function getPending()
    {
        return Grade::pending()->get();
    }

    /**
     * Récupère les grades avec conflits.
     */
    public function getConflicts()
    {
        return Grade::where('sync_status', 'conflict')->get();
    }

    /**
     * Supprime un grade.
     */
    public function delete(Grade $grade): void
    {
        $grade->delete();
    }
}
