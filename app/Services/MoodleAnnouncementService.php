<?php

namespace App\Services;

use App\Models\Announcement;
use App\Models\Course;
use Exception;

class MoodleAnnouncementService
{
    protected MoodleWebService $webService;

    public function __construct(MoodleWebService $webService)
    {
        $this->webService = $webService;
    }

    /**
     * Synchronise les annonces d'un cours depuis Moodle
     */
    public function syncCourseAnnouncements(int $courseId): array
    {
        try {
            $course = Course::findOrFail($courseId);
            if (!$course->moodle_id) {
                throw new Exception("Course not linked to Moodle");
            }

            $forums = $this->webService->getForumPosts($course->moodle_id);
            
            if (!is_array($forums)) {
                return ['synced' => 0, 'errors' => 0];
            }

            $synced = 0;
            $errors = 0;

            // Rechercher les annonces (forum de type "news")
            foreach ($forums as $forum) {
                if (isset($forum['type']) && $forum['type'] === 'news') {
                    try {
                        $announcement = Announcement::updateOrCreate(
                            ['moodle_id' => $forum['id']],
                            [
                                'course_id' => $courseId,
                                'subject' => $forum['name'] ?? 'Annonce',
                                'message' => $forum['intro'] ?? '',
                                'status' => 1,
                                'published_at' => now(),
                            ]
                        );
                        $synced++;
                    } catch (Exception $e) {
                        logger()->error("Announcement sync error: " . $e->getMessage());
                        $errors++;
                    }
                }
            }

            return ['synced' => $synced, 'errors' => $errors];
        } catch (Exception $e) {
            logger()->error("Announcement service error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Récupère les annonces d'un cours
     */
    public function getCourseAnnouncements(int $courseId)
    {
        return Announcement::forCourse($courseId)
            ->visible()
            ->orderBy('published_at', 'desc')
            ->get();
    }

    /**
     * Crée une annonce
     */
    public function createAnnouncement(int $courseId, int $userId, string $subject, string $message): Announcement
    {
        return Announcement::create([
            'course_id' => $courseId,
            'user_id' => $userId,
            'subject' => $subject,
            'message' => $message,
            'status' => 1,
            'published_at' => now(),
        ]);
    }

    /**
     * Met à jour une annonce
     */
    public function updateAnnouncement(int $announcementId, array $data): Announcement
    {
        $announcement = Announcement::findOrFail($announcementId);
        $announcement->update($data);
        return $announcement;
    }

    /**
     * Supprime une annonce
     */
    public function deleteAnnouncement(int $announcementId): bool
    {
        return Announcement::destroy($announcementId) > 0;
    }
}
