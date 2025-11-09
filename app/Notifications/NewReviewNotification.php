<?php

namespace App\Notifications;

use App\Models\EventReview;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewReviewNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected EventReview $review;

    public function __construct(EventReview $review)
    {
        $this->review = $review;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $event = $this->review->event;
        $rating = str_repeat('⭐', $this->review->rating);

        return (new MailMessage)
            ->subject('Neue Bewertung wartet auf Freigabe')
            ->greeting("Hallo {$notifiable->name}!")
            ->line("Eine neue Bewertung für deine Veranstaltung **{$event->title}** wartet auf Freigabe.")
            ->line("**Bewertung:** {$rating} ({$this->review->rating}/5)")
            ->line("**Von:** {$this->review->user->name}")
            ->when($this->review->comment, function ($mail) {
                return $mail->line("**Kommentar:** \"{$this->review->comment}\"");
            })
            ->action('Bewertung moderieren', route('organizer.reviews.moderate', $this->review))
            ->line('Bitte überprüfe die Bewertung und gib sie frei oder lehne sie ab.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Neue Bewertung',
            'message' => $this->review->user->name . ' hat "' . $this->review->event->title . '" mit ' . $this->review->rating . ' Sternen bewertet',
            'review_id' => $this->review->id,
            'event_id' => $this->review->event_id,
            'event_title' => $this->review->event->title,
            'user_name' => $this->review->user->name,
            'rating' => $this->review->rating,
            'comment' => $this->review->comment,
            'url' => route('organizer.reviews.moderate', $this->review),
        ];
    }
}

