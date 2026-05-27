<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Seed the roles and permissions for the Moodle client.
     *
     * Roles hierarchy:
     *   ROLE_MANAGER  → All permissions (site-level admin, single user)
     *   ROLE_TEACHER  → Manage courses, grades, participants, content
     *   ROLE_STUDENT  → View content, submit assignments, view own grades
     *   ROLE_USER     → Minimal read-only access (guest-like)
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ─── Permissions ───────────────────────────────────────────

        $permissions = [
            // Site management
            'manage_site'         => 'Gérer les paramètres du site',
            'manage_users'        => 'Gérer les utilisateurs',
            'manage_sync'         => 'Gérer la synchronisation Moodle',

            // Courses
            'manage_courses'      => 'Créer, modifier, supprimer des cours',
            'view_courses'        => 'Voir les cours',

            // Sections
            'manage_sections'     => 'Gérer les sections de cours',

            // Announcements
            'view_announcements'  => 'Voir les annonces',
            'create_announcement' => 'Créer une annonce',
            'edit_announcement'   => 'Modifier une annonce',
            'delete_announcement' => 'Supprimer une annonce',

            // Documents
            'view_documents'      => 'Voir les documents',
            'upload_document'     => 'Téléverser un document',
            'edit_document'       => 'Modifier un document',
            'delete_document'     => 'Supprimer un document',

            // Participants
            'view_participants'   => 'Voir les participants',
            'manage_participants' => 'Gérer les participants',
            'enrol_user'          => 'Inscrire un utilisateur',
            'unenrol_user'        => 'Désinscrire un utilisateur',

            // Grades
            'view_grades'         => 'Voir toutes les notes',
            'view_own_grades'     => 'Voir ses propres notes',
            'create_grade'        => 'Créer une note',
            'edit_grade'          => 'Modifier une note',
            'delete_grade'        => 'Supprimer une note',

            // Assignments
            'create_assignment'   => 'Créer un devoir',
            'edit_assignment'     => 'Modifier un devoir',
            'delete_assignment'   => 'Supprimer un devoir',
            'submit_assignment'   => 'Soumettre un devoir',

            // Competencies
            'view_competencies'       => 'Voir les compétences',
            'manage_competencies'     => 'Gérer les compétences',
            'view_own_competencies'   => 'Voir ses propres compétences',
            'mark_competency_complete' => 'Marquer une compétence complète',
        ];

        foreach ($permissions as $name => $description) {
            Permission::firstOrCreate(['name' => $name]);
        }

        // ─── Roles ─────────────────────────────────────────────────

        // ROLE_MANAGER — All permissions (site-level administrator)
        $managerRole = Role::firstOrCreate(['name' => 'ROLE_MANAGER']);
        $managerRole->syncPermissions(array_keys($permissions));

        // ROLE_TEACHER — Course management, grading, content
        $teacherRole = Role::firstOrCreate(['name' => 'ROLE_TEACHER']);
        $teacherRole->syncPermissions([
            'view_courses',
            'manage_courses',
            'manage_sections',
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
            'create_assignment',
            'edit_assignment',
            'delete_assignment',
            'view_competencies',
            'manage_competencies',
            'mark_competency_complete',
        ]);

        // ROLE_STUDENT — View content, submit work, view own grades
        $studentRole = Role::firstOrCreate(['name' => 'ROLE_STUDENT']);
        $studentRole->syncPermissions([
            'view_courses',
            'view_announcements',
            'view_documents',
            'view_participants',
            'view_own_grades',
            'submit_assignment',
            'view_competencies',
            'view_own_competencies',
        ]);

        // ROLE_USER — Minimal guest-like access
        $guestRole = Role::firstOrCreate(['name' => 'ROLE_USER']);
        $guestRole->syncPermissions([
            'view_courses',
            'view_announcements',
            'view_documents',
            'view_participants',
            'view_competencies',
        ]);
    }
}
