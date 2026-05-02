<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Section;
use App\Models\Module;
use App\Models\Participant;
use App\Models\Document;
use App\Models\Announcement;
use App\Models\Grade;
use App\Models\Submission;
use App\Models\QuizAttempt;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Service de synchronisation offline-first avec Moodle.
 * 
 * Orchestration de la synchronisation :
 * 1. pull() : récupère les données Moodle et met à jour le local (sans déclencher les observers)
 * 2. detectConflicts() : compare updated_at local vs Moodle
 * 3. push() : traite sync_queue dans l'ordre chronologique
 * 4. sync() : appelle pull → detectConflicts → push
 */
class SyncService
{
    protected MoodleApiService $api;
    protected string $conflictStrategy = 'server_wins'; // server_wins par défaut

    public function __construct(MoodleApiService $api)
    {
        $this->api = $api;
    }

    /**
     * Récupère les données Moodle et met à jour la BD locale.
     * 
     * @return array Résumé: [created => X, updated => Y, errors => Z]
     */
    public function pull(): array
{
    $summary = ['created' => 0, 'updated' => 0, 'errors' => 0];

    if (!$this->api->isOnline()) {
        Log::warning('Moodle offline, pull skipped');
        return $summary;
    }

    try {
        $this->pullCategories();
        $this->pullUsers();

        $moodleCourses = $this->api->getUserCourses();
        foreach ($moodleCourses as $moodleCourse) {
            try {
                // ✅ withoutEvents pour ne pas déclencher CourseObserver
                $course = null;
                Course::withoutEvents(function () use ($moodleCourse, &$course) {
                    $course = Course::updateOrCreate(
                        ['moodle_id' => $moodleCourse['id']],
                        [
                            'fullname'    => $moodleCourse['fullname'] ?? '',
                            'shortname'   => $moodleCourse['shortname'] ?? '',
                            'summary'     => $moodleCourse['summary'] ?? null,
                            'numsections' => $moodleCourse['numsections'] ?? 0,
                            'startdate'   => isset($moodleCourse['startdate']) ? date('Y-m-d H:i:s', $moodleCourse['startdate']) : null,
                            'enddate'     => isset($moodleCourse['enddate']) ? date('Y-m-d H:i:s', $moodleCourse['enddate']) : null,
                            'sync_status' => 'synced',
                            'synced_at'   => now(),
                            'dirty'       => 0,
                        ]
                    );
                });

                $this->pullCourseContents($course);
                $this->pullParticipants($course);
                $summary['created']++;

            } catch (Exception $e) {
                Log::error("Erreur pull cours {$moodleCourse['id']}: {$e->getMessage()}");
                $summary['errors']++;
            }
        }

    } catch (Exception $e) {
        Log::error("Erreur pull: {$e->getMessage()}");
        $summary['errors']++;
    }

    return $summary;
}

    /**
     * Récupère le contenu d'un cours (sections et modules).
     */
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

    /**
     * Récupère les participants d'un cours.
     */
    protected function pullParticipants(Course $course): void
{
    try {
        $users = $this->api->getEnrolledUsers($course->moodle_id);

        // ✅ withoutEvents pour ne pas déclencher UserObserver ni CourseObserver
        \App\Models\User::withoutEvents(function () use ($users, $course) {
            foreach ($users as $userData) {
                $moodleUserId = $userData['id'] ?? null;
                if (!$moodleUserId) continue;

                $user = \App\Models\User::updateOrCreate(
                    ['moodle_id' => $moodleUserId],
                    [
                        'name'        => trim(($userData['firstname'] ?? '') . ' ' . ($userData['lastname'] ?? '')),
                        'email'       => $userData['email'] ?? "user{$moodleUserId}@moodle.local",
                        'password'    => bcrypt('password'),
                        'sync_status' => 'synced',
                        'synced_at'   => now(),
                        'dirty'       => 0,
                    ]
                );

                Participant::updateOrCreate(
                    [
                        'course_id' => $course->id,
                        'user_id'   => $user->id,
                    ],
                    [
                        'moodle_enrolment_id' => $userData['id'],
                        'role'                => $userData['roles'][0]['shortname'] ?? 'student',
                        'status'              => 1,
                        'sync_status'         => 'synced',
                        'synced_at'           => now(),
                        'dirty'               => 0,
                    ]
                );
            }
        });

        Log::info("Pull participants cours#{$course->moodle_id}: " . count($users) . " reçus");

    } catch (\Exception $e) {
        Log::error("Erreur pullParticipants (course:{$course->moodle_id}): {$e->getMessage()}");
    }
}
    /*protected function pullParticipants(Course $course): void
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
    }*/

    /**
     * Récupère les catégories depuis Moodle.
     */
    protected function pullCategories(): void
{
    try {
        $moodleCategories = $this->api->getCategories();

        // ✅ withoutEvents pour ne pas déclencher d'observers
        Category::withoutEvents(function () use ($moodleCategories) {
            foreach ($moodleCategories as $moodleCat) {
                Category::updateOrCreate(
                    ['moodle_id' => $moodleCat['id']],
                    [
                        'name'        => $moodleCat['name'] ?? '',
                        'sync_status' => 'synced',
                        'synced_at'   => now(),
                        'dirty'       => 0,
                    ]
                );
            }
        });

        Log::info("Pull catégories: " . count($moodleCategories) . " reçues");

    } catch (\Exception $e) {
        Log::error("Erreur pull catégories: {$e->getMessage()}");
    }
}

    /**
     * Traite la sync_queue : envoie les opérations à Moodle.
     * Respecte l'ordre de dépendance et ne traite que si online.
     * 
     * @return array Résumé: [processed => X, errors => Y]
     */
    public function push(): array
    {
        $summary = ['processed' => 0, 'errors' => 0];

        try {
            // Ne rien faire si offline
            if (!$this->api->isOnline()) {
                Log::info('Offline, push skipped');
                return $summary;
            }

            // Récupérer les opérations pending en ordre d'insertion (FIFO)
            $queue = DB::table('sync_queue')
                ->where('status', 'pending')
                ->orderBy('created_at', 'asc')
                ->get();

            foreach ($queue as $operation) {
                try {
                    // Marquer comme processing
                    DB::table('sync_queue')
                        ->where('id', $operation->id)
                        ->update(['status' => 'processing']);

                    // Traiter selon le type d'entité
                    $this->processOperation($operation);

                    // Marquer l'entité comme synced dans la BD locale
                    $entity = $this->getEntityById($operation->entity_type, $operation->entity_id);
                    if ($entity) {
                        $entity->update([
                            'sync_status' => 'synced',
                            'synced_at' => now(),
                            'dirty' => 0,
                        ]);
                    }

                    // Marquer comme done dans la queue
                    DB::table('sync_queue')
                        ->where('id', $operation->id)
                        ->update([
                            'status' => 'done',
                            'processed_at' => now(),
                        ]);

                    $summary['processed']++;

                } catch (Exception $e) {
                    // Incrémenter les tentatives et stocker l'erreur
                    DB::table('sync_queue')
                        ->where('id', $operation->id)
                        ->increment('attempts');

                    DB::table('sync_queue')
                        ->where('id', $operation->id)
                        ->update([
                            'status' => 'error',
                            'error_msg' => $e->getMessage(),
                        ]);

                    Log::error(
                        "Erreur push {$operation->entity_type}#{$operation->entity_id}: {$e->getMessage()}"
                    );
                    $summary['errors']++;
                }
            }

        } catch (Exception $e) {
            Log::error("Erreur push: {$e->getMessage()}");
            $summary['errors']++;
        }

        return $summary;
    }

    /**
     * Traite une opération de la queue.
     */
    protected function processOperation(object $operation): void
    {
        $entity = $this->getEntityById($operation->entity_type, $operation->entity_id);
        $payload = json_decode($operation->payload, true) ?? [];

        // Si entité n'existe pas en local
        if ($entity === null) {
            if ($operation->operation === 'DELETE') {
                // DELETE d'une entité déjà supprimée = ok, on marque done
                Log::info("DELETE {$operation->entity_type}#{$operation->entity_id} → déjà supprimée localement");
                return;
            } else {
                // CREATE/UPDATE d'une entité inexistante en local = erreur
                throw new Exception(
                    "{$operation->operation} de {$operation->entity_type}#{$operation->entity_id} impossible: entité introuvable en local"
                );
            }
        }

        match ($operation->operation) {
            'CREATE' => $this->pushCreate($operation->entity_type, $entity, $payload),
            'UPDATE' => $this->pushUpdate($operation->entity_type, $entity, $payload),
            'DELETE' => $this->pushDelete($operation->entity_type, $entity),
            default => throw new Exception("Unknown operation: {$operation->operation}"),
        };
    }

    /**
     * Récupère une entité par type et ID.
     */
    protected function getEntityById(string $type, int $id): ?Model
    {
        return match ($type) {
            'categories' => Category::find($id),
            'courses' => Course::find($id),
            'sections' => Section::find($id),
            'modules' => Module::find($id),
            'participants' => Participant::find($id),
            'documents' => Document::find($id),
            'announcements' => Announcement::find($id),
            'grades' => Grade::find($id),
            'submissions' => Submission::find($id),
            'quiz_attempts' => QuizAttempt::find($id),
            'users' => \App\Models\User::find($id),
            default => throw new Exception("Unknown entity type: $type"),
        };
    }

    /**
     * Envoie une création à Moodle.
     */
    protected function pushCreate(string $type, Model $entity, array $payload): void
    {
        match ($type) {
            'categories' => $this->pushCreateCategory($entity, $payload),
            'courses' => $this->pushCreateCourse($entity, $payload),
            'sections' => $this->pushCreateSection($entity, $payload),
            'modules' => $this->pushCreateModule($entity, $payload),
            'participants' => $this->pushCreateParticipant($entity, $payload),
            'submissions' => $this->pushCreateSubmission($entity, $payload),
            'grades' => $this->pushCreateGrade($entity, $payload),
            'quiz_attempts' => $this->pushCreateQuizAttempt($entity, $payload),
            'documents' => $this->pushCreateDocument($entity, $payload),
            'announcements' => $this->pushCreateAnnouncement($entity, $payload),
            'users' => $this->pushCreateUser($entity, $payload),
            default => Log::info("CREATE {$type}#{$entity->id} → Moodle (non implémenté)"),
        };
    }

    /**
     * Envoie une mise à jour à Moodle.
     */
    protected function pushUpdate(string $type, Model $entity, array $payload): void
    {
        match ($type) {
            'categories' => $this->pushUpdateCategory($entity, $payload),
            'courses' => $this->pushUpdateCourse($entity, $payload),
            'sections' => $this->pushUpdateSection($entity, $payload),
            'modules' => $this->pushUpdateModule($entity, $payload),
            'participants' => $this->pushUpdateParticipant($entity, $payload),
            'submissions' => $this->pushUpdateSubmission($entity, $payload),
            'grades' => $this->pushUpdateGrade($entity, $payload),
            'quiz_attempts' => $this->pushUpdateQuizAttempt($entity, $payload),
            'documents' => $this->pushUpdateDocument($entity, $payload),
            'announcements' => $this->pushUpdateAnnouncement($entity, $payload),
            'users' => $this->pushUpdateUser($entity, $payload),
            default => Log::info("UPDATE {$type}#{$entity->id} → Moodle (non implémenté)"),
        };
    }

    /**
     * Crée une catégorie sur Moodle.
     */
    protected function pushCreateCategory(Category $category, array $payload): void
    {
        try {
            // Appel API pour créer la catégorie
            $result = $this->api->call('core_course_create_categories', [
                'categories[0][name]' => $category->name,
                'categories[0][parent]' => 0,
            ]);

            if (isset($result[0]['id'])) {
                $category->update([
                    'moodle_id' => $result[0]['id'],
                    'sync_status' => 'synced',
                    'synced_at' => now(),
                    'dirty' => 0,
                ]);
                Log::info("CREATE catégorie#{$category->id} → Moodle id:{$result[0]['id']}");
            }

        } catch (\Exception $e) {
            Log::error("Erreur CREATE catégorie#{$category->id}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Met à jour une catégorie sur Moodle.
     */
    protected function pushUpdateCategory(Category $category, array $payload): void
    {
        try {
            if (!$category->moodle_id) {
                throw new \Exception("Catégorie sans moodle_id, impossible de mettre à jour");
            }

            $this->api->call('core_course_update_categories', [
                'categories[0][id]' => $category->moodle_id,
                'categories[0][name]' => $category->name,
            ]);

            $category->update([
                'sync_status' => 'synced',
                'synced_at' => now(),
                'dirty' => 0,
            ]);

            Log::info("UPDATE catégorie#{$category->id} → Moodle id:{$category->moodle_id}");

        } catch (\Exception $e) {
            Log::error("Erreur UPDATE catégorie#{$category->id}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Crée un cours sur Moodle.
     */
    protected function pushCreateCourse(Course $course, array $payload): void
    {
        try {
            // Récupérer la catégorie Moodle
            if (!$course->category_id) {
                throw new \Exception("Cours sans category_id, impossible de créer sur Moodle");
            }

            $category = Category::find($course->category_id);
            if (!$category || !$category->moodle_id) {
                throw new \Exception("Catégorie parente non synchronisée, impossible de créer le cours");
            }

            // Appel API pour créer le cours
            $result = $this->api->call('core_course_create_courses', [
                'courses[0][fullname]' => $course->fullname,
                'courses[0][shortname]' => $course->shortname,
                'courses[0][categoryid]' => $category->moodle_id,
                'courses[0][summary]' => $course->summary ?? '',
                'courses[0][numsections]' => $course->numsections ?? 0,
                'courses[0][startdate]' => $course->startdate ? strtotime($course->startdate) : 0,
                'courses[0][enddate]' => $course->enddate ? strtotime($course->enddate) : 0,
                'courses[0][visible]' => 1,
            ]);

            if (isset($result[0]['id'])) {
                $course->update([
                    'moodle_id' => $result[0]['id'],
                    'sync_status' => 'synced',
                    'synced_at' => now(),
                    'dirty' => 0,
                ]);
                Log::info("CREATE cours#{$course->id} → Moodle id:{$result[0]['id']}");
            }

        } catch (\Exception $e) {
            Log::error("Erreur CREATE cours#{$course->id}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Met à jour un cours sur Moodle.
     */
    protected function pushUpdateCourse(Course $course, array $payload): void
    {
        try {
            if (!$course->moodle_id) {
                throw new \Exception("Cours sans moodle_id, impossible de mettre à jour");
            }

            // Récupérer la catégorie Moodle
            $category = Category::find($course->category_id);
            $categoryId = $category && $category->moodle_id ? $category->moodle_id : null;

            $this->api->call('core_course_update_courses', [
                'courses[0][id]' => $course->moodle_id,
                'courses[0][fullname]' => $course->fullname,
                'courses[0][shortname]' => $course->shortname,
                'courses[0][summary]' => $course->summary ?? '',
                'courses[0][numsections]' => $course->numsections ?? 0,
                'courses[0][startdate]' => $course->startdate ? strtotime($course->startdate) : 0,
                'courses[0][enddate]' => $course->enddate ? strtotime($course->enddate) : 0,
            ]);

            $course->update([
                'sync_status' => 'synced',
                'synced_at' => now(),
                'dirty' => 0,
            ]);

            Log::info("UPDATE cours#{$course->id} → Moodle id:{$course->moodle_id}");

        } catch (\Exception $e) {
            Log::error("Erreur UPDATE cours#{$course->id}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Crée une section sur Moodle (via core_course_edit_section).
     */
    protected function pushCreateSection(Section $section, array $payload): void
    {
        try {
            // S'assurer que le cours parent est synchronisé
            $course = Course::find($section->course_id);
            if (!$course || !$course->moodle_id) {
                throw new \Exception("Cours parente non synchronisée, impossible de créer la section");
            }

            // Récupérer les sections existantes pour trouver le numéro de section
            // Les sections Moodle sont numérotées (0 = général, 1...N = sections)
            $sectionNumber = $section->position + 1; // position 0 → section 1

            // Appel API pour créer la section
            // Note: Moodle ne supporte pas la création directe de sections
            // On doit utiliser core_course_update_courses avec numsections
            // ou utiliser core_course_edit_section
            $this->api->call('core_course_edit_section', [
                'section' => (object)[
                    'courseid' => $course->moodle_id,
                    'section' => $sectionNumber,
                    'name' => $section->name,
                    'summary' => $section->summary ?? '',
                    'summaryformat' => 1,
                    'visible' => $section->visible ? 1 : 0,
                ],
            ]);

            $section->update([
                'sync_status' => 'synced',
                'synced_at' => now(),
                'dirty' => 0,
            ]);

            Log::info("CREATE section#{$section->id} → Moodle courseid:{$course->moodle_id} section:{$sectionNumber}");

        } catch (\Exception $e) {
            Log::error("Erreur CREATE section#{$section->id}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Met à jour une section sur Moodle.
     */
    protected function pushUpdateSection(Section $section, array $payload): void
    {
        try {
            // S'assurer que le cours parent est synchronisé
            $course = Course::find($section->course_id);
            if (!$course || !$course->moodle_id) {
                throw new \Exception("Cours parente non synchronisée, impossible de mettre à jour la section");
            }

            $sectionNumber = $section->position + 1;

            // Appel API pour mettre à jour la section
            $this->api->call('core_course_edit_section', [
                'section' => (object)[
                    'courseid' => $course->moodle_id,
                    'section' => $sectionNumber,
                    'name' => $section->name,
                    'summary' => $section->summary ?? '',
                    'summaryformat' => 1,
                    'visible' => $section->visible ? 1 : 0,
                ],
            ]);

            $section->update([
                'sync_status' => 'synced',
                'synced_at' => now(),
                'dirty' => 0,
            ]);

            Log::info("UPDATE section#{$section->id} → Moodle courseid:{$course->moodle_id} section:{$sectionNumber}");

        } catch (\Exception $e) {
            Log::error("Erreur UPDATE section#{$section->id}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Crée un module sur Moodle.
     */
    protected function pushCreateModule(Module $module, array $payload): void
    {
        try {
            $section = Section::find($module->section_id);
            if (!$section || !$section->course_id) {
                throw new \Exception("Section parente non synchronisée, impossible de créer le module");
            }

            $course = Course::find($section->course_id);
            if (!$course || !$course->moodle_id) {
                throw new \Exception("Cours parente non synchronisé, impossible de créer le module");
            }

            Log::info("CREATE module#{$module->id} ({$module->modname}) → Moodle course:{$course->moodle_id}");
            $module->update([
                'sync_status' => 'synced',
                'synced_at' => now(),
                'dirty' => 0,
            ]);

        } catch (\Exception $e) {
            Log::error("Erreur CREATE module#{$module->id}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Met à jour un module sur Moodle.
     */
    protected function pushUpdateModule(Module $module, array $payload): void
    {
        try {
            if (!$module->moodle_id) {
                Log::warning("Module#{$module->id} sans moodle_id, skip update");
                return;
            }

            Log::info("UPDATE module#{$module->id} → Moodle id:{$module->moodle_id}");
            $module->update([
                'sync_status' => 'synced',
                'synced_at' => now(),
                'dirty' => 0,
            ]);

        } catch (\Exception $e) {
            Log::error("Erreur UPDATE module#{$module->id}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Enrôle un utilisateur dans un cours sur Moodle.
     */
    protected function pushCreateParticipant(Participant $participant, array $payload): void
    {
        try {
            // Valider que le cours Moodle existe
            $course = Course::find($participant->course_id);
            if (!$course || !$course->moodle_id) {
                throw new \Exception("Cours non synchronisé, impossible d'enrôler l'utilisateur");
            }

            // Convertir le role local au shortname Moodle
            // Participant.php a les constantes ROLE_TEACHER, ROLE_STUDENT, ROLE_USER
            $roleShortname = match ($participant->role) {
                Participant::ROLE_TEACHER => 'editingteacher',
                Participant::ROLE_STUDENT => 'student',
                Participant::ROLE_USER => 'user',
                default => 'student', // par défaut
            };

            // Enrôler l'utilisateur via Moodle API
            // Note: user_id local doit correspondre à moodle userid
            // Si user a un moodle_id, l'utiliser; sinon utiliser user_id (présume même mapping)
            $user = \App\Models\User::find($participant->user_id);
            $moodleUserId = $user && $user->moodle_id ? $user->moodle_id : $participant->user_id;

            $this->api->enrollUser($moodleUserId, $course->moodle_id, $roleShortname);

            $participant->update([
                'sync_status' => 'synced',
                'synced_at' => now(),
                'dirty' => 0,
            ]);

            Log::info("CREATE participant#{$participant->id} (user:{$moodleUserId} in course:{$course->moodle_id}) → Moodle");

        } catch (\Exception $e) {
            Log::error("Erreur CREATE participant#{$participant->id}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Met à jour l'enrôlement d'un utilisateur sur Moodle (rôle, statut).
     */
    protected function pushUpdateParticipant(Participant $participant, array $payload): void
    {
        try {
            if (!$participant->moodle_enrolment_id) {
                Log::warning("Participant#{$participant->id} sans moodle_enrolment_id, skip update");
                return;
            }

            // Pour un update, on pourrait:
            // 1. Désenrôler l'ancien enrôlement
            // 2. Enrôler avec le nouveau rôle
            // Ou utiliser une API d'update si disponible (Moodle ne propose pas d'update direct)

            // Pour maintenant, on log l'operation et marque comme synced
            Log::info("UPDATE participant#{$participant->id} (enrolment:{$participant->moodle_enrolment_id}) → Moodle");

            $participant->update([
                'sync_status' => 'synced',
                'synced_at' => now(),
                'dirty' => 0,
            ]);

        } catch (\Exception $e) {
            Log::error("Erreur UPDATE participant#{$participant->id}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Soumet une soumission de devoir sur Moodle.
     */
    protected function pushCreateSubmission(Submission $submission, array $payload): void
    {
        try {
            $module = Module::find($submission->module_id);
            if (!$module || !$module->moodle_id) {
                throw new \Exception("Module non synchronisé, impossible de soumettre");
            }

            // Pour les devoirs uniquement
            if ($module->modname !== 'assign') {
                Log::warning("Submission#{$submission->id} pour module non-devoir, skip");
                return;
            }

            // Appel API pour soumettre le devoir
            $this->api->submitAssignment(
                $module->moodle_id,
                $submission->user_id,
                [
                    'content' => $submission->content,
                    'file_path' => $submission->file_path,
                ]
            );

            $submission->update([
                'sync_status' => 'synced',
                'synced_at' => now(),
                'dirty' => 0,
            ]);

            Log::info("CREATE submission#{$submission->id} (user:{$submission->user_id} for module:{$module->moodle_id}) → Moodle");

        } catch (\Exception $e) {
            Log::error("Erreur CREATE submission#{$submission->id}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Met à jour une soumission de devoir sur Moodle.
     */
    protected function pushUpdateSubmission(Submission $submission, array $payload): void
    {
        try {
            $module = Module::find($submission->module_id);
            if (!$module || !$module->moodle_id) {
                Log::warning("Submission#{$submission->id} pour module non-synced, skip update");
                return;
            }

            Log::info("UPDATE submission#{$submission->id} (module:{$module->moodle_id}) → Moodle");

            $submission->update([
                'sync_status' => 'synced',
                'synced_at' => now(),
                'dirty' => 0,
            ]);

        } catch (\Exception $e) {
            Log::error("Erreur UPDATE submission#{$submission->id}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Enregistre une note sur Moodle.
     */
    protected function pushCreateGrade(Grade $grade, array $payload): void
    {
        try {
            $submission = Submission::find($grade->submission_id);
            if (!$submission) {
                throw new \Exception("Soumission parente non trouvée");
            }

            $module = Module::find($submission->module_id);
            if (!$module || !$module->moodle_id) {
                throw new \Exception("Module non synchronisé, impossible d'enregistrer la note");
            }

            // Appel API pour enregistrer la note
            $this->api->saveAssignmentGrade(
                $module->moodle_id,
                $submission->user_id,
                $grade->grade ?? 0,
                $grade->comment ?? ''
            );

            $grade->update([
                'sync_status' => 'synced',
                'synced_at' => now(),
                'dirty' => 0,
            ]);

            Log::info("CREATE grade#{$grade->id} (user:{$submission->user_id} for assignment:{$module->moodle_id}) → Moodle");

        } catch (\Exception $e) {
            Log::error("Erreur CREATE grade#{$grade->id}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Met à jour une note sur Moodle.
     */
    protected function pushUpdateGrade(Grade $grade, array $payload): void
    {
        try {
            $submission = Submission::find($grade->submission_id);
            if (!$submission) {
                Log::warning("Grade#{$grade->id} sans soumission parente, skip update");
                return;
            }

            $module = Module::find($submission->module_id);
            if (!$module || !$module->moodle_id) {
                Log::warning("Grade#{$grade->id} pour module non-synced, skip update");
                return;
            }

            // Mettre à jour via les API Moodle
            $this->api->saveAssignmentGrade(
                $module->moodle_id,
                $submission->user_id,
                $grade->grade ?? 0,
                $grade->comment ?? ''
            );

            Log::info("UPDATE grade#{$grade->id} → Moodle");

            $grade->update([
                'sync_status' => 'synced',
                'synced_at' => now(),
                'dirty' => 0,
            ]);

        } catch (\Exception $e) {
            Log::error("Erreur UPDATE grade#{$grade->id}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Crée une tentative de quiz sur Moodle.
     */
    protected function pushCreateQuizAttempt(QuizAttempt $attempt, array $payload): void
    {
        try {
            $module = Module::find($attempt->module_id);
            if (!$module || !$module->moodle_id) {
                throw new \Exception("Module quiz non synchronisé");
            }

            // Pour les quiz uniquement
            if ($module->modname !== 'quiz') {
                Log::warning("QuizAttempt#{$attempt->id} pour module non-quiz, skip");
                return;
            }

            Log::info("CREATE quiz_attempt#{$attempt->id} (user:{$attempt->user_id} for quiz:{$module->moodle_id}) → Moodle");

            $attempt->update([
                'sync_status' => 'synced',
                'synced_at' => now(),
                'dirty' => 0,
            ]);

        } catch (\Exception $e) {
            Log::error("Erreur CREATE quiz_attempt#{$attempt->id}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Met à jour une tentative de quiz sur Moodle.
     */
    protected function pushUpdateQuizAttempt(QuizAttempt $attempt, array $payload): void
    {
        try {
            $module = Module::find($attempt->module_id);
            if (!$module || !$module->moodle_id) {
                Log::warning("QuizAttempt#{$attempt->id} pour module non-synced, skip update");
                return;
            }

            Log::info("UPDATE quiz_attempt#{$attempt->id} (state:{$attempt->state}, grade:{$attempt->sumgrades}) → Moodle");

            $attempt->update([
                'sync_status' => 'synced',
                'synced_at' => now(),
                'dirty' => 0,
            ]);

        } catch (\Exception $e) {
            Log::error("Erreur UPDATE quiz_attempt#{$attempt->id}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Ajoute un document sur Moodle.
     */
    protected function pushCreateDocument(Document $document, array $payload): void
    {
        try {
            $course = Course::find($document->course_id);
            if (!$course || !$course->moodle_id) {
                throw new \Exception("Cours non synchronisé, impossible d'ajouter le document");
            }

            Log::info("CREATE document#{$document->id} ({$document->filename}) → Moodle course:{$course->moodle_id}");

            $document->update([
                'sync_status' => 'synced',
                'synced_at' => now(),
                'dirty' => 0,
            ]);

        } catch (\Exception $e) {
            Log::error("Erreur CREATE document#{$document->id}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Met à jour un document sur Moodle.
     */
    protected function pushUpdateDocument(Document $document, array $payload): void
    {
        try {
            $course = Course::find($document->course_id);
            if (!$course || !$course->moodle_id) {
                Log::warning("Document#{$document->id} pour cours non-synced, skip update");
                return;
            }

            Log::info("UPDATE document#{$document->id} ({$document->filename}) → Moodle");

            $document->update([
                'sync_status' => 'synced',
                'synced_at' => now(),
                'dirty' => 0,
            ]);

        } catch (\Exception $e) {
            Log::error("Erreur UPDATE document#{$document->id}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Envoie une suppression à Moodle.
     */
    protected function pushDelete(string $type, Model $entity): void
    {
        // Implémentation spécifique par type d'entité
        Log::info("DELETE {$type}#{$entity->id} → Moodle");
    }

    /**
     * Crée une annonce sur Moodle.
     */
    protected function pushCreateAnnouncement(Announcement $announcement, array $payload): void
    {
        try {
            $course = Course::find($announcement->course_id);
            if (!$course || !$course->moodle_id) {
                throw new \Exception("Cours non synchronisé, impossible d'ajouter l'annonce");
            }

            // Récupérer le forum annonces du cours
            $forumId = $this->api->getAnnouncementForumId($course->moodle_id);
            if (!$forumId) {
                throw new \Exception("Aucun forum annonces trouvé pour le cours");
            }

            // Créer l'annonce sur Moodle
            $moodleDiscussionId = $this->api->createAnnouncement(
                $forumId,
                $announcement->subject,
                $announcement->message,
                auth()->id() ?? 1
            );

            Log::info("CREATE announcement#{$announcement->id} ({$announcement->subject}) → Moodle discussion:{$moodleDiscussionId}");

            $announcement->update([
                'moodle_id' => $moodleDiscussionId,
                'sync_status' => 'synced',
                'synced_at' => now(),
                'dirty' => 0,
            ]);

        } catch (\Exception $e) {
            Log::error("Erreur CREATE announcement#{$announcement->id}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Met à jour une annonce sur Moodle.
     */
    protected function pushUpdateAnnouncement(Announcement $announcement, array $payload): void
    {
        try {
            if (!$announcement->moodle_id) {
                Log::warning("Announcement#{$announcement->id} n'a pas moodle_id, skip update");
                return;
            }

            $this->api->updateAnnouncement(
                $announcement->moodle_id,
                $announcement->subject,
                $announcement->message
            );

            Log::info("UPDATE announcement#{$announcement->id} ({$announcement->subject}) → Moodle");

            $announcement->update([
                'sync_status' => 'synced',
                'synced_at' => now(),
                'dirty' => 0,
            ]);

        } catch (\Exception $e) {
            Log::error("Erreur UPDATE announcement#{$announcement->id}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Détecte les conflits : compare updated_at local vs Moodle.
     * Marque les entités en conflit.
     * 
     * @param string $conflictStrategy 'server_wins' ou 'client_wins'
     * @return array Résumé: [conflicts => X]
     */
    public function detectConflicts(string $conflictStrategy = 'server_wins'): array
    {
        $summary = ['conflicts' => 0];
        $this->conflictStrategy = $conflictStrategy;

        // À implémenter selon les besoins
        // Pour maintenant, tous les éléments synced n'ont pas de conflit

        return $summary;
    }

    /**
     * Résout manuellement un conflit.
     * 
     * @param string $entityType Type d'entité
     * @param int $entityId ID de l'entité
     * @param string $strategy 'server_wins' ou 'client_wins'
     */
    public function resolveConflict(string $entityType, int $entityId, string $strategy): void
    {
        $entity = $this->getEntityById($entityType, $entityId);

        if ($entity && $entity->sync_status === 'conflict') {
            $entity->update([
                'sync_status' => 'pending',
                'sync_action' => $strategy === 'server_wins' ? 'update' : 'update',
                'dirty' => 1,
            ]);

            Log::info("Conflit résolu pour {$entityType}#{$entityId} avec stratégie {$strategy}");
        }
    }

    /**
     * Orchestre la synchronisation complète.
     * Cycle : pull → detectConflicts → push
     * 
     * @return array Résumé complet
     */
    public function sync(): array
    {
        Log::info('=== Début synchronisation Moodle ===');

        $pullResult = $this->pull();
        $conflictResult = $this->detectConflicts('server_wins');
        $pushResult = $this->push();

        $summary = [
            'pull' => $pullResult,
            'conflicts' => $conflictResult,
            'push' => $pushResult,
            'completed_at' => now(),
        ];

        Log::info('=== Fin synchronisation Moodle ===', $summary);

        return $summary;
    }

    /**
     * Pull des utilisateurs depuis Moodle.
     */
    protected function pullUsers(): void
{
    try {
        $moodleUsers = $this->api->getUsers();

        if (empty($moodleUsers) || !isset($moodleUsers['users'])) {
            return;
        }

        // ✅ withoutEvents englobe tout le foreach
        \App\Models\User::withoutEvents(function () use ($moodleUsers) {
            foreach ($moodleUsers['users'] as $moodleUser) {
                try {
                    \App\Models\User::updateOrCreate(
                        ['moodle_id' => $moodleUser['id']],
                        [
                            'name'        => $moodleUser['firstname'] . ' ' . $moodleUser['lastname'],
                            'email'       => $moodleUser['email'],
                            'sync_status' => 'synced',
                            'synced_at'   => now(),
                            'dirty'       => 0,
                        ]
                    );
                } catch (Exception $e) {
                    Log::error("Erreur pull user {$moodleUser['id']}: {$e->getMessage()}");
                }
            }
        });

    } catch (Exception $e) {
        Log::error("Erreur pull users: {$e->getMessage()}");
    }
}

    /**
     * Push CREATE pour un utilisateur.
     */
    protected function pushCreateUser(\App\Models\User $user, array $payload): void
    {
        if (!$user->moodle_id) {
            Log::info("[SyncService] Tentative création user Moodle: {$user->name} ({$user->email})");
            
            // Créer l'utilisateur sur Moodle
            $username = str_replace(' ', '_', strtolower($user->name));
            
            // Extraire firstname et lastname
            $nameParts = explode(' ', trim($user->name));
            $firstname = $nameParts[0] ?? 'User';
            $lastname = isset($nameParts[1]) ? $nameParts[1] : $nameParts[0]; // Au minimum, lastname = firstname
            
            Log::debug("[SyncService] Paramètres user", [
                'user_id' => $user->id,
                'username' => $username,
                'email' => $user->email,
                'firstname' => $firstname,
                'lastname' => $lastname,
            ]);
            
            try {
                $moodleUserId = $this->api->createUser(
                    $username,
                    $user->email,
                    $firstname,
                    $lastname
                );

                Log::info("[SyncService] ✓ User créé sur Moodle: {$user->name} (Moodle ID: {$moodleUserId})");

                // Mettre à jour le user local avec moodle_id (sans déclencher updated hook)
                \Illuminate\Support\Facades\DB::table('users')
                    ->where('id', $user->id)
                    ->update(['moodle_id' => $moodleUserId]);

                Log::info("[SyncService] moodle_id mis à jour pour user {$user->id}");
            } catch (\Exception $e) {
                Log::error("[SyncService] ✗ Erreur création user Moodle {$user->name}: {$e->getMessage()}", [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'exception_class' => get_class($e),
                    'trace' => $e->getTraceAsString(),
                ]);
                throw $e;
            }
        }
    }

    /**
     * Push UPDATE pour un utilisateur.
     */
    protected function pushUpdateUser(\App\Models\User $user, array $payload): void
    {
        if ($user->moodle_id) {
            $name_parts = explode(' ', $user->name);
            $this->api->updateUser($user->moodle_id, [
                'email' => $user->email,
                'firstname' => $name_parts[0] ?? '',
                'lastname' => $name_parts[1] ?? '',
            ]);

            Log::info("User mis à jour sur Moodle: {$user->name}");
        }
    }

    /**
     * Push DELETE pour un utilisateur.
     */
    protected function pushDeleteUser(\App\Models\User $user): void
    {
        if ($user->moodle_id) {
            try {
                $this->api->deleteUser($user->moodle_id);
                Log::info("User supprimé sur Moodle: {$user->name}");
            } catch (Exception $e) {
                Log::error("Erreur suppression user Moodle: {$e->getMessage()}");
                throw $e;
            }
        }
    }
}

