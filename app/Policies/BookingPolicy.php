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
     * Determine whether the user can download booking documents (ticket, invoice, certificate, ical).
     *
     * Erlaubt Zugriff für:
     * (a) Admin
     * (b) Eingeloggten Buchungsinhaber
     * (c) Organisations-Owner/Admin der veranstaltenden Organisation
     * (d) Gäste mit verifiziertem Session-Token (nach E-Mail-Verifikation)
     *
     * ?User: nullable für Gast-Zugriff.
     */
    public function download(?User $user, Booking $booking): bool
    {
        // Admin hat immer Zugriff
        if ($user && $user->hasRole('admin')) {
            return true;
        }

        // Eingeloggter Buchungsinhaber
        if ($user && $booking->user_id && $user->id === $booking->user_id) {
            return true;
        }

        // Organisationsmitglieder können Dokumente einsehen (z.B. Check-In)
        if ($user && $booking->event && $booking->event->organization) {
            $role = $user->getRoleInOrganization($booking->event->organization);
            if (in_array($role, ['owner', 'admin'])) {
                return true;
            }
        }

        // Gast mit verifiziertem Session-Token (nach E-Mail-Verifikation gesetzt)
        if (session()->has('booking_access_' . $booking->id)) {
            return true;
        }

        return false;
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
     * Nur Admins dürfen soft-gelöschte Buchungen wiederherstellen.
     */
    public function restore(User $user, Booking $booking): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     * Niemals erlaubt – DSGVO-Aufbewahrungspflicht (10 Jahre für Rechnungen).
     */
    public function forceDelete(User $user, Booking $booking): bool
    {
        return false;
    }
}
