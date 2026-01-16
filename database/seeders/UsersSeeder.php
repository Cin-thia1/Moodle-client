<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UsersSeeder extends Seeder
{
    public function run()
    {
                // Créer un utilisateur enseignant
        $teacher1 = User::firstOrCreate(
            [
                'email' => 'test@gmail.com',
            ],
            [
                'name' => 'Enseignant',
                'password' => Hash::make('testtest'), // Mot de passe sécurisé
            ]
        );
   // Assigner les rôles à l'enseignant
        $teacherRole = 'ROLE_TEACHER';
        $teacher1->assignRole($teacherRole);
        //$teacher2->assignRole($teacherRole);

        $this->command->info('Utilisateur étudiant et enseignant créés avec succès.');
    }
}
