<?php

namespace App\Console\Commands;

use App\Models\FeaturedEventFee;
use App\Models\User;
use App\Notifications\PendingFeaturedPaymentAdminNotification;
use Illuminate\Console\Command;

/**
 * Benachrichtigt Admins über ausstehende Featured-Event-Zahlungen,
 * die seit mehr als 7 Tagen offen sind.
 *
 * Wird täglich via Scheduler ausgeführt.
 */
class NotifyPendingFeaturedPayments extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'featured:notify-pending-payments
                            {--days=7 : Anzahl der Tage, nach denen eine Zahlung als "überfällig" gilt}
                            {--dry-run : Nur anzeigen, keine Benachrichtigungen senden}';

    /**
     * The console command description.
     */
    protected $description = 'Benachrichtigt Admins über ausstehende Featured-Event-Zahlungen (Standard: > 7 Tage offen)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $dryRun = $this->option('dry-run');

        $cutoff = now()->subDays($days);

        $pendingFees = FeaturedEventFee::query()
            ->where('payment_status', 'pending')
            ->where('created_at', '<=', $cutoff)
            ->with(['event', 'user'])
            ->orderBy('created_at')
            ->get();

        if ($pendingFees->isEmpty()) {
            $this->info("Keine ausstehenden Featured-Zahlungen älter als {$days} Tage.");
            return self::SUCCESS;
        }

        $this->info("Gefunden: {$pendingFees->count()} ausstehende Zahlungen älter als {$days} Tage.");

        if ($dryRun) {
            $this->table(
                ['Event', 'Betrag', 'Erstellt am', 'Tage offen'],
                $pendingFees->map(fn ($fee) => [
                    $fee->event->title ?? '(unbekannt)',
                    number_format($fee->fee_amount, 2, ',', '.') . ' €',
                    $fee->created_at->format('d.m.Y'),
                    $fee->created_at->diffInDays(now()),
                ])
            );
            $this->warn('Dry-run: Keine Benachrichtigungen gesendet.');
            return self::SUCCESS;
        }

        // Admin-User ermitteln
        $admins = User::role('admin')->get();

        if ($admins->isEmpty()) {
            $this->error('Keine Admin-User gefunden. Benachrichtigung konnte nicht gesendet werden.');
            return self::FAILURE;
        }

        $notification = new PendingFeaturedPaymentAdminNotification($pendingFees);

        foreach ($admins as $admin) {
            try {
                $admin->notify($notification);
                $this->info("Benachrichtigung gesendet an: {$admin->email}");
            } catch (\Exception $e) {
                $this->error("Fehler beim Senden an {$admin->email}: {$e->getMessage()}");
            }
        }

        $this->info("Fertig. {$admins->count()} Admin(s) benachrichtigt.");

        return self::SUCCESS;
    }
}

