<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Models\Event;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Information an Buchende über wichtige Änderungen einer Veranstaltung
 * (Termin, Ort, Titel) sowie über neue Online-Zugangsdaten.
 */
class EventUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $event;
    protected $booking;
    protected $changes;
    protected bool $includeAccessData;

    /**
     * @param  array<string, string>  $changes  Bezeichnung => "alt → neu"
     * @param  bool  $includeAccessData  Neue Online-Zugangsdaten mitsenden (nur bei freigeschaltetem Zugang)
     */
    public function __construct(Event $event, Booking $booking, array $changes = [], bool $includeAccessData = false)
    {
        $this->event = $event;
        $this->booking = $booking;
        $this->changes = $changes;
        $this->includeAccessData = $includeAccessData;
    }

    public function via(object $notifiable): array
    {
        // Gäste erhalten die Information ausschließlich per E-Mail
        if (!$notifiable instanceof User) {
            return ['mail'];
        }

        $channels = ['database'];

        // Neue Zugangsdaten sind für die Teilnahme notwendig und werden immer per E-Mail versendet
        $preferences = $notifiable->notification_preferences ?? [];
        if ($this->includeAccessData || ($preferences['email_event_updated'] ?? true)) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $bookingId = $this->booking->id;
        $subject = $this->includeAccessData && empty($this->changes)
            ? 'Neue Zugangsdaten: ' . $this->event->title
            : 'Veranstaltung aktualisiert: ' . $this->event->title;

        $mailMessage = (new MailMessage)
            ->withSymfonyMessage(fn ($message) => $message->getHeaders()->addTextHeader('X-Booking-Id', (string) $bookingId))
            ->subject($subject)
            ->greeting('Hallo ' . $this->booking->customer_name . ',')
            ->line('es gibt Neuigkeiten zu Ihrer gebuchten Veranstaltung **' . $this->event->title . '**.');

        if (!empty($this->changes)) {
            $mailMessage->line('Folgende Angaben haben sich geändert:');
            foreach ($this->changes as $field => $change) {
                $mailMessage->line("- **{$field}:** {$change}");
            }
        }

        if ($this->includeAccessData) {
            $mailMessage->line('**Ihre aktuellen Online-Zugangsdaten:**')
                ->line('Zugangslink: ' . $this->event->online_url);
            if ($this->event->online_access_code) {
                $mailMessage->line('Zugangscode: ' . $this->event->online_access_code);
            }
            $mailMessage->line('Bitte verwenden Sie ab sofort nur noch diese Zugangsdaten.');
        }

        $mailMessage->line('Termin: ' . $this->event->start_date->format('d.m.Y H:i') . ' Uhr');
        if ($this->event->requiresVenue() && $this->event->venue_name) {
            $mailMessage->line('Ort: ' . trim($this->event->venue_name . ', ' . $this->event->venue_city, ', '));
        }

        return $mailMessage
            ->action('Buchung ansehen', $this->booking->manageUrl())
            ->line('Ihre Buchung bleibt gültig. Bei Fragen wenden Sie sich bitte an den Veranstalter'
                . ($this->event->getOrganizerEmail() ? ' (' . $this->event->getOrganizerEmail() . ')' : '') . '.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->includeAccessData && empty($this->changes) ? 'Neue Zugangsdaten' : 'Veranstaltung aktualisiert',
            'message' => 'Die Veranstaltung "' . $this->event->title . '" wurde aktualisiert.',
            'event_id' => $this->event->id,
            'event_title' => $this->event->title,
            'event_slug' => $this->event->slug,
            'booking_id' => $this->booking->id,
            'changes' => $this->changes,
            'url' => route('bookings.show', $this->booking->booking_number),
        ];
    }
}
