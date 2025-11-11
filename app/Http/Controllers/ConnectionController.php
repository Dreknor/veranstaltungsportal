<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserConnection;
use App\Notifications\ConnectionAcceptedNotification;
use App\Notifications\ConnectionDeclinedNotification;
use App\Notifications\ConnectionRequestNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConnectionController extends Controller
{
    /**
     * Display the user's connections
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $following = $user->following()
            ->withCount(['events', 'bookings'])
            ->paginate(12, ['*'], 'following');

        $followers = $user->followers()
            ->withCount(['events', 'bookings'])
            ->paginate(12, ['*'], 'followers');

        $stats = [
            'following_count' => $user->getFollowingCount(),
            'followers_count' => $user->getFollowersCount(),
            'pending_requests_count' => $user->getPendingRequestsCount(),
        ];

        return view('connections.index', compact('following', 'followers', 'stats'));
    }

    /**
     * Display pending connection requests
     */
    public function requests(Request $request): View
    {
        $user = $request->user();

        $received = $user->pendingFollowerRequests()
            ->with('follower')
            ->latest()
            ->paginate(12, ['*'], 'received');

        $sent = $user->pendingFollowingRequests()
            ->with('following')
            ->latest()
            ->paginate(12, ['*'], 'sent');

        return view('connections.requests', compact('received', 'sent'));
    }

    /**
     * Display suggested connections
     */
    public function suggestions(Request $request): View
    {
        $user = $request->user();

        $suggestions = $user->getSuggestedConnections(20);

        return view('connections.suggestions', compact('suggestions'));
    }

    /**
     * Search for users to connect with
     */
    public function search(Request $request): View
    {
        $query = $request->input('q');
        $user = $request->user();

        $users = User::where('id', '!=', $user->id)
            ->when($query, function ($queryBuilder) use ($query) {
                $queryBuilder->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                        ->orWhere('first_name', 'like', "%{$query}%")
                        ->orWhere('last_name', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%");
                });
            })
            ->withCount(['events', 'bookings', 'followers'])
            ->paginate(12);

        return view('connections.search', compact('users', 'query'));
    }

    /**
     * Send a connection request
     */
    public function send(Request $request, User $user): RedirectResponse
    {
        $currentUser = $request->user();

        // Prevent self-connection
        if ($currentUser->id === $user->id) {
            return back()->with('error', 'Sie können sich nicht mit sich selbst verbinden.');
        }

        // Check if user allows connections
        $allowNetworking = isset($user->allow_networking) ? $user->allow_networking : true;
        if (!$allowNetworking) {
            return back()->with('error', 'Dieser Benutzer akzeptiert keine Verbindungsanfragen.');
        }

        // Check if already connected or pending
        if ($currentUser->isFollowing($user)) {
            return back()->with('error', 'Sie sind bereits mit diesem Benutzer verbunden.');
        }

        if ($currentUser->hasPendingConnectionWith($user)) {
            return back()->with('error', 'Es gibt bereits eine ausstehende Verbindungsanfrage.');
        }

        // Check if user is blocked
        if ($currentUser->hasBlocked($user) || $user->hasBlocked($currentUser)) {
            return back()->with('error', 'Verbindung nicht möglich.');
        }

        // Send connection request
        $connection = $currentUser->sendConnectionRequest($user);

        // Send appropriate notification
        if ($connection->status === 'accepted') {
            // Auto-accepted because of mutual connection
            $user->notify(new ConnectionAcceptedNotification($currentUser));
            return back()->with('success', 'Sie folgen sich jetzt gegenseitig.');
        } else {
            // Pending - needs approval
            $user->notify(new ConnectionRequestNotification($currentUser));
            return back()->with('success', 'Verbindungsanfrage gesendet.');
        }
    }

    /**
     * Accept a connection request
     */
    public function accept(Request $request, User $user): RedirectResponse
    {
        $currentUser = $request->user();

        if ($currentUser->acceptConnectionRequest($user)) {
            // Send notification
            $user->notify(new ConnectionAcceptedNotification($currentUser));

            return back()->with('success', 'Verbindungsanfrage akzeptiert.');
        }

        return back()->with('error', 'Verbindungsanfrage nicht gefunden.');
    }

    /**
     * Decline a connection request
     */
    public function decline(Request $request, User $user): RedirectResponse
    {
        $currentUser = $request->user();

        if ($currentUser->declineConnectionRequest($user)) {
            // Send notification to the user whose request was declined
            $user->notify(new ConnectionDeclinedNotification($currentUser));

            return back()->with('success', 'Verbindungsanfrage abgelehnt.');
        }

        return back()->with('error', 'Verbindungsanfrage nicht gefunden.');
    }

    /**
     * Remove a connection
     */
    public function remove(Request $request, User $user): RedirectResponse
    {
        $currentUser = $request->user();

        if ($currentUser->removeConnection($user)) {
            return back()->with('success', 'Verbindung entfernt.');
        }

        return back()->with('error', 'Verbindung nicht gefunden.');
    }

    /**
     * Block a user
     */
    public function block(Request $request, User $user): RedirectResponse
    {
        $currentUser = $request->user();

        // Prevent self-blocking
        if ($currentUser->id === $user->id) {
            return back()->with('error', 'Sie können sich nicht selbst blockieren.');
        }

        $currentUser->blockUser($user);

        return back()->with('success', 'Benutzer blockiert.');
    }

    /**
     * Unblock a user
     */
    public function unblock(Request $request, User $user): RedirectResponse
    {
        $currentUser = $request->user();

        if ($currentUser->unblockUser($user)) {
            return back()->with('success', 'Benutzer entsperrt.');
        }

        return back()->with('error', 'Benutzer war nicht blockiert.');
    }

    /**
     * Display blocked users
     */
    public function blocked(Request $request): View
    {
        $user = $request->user();

        $blockedUsers = UserConnection::where('follower_id', $user->id)
            ->where('status', 'blocked')
            ->with('following')
            ->latest()
            ->paginate(12);

        return view('connections.blocked', compact('blockedUsers'));
    }

    /**
     * Cancel a sent connection request
     */
    public function cancel(Request $request, User $user): RedirectResponse
    {
        $currentUser = $request->user();

        $deleted = UserConnection::where('follower_id', $currentUser->id)
            ->where('following_id', $user->id)
            ->where('status', 'pending')
            ->delete();

        if ($deleted) {
            return back()->with('success', 'Verbindungsanfrage zurückgezogen.');
        }

        return back()->with('error', 'Verbindungsanfrage nicht gefunden.');
    }
}

