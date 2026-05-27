<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Create the Manager user from .env credentials.
     * This is the single site-level administrator.
     */
    public function run(): void
    {
        $email = env('MANAGER_EMAIL', 'admin@moodle-client.local');
        $password = env('MANAGER_PASSWORD', 'changeme');

        $manager = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Manager',
                'password' => Hash::make($password),
                'profile_picture' => 'images/default-profile-picture.png',
            ]
        );

        // Ensure ROLE_MANAGER exists before assigning
        Role::firstOrCreate(['name' => 'ROLE_MANAGER']);

        $manager->syncRoles(['ROLE_MANAGER']);

        $this->command->info("Manager user created/updated: {$email}");
    }
}
