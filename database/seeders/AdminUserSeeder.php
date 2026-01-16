<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Créer ou récupérer l'utilisateur administrateur
        $admin = User::firstOrCreate(
            [
                'email' => 'stephaneatabong45@gmail.com',
            ],
            [
                'name' => 'admin User',
                'password' => Hash::make('Atabong1@'), // Mot de passe sécurisé
            ]
        );

        // Assigner les rôles existants à l'utilisateur
        $roles = ['ROLE_ADMIN','ROLE_TEACHER'];

        foreach ($roles as $roleName) {
            $role = Role::where('name', $roleName)->first();

            if ($role) {
                $admin->assignRole($roleName);
            } else {
                $this->command->error("Role '{$roleName}' not found. Did you forget to seed the roles?");
            }
        }
    }
}
