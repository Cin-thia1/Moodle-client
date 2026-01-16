<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MoodleUserCourseService
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
     * Récupérer tous les cours auxquels un utilisateur est inscrit
     */
    public function getCoursDeLUtilisateur(int $userId, bool $inclureNombreUtilisateurs = false): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_enrol_get_users_courses',
                'userid' => $userId
            ]);

            // Option pour inclure le nombre d'utilisateurs par cours
            if ($inclureNombreUtilisateurs) {
                $params['returnusercount'] = 1;
            }

            $response = Http::get($this->apiUrl, $params);
            $data = $response->json();

            // Vérification des erreurs
            if (isset($data['errorcode']) || isset($data['exception'])) {
                Log::error('Erreur API Moodle (getCoursDeLUtilisateur): ' . ($data['message'] ?? 'Erreur inconnue'));
                return [];
            }

            return $data;
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (getCoursDeLUtilisateur): ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Filtrer les cours d'un utilisateur
     */
    public function filtrerCours(int $userId, array $criteres = []): array
    {
        try {
            $tousCours = $this->getCoursDeLUtilisateur($userId);
            
            // Filtrage des cours selon les critères
            $coursFiltres = array_filter($tousCours, function($cours) use ($criteres) {
                foreach ($criteres as $cle => $valeur) {
                    if (!isset($cours[$cle]) || $cours[$cle] != $valeur) {
                        return false;
                    }
                }
                return true;
            });

            return array_values($coursFiltres);
        } catch (\Exception $e) {
            Log::error('Erreur de filtrage des cours (filtrerCours): ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Compter le nombre de cours d'un utilisateur
     */
    public function compterCours(int $userId): int
    {
        try {
            $cours = $this->getCoursDeLUtilisateur($userId);
            return count($cours);
        } catch (\Exception $e) {
            Log::error('Erreur de comptage des cours (compterCours): ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Vérifier si un utilisateur est inscrit à un cours spécifique
     */
    public function estInscritAuCours(int $userId, int $courseId): bool
    {
        try {
            $cours = $this->getCoursDeLUtilisateur($userId);
            
            foreach ($cours as $coursUtilisateur) {
                if ($coursUtilisateur['id'] == $courseId) {
                    return true;
                }
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Erreur de vérification d\'inscription (estInscritAuCours): ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtenir les détails des cours d'un utilisateur avec des informations supplémentaires
     */
    public function obtenirDetailsCours(int $userId): array
    {
        try {
            $cours = $this->getCoursDeLUtilisateur($userId, true);
            
            // Enrichissement des données de cours
            return array_map(function($coursItem) {
                return [
                    'id' => $coursItem['id'],
                    'fullname' => $coursItem['fullname'],
                    'shortname' => $coursItem['shortname'],
                    'usercount' => $coursItem['usercount'] ?? 0,
                    'startdate' => date('Y-m-d', $coursItem['startdate'] ?? time()),
                    'visible' => $coursItem['visible'] ?? true
                ];
            }, $cours);
        } catch (\Exception $e) {
            Log::error('Erreur de récupération des détails de cours (obtenirDetailsCours): ' . $e->getMessage());
            return [];
        }
    }
}