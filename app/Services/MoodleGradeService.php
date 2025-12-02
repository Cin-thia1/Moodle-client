<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MoodleGradeService
{
    protected string $apiUrl;
    protected string $token;
    protected array $defaultParams;

    public function __construct()
    {
        $this->apiUrl = config('moodle.api_url');
        $this->token = config('moodle.api_token');
        $this->defaultParams = [
            'wstoken' => $this->token,
            'moodlewsrestformat' => 'json'
        ];
    }

    /**
     * Récupérer toutes les notes pour un utilisateur dans un cours
     */
    public function getNotesPourUtilisateur(int $userId, int $courseId): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'gradereport_user_get_grades_table',
                'courseid' => $courseId,
                'userid' => $userId
            ]);

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (getNotesPourUtilisateur): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Récupérer les notes de tous les étudiants pour un cours
     */
    public function getNotesDuCours(int $courseId): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'gradereport_overview_get_course_grades',
                'courseid' => $courseId
            ]);

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (getNotesDuCours): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Mettre à jour une note pour un utilisateur
     */
    public function mettreAJourNote(int $gradeid, float $nouveauNote, string $feedback = ''): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_grades_update_grades',
                'gradeid' => $gradeid,
                'grade' => $nouveauNote
            ]);

            // Ajouter un commentaire si fourni
            if (!empty($feedback)) {
                $params['feedback'] = $feedback;
            }

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (mettreAJourNote): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Calculer la moyenne générale d'un utilisateur
     */
    public function calculerMoyenneGenerale(int $userId, int $courseId): float
    {
        try {
            $notes = $this->getNotesPourUtilisateur($userId, $courseId);
            
            // Extraire et calculer la moyenne
            $totalNotes = 0;
            $nombreNotes = 0;

            foreach ($notes['tables'][0]['tabledata'] as $item) {
                if (isset($item['grade']['content'])) {
                    $note = floatval(str_replace(',', '.', $item['grade']['content']));
                    $totalNotes += $note;
                    $nombreNotes++;
                }
            }

            return $nombreNotes > 0 ? $totalNotes / $nombreNotes : 0;
        } catch (\Exception $e) {
            Log::error('Erreur de calcul de moyenne (calculerMoyenneGenerale): ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Exporter les notes d'un cours
     */
    public function exporterNotes(int $courseId, string $format = 'csv'): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'gradereport_export_get_export_action',
                'courseid' => $courseId,
                'format' => $format
            ]);

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (exporterNotes): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Vérifier le bulletin de notes d'un utilisateur
     */
    public function verifierBulletinNotes(int $userId): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'gradereport_overview_get_grade_overview',
                'userid' => $userId
            ]);

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (verifierBulletinNotes): ' . $e->getMessage());
            throw $e;
        }
    }
}