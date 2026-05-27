<?php

namespace App\Services\Sync;

use App\Models\Course;
use App\Models\Section;
use App\Models\Module;
use App\Models\Category;
use App\Models\Participant;
use Illuminate\Database\Eloquent\Model;
use Exception;

class CourseSyncHandler extends BaseSyncHandler
{
    public function pull(): array
    {
        $summary = ['created' => 0, 'updated' => 0, 'errors' => 0];
        try {
            $moodleCourses = $this->api->getAllCourses();
            foreach ($moodleCourses as $moodleCourse) {
                // Ignorer le cours racine du site Moodle (généralement ID = 1)
                if (isset($moodleCourse['id']) && $moodleCourse['id'] == 1) {
                    continue;
                }
                
                try {
                    $course = Course::updateOrCreate(
                        ['moodle_id' => $moodleCourse['id']],
                        [
                            'fullname' => $moodleCourse['fullname'] ?? '',
                            'shortname' => $moodleCourse['shortname'] ?? '',
                            'summary' => $moodleCourse['summary'] ?? null,
                            'numsections' => $moodleCourse['numsections'] ?? 0,
                            'startdate' => isset($moodleCourse['startdate']) ? date('Y-m-d H:i:s', $moodleCourse['startdate']) : null,
                            'enddate' => isset($moodleCourse['enddate']) ? date('Y-m-d H:i:s', $moodleCourse['enddate']) : null,
                            'sync_status' => 'synced',
                            'synced_at' => now(),
                            'dirty' => 0,
                        ]
                    );

                    $this->pullCourseContents($course);
                    $this->pullParticipants($course);
                    $summary['updated']++;

                } catch (Exception $e) {
                    $this->logError("Erreur pull cours {$moodleCourse['id']}", $e);
                    $summary['errors']++;
                }
            }
        } catch (Exception $e) {
            $this->logError("Erreur pull courses", $e);
            $summary['errors']++;
        }
        return $summary;
    }

    protected function pullCourseContents(Course $course): void
    {
        $contents = $this->api->getCourseContents($course->moodle_id);

        foreach ($contents as $sectionData) {
            $section = Section::updateOrCreate(
                ['moodle_id' => $sectionData['id']],
                [
                    'course_id' => $course->id,
                    'name' => $sectionData['name'] ?? '',
                    'summary' => $sectionData['summary'] ?? null,
                    'position' => $sectionData['section'] ?? 0,
                    'visible' => $sectionData['visible'] ?? 1,
                    'sync_status' => 'synced',
                    'synced_at' => now(),
                    'dirty' => 0,
                ]
            );

            // Modules dans la section
            foreach ($sectionData['modules'] ?? [] as $moduleData) {
                Module::updateOrCreate(
                    ['moodle_id' => $moduleData['id']],
                    [
                        'section_id' => $section->id,
                        'name' => $moduleData['name'] ?? '',
                        'modname' => $moduleData['modname'] ?? '',
                        'modplural' => $moduleData['modplural'] ?? '',
                        'intro' => $moduleData['description'] ?? null,
                        'position' => $moduleData['position'] ?? 0,
                        'visible' => $moduleData['visible'] ?? 1,
                        'completion' => $moduleData['completion'] ?? 0,
                        'downloadcontent' => $moduleData['downloadcontent'] ?? false,
                        'file_path' => $moduleData['url'] ?? '',
                        'sync_status' => 'synced',
                        'synced_at' => now(),
                        'dirty' => 0,
                    ]
                );
            }
        }
    }

    protected function pullParticipants(Course $course): void
    {
        $users = $this->api->getEnrolledUsers($course->moodle_id);

        foreach ($users as $userData) {
            Participant::updateOrCreate(
                ['moodle_enrolment_id' => $userData['id']],
                [
                    'course_id' => $course->id,
                    'user_id' => $userData['userid'],
                    'role' => $userData['roles'][0]['shortname'] ?? 'student',
                    'status' => 1, // actif
                    'sync_status' => 'synced',
                    'synced_at' => now(),
                    'dirty' => 0,
                ]
            );
        }
    }

    protected function getEntity(int $id): ?Model
    {
        return Course::find($id);
    }

    protected function pushCreate(Model $entity, array $payload): void
    {
        /** @var Course $entity */
        try {
            if (!$entity->category_id) {
                throw new Exception("Cours sans category_id, impossible de créer sur Moodle");
            }

            $category = Category::find($entity->category_id);
            if (!$category || !$category->moodle_id) {
                throw new Exception("Catégorie parente non synchronisée, impossible de créer le cours");
            }

            $result = $this->api->call('core_course_create_courses', [
                'courses[0][fullname]' => $entity->fullname,
                'courses[0][shortname]' => $entity->shortname,
                'courses[0][categoryid]' => $category->moodle_id,
                'courses[0][summary]' => $entity->summary ?? '',
                'courses[0][numsections]' => $entity->numsections ?? 0,
                'courses[0][startdate]' => $entity->startdate ? strtotime($entity->startdate) : 0,
                'courses[0][enddate]' => $entity->enddate ? strtotime($entity->enddate) : 0,
                'courses[0][visible]' => 1,
            ]);

            if (isset($result[0]['id'])) {
                $entity->update([
                    'moodle_id' => $result[0]['id'],
                    'sync_status' => 'synced',
                    'synced_at' => now(),
                    'dirty' => 0,
                ]);
                $this->logInfo("CREATE cours#{$entity->id} → Moodle id:{$result[0]['id']}");
            }

        } catch (Exception $e) {
            $this->logError("Erreur CREATE cours#{$entity->id}", $e);
            throw $e;
        }
    }

    protected function pushUpdate(Model $entity, array $payload): void
    {
        /** @var Course $entity */
        try {
            if (!$entity->moodle_id) {
                throw new Exception("Cours sans moodle_id, impossible de mettre à jour");
            }

            $this->api->call('core_course_update_courses', [
                'courses[0][id]' => $entity->moodle_id,
                'courses[0][fullname]' => $entity->fullname,
                'courses[0][shortname]' => $entity->shortname,
                'courses[0][summary]' => $entity->summary ?? '',
                'courses[0][numsections]' => $entity->numsections ?? 0,
                'courses[0][startdate]' => $entity->startdate ? strtotime($entity->startdate) : 0,
                'courses[0][enddate]' => $entity->enddate ? strtotime($entity->enddate) : 0,
            ]);

            $entity->update([
                'sync_status' => 'synced',
                'synced_at' => now(),
                'dirty' => 0,
            ]);

            $this->logInfo("UPDATE cours#{$entity->id} → Moodle id:{$entity->moodle_id}");

        } catch (Exception $e) {
            $this->logError("Erreur UPDATE cours#{$entity->id}", $e);
            throw $e;
        }
    }
}
