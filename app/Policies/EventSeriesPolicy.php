<?php

namespace App\Policies;

use App\Models\EventSeries;
use App\Models\User;

class EventSeriesPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, EventSeries $eventSeries): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        return $eventSeries->organization && $user->isMemberOf($eventSeries->organization);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->activeOrganizations()->exists() || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, EventSeries $eventSeries): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        if (!$eventSeries->organization) {
            return false;
        }
        $role = $user->getRoleInOrganization($eventSeries->organization);
        return in_array($role, ['owner', 'admin']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, EventSeries $eventSeries): bool
    {
        return $this->update($user, $eventSeries);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, EventSeries $eventSeries): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, EventSeries $eventSeries): bool
    {
        return false;
    }
}
