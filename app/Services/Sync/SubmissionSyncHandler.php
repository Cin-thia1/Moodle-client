<?php

namespace App\Services\Sync;

use App\Models\Submission;
use App\Models\Module;
use Illuminate\Database\Eloquent\Model;
use Exception;

class SubmissionSyncHandler extends BaseSyncHandler
{
    public function pull(): array
    {
        return ['created' => 0, 'updated' => 0, 'errors' => 0];
    }

    protected function getEntity(int $id): ?Model
    {
        return Submission::find($id);
    }

    protected function pushCreate(Model $entity, array $payload): void
    {
        /** @var Submission $entity */
        try {
            $module = Module::find($entity->module_id);
            if (!$module || !$module->moodle_id) {
                throw new Exception("Module non synchronisé, impossible de soumettre");
            }

            if ($module->modname !== 'assign') {
                $this->logInfo("Submission#{$entity->id} pour module non-devoir, skip");
                return;
            }

            $this->api->submitAssignment(
                $module->moodle_id,
                $entity->user_id,
                [
                    'content' => $entity->content,
                    'file_path' => $entity->file_path,
                ]
            );

            $entity->update([
                'sync_status' => 'synced',
                'synced_at' => now(),
                'dirty' => 0,
            ]);

            $this->logInfo("CREATE submission#{$entity->id} (user:{$entity->user_id} for module:{$module->moodle_id}) → Moodle");

        } catch (Exception $e) {
            $this->logError("Erreur CREATE submission#{$entity->id}", $e);
            throw $e;
        }
    }

    protected function pushUpdate(Model $entity, array $payload): void
    {
        /** @var Submission $entity */
        try {
            $module = Module::find($entity->module_id);
            if (!$module || !$module->moodle_id) {
                $this->logInfo("Submission#{$entity->id} pour module non-synced, skip update");
                return;
            }

            $this->logInfo("UPDATE submission#{$entity->id} (module:{$module->moodle_id}) → Moodle (Simulation)");

            $entity->update([
                'sync_status' => 'synced',
                'synced_at' => now(),
                'dirty' => 0,
            ]);

        } catch (Exception $e) {
            $this->logError("Erreur UPDATE submission#{$entity->id}", $e);
            throw $e;
        }
    }
}
