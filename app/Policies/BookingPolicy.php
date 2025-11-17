<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
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
    public function view(User $user, Booking $booking): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        // Participant can view their own booking
        if ($booking->user_id && $user->id === $booking->user_id) {
            return true;
        }

        // Organization members can view
        return $booking->event && $booking->event->organization && $user->isMemberOf($booking->event->organization);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Booking $booking): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if (!$booking->event || !$booking->event->organization) {
            return false;
        }

        $role = $user->getRoleInOrganization($booking->event->organization);
        return in_array($role, ['owner', 'admin']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Booking $booking): bool
    {
        return $this->update($user, $booking);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Booking $booking): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Booking $booking): bool
    {
        return false;
    }
}
