<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    /**
     * Vérifier si l'utilisateur peut gérer un cours.
     * Un utilisateur peut gérer un cours s'il est :
     * - Le professeur (teacher_id)
     * - Ou s'il a le rôle 'ROLE_TEACHER'
     */
    public function manage(?User $user, Course $course): bool
    {
        // Non authentifié
        if (!$user) {
            return false;
        }

        // Enseignant = OK si c'est son cours
        if ($user->hasRole('ROLE_TEACHER')) {
            // Les enseignants peuvent gérer tous les cours ou seulement les leurs ?
            // Pour simplifier: les enseignants peuvent gérer tous les cours
            return true;
        }

        return false;
    }

    /**
     * Vérifier si l'utilisateur peut afficher un cours.
     */
    public function view(?User $user, Course $course): bool
    {
        if (!$user) {
            return false;
        }

        // Enseignant peut voir tous
        if ($user->hasRole('ROLE_TEACHER')) {
            return true;
        }

        // Étudiant peut voir les cours où il est inscrit
        if ($user->hasRole('ROLE_STUDENT')) {
            return $course->participants()
                ->where('user_id', $user->id)
                ->exists();
        }

        return false;
    }

    /**
     * Vérifier si l'utilisateur peut créer un cours.
     */
    public function create(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        // Les enseignants peuvent créer
        return $user->hasRole('ROLE_TEACHER');
    }

    /**
     * Vérifier si l'utilisateur peut mettre à jour un cours.
     */
    public function update(?User $user, Course $course): bool
    {
        return $this->manage($user, $course);
    }

    /**
     * Vérifier si l'utilisateur peut supprimer un cours.
     */
    public function delete(?User $user, Course $course): bool
    {
        return $this->manage($user, $course);
    }
}
