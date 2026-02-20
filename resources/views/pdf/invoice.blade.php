{{-- Daten aus billing_data (JSON) mit Fallback auf direkte DB-Spalten --}}
@php
    $billingData  = $event->organizer->billing_data ?? [];
    $orgAddress   = $billingData['company_address']     ?? $event->organizer->billing_address     ?? '';
    $orgPostal    = $billingData['company_postal_code'] ?? $event->organizer->billing_postal_code ?? '';
    $orgCity      = $billingData['company_city']        ?? $event->organizer->billing_city        ?? '';
    $orgEmail     = $billingData['company_email']       ?? $event->organizer->email               ?? '';
    $orgPhone     = $billingData['company_phone']       ?? $event->organizer->phone               ?? '';
    $orgTaxId     = $billingData['tax_id']              ?? $event->organizer->tax_id              ?? '';
    $orgVatId     = $billingData['vat_id']              ?? '';
    $orgName      = $billingData['company_name']        ?? $event->organizer->name                ?? '';

    // Logo als base64 einbetten, damit DomPDF es zuverlässig darstellt
    $logoBase64 = null;
    if ($event->organizer->logo) {
        $logoAbs = public_path('storage/' . $event->organizer->logo);
        if (file_exists($logoAbs)) {
            $mime = mime_content_type($logoAbs);
            $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoAbs));
        }
    }
@endphp
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rechnung - {{ $invoiceNumber }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9pt;
            line-height: 1.3;
            color: #333;
        }
        .container {
            padding: 20px 25px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #3b82f6;
        }
        .company-info h1 {
            color: #3b82f6;
            font-size: 16pt;
            margin-bottom: 3px;
        }
        .company-info p {
            color: #64748b;
            font-size: 8pt;
            line-height: 1.2;
        }
        .invoice-info {
            text-align: right;
        }
        .invoice-info h2 {
            color: #1e293b;
            font-size: 18pt;
            margin-bottom: 3px;
        }
        .invoice-info p {
            color: #64748b;
            font-size: 8pt;
            line-height: 1.2;
        }
        .addresses {
            margin-bottom: 12px;
        }
        .address-block {
            width: 48%;
        }
        .address-block h3 {
            color: #1e293b;
            font-size: 10pt;
            margin-bottom: 4px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 2px;
        }
        .address-block p {
            color: #475569;
            font-size: 8pt;
            line-height: 1.3;
        }
        .invoice-details {
            background: #f8fafc;
            padding: 8px 10px;
            margin-bottom: 12px;
            border-radius: 3px;
        }
        .invoice-details table {
            width: 100%;
        }
        .invoice-details td {
            padding: 2px 0;
            font-size: 8pt;
        }
        .invoice-details td:first-child {
            color: #64748b;
            font-weight: bold;
            width: 40%;
        }
        .invoice-details td:last-child {
            color: #1e293b;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .items-table thead {
            background: #3b82f6;
            color: white;
        }
        .items-table th {
            padding: 6px 8px;
            text-align: left;
            font-size: 8pt;
        }
        .items-table td {
            padding: 5px 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 8pt;
        }
        .items-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        .items-table .text-right {
            text-align: right;
        }
        .items-table .text-center {
            text-align: center;
        }
        .totals {
            margin-left: 60%;
            margin-bottom: 12px;
        }
        .totals table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals td {
            padding: 4px 8px;
            font-size: 8pt;
        }
        .totals td:first-child {
            color: #64748b;
            font-weight: bold;
        }
        .totals td:last-child {
            text-align: right;
            color: #1e293b;
        }
        .totals .total-row {
            background: #3b82f6;
            color: white;
            font-weight: bold;
            font-size: 10pt;
        }
        .totals .total-row td {
            color: white;
            padding: 8px;
        }
        .tax-info {
            background: #f1f5f9;
            padding: 8px 10px;
            margin-bottom: 10px;
            border-radius: 3px;
        }
        .tax-info h4 {
            color: #1e293b;
            font-size: 9pt;
            margin-bottom: 5px;
        }
        .tax-info table {
            width: 100%;
            font-size: 8pt;
        }
        .tax-info td {
            padding: 3px;
            color: #475569;
        }
        .payment-info {
            background: #fef3c7;
            border-left: 3px solid #f59e0b;
            padding: 10px;
            margin-bottom: 10px;
        }
        .payment-info h4 {
            color: #92400e;
            font-size: 9pt;
            margin-bottom: 5px;
        }
        .payment-info p {
            color: #78350f;
            font-size: 8pt;
            line-height: 1.3;
        }
        .notes {
            margin-bottom: 10px;
        }
        .notes h4 {
            color: #1e293b;
            font-size: 9pt;
            margin-bottom: 5px;
        }
        .notes p {
            color: #64748b;
            font-size: 7pt;
            line-height: 1.3;
        }
        .footer {
            margin-top: 15px;
            padding-top: 8px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            color: #94a3b8;
            font-size: 7pt;
        }
        .footer p {
            margin-bottom: 2px;
        }
    </style>
</head>
<body>
    <div class="container">
        @if(!empty($isSample))
        <div style="background:#fef9c3;border:2px solid #f59e0b;color:#92400e;padding:8px 12px;margin-bottom:12px;border-radius:4px;font-size:8pt;font-weight:bold;text-align:center;">
            ⚠ BEISPIELRECHNUNG – Diese Rechnung ist nicht gültig und dient nur zur Vorschau des Formats.
        </div>
        @endif

        <!-- Header -->
        <div class="header">
            <div class="company-info">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="{{ $orgName }}" style="max-height: 60px; max-width: 200px; margin-bottom: 5px; display: block;">
                @else
                    <h1 style="color: #3b82f6; font-size: 16pt; margin-bottom: 3px;">{{ $orgName }}</h1>
                @endif
                <p style="font-size: 7pt; line-height: 1.4;">
                    @if($orgAddress && $orgPostal && $orgCity)
                        {{ $orgAddress }}, {{ $orgPostal }} {{ $orgCity }}
                    @elseif($orgAddress)
                        {{ $orgAddress }}
                    @endif
                    @if($orgEmail || $orgPhone)
                        @if($orgAddress)<br>@endif
                        @if($orgEmail){{ $orgEmail }}@endif
                        @if($orgEmail && $orgPhone) • @endif
                        @if($orgPhone){{ $orgPhone }}@endif
                    @endif
                    @if($orgTaxId)
                        <br>Steuer-Nr.: {{ $orgTaxId }}
                    @endif
                    @if($orgVatId)
                        <br>USt-IdNr.: {{ $orgVatId }}
                    @endif
                </p>
            </div>
            <div class="invoice-info">
                <h2>RECHNUNG</h2>
                <p>Nr. {{ $invoiceNumber }}<br>{{ $invoiceDate }}</p>
            </div>
        </div>

        <!-- Addresses -->
        <div class="addresses">
            <div class="address-block">
                <h3>Rechnungsempfänger</h3>
                <p>
                    <strong>{{ $booking->customer_name }}</strong><br>
                    {{ $booking->customer_email }}
                    @if($booking->customer_phone)
                        • {{ $booking->customer_phone }}
                    @endif
                </p>
            </div>
        </div>

        <!-- Invoice Details -->
        <div class="invoice-details">
            <table>
                <tr>
                    <td>Buchung:</td>
                    <td><strong>{{ $booking->booking_number }}</strong> vom {{ $booking->created_at->format('d.m.Y') }}</td>
                </tr>
                <tr>
                    <td>Veranstaltung:</td>
                    <td>
                        <strong>{{ $event->title }}</strong> am {{ \Carbon\Carbon::parse($event->start_date)->format('d.m.Y H:i') }} Uhr
                        @if($event->location)
                            , {{ $event->location }}
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Pos.</th>
                    <th>Beschreibung</th>
                    <th class="text-center">Anz.</th>
                    <th class="text-right">Preis</th>
                    <th class="text-right">MwSt.</th>
                    <th class="text-right">Gesamt</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item['description'] }}</strong>
                        <small style="color: #64748b; display: block;">{{ $item['ticket_type'] }}</small>
                    </td>
                    <td class="text-center">{{ $item['quantity'] }}</td>
                    <td class="text-right">{{ number_format($item['unit_price'], 2, ',', '.') }} €</td>
                    <td class="text-right">{{ $item['tax_rate'] }}%</td>
                    <td class="text-right">{{ number_format($item['total'], 2, ',', '.') }} €</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals">
            <table>
                @if($discountAmount > 0)
                <tr>
                    <td>Zwischensumme:</td>
                    <td>{{ number_format($totalAmount + $discountAmount, 2, ',', '.') }} €</td>
                </tr>
                <tr>
                    <td>Rabatt:</td>
                    <td>-{{ number_format($discountAmount, 2, ',', '.') }} €</td>
                </tr>
                @endif
                <tr>
                    <td colspan="2" style="padding-top: 3px; border-top: 1px solid #e2e8f0; font-size: 7pt; color: #64748b;">
                        inkl. {{ number_format($taxAmount, 2, ',', '.') }} € MwSt. ({{ $taxRate }}%)
                    </td>
                </tr>
                <tr class="total-row">
                    <td>Gesamtbetrag:</td>
                    <td>{{ number_format($totalAmount, 2, ',', '.') }} €</td>
                </tr>
            </table>
        </div>

        <!-- Tax Breakdown -->
        <div class="tax-info">
            <h4>Steueraufschlüsselung</h4>
            <table>
                <tr style="font-weight: bold;">
                    <td style="width: 25%;">MwSt.-Satz</td>
                    <td style="width: 25%; text-align: right;">Netto</td>
                    <td style="width: 25%; text-align: right;">MwSt.</td>
                    <td style="width: 25%; text-align: right;">Brutto</td>
                </tr>
                <tr>
                    <td>{{ $taxRate }}%</td>
                    <td style="text-align: right;">{{ number_format($netTotal - $discountAmount, 2, ',', '.') }} €</td>
                    <td style="text-align: right;">{{ number_format($taxAmount, 2, ',', '.') }} €</td>
                    <td style="text-align: right;">{{ number_format($totalAmount, 2, ',', '.') }} €</td>
                </tr>
            </table>
        </div>

        <!-- Payment Info -->
        @if($booking->payment_status !== 'paid')
        <div class="payment-info">
            <h4>💳 Zahlungsinformationen</h4>
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div style="flex: 1; padding-right: 15px;">
                    <p>
                        Bitte überweisen Sie <strong>{{ number_format($totalAmount, 2, ',', '.') }} €</strong> unter Angabe <strong>{{ $booking->booking_number }}</strong>:<br>
                        @if($event->organizer->bank_account)
                            @php
                                $bankAccount = is_string($event->organizer->bank_account)
                                    ? json_decode($event->organizer->bank_account, true)
                                    : $event->organizer->bank_account;
                            @endphp
                            @if(is_array($bankAccount) && !empty($bankAccount))
                                <strong>{{ $bankAccount['account_holder'] ?? $event->organizer->name }}</strong><br>
                                IBAN: {{ $bankAccount['iban'] ?? 'N/A' }}
                                @if(isset($bankAccount['bic']))
                                    • BIC: {{ $bankAccount['bic'] }}
                                @endif
                            @else
                                <em>Bankverbindung wird vom Veranstalter bereitgestellt.</em>
                            @endif
                        @else
                            <em>Bankverbindung wird vom Veranstalter bereitgestellt.</em>
                        @endif
                    </p>
                </div>
                @if(isset($paymentQrCode))
                <div style="text-align: center; padding-left: 15px; border-left: 2px solid #f59e0b;">
                    <img src="{{ $paymentQrCode }}" alt="Payment QR Code" style="width: 110px; height: 110px; display: block;">
                    <p style="font-size: 6pt; color: #78350f; margin-top: 3px; line-height: 1.2;">
                        <strong>QR-Code scannen</strong><br>
                        für einfache Überweisung
                    </p>
                </div>
                @endif
            </div>
        </div>
        @else
        <div class="payment-info" style="background: #d1fae5; border-left-color: #10b981;">
            <h4 style="color: #065f46;">✅ Zahlungsinformationen</h4>
            <p style="color: #064e3b;">
                <strong>Bezahlt</strong> am {{ $booking->updated_at->format('d.m.Y') }} via {{ ucfirst($booking->payment_method ?? 'Überweisung') }}<br>
                Vielen Dank für Ihre Zahlung!
            </p>
        </div>
        @endif

        <!-- Notes -->
        @if($notes)
        <div class="notes">
            <h4>Anmerkungen</h4>
            <p>{{ $notes }}</p>
        </div>
        @endif

        <div class="notes">
            <p>
                Diese Rechnung wurde elektronisch erstellt und ist ohne Unterschrift gültig.
                @if(isset($paymentQrCode))
                    QR-Code für schnelle Überweisung scannen.
                @endif
                Bei Fragen wenden Sie sich an den Veranstalter.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                <strong>{{ $orgName }}</strong>
                @if($event->organizer->website || $orgEmail || $orgPhone)
                    •
                    @if($event->organizer->website){{ $event->organizer->website }}@endif
                    @if($event->organizer->website && ($orgEmail || $orgPhone)) • @endif
                    @if($orgEmail){{ $orgEmail }}@endif
                    @if($orgEmail && $orgPhone) • @endif
                    @if($orgPhone){{ $orgPhone }}@endif
                @endif
            </p>
            <p>Erstellt: {{ now()->format('d.m.Y H:i') }}</p>
        </div>
    </div>
</body>
</html>

