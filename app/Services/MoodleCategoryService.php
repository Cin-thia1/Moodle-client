<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MoodleCategoryService
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
     * Récupérer toutes les catégories
     */
    public function getToutesCategories(int $parent = 0): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_course_get_categories',
                'criteria' => [
                    ['key' => 'parent', 'value' => $parent]
                ]
            ]);

            $response = Http::get($this->apiUrl, $params);
            $data = $response->json();

            if (isset($data['errorcode']) || isset($data['exception'])) {
                Log::error('Erreur API Moodle (getToutesCategories): ' . ($data['message'] ?? 'Erreur inconnue'));
                return [];
            }

            return $data;
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (getToutesCategories): ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Créer une nouvelle catégorie
     */
    public function creerCategorie(string $nom, int $parent = 0, string $description = ''): ?int
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_course_create_categories',
                'categories' => [[
                    'name' => $nom,
                    'parent' => $parent,
                    'description' => $description
                ]]
            ]);

            $response = Http::get($this->apiUrl, $params);
            $data = $response->json();

            if (isset($data['errorcode']) || isset($data['exception'])) {
                Log::error('Erreur API Moodle (creerCategorie): ' . ($data['message'] ?? 'Erreur inconnue'));
                return null;
            }

            return $data[0]['id'] ?? null;
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (creerCategorie): ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Mettre à jour une catégorie
     */
    public function modifierCategorie(int $categoryId, array $modifications): bool
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_course_update_categories',
                'categories' => [[
                    'id' => $categoryId,
                    ...$modifications
                ]]
            ]);

            $response = Http::get($this->apiUrl, $params);
            $data = $response->json();

            if (isset($data['errorcode']) || isset($data['exception'])) {
                Log::error('Erreur API Moodle (modifierCategorie): ' . ($data['message'] ?? 'Erreur inconnue'));
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (modifierCategorie): ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Supprimer une catégorie
     */
    public function supprimerCategorie(int $categoryId): bool
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_course_delete_categories',
                'categoryids' => [$categoryId]
            ]);

            $response = Http::get($this->apiUrl, $params);
            $data = $response->json();

            if (isset($data['errorcode']) || isset($data['exception'])) {
                Log::error('Erreur API Moodle (supprimerCategorie): ' . ($data['message'] ?? 'Erreur inconnue'));
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (supprimerCategorie): ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtenir les détails d'une catégorie spécifique
     */
    public function obtenirDetailsCategorie(int $categoryId): ?array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_course_get_categories',
                'criteria' => [
                    ['key' => 'id', 'value' => $categoryId]
                ]
            ]);

            $response = Http::get($this->apiUrl, $params);
            $data = $response->json();

            if (isset($data['errorcode']) || isset($data['exception'])) {
                Log::error('Erreur API Moodle (obtenirDetailsCategorie): ' . ($data['message'] ?? 'Erreur inconnue'));
                return null;
            }

            return $data[0] ?? null;
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (obtenirDetailsCategorie): ' . $e->getMessage());
            return null;
        }
    }
}