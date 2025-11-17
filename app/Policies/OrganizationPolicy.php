<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;

class OrganizationPolicy
{
    /**
     * Determine if the given organization can be viewed by the user.
     */
    public function view(User $user, Organization $organization): bool
    {
        return $user->isMemberOf($organization) || $user->hasRole('admin');
    }

    /**
     * Determine if the user can create organizations.
     */
    public function create(User $user): bool
    {
        return $user->isOrganizer() || $user->hasRole('admin');
    }

    /**
     * Determine if the user can update the organization.
     */
    public function update(User $user, Organization $organization): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return $user->canManageOrganization($organization);
    }

    /**
     * Determine if the user can delete the organization.
     */
    public function delete(User $user, Organization $organization): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return $user->isOwnerOf($organization);
    }

    /**
     * Determine if the user can manage team members.
     */
    public function manageTeam(User $user, Organization $organization): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return $user->canManageOrganization($organization);
    }

    /**
     * Determine if the user can manage events for this organization.
     */
    public function manageEvents(User $user, Organization $organization): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        // Owners and admins can manage events, members can only view
        $role = $user->getRoleInOrganization($organization);
        return in_array($role, ['owner', 'admin']);
    }
}

