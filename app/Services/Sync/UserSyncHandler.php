<?php

namespace App\Services\Sync;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Exception;

class UserSyncHandler extends BaseSyncHandler
{
    public function pull(): array
    {
        $summary = ['created' => 0, 'updated' => 0, 'errors' => 0];
        try {
            $moodleUsers = $this->api->getUsers([]); // get all users
            if (isset($moodleUsers['users'])) {
                foreach ($moodleUsers['users'] as $moodleUser) {
                    // Ignorer les comptes système
                    if ($moodleUser['id'] <= 2 && $moodleUser['username'] === 'admin') {
                        continue;
                    }

                    $user = User::where('moodle_id', $moodleUser['id'])
                                ->orWhere('email', $moodleUser['email'])
                                ->first();

                    if ($user) {
                        $user->update([
                            'moodle_id' => $moodleUser['id'], // Au cas où on a matché par email
                            'name' => $moodleUser['fullname'],
                            'username' => $moodleUser['username'] ?? null,
                            'email' => $moodleUser['email'],
                            'sync_status' => 'synced',
                            'synced_at' => now(),
                            'dirty' => 0,
                        ]);
                        $summary['updated']++;
                    } else {
                        // Créer le nouvel utilisateur
                        $newUser = User::create([
                            'name' => $moodleUser['fullname'] ?? ($moodleUser['firstname'] . ' ' . $moodleUser['lastname']),
                            'username' => $moodleUser['username'] ?? null,
                            'email' => $moodleUser['email'],
                            'password' => Str::random(16), // Haché automatiquement par le modèle User
                            'moodle_id' => $moodleUser['id'],
                            'must_change_password' => true,
                            'profile_picture' => 'images/default-profile-picture.png',
                            'sync_status' => 'synced',
                            'synced_at' => now(),
                            'dirty' => 0,
                        ]);
                        
                        $newUser->assignRole('ROLE_STUDENT');
                        $summary['created']++;
                    }
                }
            }
        } catch (Exception $e) {
            $this->logError("Erreur pull users", $e);
            $summary['errors']++;
        }
        return $summary;
    }

    protected function getEntity(int $id): ?Model
    {
        return User::find($id);
    }

    protected function pushCreate(Model $entity, array $payload): void
    {
        /** @var User $entity */
        try {
            $parts = explode(' ', $entity->name, 2);
            $firstname = $parts[0];
            $lastname = isset($parts[1]) ? $parts[1] : 'User';

            $userId = $this->api->createUser(
                $entity->email, // Using email as username is typical
                $entity->email,
                $firstname,
                $lastname
            );

            if ($userId) {
                $entity->update([
                    'moodle_id' => $userId,
                    'sync_status' => 'synced',
                    'synced_at' => now(),
                    'dirty' => 0,
                ]);
                $this->logInfo("CREATE user#{$entity->id} → Moodle id:{$userId}");
            }

        } catch (Exception $e) {
            $this->logError("Erreur CREATE user#{$entity->id}", $e);
            throw $e;
        }
    }

    protected function pushUpdate(Model $entity, array $payload): void
    {
        /** @var User $entity */
        try {
            if (!$entity->moodle_id) {
                throw new Exception("Utilisateur sans moodle_id, impossible de mettre à jour");
            }

            $parts = explode(' ', $entity->name, 2);
            $firstname = $parts[0];
            $lastname = isset($parts[1]) ? $parts[1] : 'User';

            $this->api->call('core_user_update_users', [
                'users[0][id]' => $entity->moodle_id,
                'users[0][email]' => $entity->email,
                'users[0][firstname]' => $firstname,
                'users[0][lastname]' => $lastname,
            ]);

            $entity->update([
                'sync_status' => 'synced',
                'synced_at' => now(),
                'dirty' => 0,
            ]);

            $this->logInfo("UPDATE user#{$entity->id} → Moodle id:{$entity->moodle_id}");

        } catch (Exception $e) {
            $this->logError("Erreur UPDATE user#{$entity->id}", $e);
            throw $e;
        }
    }
}
