<?php

namespace App\Services\Sync;

use App\Models\Section;
use App\Models\Course;
use Illuminate\Database\Eloquent\Model;
use Exception;

class SectionSyncHandler extends BaseSyncHandler
{
    public function pull(): array
    {
        return ['created' => 0, 'updated' => 0, 'errors' => 0]; // Pulled with Course
    }

    protected function getEntity(int $id): ?Model
    {
        return Section::find($id);
    }

    protected function pushCreate(Model $entity, array $payload): void
    {
        /** @var Section $entity */
        try {
            $course = Course::find($entity->course_id);
            if (!$course || !$course->moodle_id) {
                throw new Exception("Cours parent non synchronisé, impossible de créer la section");
            }

            $sectionNumber = $entity->position + 1;

            $this->api->call('core_course_edit_section', [
                'section' => (object)[
                    'courseid' => $course->moodle_id,
                    'section' => $sectionNumber,
                    'name' => $entity->name,
                    'summary' => $entity->summary ?? '',
                    'summaryformat' => 1,
                    'visible' => $entity->visible ? 1 : 0,
                ],
            ]);

            $entity->update([
                'sync_status' => 'synced',
                'synced_at' => now(),
                'dirty' => 0,
            ]);

            $this->logInfo("CREATE section#{$entity->id} → Moodle courseid:{$course->moodle_id} section:{$sectionNumber}");

        } catch (Exception $e) {
            $this->logError("Erreur CREATE section#{$entity->id}", $e);
            throw $e;
        }
    }

    protected function pushUpdate(Model $entity, array $payload): void
    {
        /** @var Section $entity */
        try {
            $course = Course::find($entity->course_id);
            if (!$course || !$course->moodle_id) {
                throw new Exception("Cours parent non synchronisé, impossible de mettre à jour la section");
            }

            $sectionNumber = $entity->position + 1;

            $this->api->call('core_course_edit_section', [
                'section' => (object)[
                    'courseid' => $course->moodle_id,
                    'section' => $sectionNumber,
                    'name' => $entity->name,
                    'summary' => $entity->summary ?? '',
                    'summaryformat' => 1,
                    'visible' => $entity->visible ? 1 : 0,
                ],
            ]);

            $entity->update([
                'sync_status' => 'synced',
                'synced_at' => now(),
                'dirty' => 0,
            ]);

            $this->logInfo("UPDATE section#{$entity->id} → Moodle courseid:{$course->moodle_id} section:{$sectionNumber}");

        } catch (Exception $e) {
            $this->logError("Erreur UPDATE section#{$entity->id}", $e);
            throw $e;
        }
    }
}
