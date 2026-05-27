<?php

namespace App\Services\Sync;

use App\Models\Participant;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Exception;

class ParticipantSyncHandler extends BaseSyncHandler
{
    public function pull(): array
    {
        // Enrolments are handled in CourseSyncHandler::pullCourseContents
        return ['created' => 0, 'updated' => 0, 'errors' => 0];
    }

    protected function getEntity(int $id): ?Model
    {
        return Participant::find($id);
    }

    protected function pushCreate(Model $entity, array $payload): void
    {
        /** @var Participant $entity */
        try {
            $course = Course::find($entity->course_id);
            if (!$course || !$course->moodle_id) {
                throw new Exception("Cours non synchronisé, impossible d'enrôler l'utilisateur");
            }

            $roleShortname = match ($entity->role) {
                Participant::ROLE_TEACHER => 'editingteacher',
                Participant::ROLE_STUDENT => 'student',
                Participant::ROLE_USER => 'user',
                default => 'student',
            };

            $user = User::find($entity->user_id);
            $moodleUserId = $user && $user->moodle_id ? $user->moodle_id : $entity->user_id;

            $this->api->enrollUser($moodleUserId, $course->moodle_id, $roleShortname);

            $entity->update([
                'sync_status' => 'synced',
                'synced_at' => now(),
                'dirty' => 0,
            ]);

            $this->logInfo("CREATE participant#{$entity->id} (user:{$moodleUserId} in course:{$course->moodle_id}) → Moodle");

        } catch (Exception $e) {
            $this->logError("Erreur CREATE participant#{$entity->id}", $e);
            throw $e;
        }
    }

    protected function pushUpdate(Model $entity, array $payload): void
    {
        /** @var Participant $entity */
        try {
            if (!$entity->moodle_enrolment_id) {
                $this->logInfo("Participant#{$entity->id} sans moodle_enrolment_id, skip update");
                return;
            }

            $this->logInfo("UPDATE participant#{$entity->id} (enrolment:{$entity->moodle_enrolment_id}) → Moodle (Simulation)");

            $entity->update([
                'sync_status' => 'synced',
                'synced_at' => now(),
                'dirty' => 0,
            ]);

        } catch (Exception $e) {
            $this->logError("Erreur UPDATE participant#{$entity->id}", $e);
            throw $e;
        }
    }
}
