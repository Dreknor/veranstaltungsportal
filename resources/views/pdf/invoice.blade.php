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
    // Bei Beispielrechnungen wird das Logo weggelassen, um die Dateigröße zu reduzieren
    $logoBase64 = null;
    if (empty($isSample) && $event->organizer->logo) {
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
        @include('pdf.partials.styles')
        @include('pdf.partials.invoice-styles')
    </style>
</head>
<body>
    <div class="container">
        @if(!empty($isSample))
        <div class="note note-warning" style="text-align: center; font-weight: bold;">
            BEISPIELRECHNUNG – Diese Rechnung ist nicht gültig und dient nur zur Vorschau des Formats.
        </div>
        @endif

        <!-- Header -->
        @include('pdf.partials.wordmark')
        <table class="invoice-head" style="margin-top: 10px; padding-top: 10px;">
            <tr>
                <td style="width: 60%;">
                    @if($logoBase64)
                        <img src="{{ $logoBase64 }}" alt="{{ $orgName }}" style="max-height: 60px; max-width: 200px; margin-bottom: 5px; display: block;">
                    @else
                        <div class="issuer-name">{{ $orgName }}</div>
                    @endif
                    <div class="issuer-info">
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
                    </div>
                </td>
                <td style="width: 40%;">
                    <div class="invoice-label">Rechnung</div>
                    <div class="invoice-meta">Nr. {{ $invoiceNumber }}<br>{{ $invoiceDate }}</div>
                </td>
            </tr>
        </table>

        <!-- Addresses -->
        <table class="parties">
            <tr>
                <td style="width: 55%;">
                    <h3>Rechnungsempfänger</h3>
                    <div>
                        @if(isset($booking->billing_company) && $booking->billing_company)
                            <strong>{{ $booking->billing_company }}</strong><br>
                        @endif
                        <strong>{{ $booking->customer_name }}</strong><br>
                        {{ $booking->customer_email }}
                        @if($booking->customer_phone)
                            • {{ $booking->customer_phone }}
                        @endif
                        @if(isset($booking->billing_vat_id) && $booking->billing_vat_id)
                            <br>USt-IdNr.: {{ $booking->billing_vat_id }}
                        @endif
                    </div>
                </td>
                <td style="width: 45%;"></td>
            </tr>
        </table>

        <!-- Invoice Details -->
        <table class="meta-table">
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
                        <small>{{ $item['ticket_type'] }}</small>
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
                <tr class="sub">
                    <td colspan="2">
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
        <div class="block-title">Steueraufschlüsselung</div>
        <table class="tax-table">
            <tr>
                <th style="width: 25%;">MwSt.-Satz</th>
                <th class="num" style="width: 25%;">Netto</th>
                <th class="num" style="width: 25%;">MwSt.</th>
                <th class="num" style="width: 25%;">Brutto</th>
            </tr>
            <tr>
                <td>{{ $taxRate }}%</td>
                <td class="num">{{ number_format($netTotal - $discountAmount, 2, ',', '.') }} €</td>
                <td class="num">{{ number_format($taxAmount, 2, ',', '.') }} €</td>
                <td class="num">{{ number_format($totalAmount, 2, ',', '.') }} €</td>
            </tr>
        </table>

        <!-- Payment Info -->
        @if($booking->payment_status !== 'paid')
        <div class="note note-warning">
            <h4>Zahlungsinformationen</h4>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="vertical-align: top;">
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
                    </td>
                    @if(isset($paymentQrCode))
                    <td style="width: 130px; text-align: center; vertical-align: top;">
                        <img src="{{ $paymentQrCode }}" alt="Payment QR Code" style="width: 110px; height: 110px;">
                        <div style="font-size: 6.5pt; margin-top: 3px; line-height: 1.2;">
                            <strong>QR-Code scannen</strong><br>
                            für einfache Überweisung
                        </div>
                    </td>
                    @endif
                </tr>
            </table>
        </div>
        @else
        <div class="note note-success">
            <h4>Zahlungsinformationen</h4>
            <strong>Bezahlt</strong> am {{ $booking->updated_at->format('d.m.Y') }} via {{ ucfirst($booking->payment_method ?? 'Überweisung') }}<br>
            Vielen Dank für Ihre Zahlung!
        </div>
        @endif

        <!-- Notes -->
        @if($notes)
        <div class="block-title">Anmerkungen</div>
        <p class="fine-print">{{ $notes }}</p>
        @endif

        <p class="fine-print">
            Diese Rechnung wurde elektronisch erstellt und ist ohne Unterschrift gültig.
            @if(isset($paymentQrCode))
                QR-Code für schnelle Überweisung scannen.
            @endif
            Bei Fragen wenden Sie sich an den Veranstalter.
        </p>
        @if(!isset($isSample) || !$isSample)
        <p class="fine-print">
            Diese Rechnung entspricht der europäischen Norm EN 16931 und enthält ein eingebettetes ZUGFeRD-XML (Profil EN 16931) für die automatische Weiterverarbeitung.
        </p>
        @endif

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
