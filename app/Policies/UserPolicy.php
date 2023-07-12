<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\User;

class UserPolicy {
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->role === RoleEnum::ADMIN->value)
        {
            return TRUE;
        }

        return NULL;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return FALSE;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $anotherUser): bool
    {
        return FALSE;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return FALSE;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $anotherUser): bool
    {
        return FALSE;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $anotherUser): bool
    {
        return FALSE;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $anotherUser): bool
    {
        return FALSE;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $anotherUser): bool
    {
        return FALSE;
    }
}
