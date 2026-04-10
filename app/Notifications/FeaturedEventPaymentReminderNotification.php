<?php

namespace App\Notifications;

use App\Models\FeaturedEventFee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FeaturedEventPaymentReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public FeaturedEventFee $fee
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Zahlungserinnerung: Hervorgehobene Veranstaltung „' . $this->fee->event->title . '"')
            ->greeting('Hallo ' . $notifiable->first_name . ',')
            ->line('Dies ist eine Erinnerung für Ihre ausstehende Hervorhebungsgebühr.')
            ->line('**Veranstaltung:** ' . $this->fee->event->title)
            ->line('**Betrag:** ' . number_format($this->fee->fee_amount, 2, ',', '.') . ' €')
            ->line('**Zeitraum:** Von ' . \Carbon\Carbon::parse($this->fee->featured_start_date)->format('d.m.Y') . ' bis ' . \Carbon\Carbon::parse($this->fee->featured_end_date)->format('d.m.Y'))
            ->line('Bitte begleichen Sie die Gebühr, damit Ihre Veranstaltung als hervorgehobene Veranstaltung angezeigt wird.')
            ->action('Jetzt bezahlen', route('organizer.featured-events.payment', $this->fee))
            ->line('Bei Fragen stehen wir Ihnen gerne zur Verfügung.');
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'Zahlungserinnerung: Hervorgehobene Veranstaltung',
            'message' => 'Ausstehende Zahlung für hervorgehobene Veranstaltung „' . $this->fee->event->title . '" über ' . number_format($this->fee->fee_amount, 2, ',', '.') . ' €',
            'action_url' => route('organizer.featured-events.payment', $this->fee),
            'type' => 'featured_payment_reminder',
            'fee_id' => $this->fee->id,
        ];
    }
}

