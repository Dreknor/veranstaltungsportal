<?php

namespace App\Notifications;

use App\Models\EventReview;
use App\Notifications\Concerns\SendsWebPush;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewReviewNotification extends Notification implements ShouldQueue
{
    use Queueable, SendsWebPush;

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
        return $this->withWebPush(['mail', 'database'], $notifiable);
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $event = $this->review->event;

        return (new MailMessage)
            ->subject('Neue Bewertung wartet auf Freigabe')
            ->greeting("Hallo {$notifiable->fullName()}!")
            ->line("Eine neue Bewertung für Ihre Veranstaltung **{$event->title}** wartet auf Freigabe.")
            ->line("**Bewertung:** {$this->review->rating} von 5 Sternen")
            ->line("**Von:** {$this->review->reviewerName()}")
            ->when($this->review->comment, function ($mail) {
                return $mail->line("**Kommentar:** \"{$this->review->comment}\"");
            })
            ->action('Bewertung moderieren', route('organizer.reviews.moderate', $this->review))
            ->line('Bitte überprüfen Sie die Bewertung und geben Sie sie frei oder lehnen Sie sie ab.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Neue Bewertung',
            'message' => $this->review->reviewerName().' hat "'.$this->review->event->title.'" mit '.$this->review->rating.' Sternen bewertet',
            'review_id' => $this->review->id,
            'event_id' => $this->review->event_id,
            'event_title' => $this->review->event->title,
            'user_name' => $this->review->reviewerName(),
            'rating' => $this->review->rating,
            'comment' => $this->review->comment,
            'url' => route('organizer.reviews.moderate', $this->review),
        ];
    }
}
