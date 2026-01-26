<?php

namespace App\Services;

use App\Models\Participant;
use App\Models\User;
use App\Models\Course;
use Exception;

class MoodleParticipantService
{
    protected MoodleWebService $webService;

    public function __construct(MoodleWebService $webService)
    {
        $this->webService = $webService;
    }

    /**
     * Synchronise les participants d'un cours depuis Moodle
     */
    public function syncCourseParticipants(int $courseId): array
    {
        try {
            $course = Course::findOrFail($courseId);
            if (!$course->moodle_id) {
                throw new Exception("Course not linked to Moodle");
            }

            $enrolledUsers = $this->webService->getCourseEnrolledUsers($course->moodle_id);

            $synced = 0;
            $errors = 0;

            if (is_array($enrolledUsers)) {
                foreach ($enrolledUsers as $moodleUser) {
                    try {
                        // Récupère ou crée l'utilisateur
                        $user = User::updateOrCreate(
                            ['moodle_id' => $moodleUser['id']],
                            [
                                'name' => $moodleUser['fullname'] ?? $moodleUser['username'] ?? 'User',
                                'email' => $moodleUser['email'] ?? 'noemail@example.com',
                            ]
                        );

                        // Récupère le rôle depuis les enrôlements
                        $role = $this->extractRole($moodleUser);

                        // Crée ou met à jour le participant
                        $participant = Participant::updateOrCreate(
                            [
                                'moodle_enrolment_id' => $moodleUser['enrollmentid'] ?? null,
                                'course_id' => $courseId,
                                'user_id' => $user->id,
                            ],
                            [
                                'role' => $role,
                                'status' => 1,
                                'enrolled_at' => now(),
                            ]
                        );

                        // Synchroniser avec la table pivot course_user
                        $course->users()->syncWithoutDetaching([
                            $user->id => [
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]
                        ]);

                        $synced++;
                    } catch (Exception $e) {
                        logger()->error("Participant sync error: " . $e->getMessage());
                        $errors++;
                    }
                }
            }

            return ['synced' => $synced, 'errors' => $errors];
        } catch (Exception $e) {
            logger()->error("Participant service error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Extrait le rôle depuis les données Moodle
     * Moodle roles: editingteacher (4), teacher (3), student (5), user (0)
     */
    private function extractRole(array $moodleUser): string
    {
        $roles = $moodleUser['roles'] ?? [];

        foreach ($roles as $role) {
            $roleId = $role['roleid'] ?? null;

            // Rôles standard Moodle
            if (in_array($roleId, [3, 4])) { // teacher, editingteacher
                return Participant::ROLE_TEACHER;
            }
            if ($roleId === 5) { // student
                return Participant::ROLE_STUDENT;
            }
        }

        return Participant::ROLE_USER; // Défaut: guest/user
    }

    /**
     * Récupère les participants d'un cours
     */
    public function getCourseParticipants(int $courseId)
    {
        return Participant::forCourse($courseId)
            ->active()
            ->with('user')
            ->orderBy('role', 'asc')
            ->get();
    }

    /**
     * Récupère les participants par rôle
     */
    public function getParticipantsByRole(int $courseId, string $role)
    {
        return Participant::forCourse($courseId)
            ->byRole($role)
            ->active()
            ->with('user')
            ->get();
    }

    /**
     * Enrôle un utilisateur à un cours
     */
    public function enrollUser(int $courseId, int $userId, string $role): Participant
    {
        $participant = Participant::create([
            'course_id' => $courseId,
            'user_id' => $userId,
            'role' => $role,
            'status' => 1,
            'enrolled_at' => now(),
        ]);

        // Ajouter l'utilisateur à la table pivot course_user
        $course = Course::find($courseId);
        if ($course) {
            $course->users()->syncWithoutDetaching([
                $userId => [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);
        }

        return $participant;
    }

    /**
     * Désenrôle un utilisateur d'un cours
     */
    public function unenrollUser(int $courseId, int $userId): bool
    {
        $participant = Participant::where('course_id', $courseId)
            ->where('user_id', $userId)
            ->first();

        if ($participant) {
            $participant->update([
                'status' => 0,
                'unenrolled_at' => now(),
            ]);

            // Retirer l'utilisateur de la table pivot course_user
            $course = Course::find($courseId);
            if ($course) {
                $course->users()->detach($userId);
            }

            return true;
        }

        return false;
    }

    /**
     * Change le rôle d'un participant
     */
    public function changeRole(int $courseId, int $userId, string $role): Participant
    {
        $participant = Participant::where('course_id', $courseId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $participant->update(['role' => $role]);
        return $participant;
    }
}
