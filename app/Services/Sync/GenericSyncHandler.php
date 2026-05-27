<?php

namespace App\Services\Sync;

use Illuminate\Database\Eloquent\Model;

class GenericSyncHandler extends BaseSyncHandler
{
    protected string $modelClass;

    public function __construct(string $modelClass)
    {
        $this->modelClass = $modelClass;
    }

    public function pull(): array
    {
        return ['created' => 0, 'updated' => 0, 'errors' => 0];
    }

    protected function getEntity(int $id): ?Model
    {
        return ($this->modelClass)::find($id);
    }

    protected function pushCreate(Model $entity, array $payload): void
    {
        $this->logInfo("CREATE " . class_basename($entity) . "#{$entity->id} → Moodle (Simulation)");
        $entity->update([
            'sync_status' => 'synced',
            'synced_at' => now(),
            'dirty' => 0,
        ]);
    }

    protected function pushUpdate(Model $entity, array $payload): void
    {
        $this->logInfo("UPDATE " . class_basename($entity) . "#{$entity->id} → Moodle (Simulation)");
        $entity->update([
            'sync_status' => 'synced',
            'synced_at' => now(),
            'dirty' => 0,
        ]);
    }
}
