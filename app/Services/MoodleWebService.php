<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class MoodleWebService
{
    protected string $baseUrl;
    protected string $token;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('moodle.api_url'), '/');
        $this->token = config('moodle.api_token');
    }

    /**
     * Effectue un appel à l'API Web Services de Moodle
     */
    public function call(string $function, array $params = []): mixed
    {
        try {
            $response = Http::get("{$this->baseUrl}/webservice/rest/server.php", [
                'wstoken' => $this->token,
                'wsfunction' => $function,
                'moodlewsrestformat' => 'json',
                ...$params,
            ]);

            if ($response->failed()) {
                throw new Exception("Moodle API error: {$response->status()}");
            }

            $data = $response->json();

            if (isset($data['exception'])) {
                throw new Exception("Moodle exception: {$data['message']}");
            }

            return $data;
        } catch (Exception $e) {
            logger()->error("Moodle WS error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Récupère un utilisateur Moodle par ID
     */
    public function getUser(int $moodleId)
    {
        return $this->call('core_user_get_users_by_field', [
            'field' => 'id',
            'values[0]' => $moodleId,
        ]);
    }

    /**
     * Récupère un cours Moodle par ID
     */
    public function getCourse(int $moodleId)
    {
        return $this->call('core_course_get_courses_by_field', [
            'field' => 'id',
            'value' => $moodleId,
        ]);
    }

    /**
     * Récupère les participants d'un cours
     */
    public function getCourseEnrolledUsers(int $courseId)
    {
        return $this->call('core_enrol_get_enrolled_users', [
            'courseid' => $courseId,
        ]);
    }

    /**
     * Récupère les annonces (forums) d'un cours
     */
    public function getForumPosts(int $courseId)
    {
        return $this->call('mod_forum_get_forums_by_courses', [
            'courseids[0]' => $courseId,
        ]);
    }

    /**
     * Récupère les fichiers d'un cours
     */
    public function getCourseFiles(int $courseId)
    {
        return $this->call('core_files_get_files', [
            'contextid' => $courseId,
            'component' => 'course',
            'filearea' => 'summary',
            'itemid' => 0,
        ]);
    }

    /**
     * Récupère les notes d'un cours
     */
    public function getGrades(int $courseId, int $userId = null)
    {
        $params = ['courseid' => $courseId];
        if ($userId) {
            $params['userid'] = $userId;
        }
        return $this->call('gradereport_user_get_grades_table', $params);
    }

    /**
     * Récupère les compétences d'un cours
     */
    public function getCourseCompetencies(int $courseId)
    {
        return $this->call('core_competency_list_competencies_in_course', [
            'courseid' => $courseId,
        ]);
    }

    /**
     * Récupère les compétences d'un utilisateur
     */
    public function getUserCompetencies(int $userId)
    {
        return $this->call('core_competency_user_competencies_by_competencies', [
            'userid' => $userId,
        ]);
    }
}
