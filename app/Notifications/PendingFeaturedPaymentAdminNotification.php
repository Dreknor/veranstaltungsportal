<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

/**
 * Admin-Benachrichtigung für ausstehende Featured-Event-Zahlungen (>7 Tage).
 * Wird täglich vom Scheduler ausgelöst wenn offene Zahlungen vorliegen.
 */
class PendingFeaturedPaymentAdminNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param  Collection  $pendingFees  Kollektion von FeaturedEventFee-Modellen
     */
    public function __construct(
        public readonly Collection $pendingFees
    ) {}

    public function via(mixed $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        $count = $this->pendingFees->count();
        $totalAmount = $this->pendingFees->sum('fee_amount');

        $mail = (new MailMessage)
            ->subject("⚠️ {$count} ausstehende Featured-Zahlungen (> 7 Tage)")
            ->greeting('Hallo ' . ($notifiable->first_name ?? 'Admin') . ',')
            ->line("Es gibt **{$count} ausstehende Featured-Event-Zahlungen**, die seit mehr als 7 Tagen offen sind.")
            ->line('**Gesamtbetrag ausstehend:** ' . number_format($totalAmount, 2, ',', '.') . ' €');

        foreach ($this->pendingFees->take(10) as $fee) {
            $daysOverdue = $fee->created_at->diffInDays(now());
            $mail->line("• **{$fee->event->title}** – {$fee->fee_amount} € – seit {$daysOverdue} Tagen offen");
        }

        if ($count > 10) {
            $mail->line('... und ' . ($count - 10) . ' weitere.');
        }

        return $mail
            ->action('Im Admin-Panel anzeigen', url('/admin/featured-events'))
            ->line('Bitte bearbeiten Sie diese offenen Zahlungen zeitnah.');
    }

    public function toDatabase(mixed $notifiable): array
    {
        $count = $this->pendingFees->count();
        $totalAmount = $this->pendingFees->sum('fee_amount');

        return [
            'title' => "⚠️ {$count} ausstehende Featured-Zahlungen",
            'message' => "{$count} Featured-Event-Zahlungen über insgesamt " . number_format($totalAmount, 2, ',', '.') . ' € sind seit mehr als 7 Tagen offen.',
            'action_url' => url('/admin/featured-events'),
            'type' => 'pending_featured_payment_admin',
            'count' => $count,
            'total_amount' => $totalAmount,
        ];
    }
}

