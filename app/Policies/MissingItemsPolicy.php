<?php

namespace App\Policies;

use App\Models\User;
use App\Models\MissingItems;

class MissingItemsPolicy
{
    /**
     * Determine whether the user can view any missing items.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_missing::items');
    }

    /**
     * Determine whether the user can view the missing item.
     */
    public function view(User $user, MissingItems $missingItem): bool
    {
        return $user->can('view_missing::items');
    }

    /**
     * Determine whether the user can create missing items.
     */
    public function create(User $user): bool
    {
        return $user->can('create_missing::items');
    }

    /**
     * Determine whether the user can update the missing item.
     */
    public function update(User $user, MissingItems $missingItem): bool
    {
        return $user->can('update_missing::items');
    }

    /**
     * Determine whether the user can delete the missing item.
     */
    public function delete(User $user, MissingItems $missingItem): bool
    {
        return $user->can('delete_missing::items');
    }

    /**
     * Determine whether the user can delete any missing items.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_missing::items');
    }

    /**
     * Determine whether the user can force delete the missing item.
     */
    public function forceDelete(User $user, MissingItems $missingItem): bool
    {
        return $user->can('force_delete_missing::items');
    }

    /**
     * Determine whether the user can force delete any missing items.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_missing::items');
    }

    /**
     * Determine whether the user can restore the missing item.
     */
    public function restore(User $user, MissingItems $missingItem): bool
    {
        return $user->can('restore_missing::items');
    }

    /**
     * Determine whether the user can restore any missing items.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_missing::items');
    }

    /**
     * Determine whether the user can replicate the missing item.
     */
    public function replicate(User $user, MissingItems $missingItem): bool
    {
        return $user->can('replicate_missing::items');
    }

    /**
     * Determine whether the user can reorder missing items.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_missing::items');
    }
}
