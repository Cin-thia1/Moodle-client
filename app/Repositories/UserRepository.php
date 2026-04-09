<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserRepository
{
    /**
     * Crée un nouvel utilisateur et enqueue l'opération de sync.
     */
    public function create(array $data): User
    {
        $user = User::create($data);

        // Enqueuer l'opération CREATE (insertOrIgnore pour éviter doublons)
        DB::table('sync_queue')->insertOrIgnore([
            'operation' => 'CREATE',
            'entity_type' => 'users',
            'entity_id' => $user->id,
            'payload' => json_encode($user->toArray()),
            'status' => 'pending',
            'created_at' => now(),
        ]);

        Log::info("User créé: ID={$user->id}, enqueued");

        return $user;
    }

    /**
     * Met à jour un utilisateur et enqueue l'opération de sync.
     */
    public function update(User $user, array $data): User
    {
        $user->update($data);
        $user->update(['dirty' => true]);

        // Enqueuer l'opération UPDATE (insertOrIgnore pour éviter doublons)
        DB::table('sync_queue')->insertOrIgnore([
            'operation' => 'UPDATE',
            'entity_type' => 'users',
            'entity_id' => $user->id,
            'payload' => json_encode($user->toArray()),
            'status' => 'pending',
            'created_at' => now(),
        ]);

        Log::info("User mis à jour: ID={$user->id}, enqueued");

        return $user;
    }

    /**
     * Supprime un utilisateur (soft delete logique) et enqueue l'opération.
     */
    public function delete(User $user): bool
    {
        // Enqueuer l'opération DELETE avant suppression (insertOrIgnore pour éviter doublons)
        DB::table('sync_queue')->insertOrIgnore([
            'operation' => 'DELETE',
            'entity_type' => 'users',
            'entity_id' => $user->id,
            'payload' => json_encode($user->toArray()),
            'status' => 'pending',
            'created_at' => now(),
        ]);

        Log::info("User supprimé: ID={$user->id}, enqueued");

        return true;
    }

    /**
     * Récupère un utilisateur par ID.
     */
    public function getById(int $id): ?User
    {
        return User::find($id);
    }

    /**
     * Récupère tous les utilisateurs ayant des opérations pending.
     */
    public function getPending(): object
    {
        return User::pending()->get();
    }

    /**
     * Récupère tous les utilisateurs avec des conflits.
     */
    public function getConflicts(): object
    {
        return User::conflicts()->get();
    }

    /**
     * Récupère les utilisateurs "dirty" (modifiés localement).
     */
    public function getDirty(): object
    {
        return User::dirty()->get();
    }

    /**
     * Récupère les utilisateurs "synced".
     */
    public function getSynced(): object
    {
        return User::synced()->get();
    }

    /**
     * Tous les utilisateurs.
     */
    public function getAll(): object
    {
        return User::all();
    }
}
