<?php

namespace App\Services\Sync;

use App\Models\Grade;
use App\Models\Submission;
use App\Models\Module;
use App\Models\Participant;
use App\Models\Course;
use Illuminate\Database\Eloquent\Model;
use Exception;

class GradeSyncHandler extends BaseSyncHandler
{
    public function pull(): array
    {
        return ['created' => 0, 'updated' => 0, 'errors' => 0];
    }

    protected function getEntity(int $id): ?Model
    {
        return Grade::find($id);
    }

    protected function pushCreate(Model $entity, array $payload): void
    {
        /** @var Grade $entity */
        try {
            $submission = Submission::find($entity->submission_id);
            if (!$submission) {
                throw new Exception("Soumission parente non trouvée");
            }

            $module = Module::find($submission->module_id);
            if (!$module || !$module->moodle_id) {
                throw new Exception("Module non synchronisé, impossible d'enregistrer la note");
            }

            // Récupérer le user_id de la soumission sur moodle
            $studentId = $submission->user_id;

            $this->api->saveGrade(
                $module->moodle_id,
                $studentId,
                $entity->grade,
                $entity->feedback ?? ''
            );

            $entity->update([
                'sync_status' => 'synced',
                'synced_at' => now(),
                'dirty' => 0,
            ]);

            $this->logInfo("CREATE grade#{$entity->id} (submission:{$submission->id}) → Moodle module:{$module->moodle_id}");

        } catch (Exception $e) {
            $this->logError("Erreur CREATE grade#{$entity->id}", $e);
            throw $e;
        }
    }

    protected function pushUpdate(Model $entity, array $payload): void
    {
        /** @var Grade $entity */
        try {
            $submission = Submission::find($entity->submission_id);
            if (!$submission) {
                $this->logInfo("Grade#{$entity->id} sans submission, skip update");
                return;
            }

            $module = Module::find($submission->module_id);
            if (!$module || !$module->moodle_id) {
                $this->logInfo("Grade#{$entity->id} avec module non-synced, skip update");
                return;
            }

            $this->logInfo("UPDATE grade#{$entity->id} → Moodle module:{$module->moodle_id} (Simulation)");

            $entity->update([
                'sync_status' => 'synced',
                'synced_at' => now(),
                'dirty' => 0,
            ]);

        } catch (Exception $e) {
            $this->logError("Erreur UPDATE grade#{$entity->id}", $e);
            throw $e;
        }
    }
}
