<?php

namespace App\Services\Sync;

use App\Models\Module;
use App\Models\Section;
use App\Models\Course;
use Illuminate\Database\Eloquent\Model;
use Exception;

class ModuleSyncHandler extends BaseSyncHandler
{
    public function pull(): array
    {
        return ['created' => 0, 'updated' => 0, 'errors' => 0]; // Pulled with Course
    }

    protected function getEntity(int $id): ?Model
    {
        return Module::find($id);
    }

    protected function pushCreate(Model $entity, array $payload): void
    {
        /** @var Module $entity */
        try {
            $section = Section::find($entity->section_id);
            if (!$section || !$section->course_id) {
                throw new Exception("Section parente non synchronisée, impossible de créer le module");
            }

            $course = Course::find($section->course_id);
            if (!$course || !$course->moodle_id) {
                throw new Exception("Cours parent non synchronisé, impossible de créer le module");
            }

            $this->logInfo("CREATE module#{$entity->id} ({$entity->modname}) → Moodle course:{$course->moodle_id} (Simulation)");
            $entity->update([
                'sync_status' => 'synced',
                'synced_at' => now(),
                'dirty' => 0,
            ]);

        } catch (Exception $e) {
            $this->logError("Erreur CREATE module#{$entity->id}", $e);
            throw $e;
        }
    }

    protected function pushUpdate(Model $entity, array $payload): void
    {
        /** @var Module $entity */
        try {
            if (!$entity->moodle_id) {
                $this->logInfo("Module#{$entity->id} sans moodle_id, skip update");
                return;
            }

            $this->logInfo("UPDATE module#{$entity->id} → Moodle id:{$entity->moodle_id} (Simulation)");
            $entity->update([
                'sync_status' => 'synced',
                'synced_at' => now(),
                'dirty' => 0,
            ]);

        } catch (Exception $e) {
            $this->logError("Erreur UPDATE module#{$entity->id}", $e);
            throw $e;
        }
    }
}
