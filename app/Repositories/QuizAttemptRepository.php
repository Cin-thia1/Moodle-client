<?php

namespace App\Repositories;

use App\Models\QuizAttempt;
use Illuminate\Support\Facades\DB;

/**
 * Repository pour gestion locale des tentatives de quiz avec sync.
 */
class QuizAttemptRepository
{
    /**
     * Crée une tentative de quiz localement et enqueue la sync.
     */
    public function create(int $moduleId, int $userId, array $data): QuizAttempt
    {
        $attempt = QuizAttempt::create([
            'module_id' => $moduleId,
            'user_id' => $userId,
            'attempt' => $data['attempt'] ?? 1,
            'state' => $data['state'] ?? 'inprogress',
            'sumgrades' => $data['sumgrades'] ?? 0,
            'timestart' => $data['timestart'] ?? now(),
            'timefinish' => $data['timefinish'] ?? null,
            'moodle_attempt_id' => $data['moodle_attempt_id'] ?? null,
            'sync_status' => 'pending',
            'sync_action' => 'create',
            'dirty' => 1,
        ]);

        // Enqueue l'opération de création
        DB::table('sync_queue')->updateOrInsert(
            [
                'operation' => 'CREATE',
                'entity_type' => 'quiz_attempts',
                'entity_id' => $attempt->id,
                'status' => 'pending',
            ],
            [
                'payload' => json_encode([
                    'module_id' => $moduleId,
                    'user_id' => $userId,
                    'attempt' => $attempt->attempt,
                    'state' => $attempt->state,
                    'sumgrades' => $attempt->sumgrades,
                ]),
                'created_at' => now(),
            ]
        );

        return $attempt;
    }

    /**
     * Met à jour une tentative de quiz et enqueue la sync.
     */
    public function update(QuizAttempt $attempt, array $data): QuizAttempt
    {
        $oldValues = $attempt->only(['state', 'sumgrades', 'timefinish']);

        $attempt->update([
            'state' => $data['state'] ?? $attempt->state,
            'sumgrades' => $data['sumgrades'] ?? $attempt->sumgrades,
            'timefinish' => $data['timefinish'] ?? $attempt->timefinish,
            'sync_status' => 'pending',
            'sync_action' => 'update',
            'dirty' => 1,
        ]);

        // Enqueue l'opération de mise à jour
        DB::table('sync_queue')->updateOrInsert(
            [
                'operation' => 'UPDATE',
                'entity_type' => 'quiz_attempts',
                'entity_id' => $attempt->id,
                'status' => 'pending',
            ],
            [
                'payload' => json_encode([
                    'state' => $attempt->state,
                    'sumgrades' => $attempt->sumgrades,
                    'old_values' => $oldValues,
                ]),
                'created_at' => now(),
            ]
        );

        return $attempt;
    }

    /**
     * Récupère une tentative par ID.
     */
    public function getById(int $id): ?QuizAttempt
    {
        return QuizAttempt::find($id);
    }

    /**
     * Récupère les tentatives d'un quiz (module).
     */
    public function getByModuleId(int $moduleId)
    {
        return QuizAttempt::where('module_id', $moduleId)
            ->with('user', 'module')
            ->orderBy('attempt')
            ->get();
    }

    /**
     * Récupère les tentatives d'un utilisateur pour un quiz.
     */
    public function getByModuleAndUser(int $moduleId, int $userId)
    {
        return QuizAttempt::where('module_id', $moduleId)
            ->where('user_id', $userId)
            ->with('user', 'module')
            ->orderBy('attempt')
            ->get();
    }

    /**
     * Récupère les tentatives en attente de sync.
     */
    public function getPending()
    {
        return QuizAttempt::pending()->get();
    }

    /**
     * Récupère les tentatives avec conflits.
     */
    public function getConflicts()
    {
        return QuizAttempt::where('sync_status', 'conflict')->get();
    }

    /**
     * Supprime une tentative.
     */
    public function delete(QuizAttempt $attempt): void
    {
        $attempt->delete();
    }
}
