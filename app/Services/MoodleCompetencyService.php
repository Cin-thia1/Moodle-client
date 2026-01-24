<?php

namespace App\Services;

use App\Models\Competency;
use App\Models\CourseCompetency;
use App\Models\UserCompetency;
use App\Models\Course;
use App\Models\User;
use Exception;

class MoodleCompetencyService
{
    protected MoodleWebService $webService;

    public function __construct(MoodleWebService $webService)
    {
        $this->webService = $webService;
    }

    /**
     * Synchronise les compétences d'un cours depuis Moodle
     */
    public function syncCourseCompetencies(int $courseId): array
    {
        try {
            $course = Course::findOrFail($courseId);
            if (!$course->moodle_id) {
                throw new Exception("Course not linked to Moodle");
            }

            $competencies = $this->webService->getCourseCompetencies($course->moodle_id);

            $synced = 0;
            $errors = 0;

            if (is_array($competencies)) {
                foreach ($competencies as $index => $comp) {
                    try {
                        // Crée ou met à jour la compétence
                        $competency = Competency::updateOrCreate(
                            ['moodle_id' => $comp['id'] ?? null],
                            [
                                'shortname' => $comp['shortname'] ?? 'Competency ' . ($index + 1),
                                'idnumber' => $comp['idnumber'] ?? null,
                                'description' => $comp['description'] ?? '',
                                'description_long' => $comp['descriptionformat'] ?? null,
                                'status' => 1,
                            ]
                        );

                        // Associe la compétence au cours
                        CourseCompetency::updateOrCreate(
                            [
                                'moodle_id' => $comp['courseid_' . $comp['id']] ?? null,
                                'course_id' => $courseId,
                                'competency_id' => $competency->id,
                            ],
                            [
                                'sort_order' => $index,
                            ]
                        );

                        $synced++;
                    } catch (Exception $e) {
                        logger()->error("Competency sync error: " . $e->getMessage());
                        $errors++;
                    }
                }
            }

            return ['synced' => $synced, 'errors' => $errors];
        } catch (Exception $e) {
            logger()->error("Competency service error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Synchronise les compétences d'un utilisateur depuis Moodle
     */
    public function syncUserCompetencies(int $userId): array
    {
        try {
            $user = User::findOrFail($userId);
            if (!$user->moodle_id) {
                throw new Exception("User not linked to Moodle");
            }

            $userComps = $this->webService->getUserCompetencies($user->moodle_id);

            $synced = 0;
            $errors = 0;

            if (is_array($userComps)) {
                foreach ($userComps as $comp) {
                    try {
                        // Récupère ou crée la compétence
                        $competency = Competency::updateOrCreate(
                            ['moodle_id' => $comp['competency']['id'] ?? null],
                            [
                                'shortname' => $comp['competency']['shortname'] ?? 'Competency',
                                'idnumber' => $comp['competency']['idnumber'] ?? null,
                                'description' => $comp['competency']['description'] ?? '',
                                'status' => 1,
                            ]
                        );

                        // Crée ou met à jour la compétence utilisateur
                        $userCompetency = UserCompetency::updateOrCreate(
                            [
                                'moodle_id' => $comp['id'] ?? null,
                                'user_id' => $userId,
                                'competency_id' => $competency->id,
                            ],
                            [
                                'proficiency' => $comp['proficiency'] ?? 0,
                                'grade' => $comp['grade'] ?? null,
                                'reviewed_at' => isset($comp['reviewedat']) ? \Carbon\Carbon::createFromTimestamp($comp['reviewedat']) : null,
                            ]
                        );

                        $synced++;
                    } catch (Exception $e) {
                        logger()->error("UserCompetency sync error: " . $e->getMessage());
                        $errors++;
                    }
                }
            }

            return ['synced' => $synced, 'errors' => $errors];
        } catch (Exception $e) {
            logger()->error("UserCompetency service error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Récupère les compétences d'un cours
     */
    public function getCourseCompetencies(int $courseId)
    {
        return CourseCompetency::where('course_id', $courseId)
            ->with('competency')
            ->orderBy('sort_order', 'asc')
            ->get();
    }

    /**
     * Récupère les compétences d'un utilisateur
     */
    public function getUserCompetencies(int $userId)
    {
        return UserCompetency::where('user_id', $userId)
            ->with('competency')
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    /**
     * Récupère les compétences complétées d'un utilisateur
     */
    public function getUserCompletedCompetencies(int $userId)
    {
        return UserCompetency::where('user_id', $userId)
            ->complete()
            ->with('competency')
            ->get();
    }

    /**
     * Crée une compétence
     */
    public function createCompetency(array $data): Competency
    {
        $data['status'] = $data['status'] ?? 1;
        return Competency::create($data);
    }

    /**
     * Met à jour une compétence utilisateur
     */
    public function updateUserCompetency(int $userId, int $competencyId, array $data): UserCompetency
    {
        $userComp = UserCompetency::where('user_id', $userId)
            ->where('competency_id', $competencyId)
            ->firstOrFail();

        $userComp->update([
            'proficiency' => $data['proficiency'] ?? $userComp->proficiency,
            'grade' => $data['grade'] ?? $userComp->grade,
            'reviewed_at' => $data['reviewed_at'] ?? $userComp->reviewed_at,
        ]);

        return $userComp;
    }

    /**
     * Récupère les statistiques de compétences pour un cours
     */
    public function getCompetencyStatistics(int $competencyId): array
    {
        $completed = UserCompetency::where('competency_id', $competencyId)
            ->complete()
            ->count();

        $total = UserCompetency::where('competency_id', $competencyId)->count();

        return [
            'total' => $total,
            'completed' => $completed,
            'percentage' => $total > 0 ? round(($completed / $total) * 100, 2) : 0,
        ];
    }
}
