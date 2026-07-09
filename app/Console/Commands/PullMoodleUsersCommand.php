<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MoodleApiService;
use App\Models\User;
use Illuminate\Support\Str;

class PullMoodleUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'moodle:pull-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pulls all users from Moodle and creates local accounts.';

    protected MoodleApiService $api;

    public function __construct(MoodleApiService $api)
    {
        parent::__construct();
        $this->api = $api;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Moodle user synchronization...');

        try {
            // Get all users from Moodle
            // An empty criteria array gets all users
            $moodleUsers = $this->api->getUsers([]);
            
            if (empty($moodleUsers) || !isset($moodleUsers['users'])) {
                $this->error('Failed to retrieve users from Moodle or no users found.');
                return Command::FAILURE;
            }

            $users = $moodleUsers['users'];
            $count = 0;

            foreach ($users as $moodleUser) {
                // Skip the Moodle admin or system accounts if needed (usually id 1 or 2)
                if ($moodleUser['id'] <= 2 && $moodleUser['username'] === 'admin') {
                     continue;
                }

                // Check if user already exists
                $localUser = User::where('moodle_id', $moodleUser['id'])
                                 ->orWhere('email', $moodleUser['email'])
                                 ->first();

                if (!$localUser) {
                    $this->info("Creating new user: {$moodleUser['email']}");
                    
                    $localUser = User::create([
                        'name' => $moodleUser['fullname'] ?? ($moodleUser['firstname'] . ' ' . $moodleUser['lastname']),
                        'username' => $moodleUser['username'] ?? null,
                        'email' => $moodleUser['email'],
                        'password' => Str::random(16), // Haché automatiquement par le modèle User (cast 'hashed')
                        'moodle_id' => $moodleUser['id'],
                        'must_change_password' => true,
                        'profile_picture' => 'images/default-profile-picture.png',
                    ]);
                    
                    // Default role assignment. 
                    // To be more accurate, we would need to check their roles in Moodle courses,
                    // but for a site-wide pull without context, we default to ROLE_STUDENT.
                    $localUser->assignRole('ROLE_STUDENT');
                    
                    $count++;
                } else {
                    // Update existing user
                    $updates = [];
                    if (!$localUser->moodle_id) {
                        $updates['moodle_id'] = $moodleUser['id'];
                    }
                    if (empty($localUser->username) && !empty($moodleUser['username'])) {
                        $updates['username'] = $moodleUser['username'];
                    }

                    // Sync name and email
                    $newName = ($moodleUser['firstname'] ?? '') . ' ' . ($moodleUser['lastname'] ?? '');
                    $newName = trim($newName);
                    if ($newName && $newName !== $localUser->name) {
                        $updates['name'] = $newName;
                    }
                    if (!empty($moodleUser['email']) && $moodleUser['email'] !== $localUser->email) {
                        $updates['email'] = $moodleUser['email'];
                    }

                    // Sync profile picture
                    if (!empty($moodleUser['profileimageurl'])) {
                        try {
                            $filename = 'moodle_pic_' . $moodleUser['id'] . '.jpg';
                            $destPath = \Illuminate\Support\Facades\Storage::disk('public')->path('profile_pictures/' . $filename);
                            if ($this->api->downloadFile($moodleUser['profileimageurl'], $destPath)) {
                                $updates['profile_picture'] = 'profile_pictures/' . $filename;
                            }
                        } catch (\Exception $e) {
                            $this->line("Could not download picture for {$moodleUser['email']}");
                        }
                    }

                    if (!empty($updates)) {
                        $localUser->update($updates);
                        $this->line("Updated existing user {$localUser->email} with Moodle data");
                    }
                }
            }

            $this->info("Synchronization complete. Created {$count} new users.");
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Error during synchronization: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
