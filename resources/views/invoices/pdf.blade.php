<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Rechnung - {{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.5;
            color: #333;
            padding: 30px;
        }
        .header {
            margin-bottom: 40px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #2563eb;
            font-size: 24pt;
            margin-bottom: 5px;
        }
        .header .subtitle {
            color: #666;
            font-size: 11pt;
        }
        .company-info {
            font-size: 9pt;
            color: #666;
            line-height: 1.4;
            margin-top: 10px;
        }
        .addresses {
            margin-bottom: 30px;
        }
        .addresses table {
            width: 100%;
        }
        .addresses td {
            vertical-align: top;
            padding: 10px;
        }
        .addresses .recipient {
            border: 1px solid #ddd;
            padding: 15px;
            background: #f9fafb;
        }
        .invoice-info {
            margin-bottom: 30px;
            text-align: right;
        }
        .invoice-info table {
            margin-left: auto;
            border-collapse: collapse;
        }
        .invoice-info td {
            padding: 5px 10px;
        }
        .invoice-info td:first-child {
            font-weight: bold;
            text-align: right;
        }
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
        }
        table.items th {
            background: #2563eb;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }
        table.items td {
            padding: 10px 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        table.items tr:last-child td {
            border-bottom: 2px solid #2563eb;
        }
        .text-right {
            text-align: right;
        }
        .totals {
            margin-top: 20px;
            margin-left: auto;
            width: 350px;
        }
        .totals table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals td {
            padding: 8px 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        .totals td:first-child {
            font-weight: bold;
        }
        .totals td:last-child {
            text-align: right;
        }
        .totals .total-row {
            background: #f3f4f6;
            font-weight: bold;
            font-size: 12pt;
        }
        .totals .total-row td {
            border-top: 2px solid #2563eb;
            border-bottom: 2px solid #2563eb;
            padding: 12px 10px;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 9pt;
            color: #666;
        }
        .footer table {
            width: 100%;
        }
        .footer td {
            vertical-align: top;
            padding: 5px 10px;
        }
        .notes {
            margin-top: 30px;
            padding: 15px;
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            font-size: 9pt;
        }
        .payment-info {
            margin-top: 30px;
            padding: 15px;
            background: #dbeafe;
            border-left: 4px solid #2563eb;
            font-size: 9pt;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rechnung</h1>
        <div class="subtitle">Platform-Fee Abrechnung</div>
        <div class="company-info">
            @if(isset($invoice->billing_data['platform']))
                @php $platform = $invoice->billing_data['platform']; @endphp
                <strong>{{ $platform['company_name'] ?? 'Event Platform' }}</strong><br>
                {{ $platform['address'] ?? '' }}<br>
                {{ $platform['postal_code'] ?? '' }} {{ $platform['city'] ?? '' }}<br>
                @if(!empty($platform['tax_id']))
                    Steuer-Nr: {{ $platform['tax_id'] }}<br>
                @endif
                @if(!empty($platform['vat_id']))
                    USt-IdNr: {{ $platform['vat_id'] }}<br>
                @endif
                E-Mail: {{ $platform['email'] ?? '' }}
            @endif
        </div>
    </div>

    <div class="addresses">
        <table>
            <tr>
                <td style="width: 60%;">
                    <div class="recipient">
                        <strong>Rechnungsempfänger:</strong><br>
                        {{ $invoice->recipient_name }}<br>
                        @if($invoice->recipient_address)
                            {!! nl2br(e($invoice->recipient_address)) !!}
                        @endif
                    </div>
                </td>
                <td style="width: 40%;"></td>
            </tr>
        </table>
    </div>

    <div class="invoice-info">
        <table>
            <tr>
                <td>Rechnungsnummer:</td>
                <td>{{ $invoice->invoice_number }}</td>
            </tr>
            <tr>
                <td>Rechnungsdatum:</td>
                <td>{{ $invoice->invoice_date->format('d.m.Y') }}</td>
            </tr>
            <tr>
                <td>Fälligkeitsdatum:</td>
                <td>{{ $invoice->due_date->format('d.m.Y') }}</td>
            </tr>
            <tr>
                <td>Veranstaltung:</td>
                <td>{{ $invoice->event->title ?? 'N/A' }}</td>
            </tr>
            @if($invoice->event)
            <tr>
                <td>Event-Datum:</td>
                <td>{{ $invoice->event->start_date->format('d.m.Y') }}</td>
            </tr>
            @endif
        </table>
    </div>

    <h3 style="margin-bottom: 15px;">Leistungsbeschreibung</h3>

    <table class="items">
        <thead>
            <tr>
                <th>Position</th>
                <th>Beschreibung</th>
                <th class="text-right">Menge</th>
                <th class="text-right">Einzelpreis</th>
                <th class="text-right">Gesamt</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($invoice->billing_data['items']))
                @foreach($invoice->billing_data['items'] as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        {{ $item['description'] ?? 'Platform-Fee' }}<br>
                        <small style="color: #666;">{{ $item['details'] ?? '' }}</small>
                    </td>
                    <td class="text-right">{{ $item['quantity'] ?? 1 }}</td>
                    <td class="text-right">{{ number_format($item['unit_price'] ?? $item['total'], 2, ',', '.') }} €</td>
                    <td class="text-right">{{ number_format($item['total'], 2, ',', '.') }} €</td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td>1</td>
                    <td>Platform-Fee für Event</td>
                    <td class="text-right">1</td>
                    <td class="text-right">{{ number_format($invoice->amount, 2, ',', '.') }} €</td>
                    <td class="text-right">{{ number_format($invoice->amount, 2, ',', '.') }} €</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="totals">
        <table>
            @if(isset($invoice->billing_data['breakdown']))
                @php $breakdown = $invoice->billing_data['breakdown']; @endphp
                @if($breakdown['booking_fees'] > 0)
                <tr>
                    <td>Buchungsgebühren (netto):</td>
                    <td>{{ number_format($breakdown['booking_fees'], 2, ',', '.') }} €</td>
                </tr>
                @endif
                @if($breakdown['featured_fees'] > 0)
                <tr>
                    <td>Featured Event Gebühren (netto):</td>
                    <td>{{ number_format($breakdown['featured_fees'], 2, ',', '.') }} €</td>
                </tr>
                @endif
            @endif
            <tr>
                <td>Zwischensumme (netto):</td>
                <td>{{ number_format($invoice->amount, 2, ',', '.') }} €</td>
            </tr>
            <tr>
                <td>MwSt. ({{ number_format($invoice->tax_rate, 1) }}%):</td>
                <td>{{ number_format($invoice->tax_amount, 2, ',', '.') }} €</td>
            </tr>
            <tr class="total-row">
                <td>Gesamtbetrag (brutto):</td>
                <td>{{ number_format($invoice->total_amount, 2, ',', '.') }} €</td>
            </tr>
        </table>
    </div>

    @if($invoice->status !== 'paid')
    <div class="payment-info">
        <strong>Zahlungsinformationen:</strong><br>
        Bitte überweisen Sie den Betrag bis zum <strong>{{ $invoice->due_date->format('d.m.Y') }}</strong>.<br>
        @if(isset($invoice->billing_data['platform']))
            @php $platform = $invoice->billing_data['platform']; @endphp
            @if(!empty($platform['bank_name']))
                <br>
                <strong>Bankverbindung:</strong><br>
                Bank: {{ $platform['bank_name'] }}<br>
                @if(!empty($platform['iban']))
                    IBAN: {{ $platform['iban'] }}<br>
                @endif
                @if(!empty($platform['bic']))
                    BIC: {{ $platform['bic'] }}<br>
                @endif
            @endif
        @endif
        Verwendungszweck: {{ $invoice->invoice_number }}
    </div>
    @else
    <div class="notes" style="background: #d1fae5; border-color: #10b981;">
        <strong>Zahlung erhalten</strong><br>
        Diese Rechnung wurde bereits am {{ $invoice->paid_at->format('d.m.Y') }} bezahlt. Vielen Dank!
    </div>
    @endif

    <div class="notes">
        <strong>Hinweise:</strong><br>
        Diese Rechnung wird gemäß § 14 UStG für erbrachte Leistungen ausgestellt.<br>
        Die Platform-Fee wird für die Bereitstellung der Event-Plattform und Zahlungsabwicklung berechnet.<br>
        Zahlungsziel: {{ $invoice->due_date->format('d.m.Y') }}
    </div>

    <div class="footer">
        <table>
            <tr>
                <td style="width: 50%;">
                    @if(isset($invoice->billing_data['platform']))
                        @php $platform = $invoice->billing_data['platform']; @endphp
                        <strong>Kontakt:</strong><br>
                        E-Mail: {{ $platform['email'] ?? 'support@platform.local' }}<br>
                        @if(!empty($platform['phone']))
                            Tel: {{ $platform['phone'] }}
                        @endif
                    @endif
                </td>
                <td style="width: 50%; text-align: right;">
                    Vielen Dank für Ihre Zusammenarbeit!
                </td>
            </tr>
        </table>
    </div>
</body>
</html>

