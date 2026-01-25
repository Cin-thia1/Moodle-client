<?php

namespace App\Services;
use App\Models\MoodleUser;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MoodleUserService
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
     * Get all users or filter by criteria
     */
    public function getUsers(array $criteria = [])
{
    try {
        $params = array_merge($this->defaultParams, [
            'wsfunction' => 'core_user_get_users',
        ]);

        // PAS de criteria pour tester "tous les users visibles"
        // Si tu veux wildcard email, décommente :
        // $params['criteria[0][key]']   = 'email';
        // $params['criteria[0][value]'] = '%';

        $response = Http::get($this->apiUrl, $params);
        $data = $response->json();

        Log::info('DEBUG getUsers FULL', [
            'url'       => $this->apiUrl . '?' . http_build_query($params),
            'status'    => $response->status(),
            'raw_body'  => $response->body(),
            'decoded'   => $data,
        ]);

        // Protection anti-crash
        $users = $data['users'] ?? [];
        if (!is_array($users)) {
            $users = [];
        }

        return [
            'users' => $users,
            'count' => count($users),
        ];
    } catch (\Exception $e) {
        Log::error('Moodle API Error (getUsers): ' . $e->getMessage());
        throw $e;
    }
}



    /**
     * Get user by ID
     */
    public function getUserById(int $userId): ?MoodleUser
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_user_get_users',
                'criteria[0][key]' => 'id',
                'criteria[0][value]' => $userId
            ]);

            $response = Http::get($this->apiUrl, $params);
            $data = $response->json();

            if (!empty($data['users'])) {
                return MoodleUser::fromArray($data['users'][0]);
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Moodle API Error (getUserById): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get user by email
     */
    public function getUserByEmail(string $email): ?MoodleUser
{
    try {
        $email = strtolower(trim($email));

        $params = array_merge($this->defaultParams, [
            'wsfunction' => 'core_user_get_users_by_field',
            'field'      => 'email',
            'values[0]'  => $email,
        ]);

        $response = Http::get($this->apiUrl, $params);

        Log::info('MOODLE getUserByEmail (by_field)', [
    'email'     => $email,
    'status'    => $response->status(),
    'found'     => !empty($data) ? count($data) : 0,
    'body'      => $response->body(),
    'full_url'  => $this->apiUrl . '?' . http_build_query($params),
]);

        $data = $response->json();

        // Réponse = tableau direct (pas 'users')
        if (is_array($data) && !empty($data) && !empty($data[0]['id'])) {
            return MoodleUser::fromArray($data[0]);
        }

        return null;
    } catch (\Exception $e) {
        Log::error('Moodle API Error (getUserByEmail by_field): ' . $e->getMessage());
        return null; // Ne bloque pas l'inscription si Moodle down
    }
}


    /**
     * Create a new user
     */
    public function createUser(array $userData): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_user_create_users',
                'users[0][username]' => $userData['username'],
                'users[0][password]' => $userData['password'],
                'users[0][firstname]' => $userData['firstname'],
                'users[0][lastname]' => $userData['lastname'],
                'users[0][email]' => $userData['email'],
            ]);

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Moodle API Error (createUser): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update an existing user
     */
    public function updateUser(int $userId, array $userData): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_user_update_users',
                'users[0][id]' => $userId
            ]);

            foreach ($userData as $key => $value) {
                $params["users[0][$key]"] = $value;
            }

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Moodle API Error (updateUser): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete a user
     */
    public function deleteUser(int $userId): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_user_delete_users',
                'userids[0]' => $userId
            ]);

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Moodle API Error (deleteUser): ' . $e->getMessage());
            throw $e;
        }
    }
}