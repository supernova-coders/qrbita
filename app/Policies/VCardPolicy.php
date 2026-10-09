<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VCard;

class VCardPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isActive();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, VCard $vCard): bool
    {
        return $user->isAdministrator() || $vCard->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isActive() && $user->vCard()->doesntExist();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, VCard $vCard): bool
    {
        return $user->isActive()
            && ($user->isAdministrator() || $vCard->user_id === $user->id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, VCard $vCard): bool
    {
        return $user->isAdministrator() || $vCard->user_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, VCard $vCard): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, VCard $vCard): bool
    {
        return false;
    }
}
