<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Services\InvoiceService;
use App\Services\ZugferdInvoiceService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Regeneriert vorhandene Rechnungs-PDFs und bettet ZUGFeRD-XML ein.
 *
 * Verwendung:
 *   php artisan invoices:regen-zugferd [--dry-run] [--limit=50]
 */
class RegenZugferdInvoices extends Command
{
    protected $signature = 'invoices:regen-zugferd
                            {--dry-run : Nur zeigen, welche Rechnungen betroffen wären}
                            {--limit=100 : Maximale Anzahl zu verarbeitender Rechnungen}';

    protected $description = 'Regeneriert bestehende Rechnungs-PDFs mit ZUGFeRD EN 16931 XML-Einbettung';

    public function __construct(
        protected ZugferdInvoiceService $zugferdService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $limit  = (int) $this->option('limit');

        $this->info("ZUGFeRD Rechnungsregenerierung" . ($dryRun ? ' (DRY RUN)' : ''));
        $this->line("Verarbeite maximal {$limit} Rechnungen...");

        $invoices = Invoice::whereNotNull('pdf_path')
            ->orderBy('invoice_date', 'desc')
            ->limit($limit)
            ->get();

        $this->info("Gefunden: {$invoices->count()} Rechnungen mit PDF-Pfad.");

        $processed = 0;
        $failed    = 0;

        $bar = $this->output->createProgressBar($invoices->count());
        $bar->start();

        foreach ($invoices as $invoice) {
            $pdfPath = storage_path("app/{$invoice->pdf_path}");

            if (!file_exists($pdfPath)) {
                $this->newLine();
                $this->warn("PDF nicht gefunden: {$invoice->invoice_number} -> {$pdfPath}");
                $bar->advance();
                continue;
            }

            if ($dryRun) {
                $bar->advance();
                $processed++;
                continue;
            }

            try {
                $existingPdf     = file_get_contents($pdfPath);
                $zugferdPdfContent = $this->zugferdService->embedZugferdInPdfFromInvoice($existingPdf, $invoice);
                file_put_contents($pdfPath, $zugferdPdfContent);
                $processed++;
            } catch (\Throwable $e) {
                $failed++;
                Log::error("ZUGFeRD-Regen fehlgeschlagen für Invoice {$invoice->id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Fertig! Verarbeitet: {$processed}, Fehlgeschlagen: {$failed}");

        return self::SUCCESS;
    }
}

