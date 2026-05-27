<?php



namespace App\Observers;

use App\Models\User;

class UserObserver
{
    public function created(User $user): void
    {
        // Géré par UserRepository
    }

    public function updated(User $user): void
    {
        // Géré par UserRepository
    }

    public function deleting(User $user): void
    {
        // Géré par UserRepository
    }
}


