<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventReview;
use App\Notifications\NewReviewNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventReviewController extends Controller
{
    public function store(Request $request, Event $event)
    {
        // Prüfe ob User schon eine Review geschrieben hat
        if ($event->reviews()->where('user_id', auth()->id())->exists()) {
            return back()->with('error', 'Du hast bereits eine Bewertung für dieses Event abgegeben.');
        }

        // Optional: Prüfe ob User an dem Event teilgenommen hat
        $hasAttended = $event->bookings()
            ->where('user_id', auth()->id())
            ->whereIn('status', ['confirmed', 'completed'])
            ->exists();

        if (!$hasAttended) {
            return back()->with('error', 'Du kannst nur Events bewerten, an denen du teilgenommen hast.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['event_id'] = $event->id;
        $validated['is_approved'] = false; // Review muss moderiert werden

        $review = EventReview::create($validated);

        // Benachrichtige den Veranstalter über neue Review
        $event->user->notify(new NewReviewNotification($review));

        return back()->with('success', 'Vielen Dank für deine Bewertung! Sie wird nach Prüfung freigegeben.');
    }

    public function update(Request $request, EventReview $review)
    {
        if ($review->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review->update($validated);

        // Aktualisiere durchschnittliche Bewertung des Events
        $this->updateEventAverageRating($review->event);

        return back()->with('success', 'Bewertung aktualisiert!');
    }

    public function destroy(EventReview $review)
    {
        if ($review->user_id !== auth()->id()) {
            abort(403);
        }

        $event = $review->event;
        $review->delete();

        // Aktualisiere durchschnittliche Bewertung des Events
        $this->updateEventAverageRating($event);

        return back()->with('success', 'Bewertung gelöscht!');
    }

    private function updateEventAverageRating(Event $event)
    {
        $averageRating = $event->reviews()->avg('rating');
        $event->update(['average_rating' => $averageRating]);
    }
}

