<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{

    /**
     * Display user's favorite events
     */
    public function index()
    {
        $user = Auth::user();

        // Get favorite event IDs from user's favorites JSON field or separate table
        $favoriteEvents = $user->favoriteEvents()
            ->with(['category', 'user'])
            ->where('is_published', true)
            ->orderBy('start_date', 'asc')
            ->paginate(12);

        return view('user.favorites', compact('favoriteEvents'));
    }

    /**
     * Add event to favorites
     */
    public function store(Event $event)
    {
        $user = Auth::user();

        // Check if already favorited
        if ($user->favoriteEvents()->where('event_id', $event->id)->exists()) {
            return response()->json([
                'message' => 'Event ist bereits in Ihren Favoriten',
                'favorited' => true,
            ], 200);
        }

        // Add to favorites
        $user->favoriteEvents()->attach($event->id);

        return response()->json([
            'message' => 'Event zu Favoriten hinzugefügt',
            'favorited' => true,
        ], 200);
    }

    /**
     * Remove event from favorites
     */
    public function destroy(Event $event)
    {
        $user = Auth::user();

        $user->favoriteEvents()->detach($event->id);

        return response()->json([
            'message' => 'Event aus Favoriten entfernt',
            'favorited' => false,
        ], 200);
    }

    /**
     * Toggle favorite status
     */
    public function toggle(Event $event)
    {
        $user = Auth::user();

        $isFavorited = $user->favoriteEvents()->where('event_id', $event->id)->exists();

        if ($isFavorited) {
            $user->favoriteEvents()->detach($event->id);
            $message = 'Event aus Favoriten entfernt';
            $favorited = false;
        } else {
            $user->favoriteEvents()->attach($event->id);
            $message = 'Event zu Favoriten hinzugefügt';
            $favorited = true;
        }

        return response()->json([
            'message' => $message,
            'favorited' => $favorited,
        ], 200);
    }
}

