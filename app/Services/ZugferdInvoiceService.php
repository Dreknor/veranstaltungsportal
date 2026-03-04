<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Invoice;
use Carbon\Carbon;
use DateTime;
use horstoeko\zugferd\ZugferdDocumentBuilder;
use horstoeko\zugferd\ZugferdDocumentPdfMerger;
use horstoeko\zugferd\ZugferdProfiles;
use horstoeko\zugferd\codelists\ZugferdInvoiceType;
use horstoeko\zugferd\codelists\ZugferdPaymentMeans;
use Illuminate\Support\Facades\Log;

/**
 * ZugferdInvoiceService
 *
 * Erstellt ZUGFeRD-konforme Rechnungen (EN 16931).
 * Das XML-Dokument wird in ein bestehendes DomPDF-PDF als PDF/A-3 eingebettet.
 */
class ZugferdInvoiceService
{
    /**
     * Erstellt ein ZUGFeRD-PDF aus einem bestehenden PDF-Inhalt (als String)
     * und den Buchungsdaten.
     *
     * @param  string  $pdfContent  Binärer PDF-Inhalt (z. B. von DomPDF)
     * @param  Booking $booking     Buchungsobjekt mit geladenen Beziehungen
     * @param  array   $invoiceData Berechnete Rechnungsdaten (aus TicketPdfService)
     * @return string  Binärer PDF/A-3-Inhalt mit eingebettetem ZUGFeRD-XML
     */
    public function embedZugferdInPdf(string $pdfContent, Booking $booking, array $invoiceData): string
    {
        try {
            $xmlContent = $this->buildZugferdXml($booking, $invoiceData);
            return $this->mergePdfWithXml($pdfContent, $xmlContent);
        } catch (\Throwable $e) {
            Log::error('ZUGFeRD-Einbettung fehlgeschlagen', [
                'booking_id' => $booking->id,
                'error'      => $e->getMessage(),
                'trace'      => $e->getTraceAsString(),
            ]);
            // Fallback: original PDF ohne ZUGFeRD zurückgeben
            return $pdfContent;
        }
    }

    /**
     * Erstellt ein ZUGFeRD-PDF aus einem bestehenden PDF-Inhalt (als String)
     * und einem Invoice-Modell (Platform-Gebühren-Rechnung).
     *
     * @param  string  $pdfContent Binärer PDF-Inhalt
     * @param  Invoice $invoice    Invoice-Modell mit billing_data
     * @return string  Binärer PDF/A-3-Inhalt
     */
    public function embedZugferdInPdfFromInvoice(string $pdfContent, Invoice $invoice): string
    {
        try {
            $xmlContent = $this->buildZugferdXmlFromInvoice($invoice);
            return $this->mergePdfWithXml($pdfContent, $xmlContent);
        } catch (\Throwable $e) {
            Log::error('ZUGFeRD-Einbettung (Invoice) fehlgeschlagen', [
                'invoice_id' => $invoice->id,
                'error'      => $e->getMessage(),
            ]);
            return $pdfContent;
        }
    }

    /**
     * Erstellt ein ZUGFeRD-PDF für eine Beispielrechnung (keine echten Booking-Daten).
     *
     * @param  string $pdfContent   Binärer PDF-Inhalt (z. B. von DomPDF)
     * @param  array  $sampleData   Beispieldaten (invoiceNumber, invoiceDate, seller, buyer, items, …)
     * @return string Binärer PDF/A-3-Inhalt mit eingebettetem ZUGFeRD-XML
     */
    public function embedZugferdInPdfForSample(string $pdfContent, array $sampleData): string
    {
        try {
            $xmlContent = $this->buildZugferdXmlForSample($sampleData);
            return $this->mergePdfWithXml($pdfContent, $xmlContent);
        } catch (\Throwable $e) {
            Log::error('ZUGFeRD-Einbettung (Beispielrechnung) fehlgeschlagen', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            // Fallback: original PDF ohne ZUGFeRD zurückgeben
            return $pdfContent;
        }
    }

    /**
     * Baut das ZUGFeRD-XML für eine Beispielrechnung aus einem generischen Array.
     */
    public function buildZugferdXmlForSample(array $data): string
    {
        $builder = ZugferdDocumentBuilder::createNew(ZugferdProfiles::PROFILE_EN16931);

        $invoiceNumber = $data['invoiceNumber'] ?? 'DEMO-00001';
        $invoiceDate   = $this->resolveDate($data['invoiceDate'] ?? null, new DateTime());

        $builder->setDocumentInformation(
            $invoiceNumber,
            ZugferdInvoiceType::INVOICE,
            $invoiceDate,
            'EUR'
        );
        $builder->setDocumentBuyerReference($invoiceNumber);

        // --- Verkäufer ---
        $seller = $data['seller'] ?? [];
        $sellerName    = $seller['name']    ?? 'Veranstalter';
        $sellerAddress = $seller['address'] ?? '';
        $sellerPostal  = $seller['postal']  ?? '';
        $sellerCity    = $seller['city']    ?? '';
        $sellerCountry = $this->isoCountry($seller['country'] ?? 'DE');
        $sellerVatId   = $seller['vat_id']  ?? '';
        $sellerTaxId   = $seller['tax_id']  ?? '';

        $builder->setDocumentSeller($sellerName);
        $builder->setDocumentSellerAddress($sellerAddress, null, null, $sellerPostal, $sellerCity, $sellerCountry);
        if ($sellerVatId) {
            $builder->addDocumentSellerTaxRegistration('VA', $sellerVatId);
        }
        if ($sellerTaxId) {
            $builder->addDocumentSellerTaxRegistration('FC', $sellerTaxId);
        }

        // --- Käufer ---
        $buyer = $data['buyer'] ?? [];
        $builder->setDocumentBuyer($buyer['name'] ?? 'Max Mustermann');
        $builder->setDocumentBuyerAddress(
            $buyer['address'] ?? '',
            null, null,
            $buyer['postal']  ?? '',
            $buyer['city']    ?? '',
            $this->isoCountry($buyer['country'] ?? 'DE')
        );
        if (!empty($buyer['email'])) {
            $builder->setDocumentBuyerCommunication('EM', $buyer['email']);
        }

        // --- Positionen ---
        $items      = $data['items'] ?? [];
        $taxRate    = (float) ($data['taxRate'] ?? 19);
        $lineTotals = 0.0;

        foreach ($items as $index => $item) {
            $posId        = (string) ($index + 1);
            $quantity     = (float) ($item['quantity'] ?? 1);
            $unitPrice    = (float) ($item['unit_price'] ?? 0);
            $itemGross    = (float) ($item['total'] ?? $unitPrice * $quantity);
            $itemRate     = (float) ($item['tax_rate'] ?? $taxRate);
            $netUnitPrice = round($unitPrice / (1 + $itemRate / 100), 10);
            $itemNet      = round($itemGross / (1 + $itemRate / 100), 10);
            $lineTotals  += $itemNet;

            $productName = trim(($item['description'] ?? '') . (!empty($item['ticket_type']) ? ' – ' . $item['ticket_type'] : ''));
            if (empty($productName)) {
                $productName = 'Ticket';
            }

            $builder
                ->addNewPosition($posId)
                ->setDocumentPositionProductDetails($productName)
                ->setDocumentPositionGrossPrice($unitPrice)
                ->setDocumentPositionNetPrice($netUnitPrice)
                ->setDocumentPositionNetPriceTax('S', 'VAT', $itemRate, 0.0)
                ->setDocumentPositionQuantity($quantity, 'C62')
                ->addDocumentPositionTax('S', 'VAT', $itemRate)
                ->setDocumentPositionLineSummation($itemNet);
        }

        // --- Steuer ---
        $totalAmount       = (float) ($data['totalAmount'] ?? 0);
        $netTotal          = round($totalAmount / (1 + $taxRate / 100), 2);
        $taxAmount         = round($totalAmount - $netTotal, 2);
        $lineTotalsRounded = round($lineTotals, 2);
        $netDiscount       = max(0.0, round($lineTotalsRounded - $netTotal, 2));

        $builder->addDocumentTaxSimple('S', 'VAT', $netTotal, $taxAmount, $taxRate);

        // --- Zahlungsbedingungen ---
        $dueDate = (new DateTime())->modify('+14 days');
        $builder->addDocumentPaymentTerm(null, $dueDate);

        // --- Bankverbindung (optional) ---
        $iban = $data['seller']['iban'] ?? null;
        $bic  = $data['seller']['bic']  ?? null;
        if ($iban) {
            $builder->addDocumentPaymentMeanToCreditTransfer($iban, null, null, $bic ?: null);
        }

        // --- Summation ---
        $builder->setDocumentSummation(
            round($totalAmount, 2),
            round($totalAmount, 2),
            $lineTotalsRounded,
            0.0,
            $netDiscount,
            round($netTotal, 2),
            round($taxAmount, 2)
        );

        return $builder->getContent();
    }

    // -------------------------------------------------------------------------
    // XML-Aufbau für Booking-basierte Rechnungen (TicketPdfService)
    // -------------------------------------------------------------------------

    /**
     * Baut das ZUGFeRD-XML für eine Buchungsrechnung.
     */
    public function buildZugferdXml(Booking $booking, array $invoiceData): string
    {
        $builder = ZugferdDocumentBuilder::createNew(ZugferdProfiles::PROFILE_EN16931);

        $invoiceNumber = $invoiceData['invoiceNumber'] ?? $booking->invoice_number ?? ('INV-' . $booking->id);
        $invoiceDate   = $this->resolveDate($invoiceData['invoiceDate'] ?? null, $booking->invoice_date ?? now());

        // --- Kopfdaten ---
        $builder->setDocumentInformation(
            $invoiceNumber,
            ZugferdInvoiceType::INVOICE,
            $invoiceDate,
            'EUR'
        );

        // BT-10 (Leitweg-ID) – für B2C optional, aber empfohlen
        $builder->setDocumentBuyerReference($invoiceNumber);

        // --- Verkäufer (Veranstalter) ---
        $this->addSellerFromBooking($builder, $booking);

        // --- Käufer (Teilnehmer) ---
        $this->addBuyerFromBooking($builder, $booking);

        // --- Positionen ---
        $items      = $invoiceData['items'] ?? [];
        $taxRate    = (float) ($invoiceData['taxRate'] ?? 19);
        $lineTotals = 0.0;

        foreach ($items as $index => $item) {
            $posId        = (string) ($index + 1);
            $quantity     = (float) ($item['quantity'] ?? 1);
            $unitPrice    = (float) ($item['unit_price'] ?? 0);          // Bruttopreis je Einheit
            $itemGross    = (float) ($item['total'] ?? $unitPrice * $quantity);
            $itemRate     = (float) ($item['tax_rate'] ?? $taxRate);
            $netUnitPrice = round($unitPrice / (1 + $itemRate / 100), 10);
            $itemNet      = round($itemGross / (1 + $itemRate / 100), 10);

            $lineTotals += $itemNet;

            $productName = trim(($item['description'] ?? '') . ($item['ticket_type'] ?? '' ? ' – ' . $item['ticket_type'] : ''));
            if (empty($productName)) {
                $productName = 'Ticket';
            }

            $builder
                ->addNewPosition($posId)
                ->setDocumentPositionProductDetails($productName)
                ->setDocumentPositionGrossPrice($unitPrice)
                ->setDocumentPositionNetPrice($netUnitPrice)
                ->setDocumentPositionNetPriceTax('S', 'VAT', $itemRate, 0.0)
                ->setDocumentPositionQuantity($quantity, 'C62') // C62 = Stück (UN/CEFACT)
                ->addDocumentPositionTax('S', 'VAT', $itemRate)
                ->setDocumentPositionLineSummation($itemNet);
        }

        // --- Steuer auf Dokumentenebene ---
        $totalAmount    = (float) ($invoiceData['totalAmount'] ?? 0);
        $netTotal       = round($totalAmount / (1 + $taxRate / 100), 2);
        $taxAmount      = round($totalAmount - $netTotal, 2);

        // BT-107 (Abzüge/Rabatte auf Dokumentenebene) als Differenz zwischen
        // den Netto-Zeilensummen und dem tatsächlichen Netto-Gesamtbetrag.
        // Damit gilt garantiert: BT-109 = BT-106 - BT-107.
        $lineTotalsRounded = round($lineTotals, 2);
        $netDiscount = max(0.0, round($lineTotalsRounded - $netTotal, 2));

        $builder->addDocumentTaxSimple('S', 'VAT', $netTotal, $taxAmount, $taxRate);

        // --- Zahlungsbedingungen ---
        // Fälligkeit: entweder aus Buchungsdaten oder 14 Tage ab heute.
        // Nie in der Vergangenheit setzen (z. B. wenn Rechnung nach Event-Start erstellt wird).
        $eventStart = $booking->event->start_date ?? null;
        $calculatedDue = $eventStart && $eventStart->isFuture()
            ? $eventStart->copy()->subDays(7)->toDateTime()
            : (new DateTime())->modify('+14 days');
        $dueDate = $calculatedDue;

        $builder->addDocumentPaymentTerm(null, $dueDate);

        // --- Zahlungsart (SEPA-Überweisung wenn Bankverbindung vorhanden) ---
        $this->addPaymentMeans($builder, $booking->event->organizer ?? null);

        // --- Summation ---
        $builder->setDocumentSummation(
            round($totalAmount, 2),       // BT-112: Gesamtbetrag brutto (inkl. MwSt.)
            round($totalAmount, 2),       // BT-115: Fälligkeitsbetrag
            $lineTotalsRounded,           // BT-106: Summe der Netto-Zeilensummen
            0.0,                          // BT-108: Aufschläge auf Dokumentenebene
            $netDiscount,                 // BT-107: Abzüge auf Dokumentenebene (Netto-Rabatt)
            round($netTotal, 2),          // BT-109: Steuerbemessungsgrundlage (= BT-106 - BT-107)
            round($taxAmount, 2)          // BT-110: Gesamter Steuerbetrag
        );

        return $builder->getContent();
    }

    // -------------------------------------------------------------------------
    // XML-Aufbau für Invoice-Modell (InvoiceService – Plattformgebühren)
    // -------------------------------------------------------------------------

    /**
     * Baut das ZUGFeRD-XML für ein Invoice-Modell.
     */
    public function buildZugferdXmlFromInvoice(Invoice $invoice): string
    {
        $builder = ZugferdDocumentBuilder::createNew(ZugferdProfiles::PROFILE_EN16931);

        $invoiceDate = $invoice->invoice_date instanceof \DateTimeInterface
            ? new DateTime($invoice->invoice_date->format('Y-m-d'))
            : ($invoice->invoice_date ? new DateTime($invoice->invoice_date) : new DateTime());

        $builder->setDocumentInformation(
            $invoice->invoice_number,
            ZugferdInvoiceType::INVOICE,
            $invoiceDate,
            $invoice->currency ?? 'EUR'
        );

        $builder->setDocumentBuyerReference($invoice->invoice_number);

        // --- Verkäufer ---
        $billingData = $invoice->billing_data ?? [];
        $platform    = $billingData['platform'] ?? [];

        $sellerName    = $platform['company_name'] ?? config('monetization.platform_company_name', 'Plattform');
        $sellerAddress = $platform['address'] ?? '';
        $sellerPostal  = $platform['postal_code'] ?? '';
        $sellerCity    = $platform['city'] ?? '';
        $sellerCountry = $this->isoCountry($platform['country'] ?? 'DE');
        $sellerVatId   = $platform['vat_id'] ?? '';
        $sellerTaxId   = $platform['tax_id'] ?? '';

        $builder->setDocumentSeller($sellerName);
        $builder->setDocumentSellerAddress($sellerAddress, null, null, $sellerPostal, $sellerCity, $sellerCountry);

        if ($sellerVatId) {
            $builder->addDocumentSellerTaxRegistration('VA', $sellerVatId);
        }
        if ($sellerTaxId) {
            $builder->addDocumentSellerTaxRegistration('FC', $sellerTaxId);
        }

        // --- Käufer (Veranstalter) ---
        $organizer    = $billingData['organizer'] ?? [];
        $buyerName    = $organizer['company_name'] ?? $invoice->recipient_name ?? 'Unbekannt';
        $buyerAddress = $organizer['address'] ?? '';
        $buyerPostal  = $organizer['postal_code'] ?? '';
        $buyerCity    = $organizer['city'] ?? '';
        $buyerCountry = $this->isoCountry($organizer['country'] ?? 'DE');

        $builder->setDocumentBuyer($buyerName);
        $builder->setDocumentBuyerAddress($buyerAddress, null, null, $buyerPostal, $buyerCity, $buyerCountry);

        // --- Positionen ---
        $items      = $billingData['items'] ?? [];
        $taxRate    = (float) ($invoice->tax_rate ?? 19);
        $lineTotals = 0.0;

        if (empty($items)) {
            $items = [[
                'description' => 'Plattformgebühr',
                'quantity'    => 1,
                'unit_price'  => (float) $invoice->amount,
                'total'       => (float) $invoice->amount,
            ]];
        }

        // Platform-Fee-Items speichern NETTO-Preise (invoice->amount ist der Nettobetrag).
        // Daher: netUnitPrice = unit_price, grossUnitPrice = unit_price * (1 + taxRate/100).
        foreach ($items as $index => $item) {
            $posId        = (string) ($index + 1);
            $quantity     = (float) ($item['quantity'] ?? 1);
            $netUnitPrice = (float) ($item['unit_price'] ?? 0);  // Nettopreis je Einheit
            $itemNet      = round((float) ($item['total'] ?? $netUnitPrice * $quantity), 10);
            $grossUnitPrice = round($netUnitPrice * (1 + $taxRate / 100), 10);
            $lineTotals  += $itemNet;

            $name = trim($item['description'] ?? 'Leistung');

            $builder
                ->addNewPosition($posId)
                ->setDocumentPositionProductDetails($name)
                ->setDocumentPositionGrossPrice($grossUnitPrice)
                ->setDocumentPositionNetPrice($netUnitPrice)
                ->setDocumentPositionNetPriceTax('S', 'VAT', $taxRate, 0.0)
                ->setDocumentPositionQuantity($quantity, 'C62')
                ->addDocumentPositionTax('S', 'VAT', $taxRate)
                ->setDocumentPositionLineSummation($itemNet);
        }

        // --- Steuern ---
        $netAmount = (float) $invoice->amount;
        $taxAmount = (float) $invoice->tax_amount;

        // Sicherheitsprüfung: lineTotals sollte netAmount entsprechen.
        // Falls Items fehlen oder abweichen, wird netAmount autoritativ gesetzt.
        if (abs($lineTotals - $netAmount) > 0.02) {
            Log::warning('ZUGFeRD (Invoice): Abweichung zwischen lineTotals und invoice->amount', [
                'invoice_id' => $invoice->id,
                'lineTotals' => $lineTotals,
                'netAmount'  => $netAmount,
            ]);
            $lineTotals = $netAmount;
        }

        $builder->addDocumentTaxSimple('S', 'VAT', $netAmount, $taxAmount, $taxRate);

        // --- Zahlungsbedingungen ---
        $dueDate = $invoice->due_date instanceof \DateTimeInterface
            ? new DateTime($invoice->due_date->format('Y-m-d'))
            : ($invoice->due_date ? new DateTime($invoice->due_date) : (new DateTime())->modify('+14 days'));

        $builder->addDocumentPaymentTerm(null, $dueDate);

        // --- Bankverbindung ---
        $iban = $platform['iban'] ?? null;
        $bic  = $platform['bic'] ?? null;

        if ($iban) {
            // addDocumentPaymentMeanToCreditTransfer($payeeIban, $payeeAccountName, $payeePropId, $payeeBic, $paymentReference)
            $builder->addDocumentPaymentMeanToCreditTransfer($iban, null, null, $bic ?: null);
        }

        // --- Summation ---
        $totalAmount = (float) $invoice->total_amount;

        $builder->setDocumentSummation(
            round($totalAmount, 2),           // BT-112: Gesamtbetrag (brutto)
            round($totalAmount, 2),           // BT-115: Fälligkeitsbetrag
            round($lineTotals, 2),            // BT-106: Summe der Netto-Zeilensummen
            0.0,                              // BT-108: Aufschläge
            0.0,                              // BT-107: Abzüge
            round($netAmount, 2),             // BT-109: Steuerbemessungsgrundlage
            round($taxAmount, 2)              // BT-110: Steuerbetrag
        );

        return $builder->getContent();
    }

    // -------------------------------------------------------------------------
    // Hilfsmethoden
    // -------------------------------------------------------------------------

    /**
     * Fügt Verkäuferdaten aus einer Buchung hinzu.
     */
    private function addSellerFromBooking(ZugferdDocumentBuilder $builder, Booking $booking): void
    {
        $organizer   = $booking->event->organizer ?? null;
        $billingData = $organizer ? ($organizer->billing_data ?? []) : [];

        $sellerName    = $billingData['company_name'] ?? ($organizer ? $organizer->name : 'Veranstalter');
        $sellerAddress = $billingData['company_address'] ?? ($organizer ? ($organizer->billing_address ?? '') : '');
        $sellerPostal  = $billingData['company_postal_code'] ?? ($organizer ? ($organizer->billing_postal_code ?? '') : '');
        $sellerCity    = $billingData['company_city'] ?? ($organizer ? ($organizer->billing_city ?? '') : '');
        $sellerCountry = $this->isoCountry($billingData['company_country'] ?? 'DE');
        $sellerVatId   = $billingData['vat_id'] ?? '';
        $sellerTaxId   = $organizer ? ($organizer->tax_id ?? $billingData['tax_id'] ?? '') : '';

        $builder->setDocumentSeller($sellerName);
        $builder->setDocumentSellerAddress($sellerAddress, null, null, $sellerPostal, $sellerCity, $sellerCountry);

        if ($sellerVatId) {
            $builder->addDocumentSellerTaxRegistration('VA', $sellerVatId);
        }
        if ($sellerTaxId) {
            $builder->addDocumentSellerTaxRegistration('FC', $sellerTaxId);
        }
    }

    /**
     * Fügt Käuferdaten aus einer Buchung hinzu.
     */
    private function addBuyerFromBooking(ZugferdDocumentBuilder $builder, Booking $booking): void
    {
        $buyerName    = $booking->customer_name ?? 'Kunde';
        $buyerAddress = $booking->billing_address ?? '';
        $buyerPostal  = $booking->billing_postal_code ?? '';
        $buyerCity    = $booking->billing_city ?? '';
        $buyerCountry = $this->isoCountry($booking->billing_country ?? 'DE');

        $builder->setDocumentBuyer($buyerName);
        $builder->setDocumentBuyerAddress($buyerAddress, null, null, $buyerPostal, $buyerCity, $buyerCountry);

        if ($booking->customer_email) {
            $builder->setDocumentBuyerCommunication('EM', $booking->customer_email);
        }
    }

    /**
     * Fügt Zahlungsart hinzu (SEPA-Überweisung wenn Bankverbindung vorhanden).
     */
    private function addPaymentMeans(ZugferdDocumentBuilder $builder, $organizer): void
    {
        if (!$organizer) {
            return;
        }

        $bankAccount = is_string($organizer->bank_account ?? null)
            ? json_decode($organizer->bank_account, true)
            : ($organizer->bank_account ?? null);

        if (is_array($bankAccount) && !empty($bankAccount['iban'])) {
            // addDocumentPaymentMeanToCreditTransfer($payeeIban, $payeeAccountName, $payeePropId, $payeeBic, $paymentReference)
            $builder->addDocumentPaymentMeanToCreditTransfer(
                $bankAccount['iban'],
                null,
                null,
                $bankAccount['bic'] ?? null
            );
        }
        // Ohne Bankverbindung: keine Zahlungsart gesetzt (optional in EN 16931)
    }

    /**
     * Mischt PDF-Inhalt und ZUGFeRD-XML zu einem PDF/A-3.
     */
    private function mergePdfWithXml(string $pdfContent, string $xmlContent): string
    {
        $merger = new ZugferdDocumentPdfMerger($xmlContent, $pdfContent);
        $merger->generateDocument();
        return $merger->downloadString();
    }

    /**
     * Löst ein Datum zu einem DateTime-Objekt auf.
     */
    private function resolveDate(mixed $date, mixed $fallback): DateTime
    {
        if ($date instanceof \DateTimeInterface) {
            return new DateTime($date->format('Y-m-d'));
        }
        if (is_string($date)) {
            try {
                // Deutsches Format d.m.Y
                if (preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $date)) {
                    return DateTime::createFromFormat('d.m.Y', $date) ?: new DateTime();
                }
                return new DateTime($date);
            } catch (\Throwable) {
                // ignore
            }
        }
        if ($fallback instanceof \DateTimeInterface) {
            return new DateTime($fallback->format('Y-m-d'));
        }
        return new DateTime();
    }

    /**
     * Gibt einen ISO-3166-1-Alpha-2-Ländercode zurück.
     * Konvertiert "Deutschland", "Germany" usw. zu "DE".
     */
    private function isoCountry(string $country): string
    {
        $country = trim($country);

        // Bereits 2-buchstabiger Code
        if (strlen($country) === 2) {
            return strtoupper($country);
        }

        $map = [
            'deutschland'            => 'DE',
            'germany'                => 'DE',
            'österreich'             => 'AT',
            'austria'                => 'AT',
            'schweiz'                => 'CH',
            'switzerland'            => 'CH',
            'frankreich'             => 'FR',
            'france'                 => 'FR',
            'niederlande'            => 'NL',
            'netherlands'            => 'NL',
            'belgien'                => 'BE',
            'belgium'                => 'BE',
            'luxemburg'              => 'LU',
            'luxembourg'             => 'LU',
            'united kingdom'         => 'GB',
            'vereinigtes königreich' => 'GB',
        ];

        $normalized = mb_strtolower($country);
        return $map[$normalized] ?? 'DE';
    }
}


