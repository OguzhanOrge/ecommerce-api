<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        activity()->log('User created: ' . $user->name);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        activity()->log('User updated: ' . $user->name);
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        activity()->log('User deleted: ' . $user->name);
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        activity()->log('User restored: ' . $user->name);
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        activity()->log('User force deleted: ' . $user->name);
    }
}
