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

        $validated = $this->validateReview($request);

        // Buchung merken: zeigt später „Teilnahme am …“ und verhindert doppeltes Feedback über den Buchungslink
        $booking = $event->bookings()
            ->where('user_id', auth()->id())
            ->whereIn('status', ['confirmed', 'completed'])
            ->whereDoesntHave('review')
            ->oldest()
            ->first();

        $review = EventReview::create([
            ...$validated,
            'user_id' => auth()->id(),
            'event_id' => $event->id,
            'booking_id' => $booking?->id,
            'is_approved' => false, // Review muss moderiert werden
        ]);

        // Benachrichtige den Veranstalter über neue Review
        $event->user?->notify(new NewReviewNotification($review));

        return back()->with('success', 'Vielen Dank für Ihre Bewertung! Sie wird nach Prüfung freigegeben.');
    }

    public function update(Request $request, EventReview $review)
    {
        if ($review->user_id !== auth()->id()) {
            abort(403);
        }

        $review->fill($this->validateReview($request));

        // Eine geänderte Bewertung wird erneut geprüft – sonst ließe sich die Moderation umgehen.
        if ($review->isDirty(['rating', 'comment'])) {
            $review->resetModeration();
            $review->save();
            $review->event->user?->notify(new NewReviewNotification($review));

            return back()->with('success', 'Ihre Bewertung wurde geändert und wird nach Prüfung wieder veröffentlicht.');
        }

        $review->save();

        return back()->with('success', 'Bewertung aktualisiert.');
    }

    public function destroy(EventReview $review)
    {
        if ($review->user_id !== auth()->id()) {
            abort(403);
        }

        $review->delete();

        return back()->with('success', 'Bewertung gelöscht.');
    }

    /**
     * @return array{rating: int, comment: string|null, show_full_name: bool}
     */
    private function validateReview(Request $request): array
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'show_full_name' => 'nullable|boolean',
        ], [
            'rating.required' => 'Bitte wählen Sie eine Bewertung von 1 bis 5 Sternen.',
        ]);

        return [
            'rating' => (int) $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'show_full_name' => $request->boolean('show_full_name'),
        ];
    }
}
