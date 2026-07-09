<?php

namespace App\Services;
use App\Models\MoodleUser;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use App\Services\MoodleApiService;

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
    public function isUserTeacherInAnyCourse(int $moodleUserId): bool
{
    try {
        $params = array_merge($this->defaultParams, [
            'wsfunction' => 'core_enrol_get_users_courses',
            'userid'     => $moodleUserId,
        ]);

        $response = Http::get($this->apiUrl, $params);
        $courses = $response->json();

        if (!is_array($courses)) {
            return false;
        }

        foreach ($courses as $course) {
            if (isset($course['roles']) && is_array($course['roles'])) {
                foreach ($course['roles'] as $role) {
                    if (in_array($role['shortname'], ['teacher', 'editingteacher'])) {
                        return true;
                    }
                }
            }
        }

        return false;
    } catch (\Exception $e) {
        Log::error('Erreur vérification teacher Moodle', ['error' => $e->getMessage()]);
        return false;
    }
}

    /**
     * Convertit une image en JPEG si nécessaire (Moodle ne supporte pas WebP).
     * Retourne le chemin du fichier à utiliser (tmp si converti, original sinon).
     */
    private function ensureJpegForMoodle(string $localFilePath): array
    {
        $mime = mime_content_type($localFilePath);
        $supportedFormats = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];

        if (in_array($mime, $supportedFormats)) {
            return ['path' => $localFilePath, 'filename' => basename($localFilePath), 'tmpCreated' => false];
        }

        // Convertir en JPEG via GD
        $image = match ($mime) {
            'image/webp' => imagecreatefromwebp($localFilePath),
            'image/bmp'  => imagecreatefrombmp($localFilePath),
            default      => imagecreatefromstring(file_get_contents($localFilePath)),
        };

        if (!$image) {
            throw new \Exception("Impossible de lire l'image pour conversion: $localFilePath");
        }

        $tmpPath = sys_get_temp_dir() . '/moodle_pic_' . uniqid() . '.jpg';
        imagejpeg($image, $tmpPath, 90);
        imagedestroy($image);

        return ['path' => $tmpPath, 'filename' => 'profile.jpg', 'tmpCreated' => true];
    }

    /**
     * Update the user picture in Moodle via core_user_update_picture
     */
    public function updateUserPicture(int $moodleUserId, string $localFilePath): bool
    {
        $tmpCreated = false;
        try {
            $moodleApi = app(MoodleApiService::class);

            // Convertir en JPEG si nécessaire (Moodle ne supporte pas WebP)
            $imageInfo = $this->ensureJpegForMoodle($localFilePath);
            $uploadPath = $imageInfo['path'];
            $filename   = $imageInfo['filename'];
            $tmpCreated = $imageInfo['tmpCreated'];

            // Upload to draft area
            $draftId = $moodleApi->uploadFileToDraft($uploadPath, $filename);

            // Call core_user_update_picture
            $data = $moodleApi->call('core_user_update_picture', [
                'userid'      => $moodleUserId,
                'draftitemid' => $draftId,
            ]);

            if (isset($data['success']) && $data['success']) {
                return true;
            }

            Log::error('Moodle update picture returned success=false: ' . json_encode($data));
            return false;
        } catch (\Exception $e) {
            Log::error('Moodle API Error (updateUserPicture): ' . $e->getMessage());
            return false;
        } finally {
            // Nettoyer le fichier temporaire si créé
            if ($tmpCreated && isset($uploadPath) && file_exists($uploadPath)) {
                unlink($uploadPath);
            }
        }
    }

    /**
     * Push user changes (Name, Email, Picture) to Moodle
     */
    public function pushUserUpdates(User $user, bool $hasNewPicture): void
    {
        if (!$user->moodle_id) {
            return;
        }

        try {
            // Split name into firstname and lastname
            $parts = explode(' ', $user->name, 2);
            $firstname = $parts[0] ?: 'Unknown';
            $lastname = $parts[1] ?? 'Unknown';

            // Update user info
            $this->updateUser($user->moodle_id, [
                'firstname' => $firstname,
                'lastname' => $lastname,
                'email' => $user->email,
            ]);

            // Update picture if changed
            if ($hasNewPicture && $user->profile_picture) {
                // Determine absolute path
                $localPath = Storage::disk('public')->path($user->profile_picture);
                if (file_exists($localPath)) {
                    $this->updateUserPicture($user->moodle_id, $localPath);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error in pushUserUpdates: ' . $e->getMessage());
        }
    }

    /**
     * Pull user changes (Name, Email, Picture) from Moodle
     */
    public function pullUserUpdates(User $localUser): void
    {
        if (!$localUser->moodle_id) {
            return;
        }

        try {
            // Get latest data from Moodle
            $moodleUser = $this->getUserById($localUser->moodle_id);
            
            if (!$moodleUser) {
                return;
            }

            // MoodleUser object might not have all properties directly accessible, it's a DTO or model.
            // Wait, getUserById returns a MoodleUser object. Let's assume it has firstname, lastname, email, profileimageurl.
            // Let's use raw json if we can't be sure about MoodleUser properties, but getUserById returns MoodleUser.
            // Let's just fetch directly to avoid guessing MoodleUser structure.
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_user_get_users',
                'criteria[0][key]' => 'id',
                'criteria[0][value]' => $localUser->moodle_id
            ]);

            $response = Http::get($this->apiUrl, $params);
            $data = $response->json();

            if (!empty($data['users'][0])) {
                $mData = $data['users'][0];
                
                $newName = ($mData['firstname'] ?? '') . ' ' . ($mData['lastname'] ?? '');
                $newName = trim($newName);
                
                $updates = [];
                if ($newName && $newName !== $localUser->name) {
                    $updates['name'] = $newName;
                }
                if (!empty($mData['email']) && $mData['email'] !== $localUser->email) {
                    $updates['email'] = $mData['email'];
                }

                // Handle profile picture download
                if (!empty($mData['profileimageurl'])) {
                    $moodleImageUrl = $mData['profileimageurl'];
                    
                    // Basic check to see if we already have it or if it's default
                    // It's hard to compare Moodle URL with local path accurately without downloading or saving the URL.
                    // Let's just store the Moodle URL directly or download it.
                    // The getProfilePictureUrlAttribute in User.php handles 'profile_pictures/' or 'images/'.
                    // If we save the URL (http...) it will break `asset($this->profile_picture)`.
                    // So we download it and save it.
                    
                    $moodleApi = app(MoodleApiService::class);
                    $filename = 'moodle_pic_' . $localUser->moodle_id . '.jpg';
                    $destPath = Storage::disk('public')->path('profile_pictures/' . $filename);
                    
                    // We can check if it changed by checking file size or just overwrite it
                    // To be safe, download it every time they log in or sync
                    if ($moodleApi->downloadFile($moodleImageUrl, $destPath)) {
                        $updates['profile_picture'] = 'profile_pictures/' . $filename;
                    }
                }

                if (!empty($updates)) {
                    $localUser->update($updates);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error in pullUserUpdates: ' . $e->getMessage());
        }
    }
}