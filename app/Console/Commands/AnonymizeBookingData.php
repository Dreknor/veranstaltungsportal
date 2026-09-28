<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\BookingEmailLog;
use App\Models\BookingItem;
use App\Models\EventWaitlist;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * DSGVO: Personenbezogene Buchungsdaten nach Ablauf der Aufbewahrungsfristen anonymisieren.
 *
 * Stufe 1 (config privacy.booking_retention_months nach Veranstaltungsende):
 *   Kontaktdaten, Teilnehmerdaten, Zusatzangaben und E-Mail-Verlauf werden anonymisiert.
 *   Kostenfreie Buchungen werden vollständig anonymisiert.
 * Stufe 2 (config privacy.invoice_retention_years nach Buchung/Rechnung):
 *   Name und Rechnungsanschrift kostenpflichtiger Buchungen werden anonymisiert.
 *
 * Buchungsnummer, Beträge, Rechnungsnummern und Status bleiben für Statistik und Buchhaltung erhalten.
 */
class AnonymizeBookingData extends Command
{
    protected $signature = 'privacy:anonymize-bookings {--dry-run : Nur zählen, nichts ändern}';

    protected $description = 'Anonymisiert personenbezogene Buchungsdaten nach Ablauf der Aufbewahrungsfristen';

    public const PLACEHOLDER_NAME = 'Anonymisiert';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $contactCutoff = now()->subMonths(max(1, config('privacy.booking_retention_months', 24)));
        $invoiceCutoff = now()->subYears(max(1, config('privacy.invoice_retention_years', 10)));

        // Stufe 1: Kontakt- und Teilnehmerdaten
        $stage1 = Booking::withTrashed()
            ->whereNull('anonymized_at')
            ->whereHas('event', fn ($q) => $q->withTrashed()->where('end_date', '<', $contactCutoff))
            ->get();

        // Stufe 2: Rechnungsdaten nach Ablauf der steuerlichen Aufbewahrung
        $stage2 = Booking::withTrashed()
            ->whereNotNull('anonymized_at')
            ->where('customer_name', '!=', self::PLACEHOLDER_NAME)
            ->where(fn ($q) => $q->where('invoice_date', '<', $invoiceCutoff)
                ->orWhere(fn ($q) => $q->whereNull('invoice_date')->where('created_at', '<', $invoiceCutoff)))
            ->get();

        $waitlist = EventWaitlist::whereHas('event', fn ($q) => $q->withTrashed()
            ->where('end_date', '<', now()->subMonths(max(1, config('privacy.waitlist_retention_months', 3)))));

        $this->info("Stufe 1 (Kontaktdaten): {$stage1->count()} Buchung(en)");
        $this->info("Stufe 2 (Rechnungsdaten): {$stage2->count()} Buchung(en)");
        $this->info("Wartelisten-Einträge: {$waitlist->count()}");

        if ($dryRun) {
            return self::SUCCESS;
        }

        foreach ($stage1 as $booking) {
            DB::transaction(fn () => $this->anonymizeContactData($booking));
        }

        foreach ($stage2 as $booking) {
            $booking->forceFill($this->billingFields())->saveQuietly();
        }

        $waitlist->delete();

        return self::SUCCESS;
    }

    protected function anonymizeContactData(Booking $booking): void
    {
        // Kostenfreie oder nie bezahlte Buchungen haben keine steuerliche Relevanz → vollständig
        $billingRelevant = (float) $booking->total > 0
            && ($booking->invoice_number || in_array($booking->payment_status, ['paid', 'extern', 'refunded']));

        $data = [
            'customer_email' => "anonymisiert+{$booking->id}@invalid.local",
            'customer_phone' => null,
            'email_verification_token' => null,
            'additional_data' => null,
            'check_in_notes' => null,
            'user_id' => null,
            'anonymized_at' => now(),
        ];

        if (!$billingRelevant) {
            $data += $this->billingFields();
        }

        $booking->forceFill($data)->saveQuietly();

        BookingItem::where('booking_id', $booking->id)->update([
            'attendee_name' => null,
            'attendee_email' => null,
            'attendee_organization' => null,
            'ticket_sent_to' => null,
            'custom_fields' => null,
        ]);

        BookingEmailLog::where('booking_id', $booking->id)->update(['recipient' => 'anonymisiert']);
    }

    /**
     * @return array<string, mixed>
     */
    protected function billingFields(): array
    {
        return [
            'customer_name' => self::PLACEHOLDER_NAME,
            'customer_organization' => null,
            'billing_company' => null,
            'billing_vat_id' => null,
            'billing_address' => null,
            'billing_postal_code' => null,
            'billing_city' => null,
            'billing_country' => null,
        ];
    }
}
