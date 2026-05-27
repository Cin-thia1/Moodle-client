<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Event $event): bool
    {
        // A user can update an event if they own it.
        return $user->id === $event->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Event $event): bool
    {
        // A user can delete an event if they own it.
        return $user->id === $event->user_id;
    }
}
