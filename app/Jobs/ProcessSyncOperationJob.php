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
            // Pour utiliser getHandler, SyncService a besoin de l'exposer ou on doit l'appeler indirectement.
            // Actuellement getHandler est protected. Ajoutons une méthode processOperation dans SyncService 
            // pour traiter une opération spécifique.
            $syncService->processSingleOperation($operation);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Erreur Job ProcessSyncOperationJob #{$this->operationId}: {$e->getMessage()}");
            // Le SyncService::processSingleOperation va gérer la mise à jour du statut d'erreur dans la table sync_queue
            // On peut optionnellement fail() le job Laravel si on veut le voir dans failed_jobs
        }
    }
}
