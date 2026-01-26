<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Course;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class MoodleCourseService
{
    protected string $apiUrl;
    protected string $token;
    protected array $defaultParams;
    protected string $logFilePath;

    public function __construct()
    {
        $this->apiUrl = config('moodle.api_url');
        $this->token = config('moodle.api_token');
        $this->defaultParams = [
            'wstoken' => $this->token,
            'moodlewsrestformat' => 'json'
        ];

        $this->logFilePath = storage_path('logs/moodle_actions.txt');
    }

    /**
     * Log an action to the sync file
     */
    public function logAction(string $action, array $data): void
    {
        $logEntry = [
            'timestamp' => now()->toIso8601String(),
            'action' => $action,
            'data' => $data
        ];
        
        $logLine = json_encode($logEntry) . PHP_EOL;
        file_put_contents($this->logFilePath, $logLine, FILE_APPEND);
        
        Log::info("Moodle action logged: {$action}");
    }

    /**
     * Log course creation action
     */
    public function logCourseCreation(Course $course): void
    {
        $courseData = [
            'id' => $course->id,
            'fullname' => $course->fullname,
            'shortname' => $course->shortname,
            'category_id' => $course->category_id,
            'summary' => $course->summary,
            'numsections' => $course->numsections,
            'startdate' => $course->startdate,
            'enddate' => $course->enddate
        ];
        
        $this->logAction('course_create', $courseData);
    }

    /**
     * Log course update action
     */
    public function logCourseUpdate(Course $course): void
    {
        $courseData = [
            'id' => $course->id,
            'fullname' => $course->fullname,
            'shortname' => $course->shortname,
            'category_id' => $course->category_id,
            'summary' => $course->summary,
            'numsections' => $course->numsections,
            'startdate' => $course->startdate,
            'enddate' => $course->enddate
        ];
        
        $this->logAction('course_update', $courseData);
    }

    /**
     * Log course deletion action
     */
    public function logCourseDeletion(Course $course): void
    {
        $this->logAction('course_delete', ['id' => $course->id]);
    }

    /**
     * Get all courses
     */
    public function getAllCourses(): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_course_get_courses'
            ]);
            $response = Http::get($this->apiUrl, $params);

            $data = $response->json();


            // Check if the response contains an error
            if (isset($data['errorcode']) || isset($data['exception'])) {
                Log::error('Moodle API Error (getAllCourses): ' . $data['message']);
                return [];
            }

            return $data;
        } catch (\Exception $e) {
            Log::error('Moodle API Error (getAllCourses): ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Create a new course
     */
    public function createCourse(Course $course)
    {
        Log::Info('dans createCourse');
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_course_create_courses',
                'courses' => [
                    [
                    'fullname' => $course->fullname,
                    'shortname' => $course->shortname,
                    'categoryid' => (int)Category::where('id', $course->category_id)->value('moodle_id'),
                    'summary' => $course->summary ?? '',
                    'summaryformat' => 1, // 1 = HTML format
                    'startdate' => $course->startdate ? (int)strtotime($course->startdate) : (int)time(),
                    'enddate' => $course->enddate ? (int)strtotime($course->enddate) : 0,
                    'visible' => 1, // 1 = visible, 0 = hidden
                    ]
                ]
            ]);
            $response = Http::get($this->apiUrl, $params);
            Log::Info(' mes params:' . json_encode($params) . 'response body:' . $response->body());

            // Récupérer l'ID du cours créé
            $courseData = json_decode($response->body(), true);
            if (is_array($courseData) && !empty($courseData[0]['id'])) {
                $courseId = $courseData[0]['id'];
                
                // Étape 2: Ajouter l'administrateur comme enseignant
                $enrollParams = array_merge($this->defaultParams, [
                    'wsfunction' => 'enrol_manual_enrol_users',
                    'enrolments' => [
                        [
                            'roleid' => 3,  // 3 est généralement l'ID du rôle "Teacher" dans Moodle
                            'userid' => 2,  // ID de l'administrateur
                            'courseid' => $courseId
                        ]
                    ]
                ]);
                $enrollResponse = Http::get($this->apiUrl, $enrollParams);
                Log::Info('Enroll response: ' . $enrollResponse->body());
            }
            if (isset($data['errorcode']) || isset($data['exception'])) {
                Log::error('Moodle API Error (createCourse): ' . $data['message']);
                return false;
            }
            return true;
        } catch (\Exception $e) {
            Log::error('Moodle API Error (createCourse): ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update an existing course
     */
    public function updateCourse(int $courseId, array $courseData): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_course_update_courses',
                'courses[0][id]' => $courseId
            ]);

            foreach ($courseData as $key => $value) {
                $params["courses[0][$key]"] = $value;
            }

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Moodle API Error (updateCourse): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete a course
     */
    public function deleteCourse(int $courseId): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_course_delete_courses',
                'courseids[0]' => $courseId
            ]);

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Moodle API Error (deleteCourse): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get course contents
     */
    public function getCourseContents(int $courseId): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_course_get_contents',
                'courseid' => $courseId
            ]);

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Moodle API Error (getCourseContents): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get all course categories
     */
    public function getCategories(): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_course_get_categories'
            ]);

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Moodle API Error (getCategories): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Enroll a user in a course
     */
    public function enrollUser(int $userId, int $courseId, int $roleId = 5): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'enrol_manual_enrol_users',
                'enrolments[0][roleid]' => $roleId,
                'enrolments[0][userid]' => $userId,
                'enrolments[0][courseid]' => $courseId
            ]);

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Moodle API Error (enrollUser): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Unenroll a user from a course
     */
    public function unenrollUser(int $userId, int $courseId): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'enrol_manual_unenrol_users',
                'enrolments[0][userid]' => $userId,
                'enrolments[0][courseid]' => $courseId
            ]);

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Moodle API Error (unenrollUser): ' . $e->getMessage());
            throw $e;
        }
    }

    public function isServerAvailable(): bool
    {
        try {
            $response = Http::get($this->apiUrl, [
                'wstoken' => $this->token,
                'wsfunction' => 'core_webservice_get_site_info',
                'moodlewsrestformat' => 'json'
            ]);

            if ($response->successful()) {
                return true;
            } else {
                Log::error('Moodle API Error (isServerAvailable): ' . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Moodle API Error (isServerAvailable): ' . $e->getMessage());
            return false;
        }
    }
    /**
 * Récupère l'ID Moodle du premier teacher (editingteacher ou teacher) d'un cours
 */
public function getCourseTeacherId(int $moodleCourseId): ?int
{
    try {
        $params = array_merge($this->defaultParams, [
            'wsfunction' => 'core_enrol_get_enrolled_users',
            'courseid'   => $moodleCourseId,
        ]);

        $response = Http::get($this->apiUrl, $params);
        $enrolled = $response->json();

        if (!is_array($enrolled)) {
            return null;
        }

        foreach ($enrolled as $user) {
            if (isset($user['roles']) && is_array($user['roles'])) {
                foreach ($user['roles'] as $role) {
                    if (in_array($role['shortname'], ['teacher', 'editingteacher'])) {
                        return $user['id']; // ID Moodle du teacher
                    }
                }
            }
        }

        return null;
    } catch (\Exception $e) {
        Log::error('Erreur récupération teacher cours', [
            'course_id' => $moodleCourseId,
            'error'     => $e->getMessage(),
        ]);
        return null;
    }
}
    public function synchronizeCourses(): array
{
    Log::info('SYNCHRO COURS - Méthode atteinte ! Début de la synchro cours');
    try {
        $moodleCourses = $this->getAllCourses();

        Log::info('SYNCHRO COURS - Début', [
            'nb_cours_moodle' => count($moodleCourses),
            'ids_moodle'      => array_column($moodleCourses, 'id'),
        ]);

        if (empty($moodleCourses)) {
            Log::warning('Aucun cours récupéré depuis Moodle');
            return ['created' => 0, 'updated' => 0, 'errors' => 0];
        }

        $created = 0;
        $updated = 0;
        $errors  = 0;

        // On ignore le cours "1" (souvent le site Moodle lui-même)
        foreach (array_slice($moodleCourses, 1) as $moodleCourse) {
            try {
                $categoryId = Category::where('moodle_id', $moodleCourse['categoryid'])->value('id');

                if (!$categoryId) {
                    Log::warning("Catégorie Moodle non trouvée localement - cours ignoré", [
                        'course_fullname' => $moodleCourse['fullname'],
                        'categoryid'      => $moodleCourse['categoryid'],
                    ]);
                    $errors++;
                    continue;
                }

                $teacherIdToStore = null;
                $moodleTeacherId = $this->getCourseTeacherId($moodleCourse['id']);

                if ($moodleTeacherId) {
                    $teacherIdToStore = User::where('moodle_id', $moodleTeacherId)->value('id');
                    if (!$teacherIdToStore) {
                        Log::warning("Teacher Moodle trouvé mais pas local", [
                            'moodle_teacher_id' => $moodleTeacherId,
                            'course'            => $moodleCourse['fullname'],
                        ]);
                    }
                }

                $existingCourse = Course::where('moodle_id', $moodleCourse['id'])->first();

                $courseData = [
                    'fullname'    => $moodleCourse['fullname'],
                    'shortname'   => $moodleCourse['shortname'],
                    'summary'     => $moodleCourse['summary'] ?? '',
                    'numsections' => $moodleCourse['numsections'] ?? 0,
                    'startdate'   => !empty($moodleCourse['startdate']) && $moodleCourse['startdate'] > 0 
                                    ? date('Y-m-d', $moodleCourse['startdate']) 
                                    : null,
                    'enddate'     => !empty($moodleCourse['enddate']) && $moodleCourse['enddate'] > 0 
                                    ? date('Y-m-d', $moodleCourse['enddate']) 
                                    : null,
                    'teacher_id'  => $teacherIdToStore,
                    'category_id' => $categoryId,
                    'image'       => $moodleCourse['courseimage'] ?? null,
                    'visible'     => $moodleCourse['visible'] ?? 1,
                ];

                if ($existingCourse) {
                    $existingCourse->update($courseData);
                    $updated++;
                    Log::info('Cours mis à jour', [
                        'moodle_id' => $moodleCourse['id'],
                        'fullname'  => $moodleCourse['fullname'],
                    ]);
                } else {
                    Course::create(array_merge($courseData, ['moodle_id' => $moodleCourse['id']]));
                    $created++;
                    Log::info('Nouveau cours créé', [
                        'moodle_id' => $moodleCourse['id'],
                        'fullname'  => $moodleCourse['fullname'],
                    ]);
                }

                // 🔥 NOUVEAU : Attribuer automatiquement le rôle TEACHER
                if ($teacherIdToStore) {
                    $this->assignTeacherRole($teacherIdToStore);
                }

            } catch (\Exception $e) {
                Log::error('Erreur synchro cours individuel', [
                    'moodle_id' => $moodleCourse['id'] ?? 'inconnu',
                    'error'     => $e->getMessage(),
                ]);
                $errors++;
            }
        }

        Log::info('SYNCHRO COURS - Fin', [
            'created'      => $created,
            'updated'      => $updated,
            'errors'       => $errors,
            'total_locaux' => Course::count(),
        ]);

        return compact('created', 'updated', 'errors');

    } catch (\Exception $e) {
        Log::error('Erreur globale synchro cours', ['message' => $e->getMessage()]);
        return ['created' => 0, 'updated' => 0, 'errors' => 1];
    }
}

/**
 * 🔥 Attribuer automatiquement le rôle TEACHER à un utilisateur
 */
private function assignTeacherRole(?int $userId): void
{
    if (!$userId) {
        return;
    }

    try {
        $user = User::find($userId);
        
        if (!$user) {
            Log::warning('Utilisateur introuvable pour attribution rôle TEACHER', [
                'user_id' => $userId
            ]);
            return;
        }

        // Vérifier si l'utilisateur a déjà le rôle TEACHER
        if (!$user->hasRole('ROLE_TEACHER')) {
            $user->assignRole('ROLE_TEACHER');
            
            Log::info('✅ Rôle TEACHER attribué automatiquement', [
                'user_id' => $userId,
                'user_name' => $user->name,
                'email' => $user->email
            ]);
        }
        
    } catch (\Exception $e) {
        Log::error('❌ Erreur attribution rôle TEACHER', [
            'user_id' => $userId,
            'error' => $e->getMessage()
        ]);
    }
}
}