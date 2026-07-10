<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessSyncOperationJob implements ShouldQueue
{
    use Queueable;

    protected int $operationId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $operationId)
    {
        $this->operationId = $operationId;
    }

    /**
     * Execute the job.
     */
    public function handle(\App\Services\SyncService $syncService): void
    {
        $operation = \Illuminate\Support\Facades\DB::table('sync_queue')
            ->where('id', $this->operationId)
            ->first();

        if (!$operation) {
            return;
        }

        // Marquer comme processing
        \Illuminate\Support\Facades\DB::table('sync_queue')
            ->where('id', $this->operationId)
            ->update([
                'status' => 'processing',
                'processed_by' => 'job_'.getmypid(),
                'locked_at' => now(),
            ]);

        try {
            // Traiter l'opération
            $syncService->processOperation($operation);

            // Supprimer d'abord un éventuel ancien enregistrement 'done' pour éviter la contrainte d'unicité
            \Illuminate\Support\Facades\DB::table('sync_queue')
                ->where('entity_type', $operation->entity_type)
                ->where('entity_id', $operation->entity_id)
                ->where('operation', $operation->operation)
                ->where('status', 'done')
                ->where('id', '!=', $operation->id)
                ->delete();

            // Marquer comme done
            \Illuminate\Support\Facades\DB::table('sync_queue')
                ->where('id', $this->operationId)
                ->update([
                    'status' => 'done',
                    'processed_at' => now(),
                ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Erreur Job ProcessSyncOperationJob #{$this->operationId}: {$e->getMessage()}");
            
            \Illuminate\Support\Facades\DB::table('sync_queue')
                ->where('id', $this->operationId)
                ->increment('attempts');
                
            \Illuminate\Support\Facades\DB::table('sync_queue')
                ->where('id', $this->operationId)
                ->update([
                    'status' => 'error',
                    'error_msg' => $e->getMessage(),
                ]);
        }
    }
}
