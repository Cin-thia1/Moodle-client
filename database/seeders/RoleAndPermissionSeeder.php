<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Créer les 3 rôles
        $roles = [
            'ROLE_TEACHER' => 'Enseignant',
            'ROLE_STUDENT' => 'Étudiant',
            'ROLE_USER' => 'Utilisateur (Invité)',
        ];

        foreach ($roles as $roleName => $description) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // Définir les permissions
        $permissions = [
            // Annonces
            'view_announcements' => 'Voir les annonces',
            'create_announcement' => 'Créer une annonce',
            'edit_announcement' => 'Modifier une annonce',
            'delete_announcement' => 'Supprimer une annonce',

            // Documents
            'view_documents' => 'Voir les documents',
            'upload_document' => 'Télécharger un document',
            'edit_document' => 'Modifier un document',
            'delete_document' => 'Supprimer un document',

            // Participants
            'view_participants' => 'Voir les participants',
            'manage_participants' => 'Gérer les participants',
            'enrol_user' => 'Enrôler un utilisateur',
            'unenrol_user' => 'Désenrôler un utilisateur',

            // Notes
            'view_grades' => 'Voir les notes',
            'view_own_grades' => 'Voir ses propres notes',
            'create_grade' => 'Créer une note',
            'edit_grade' => 'Modifier une note',
            'delete_grade' => 'Supprimer une note',

            // Compétences
            'view_competencies' => 'Voir les compétences',
            'manage_competencies' => 'Gérer les compétences',
            'view_own_competencies' => 'Voir ses propres compétences',
            'mark_competency_complete' => 'Marquer une compétence comme complète',
        ];

        foreach ($permissions as $permissionName => $description) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        // Assigner les permissions aux rôles
        
        // ROLE_TEACHER
        $teacherRole = Role::firstOrCreate(['name' => 'ROLE_TEACHER']);
        $teacherPermissions = [
            'view_announcements',
            'create_announcement',
            'edit_announcement',
            'delete_announcement',
            'view_documents',
            'upload_document',
            'edit_document',
            'delete_document',
            'view_participants',
            'manage_participants',
            'enrol_user',
            'unenrol_user',
            'view_grades',
            'create_grade',
            'edit_grade',
            'delete_grade',
            'view_competencies',
            'manage_competencies',
            'mark_competency_complete',
        ];
        $teacherRole->syncPermissions($teacherPermissions);

        // ROLE_STUDENT
        $studentRole = Role::firstOrCreate(['name' => 'ROLE_STUDENT']);
        $studentPermissions = [
            'view_announcements',
            'view_documents',
            'view_participants',
            'view_grades',
            'view_own_grades',
            'view_competencies',
            'view_own_competencies',
        ];
        $studentRole->syncPermissions($studentPermissions);

        // ROLE_USER (Guest)
        $guestRole = Role::firstOrCreate(['name' => 'ROLE_USER']);
        $guestPermissions = [
            'view_announcements',
            'view_documents',
            'view_participants',
            'view_competencies',
        ];
        $guestRole->syncPermissions($guestPermissions);
    }
}
