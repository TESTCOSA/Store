<?php

namespace App\Policies;

use App\Models\Quarantine;
use App\Models\User;

class QuarantinePolicy
{
    /**
     * Create a new policy instance.
     */
    /**
     * Determine whether the user can view any quarantine items.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_quarantine');
    }

    /**
     * Determine whether the user can view the quarantine item.
     */
    public function view(User $user, Quarantine $quarantineItem): bool
    {
        return $user->can('view_quarantine');
    }

    /**
     * Determine whether the user can create quarantine items.
     */
    public function create(User $user): bool
    {
        return $user->can('create_quarantine');
    }

    /**
     * Determine whether the user can update the quarantine item.
     */
    public function update(User $user, Quarantine $quarantineItem): bool
    {
        return $user->can('update_quarantine');
    }

    /**
     * Determine whether the user can delete the quarantine item.
     */
    public function delete(User $user, Quarantine $quarantineItem): bool
    {
        return $user->can('delete_quarantine');
    }

    /**
     * Determine whether the user can delete any quarantine items.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_quarantine');
    }

    /**
     * Determine whether the user can force delete the quarantine item.
     */
    public function forceDelete(User $user, Quarantine $quarantineItem): bool
    {
        return $user->can('force_delete_quarantine');
    }

    /**
     * Determine whether the user can force delete any quarantine items.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_quarantine');
    }

    /**
     * Determine whether the user can restore the quarantine item.
     */
    public function restore(User $user, Quarantine $quarantineItem): bool
    {
        return $user->can('restore_quarantine');
    }

    /**
     * Determine whether the user can restore any quarantine items.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_quarantine');
    }

    /**
     * Determine whether the user can replicate the quarantine item.
     */
    public function replicate(User $user, Quarantine $quarantineItem): bool
    {
        return $user->can('replicate_quarantine');
    }

    /**
     * Determine whether the user can reorder quarantine items.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_quarantine');
    }
}
