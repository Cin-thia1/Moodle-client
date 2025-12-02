<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MoodleSessionService
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
     * Lister toutes les sessions
     */
    public function listerSessions(): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_session_get_sessions'
            ]);

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (listerSessions): ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Créer une nouvelle session
     */
    public function creerSession(string $name, Carbon $dateDebut, Carbon $dateFin, array $parametresSupplementaires = []): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_session_create_session',
                'session[name]' => $name,
                'session[startdate]' => $dateDebut->timestamp,
                'session[enddate]' => $dateFin->timestamp
            ]);

            // Ajouter des paramètres supplémentaires
            foreach ($parametresSupplementaires as $key => $value) {
                $params["session[$key]"] = $value;
            }

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (creerSession): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Mettre à jour une session
     */
    public function modifierSession(int $sessionId, array $donnees): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_session_update_session',
                'sessionid' => $sessionId
            ]);

            // Ajouter les données de modification
            foreach ($donnees as $key => $value) {
                $params["session[$key]"] = $value;
            }

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (modifierSession): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Supprimer une session
     */
    public function supprimerSession(int $sessionId): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_session_delete_session',
                'sessionid' => $sessionId
            ]);

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (supprimerSession): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Obtenir les détails d'une session
     */
    public function obtenirDetailsSession(int $sessionId): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_session_get_session_details',
                'sessionid' => $sessionId
            ]);

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (obtenirDetailsSession): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Ajouter des participants à une session
     */
    public function ajouterParticipants(int $sessionId, array $utilisateurs): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_session_add_participants',
                'sessionid' => $sessionId
            ]);

            // Ajouter les IDs des utilisateurs
            foreach ($utilisateurs as $index => $userId) {
                $params["userids[$index]"] = $userId;
            }

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (ajouterParticipants): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Supprimer des participants d'une session
     */
    public function supprimerParticipants(int $sessionId, array $utilisateurs): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_session_remove_participants',
                'sessionid' => $sessionId
            ]);

            // Ajouter les IDs des utilisateurs à supprimer
            foreach ($utilisateurs as $index => $userId) {
                $params["userids[$index]"] = $userId;
            }

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (supprimerParticipants): ' . $e->getMessage());
            throw $e;
        }
    }
}