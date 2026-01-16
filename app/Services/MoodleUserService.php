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
                'criteria[0][key]' => 'email',
                'criteria[0][value]' => '%'
            ]);

            if (!empty($criteria)) {
                foreach ($criteria as $key => $value) {
                    $params["criteria[0][key]"] = $key;
                    $params["criteria[0][value]"] = $value;
                }
            }

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
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
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_user_get_users',
                'criteria[0][key]' => 'email',
                'criteria[0][value]' => $email
            ]);

            $response = Http::get($this->apiUrl, $params);
            $data = $response->json();

            if (!empty($data['users'])) {
                return MoodleUser::fromArray($data['users'][0]);
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Moodle API Error (getUserByEmail): ' . $e->getMessage());
            throw $e;
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