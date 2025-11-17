<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EventPolicy
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
    public function view(User $user, Event $event): bool
    {
        // Admin can view all events
        if ($user->hasRole('admin')) {
            return true;
        }

        // Check if event belongs to user's organization
        if ($event->organization_id) {
            return $user->isMemberOf($event->organization);
        }

        // Fallback to user-based check (for legacy events)
        return $user->id === $event->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // User must have an active organization or be organizer
        return $user->isOrganizer() || $user->activeOrganizations()->count() > 0;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Event $event): bool
    {
        // Admin can update all events
        if ($user->hasRole('admin')) {
            return true;
        }

        // Check if event belongs to user's organization
        if ($event->organization_id) {
            $organization = $event->organization;
            $role = $user->getRoleInOrganization($organization);
            return in_array($role, ['owner', 'admin']);
        }

        // Fallback to user-based check (for legacy events)
        return $user->id === $event->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Event $event): bool
    {
        // Admin can delete all events
        if ($user->hasRole('admin')) {
            return true;
        }

        // Check if event belongs to user's organization
        if ($event->organization_id) {
            $organization = $event->organization;
            $role = $user->getRoleInOrganization($organization);
            return in_array($role, ['owner', 'admin']);
        }

        // Fallback to user-based check (for legacy events)
        return $user->id === $event->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Event $event): bool
    {
        return $this->delete($user, $event);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Event $event): bool
    {
        return $this->delete($user, $event);
    }

    /**
     * Determine if user can manage bookings for this event
     */
    public function manageBookings(User $user, Event $event): bool
    {
        return $this->update($user, $event);
    }

    /**
     * Determine if user can check-in attendees for this event
     */
    public function checkInAttendees(User $user, Event $event): bool
    {
        // Admin can check-in for all events
        if ($user->hasRole('admin')) {
            return true;
        }

        // Check if event belongs to user's organization (any role can check-in)
        if ($event->organization_id) {
            return $user->isMemberOf($event->organization);
        }

        // Fallback to user-based check
        return $user->id === $event->user_id;
    }
}

