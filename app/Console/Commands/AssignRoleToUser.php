<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class AssignRoleToUser extends Command
{
    protected $signature = 'user:assign-role {email} {role=ROLE_TEACHER}';
    protected $description = 'Assigner un rôle à un utilisateur';

    public function handle(): int
    {
        $email = $this->argument('email');
        $roleName = $this->argument('role');

        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->error("Utilisateur avec email '{$email}' non trouvé");
            return 1;
        }

        $role = Role::where('name', $roleName)->first();
        if (!$role) {
            $this->error("Rôle '{$roleName}' n'existe pas");
            return 1;
        }

        $user->assignRole($role);
        $this->info("Rôle '{$roleName}' assigné à {$email}");
        return 0;
    }
}
