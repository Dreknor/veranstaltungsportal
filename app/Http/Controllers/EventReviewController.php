<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventReview;
use App\Notifications\NewReviewNotification;
use Illuminate\Http\Request;

class EventReviewController extends Controller
{
    public function store(Request $request, Event $event)
    {
        // Prüfe ob User schon eine Review geschrieben hat
        if ($event->reviews()->where('user_id', auth()->id())->exists()) {
            return back()->with('error', 'Sie haben bereits eine Bewertung für diese Veranstaltung abgegeben.');
        }

        // Bewertung erst nach Ende der Veranstaltung und mit bestätigter Buchung
        if (! $event->canBeReviewedBy(auth()->user())) {
            return back()->with('error', 'Sie können Veranstaltungen erst bewerten, wenn sie stattgefunden haben und Ihre Buchung bestätigt ist.');
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

        return back()->with('success', 'Vielen Dank für Ihre Bewertung! Sie wird nach Prüfung freigegeben.');
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
