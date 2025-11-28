<?php

namespace App\Console\Commands;

use App\Mail\BookingCancellation;
use App\Mail\BookingConfirmation;
use App\Mail\EventReminderMail;
use App\Mail\PaymentConfirmed;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\PlatformFee;
use App\Models\TicketType;
use App\Models\User;
use App\Notifications\BookingConfirmedNotification;
use App\Notifications\EventReminderNotification;
use App\Notifications\NewBookingNotification;
use App\Notifications\PaymentStatusChangedNotification;
use App\Services\InvoiceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SimulateEventLifecycle extends Command
{
    protected $signature = 'events:simulate-lifecycle
                            {--no-emails : Disable actual email sending}
                            {--user= : ID of the user to use as organizer}
                            {--participant= : ID of the user to use as participant}
                            {--days=7 : Number of days until event starts}';

    protected $description = 'Simuliert den kompletten Ablauf einer Veranstaltung mit Buchung, Zahlung, Durchführung und Abrechnung';

    protected InvoiceService $invoiceService;
    protected Event $event;
    protected User $organizer;
    protected User $participant;
    protected Booking $booking;

    public function handle(InvoiceService $invoiceService)
    {
        // PRODUCTION-SCHUTZ: Befehl nicht in Production-Umgebung ausführen
        if (app()->environment('production')) {
            $this->error('❌ FEHLER: Dieser Befehl kann nicht in der Production-Umgebung ausgeführt werden!');
            $this->error('   Dieser Befehl ist nur für Entwicklung und Testing gedacht.');
            $this->newLine();
            $this->warn('💡 Tipp: Verwenden Sie eine lokale oder Staging-Umgebung.');
            return Command::FAILURE;
        }

        $this->invoiceService = $invoiceService;

        $this->info('╔═══════════════════════════════════════════════════════════════╗');
        $this->info('║   Event Lifecycle Simulation - Kompletter Veranstaltungsablauf   ║');
        $this->info('╚═══════════════════════════════════════════════════════════════╝');
        $this->newLine();

        $this->line('🔧 Umgebung: ' . app()->environment());
        $this->newLine();

        if ($this->option('no-emails')) {
            $this->warn('⚠ E-Mail Versand ist DEAKTIVIERT (nur Simulation)');
            $this->newLine();
        }

        try {
            // Schritt 1: Vorbereitung
            $this->step1_preparation();
            $this->newLine();

            // Schritt 2: Event erstellen
            $this->step2_createEvent();
            $this->newLine();

            // Schritt 3: Ticket-Typen hinzufügen
            $this->step3_createTickets();
            $this->newLine();

            // Schritt 4: Buchung erstellen
            $this->step4_createBooking();
            $this->newLine();

            // Schritt 5: Zahlung bestätigen
            $this->step5_confirmPayment();
            $this->newLine();

            // Schritt 6: Event-Erinnerung versenden
            $this->step6_sendReminder();
            $this->newLine();

            // Schritt 7: Event durchführen (Zeit simulieren)
            $this->step7_conductEvent();
            $this->newLine();

            // Schritt 8: Abrechnung erstellen
            $this->step8_generateInvoice();
            $this->newLine();

            // Zusammenfassung
            $this->displaySummary();

            $this->newLine();
            $this->info('✅ Simulation erfolgreich abgeschlossen!');

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Fehler bei der Simulation: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return Command::FAILURE;
        }
    }

    protected function step1_preparation()
    {
        $this->info('📋 Schritt 1: Vorbereitung');
        $this->line('─────────────────────────────────────────────────────────');

        // Organizer laden oder erstellen
        if ($userId = $this->option('user')) {
            $this->organizer = User::find($userId);
            if (!$this->organizer) {
                throw new \Exception("Benutzer mit ID {$userId} nicht gefunden");
            }
            $this->line("✓ Verwende vorhandenen Benutzer: {$this->organizer->name}");
        } else {
            $this->organizer = User::firstOrCreate(
                ['email' => 'organizer@test.local'],
                [
                    'name' => 'Test Veranstalter',
                    'password' => bcrypt('password'),
                    'organization_name' => 'Test Events GmbH',
                    'organization_address' => 'Musterstraße 123',
                    'organization_postal_code' => '12345',
                    'organization_city' => 'Berlin',
                    'organization_country' => 'Deutschland',
                    'tax_id' => 'DE123456789',
                ]
            );
            $this->line("✓ Veranstalter erstellt/geladen: {$this->organizer->name}");
        }

        // Teilnehmer laden oder erstellen
        if ($participantId = $this->option('participant')) {
            $this->participant = User::find($participantId);
            if (!$this->participant) {
                throw new \Exception("Teilnehmer mit ID {$participantId} nicht gefunden");
            }
            $this->line("✓ Verwende vorhandenen Teilnehmer: {$this->participant->name}");
        } else {
            $this->participant = User::firstOrCreate(
                ['email' => 'teilnehmer@test.local'],
                [
                    'name' => 'Max Mustermann',
                    'password' => bcrypt('password'),
                ]
            );
            $this->line("✓ Teilnehmer erstellt/geladen: {$this->participant->name}");
        }
    }

    protected function step2_createEvent()
    {
        $this->info('🎭 Schritt 2: Veranstaltung erstellen');
        $this->line('─────────────────────────────────────────────────────────');

        // Kategorie holen oder erstellen
        $category = EventCategory::firstOrCreate(
            ['name' => 'Workshop'],
            ['slug' => 'workshop', 'description' => 'Interaktive Workshops']
        );

        $daysUntilEvent = (int) $this->option('days');
        $startDate = now()->addDays($daysUntilEvent)->setTime(14, 0);
        $endDate = $startDate->copy()->addHours(3);

        $this->event = Event::create([
            'user_id' => $this->organizer->id,
            'event_category_id' => $category->id,
            'event_type' => 'physical',
            'title' => 'Laravel Workshop - Simulation',
            'slug' => 'laravel-workshop-simulation-' . Str::random(6),
            'description' => 'Ein interaktiver Workshop über Laravel-Entwicklung. Dies ist eine Simulation des kompletten Event-Ablaufs.',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'venue_name' => 'Konferenzzentrum Berlin',
            'venue_address' => 'Alexanderplatz 1',
            'venue_city' => 'Berlin',
            'venue_postal_code' => '10178',
            'venue_country' => 'Deutschland',
            'max_attendees' => 50,
            'is_published' => true,
            'registration_required' => true,
        ]);

        $this->line("✓ Event erstellt: {$this->event->title}");
        $this->line("  ID: {$this->event->id}");
        $this->line("  Start: {$this->event->start_date->format('d.m.Y H:i')}");
        $this->line("  Ende: {$this->event->end_date->format('d.m.Y H:i')}");
        $this->line("  Ort: {$this->event->venue_name}, {$this->event->venue_city}");
    }

    protected function step3_createTickets()
    {
        $this->info('🎫 Schritt 3: Ticket-Typen erstellen');
        $this->line('─────────────────────────────────────────────────────────');

        $tickets = [
            [
                'name' => 'Frühbucher',
                'description' => 'Spezialpreis für frühe Anmeldung',
                'price' => 49.99,
                'quantity' => 20,
            ],
            [
                'name' => 'Normalpreis',
                'description' => 'Regulärer Ticketpreis',
                'price' => 79.99,
                'quantity' => 25,
            ],
            [
                'name' => 'VIP',
                'description' => 'VIP-Ticket mit zusätzlichen Leistungen',
                'price' => 129.99,
                'quantity' => 5,
            ],
        ];

        foreach ($tickets as $ticketData) {
            $ticket = TicketType::create([
                'event_id' => $this->event->id,
                'name' => $ticketData['name'],
                'description' => $ticketData['description'],
                'price' => $ticketData['price'],
                'quantity' => $ticketData['quantity'],
                'quantity_sold' => 0,
                'is_available' => true,
            ]);

            $this->line("✓ Ticket erstellt: {$ticket->name} - €{$ticket->price}");
        }
    }

    protected function step4_createBooking()
    {
        $this->info('📝 Schritt 4: Buchung erstellen');
        $this->line('─────────────────────────────────────────────────────────');

        // Normalpreis-Ticket holen
        $ticketType = TicketType::where('event_id', $this->event->id)
            ->where('name', 'Normalpreis')
            ->first();

        if (!$ticketType) {
            throw new \Exception('Kein Ticket-Typ gefunden');
        }

        $quantity = 2;
        $subtotal = $ticketType->price * $quantity;
        $total = $subtotal;

        $this->booking = Booking::create([
            'booking_number' => $this->generateBookingNumber(),
            'event_id' => $this->event->id,
            'user_id' => $this->participant->id,
            'customer_name' => $this->participant->name,
            'customer_email' => $this->participant->email,
            'customer_phone' => '+49 30 12345678',
            'billing_address' => 'Teststraße 42',
            'billing_postal_code' => '10115',
            'billing_city' => 'Berlin',
            'billing_country' => 'Deutschland',
            'subtotal' => $subtotal,
            'discount' => 0,
            'total' => $total,
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'bank_transfer',
        ]);

        // Booking Items erstellen
        BookingItem::create([
            'booking_id' => $this->booking->id,
            'ticket_type_id' => $ticketType->id,
            'quantity' => $quantity,
            'price' => $ticketType->price,
            'subtotal' => $subtotal,
        ]);

        // Ticket-Verkauf aktualisieren
        $ticketType->increment('quantity_sold', $quantity);

        $this->line("✓ Buchung erstellt: {$this->booking->booking_number}");
        $this->line("  Kunde: {$this->booking->customer_name}");
        $this->line("  Tickets: {$quantity}x {$ticketType->name}");
        $this->line("  Gesamt: €{$this->booking->total}");
        $this->line("  Status: {$this->booking->status}");
        $this->line("  Zahlungsstatus: {$this->booking->payment_status}");

        // Benachrichtigungen und E-Mails
        if (!$this->option('no-emails')) {
            try {
                // Buchungsbestätigung an Teilnehmer
                Mail::to($this->booking->customer_email)
                    ->send(new BookingConfirmation($this->booking));
                $this->line("✓ Buchungsbestätigung versendet an {$this->booking->customer_email}");

                // Benachrichtigung an Veranstalter über neue Buchung
                $this->organizer->notify(new NewBookingNotification($this->booking));
                $this->line("✓ Veranstalter-Benachrichtigung: Neue Buchung");
            } catch (\Exception $e) {
                $this->warn("⚠ Benachrichtigungen fehlgeschlagen: {$e->getMessage()}");
            }
        } else {
            $this->line("📧 E-Mail-Versand übersprungen (--no-emails)");
            $this->line("  Würde versenden: Buchungsbestätigung");
            $this->line("  Würde benachrichtigen: Veranstalter über neue Buchung");
        }
    }

    protected function step5_confirmPayment()
    {
        $this->info('💰 Schritt 5: Zahlung bestätigen');
        $this->line('─────────────────────────────────────────────────────────');

        // Zahlung simulieren
        $this->booking->update([
            'payment_status' => 'paid',
            'payment_transaction_id' => 'SIM-' . Str::upper(Str::random(12)),
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);

        // Booking refreshen, damit alle Werte aktuell sind
        $this->booking->refresh();

        // Platform Fee berechnen und speichern
        $feePercentage = config('monetization.platform_fee_percentage', 10);
        $feeAmount = $this->booking->total * ($feePercentage / 100);

        PlatformFee::create([
            'event_id' => $this->event->id,
            'booking_id' => $this->booking->id,
            'fee_percentage' => $feePercentage,
            'booking_amount' => $this->booking->total,
            'fee_amount' => $feeAmount,
        ]);

        $this->line("✓ Zahlung bestätigt");
        $this->line("  Transaktions-ID: {$this->booking->payment_transaction_id}");
        $this->line("  Platform Fee: €{$feeAmount} ({$feePercentage}%)");

        // E-Mails und Benachrichtigungen versenden
        if (!$this->option('no-emails')) {
            try {
                // Zahlungsbestätigung an Teilnehmer
                Mail::to($this->booking->customer_email)
                    ->send(new PaymentConfirmed($this->booking));
                $this->line("✓ Zahlungsbestätigung versendet an {$this->booking->customer_email}");

                // Notification an Teilnehmer
                $this->participant->notify(new BookingConfirmedNotification($this->booking));
                $this->line("✓ Push-Benachrichtigung an Teilnehmer gesendet");

                // Notification an Veranstalter über Zahlungsänderung
                $this->organizer->notify(new PaymentStatusChangedNotification(
                    $this->booking,
                    'pending',
                    'paid'
                ));
                $this->line("✓ Veranstalter-Benachrichtigung: Zahlung eingegangen");
            } catch (\Exception $e) {
                $this->warn("⚠ E-Mail-Versand fehlgeschlagen: {$e->getMessage()}");
            }
        } else {
            $this->line("📧 E-Mail-Versand übersprungen (--no-emails)");
            $this->line("  Würde versenden: Zahlungsbestätigung an Teilnehmer");
            $this->line("  Würde benachrichtigen: Veranstalter über neue Buchung und Zahlung");
        }
    }

    protected function step6_sendReminder()
    {
        $this->info('⏰ Schritt 6: Event-Erinnerung versenden');
        $this->line('─────────────────────────────────────────────────────────');

        $hoursUntilEvent = now()->diffInHours($this->event->start_date);

        if ($hoursUntilEvent > 24) {
            $this->line("ℹ Event beginnt in {$hoursUntilEvent} Stunden");
            $this->line("  Erinnerungen werden normalerweise 24h vorher versendet");
            $this->line("  Sende Erinnerung trotzdem für Demonstrationszwecke...");
        }

        if (!$this->option('no-emails')) {
            try {
                Mail::to($this->booking->customer_email)
                    ->send(new EventReminderMail($this->event, $this->booking));
                $this->line("✓ Event-Erinnerung versendet an {$this->booking->customer_email}");

                $this->participant->notify(new EventReminderNotification($this->event, $this->booking));
                $this->line("✓ Erinnerungs-Notification gesendet");
            } catch (\Exception $e) {
                $this->warn("⚠ Erinnerung fehlgeschlagen: {$e->getMessage()}");
            }
        } else {
            $this->line("📧 Erinnerungs-Versand übersprungen (--no-emails)");
        }
    }

    protected function step7_conductEvent()
    {
        $this->info('🎉 Schritt 7: Event durchführen');
        $this->line('─────────────────────────────────────────────────────────');

        $this->line("ℹ Event ist geplant für: {$this->event->start_date->format('d.m.Y H:i')}");
        $this->line("ℹ Aktuelles Datum: " . now()->format('d.m.Y H:i'));

        // Event-Datum in die Vergangenheit setzen für Abrechnungszwecke
        $pastStartDate = now()->subDays(1);
        $pastEndDate = now()->subHours(20);

        $this->event->update([
            'start_date' => $pastStartDate,
            'end_date' => $pastEndDate,
        ]);

        // Event neu laden, damit Carbon-Instanzen korrekt sind
        $this->event->refresh();

        $this->line("✓ Event-Datum angepasst (Simulation):");
        $this->line("  Start: {$this->event->start_date->format('d.m.Y H:i')}");
        $this->line("  Ende: {$this->event->end_date->format('d.m.Y H:i')}");
        $this->line("✓ Event wurde erfolgreich durchgeführt!");
    }

    protected function step8_generateInvoice()
    {
        $this->info('📄 Schritt 8: Platform-Fee Abrechnung erstellen');
        $this->line('─────────────────────────────────────────────────────────');

        try {
            // E-Mail-Versand nur aktivieren, wenn --no-emails NICHT gesetzt ist
            $originalAutoInvoice = config('monetization.auto_invoice');

            if ($this->option('no-emails')) {
                // E-Mail-Versand deaktivieren
                config(['monetization.auto_invoice' => false]);
            } else {
                // E-Mail-Versand aktivieren
                config(['monetization.auto_invoice' => true]);
            }

            // Rechnung über InvoiceService erstellen (wie in Production)
            $invoice = $this->invoiceService->generatePlatformFeeInvoice($this->event);

            // Original-Konfiguration wiederherstellen
            config(['monetization.auto_invoice' => $originalAutoInvoice]);

            if ($invoice) {
                $this->line("✓ Rechnung erstellt: {$invoice->invoice_number}");
                $this->line("  Empfänger: {$invoice->recipient_name}");
                $this->line("  E-Mail: {$invoice->recipient_email}");
                $this->line("  Betrag (netto): €{$invoice->amount}");
                $this->line("  MwSt ({$invoice->tax_rate}%): €{$invoice->tax_amount}");
                $this->line("  Gesamt (brutto): €{$invoice->total_amount}");
                $this->line("  Fällig am: {$invoice->due_date->format('d.m.Y')}");
                $this->line("  Status: {$invoice->status}");

                // PDF-Pfad anzeigen
                if ($invoice->pdf_path) {
                    $this->line("  PDF: {$invoice->pdf_path}");
                }

                // Hinweis zum Abrufen
                $this->newLine();
                $this->line("ℹ Der Veranstalter kann die Rechnung abrufen unter:");
                $this->line("  • Dashboard > Rechnungen");
                $this->line("  • Route: " . route('organizer.invoices.show', $invoice));

                if (!$this->option('no-emails')) {
                    $this->line("✓ Rechnungs-E-Mail versendet an {$invoice->recipient_email}");
                } else {
                    $this->line("📧 Rechnungs-E-Mail Versand übersprungen (--no-emails)");
                }
            } else {
                $this->warn("⚠ Keine Rechnung erstellt");
                $this->line("  Mögliche Gründe:");
                $this->line("  • Event noch nicht beendet");
                $this->line("  • Keine Platform Fees vorhanden");
                $this->line("  • Rechnung bereits erstellt");
            }
        } catch (\Exception $e) {
            $this->error("❌ Fehler bei Rechnungserstellung: {$e->getMessage()}");
            $this->error("   Zeile: {$e->getFile()}:{$e->getLine()}");

            if ($this->option('verbose')) {
                $this->error($e->getTraceAsString());
            }
        }
    }

    protected function displaySummary()
    {
        $this->info('📊 ZUSAMMENFASSUNG');
        $this->line('═══════════════════════════════════════════════════════════');

        $this->table(
            ['Bereich', 'Details'],
            [
                ['Veranstalter', $this->organizer->name . ' (' . $this->organizer->email . ')'],
                ['Teilnehmer', $this->participant->name . ' (' . $this->participant->email . ')'],
                ['Event', $this->event->title],
                ['Event-ID', $this->event->id],
                ['Buchungs-Nr.', $this->booking->booking_number],
                ['Tickets', $this->booking->items->sum('quantity') . ' Stück'],
                ['Buchungs-Summe', '€' . $this->booking->total],
                ['Zahlungsstatus', $this->booking->payment_status],
                ['Platform Fee', '€' . PlatformFee::where('booking_id', $this->booking->id)->sum('fee_amount')],
            ]
        );

        $this->newLine();
        $this->line('🔗 Nächste Schritte:');
        $this->line('  • Buchung anzeigen: php artisan tinker → Booking::find(' . $this->booking->id . ')');
        $this->line('  • Event anzeigen: php artisan tinker → Event::find(' . $this->event->id . ')');
        $this->line('  • Rechnungen anzeigen: php artisan tinker → Invoice::where("event_id", ' . $this->event->id . ')->get()');
    }

    protected function generateBookingNumber(): string
    {
        return 'BK-' . strtoupper(Str::random(3)) . '-' . date('ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }
}

