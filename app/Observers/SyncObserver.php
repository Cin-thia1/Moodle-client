<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncObserver
{
    public static bool $muteEvents = false;

    /**
     * Handle the Model "created" event.
     */
    public function created(Model $model): void
    {
        if (self::$muteEvents) return;
        $this->enqueueOperation($model, 'CREATE');
        $this->markAsDirty($model);
    }

    /**
     * Handle the Model "updated" event.
     */
    public function updated(Model $model): void
    {
        if (self::$muteEvents) return;
        $changed = $model->getChanges();
        
        // Ignorer les changements uniquement liés aux colonnes de synchronisation
        $nonSyncChanges = collect($changed)->reject(function($value, $key) {
            return in_array($key, ['sync_status', 'sync_action', 'synced_at', 'dirty', 'updated_at']);
        })->count();

        if ($nonSyncChanges > 0) {
            $this->enqueueOperation($model, 'UPDATE');
            $this->markAsDirty($model);
        }
    }

    /**
     * Handle the Model "deleted" event.
     */
    public function deleted(Model $model): void
    {
        if (self::$muteEvents) return;
        $this->enqueueOperation($model, 'DELETE');
    }

    /**
     * Enqueue the operation in the sync_queue table.
     */
    protected function enqueueOperation(Model $model, string $operation): void
    {
        // Ne pas mettre en queue si le modèle indique qu'il ne doit pas être synchronisé
        if (method_exists($model, 'shouldSync') && !$model->shouldSync()) {
            return;
        }

        $key = [
            'operation' => $operation,
            'entity_type' => $model->getTable(),
            'entity_id' => $model->id ?? 0,
            'status' => 'pending',
        ];

        DB::table('sync_queue')->updateOrInsert($key, [
            'payload' => json_encode($model->toArray()),
            'created_at' => now(),
            'error_msg' => null,
        ]);

        Log::info("SyncObserver: {$operation} sur {$model->getTable()}#{$model->id} mis en file d'attente.");
    }

    /**
     * Marquer l'entité comme ayant des modifications locales sans déclencher l'événement updated à nouveau.
     */
    protected function markAsDirty(Model $model): void
    {
        DB::table($model->getTable())
            ->where('id', $model->id)
            ->update([
                'sync_status' => 'pending',
                'dirty' => 1,
            ]);
    }
}
