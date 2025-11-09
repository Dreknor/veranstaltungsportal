<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateEventInvoices extends Command
{
    protected $signature = 'invoices:generate-event-invoices
                            {--event= : Specific event ID to process}
                            {--force : Force regeneration even if invoice exists}';

    protected $description = 'Generiert Platform-Fee Rechnungen für beendete Events';

    protected InvoiceService $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        parent::__construct();
        $this->invoiceService = $invoiceService;
    }

    public function handle()
    {
        $this->info('🔍 Suche nach Events, die abgerechnet werden müssen...');
        $this->newLine();

        $query = Event::whereNotNull('end_date')
            ->where('end_date', '<', now())
            ->whereDoesntHave('invoices', function ($q) {
                $q->where('type', 'platform_fee');
            })
            ->with('user');

        // Spezifisches Event verarbeiten?
        if ($eventId = $this->option('event')) {
            $query->where('id', $eventId);
        }

        $events = $query->get();

        if ($events->isEmpty()) {
            $this->info('✓ Keine Events gefunden, die abgerechnet werden müssen.');
            return Command::SUCCESS;
        }

        $this->info("📋 {$events->count()} Event(s) gefunden");
        $this->newLine();

        $successCount = 0;
        $failedCount = 0;
        $skippedCount = 0;

        foreach ($events as $event) {
            try {
                $this->line("Verarbeite: {$event->title} (ID: {$event->id})");

                // Prüfe ob bereits eine Rechnung existiert (außer wenn --force)
                if (!$this->option('force')) {
                    $existingInvoice = Invoice::where('event_id', $event->id)
                        ->where('type', 'platform_fee')
                        ->first();

                    if ($existingInvoice) {
                        $this->warn("  ⏭ Rechnung existiert bereits: {$existingInvoice->invoice_number}");
                        $skippedCount++;
                        continue;
                    }
                }

                // Rechnung erstellen
                $invoice = $this->invoiceService->generatePlatformFeeInvoice($event);

                if ($invoice) {
                    $this->info("  ✓ Rechnung erstellt: {$invoice->invoice_number}");
                    $this->line("    Empfänger: {$invoice->recipient_email}");
                    $this->line("    Betrag: €{$invoice->total_amount}");

                    Log::info('Platform-Fee Rechnung automatisch erstellt', [
                        'event_id' => $event->id,
                        'invoice_id' => $invoice->id,
                        'invoice_number' => $invoice->invoice_number,
                    ]);

                    $successCount++;
                } else {
                    $this->warn("  ⚠ Keine Rechnung erstellt (keine Platform Fees vorhanden)");
                    $skippedCount++;
                }

            } catch (\Exception $e) {
                $this->error("  ✗ Fehler: {$e->getMessage()}");

                Log::error('Fehler bei automatischer Rechnungserstellung', [
                    'event_id' => $event->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                $failedCount++;
            }

            $this->newLine();
        }

        // Zusammenfassung
        $this->info('════════════════════════════════════════');
        $this->info('Zusammenfassung:');
        $this->line("  ✓ Erfolgreich: {$successCount}");
        if ($skippedCount > 0) {
            $this->line("  ⏭ Übersprungen: {$skippedCount}");
        }
        if ($failedCount > 0) {
            $this->error("  ✗ Fehlgeschlagen: {$failedCount}");
        }
        $this->info('════════════════════════════════════════');

        return $failedCount > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}

