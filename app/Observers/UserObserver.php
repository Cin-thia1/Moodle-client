<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    /**
     * Après création d'un user, enqueue CREATE
     */
    public function created(User $user): void
    {
        // Enqueuer l'opération CREATE
        DB::table('sync_queue')->insert([
            'operation' => 'CREATE',
            'entity_type' => 'users',
            'entity_id' => $user->id,
            'payload' => json_encode($user->toArray()),
            'status' => 'pending',
            'created_at' => now(),
        ]);

        // Marquer l'user localement comme pending
        $user->update([
            'sync_status' => 'pending',
            'dirty' => 1,
        ]);

        Log::info("User créé: ID={$user->id}, enqueued pour sync");
    }

    /**
     * Après mise à jour d'un user, enqueue UPDATE
     */
    public function updated(User $user): void
    {
        // Ignorer les mises à jour internes (dirty, sync_status, etc)
        $changed = $user->getChanges();
        
        // Si seulement sync_* ou dirty ont changé, ne pas enqueue
        $nonSyncChanges = collect($changed)->reject(function($value, $key) {
            return in_array($key, ['sync_status', 'sync_action', 'synced_at', 'dirty']);
        })->count();

        if ($nonSyncChanges > 0) {
            DB::table('sync_queue')->insert([
                'operation' => 'UPDATE',
                'entity_type' => 'users',
                'entity_id' => $user->id,
                'payload' => json_encode($user->toArray()),
                'status' => 'pending',
                'created_at' => now(),
            ]);

            // Marquer dirty
            if (!isset($changed['dirty'])) {
                $user->update(['dirty' => true]);
            }

            Log::info("User mis à jour: ID={$user->id}, enqueued pour sync");
        }
    }

    /**
     * Avant suppression d'un user, enqueue DELETE
     */
    public function deleting(User $user): void
    {
        DB::table('sync_queue')->insert([
            'operation' => 'DELETE',
            'entity_type' => 'users',
            'entity_id' => $user->id,
            'payload' => json_encode($user->toArray()),
            'status' => 'pending',
            'created_at' => now(),
        ]);

        Log::info("User supprimé: ID={$user->id}, enqueued pour sync");
    }
}
