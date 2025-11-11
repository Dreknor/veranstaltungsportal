<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserConnection;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserProfileController extends Controller
{
    /**
     * Display the user's public profile
     */
    public function show(Request $request, User $user): View
    {
        $currentUser = $request->user();

        // Check if profile can be viewed
        $canView = false;

        // Profile owner can always view their own profile
        if ($currentUser && $currentUser->id === $user->id) {
            $canView = true;
        }
        // Public profiles can always be viewed
        elseif (isset($user->show_profile_public) && $user->show_profile_public === true) {
            $canView = true;
        }
        // Legacy: Check old field if new field doesn't exist
        elseif (!isset($user->show_profile_public) && isset($user->show_profile_publicly) && $user->show_profile_publicly === true) {
            $canView = true;
        }
        // Connected users can view non-public profiles if networking is allowed
        elseif ($currentUser && $currentUser->isFollowing($user)) {
            // Check if networking is allowed (default to true if field doesn't exist)
            $allowNetworking = isset($user->allow_networking) ? $user->allow_networking : true;
            if ($allowNetworking) {
                $canView = true;
            }
        }

        // Check if user is blocked (before denying access)
        if ($currentUser && $currentUser->id !== $user->id) {
            if (method_exists($currentUser, 'hasBlocked') && method_exists($user, 'hasBlocked')) {
                if ($currentUser->hasBlocked($user) || $user->hasBlocked($currentUser)) {
                    abort(403, 'Dieses Profil ist nicht verfügbar.');
                }
            }
        }

        if (!$canView) {
            abort(403, 'Dieses Profil ist nicht öffentlich sichtbar.');
        }

        // Load relationships
        $user->load([
            'badges' => function ($query) {
                $query->orderBy('user_badges.earned_at', 'desc')->limit(6);
            }
        ]);

        // Get user statistics
        $stats = [
            'events_attended' => $user->bookings()->where('checked_in', true)->count(),
            'events_organized' => $user->events()->count(),
            'reviews_written' => $user->bookings()->has('review')->count(),
            'followers_count' => $user->getFollowersCount(),
            'following_count' => $user->getFollowingCount(),
            'total_hours' => $this->calculateTotalHours($user),
        ];

        // Get recent events (attended or organized)
        $recentEvents = [];
        if ($user->isOrganizer()) {
            $recentEvents = $user->events()
                ->where('start_date', '<=', now())
                ->latest('start_date')
                ->limit(6)
                ->get();
        } else {
            $recentEvents = $user->bookings()
                ->where('checked_in', true)
                ->with('event')
                ->latest('created_at')
                ->limit(6)
                ->get()
                ->pluck('event');
        }

        // Get connection status if logged in
        $connectionStatus = null;
        $isPendingRequest = false;
        // Use allow_networking, default to true if not set
        $canSendConnectionRequest = isset($user->allow_networking) ? $user->allow_networking : true;

        if ($currentUser && $currentUser->id !== $user->id) {
            if ($currentUser->isFollowing($user)) {
                $connectionStatus = 'following';
            } elseif ($currentUser->hasPendingConnectionWith($user)) {
                $connectionStatus = 'pending';
                // Check who sent the request
                $isPendingRequest = UserConnection::where('follower_id', $currentUser->id)
                    ->where('following_id', $user->id)
                    ->where('status', 'pending')
                    ->exists();
            }
        }

        // Determine what contact info can be shown
        $showEmail = false;
        $showPhone = false;

        if ($currentUser && $currentUser->isFollowing($user)) {
            $showEmail = isset($user->show_email_to_connections) ? $user->show_email_to_connections : false;
            $showPhone = isset($user->show_phone_to_connections) ? $user->show_phone_to_connections : false;
        }

        return view('users.show', compact(
            'user',
            'stats',
            'recentEvents',
            'connectionStatus',
            'isPendingRequest',
            'canSendConnectionRequest',
            'showEmail',
            'showPhone'
        ));
    }

    /**
     * Display the user's followers
     */
    public function followers(Request $request, User $user): View
    {
        $followers = $user->followers()
            ->withCount(['events', 'bookings'])
            ->paginate(12);

        return view('users.followers', compact('user', 'followers'));
    }

    /**
     * Display the user's following
     */
    public function following(Request $request, User $user): View
    {
        $following = $user->following()
            ->withCount(['events', 'bookings'])
            ->paginate(12);

        return view('users.following', compact('user', 'following'));
    }

    /**
     * Calculate total hours attended for a user
     */
    private function calculateTotalHours(User $user): float
    {
        $totalMinutes = $user->bookings()
            ->where('checked_in', true)
            ->join('events', 'bookings.event_id', '=', 'events.id')
            ->sum('events.duration');

        return round($totalMinutes / 60, 1);
    }
}

