<?php

namespace App\Services\Sync;

use App\Services\MoodleApiService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Exception;

abstract class BaseSyncHandler implements SyncHandlerInterface
{
    protected MoodleApiService $api;

    public function setApi(MoodleApiService $api): void
    {
        $this->api = $api;
    }

    /**
     * Helper log for sync events.
     */
    protected function logInfo(string $message): void
    {
        Log::info(class_basename($this) . ': ' . $message);
    }

    protected function logError(string $message, ?\Exception $e = null): void
    {
        $errorMsg = $e ? " - " . $e->getMessage() : "";
        Log::error(class_basename($this) . ': ' . $message . $errorMsg);
    }

    /**
     * Default processOperation delegates to pushCreate/Update/Delete based on operation
     */
    public function processOperation(object $operation): bool
    {
        $entity = $this->getEntity($operation->entity_id);
        $payload = json_decode($operation->payload, true) ?? [];

        if ($entity === null && $operation->operation !== 'DELETE') {
            throw new Exception(
                "{$operation->operation} de {$operation->entity_type}#{$operation->entity_id} impossible: entité introuvable en local"
            );
        }

        try {
            match ($operation->operation) {
                'CREATE' => $this->pushCreate($entity, $payload),
                'UPDATE' => $this->pushUpdate($entity, $payload),
                'DELETE' => $this->pushDelete($entity),
                default => throw new Exception("Unknown operation: {$operation->operation}"),
            };

            return true;
        } catch (\Exception $e) {
            $this->logError("Error processing operation {$operation->operation} on {$operation->entity_id}", $e);
            throw $e;
        }
    }

    abstract protected function getEntity(int $id): ?Model;

    protected function pushCreate(Model $entity, array $payload): void
    {
        $this->logInfo("CREATE not implemented for this handler.");
    }

    protected function pushUpdate(Model $entity, array $payload): void
    {
        $this->logInfo("UPDATE not implemented for this handler.");
    }

    protected function pushDelete(?Model $entity): void
    {
        $this->logInfo("DELETE not implemented for this handler.");
    }
}
