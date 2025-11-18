<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Rechnung - {{ $booking->invoice_number ?? $booking->booking_number }}</title>
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
            border-bottom: 2px solid #2563eb;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #2563eb;
            font-size: 20pt;
            margin-bottom: 5px;
        }
        .organizer-info {
            font-size: 9pt;
            color: #666;
            line-height: 1.4;
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
        .addresses .customer {
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
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        table.items td {
            padding: 10px;
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
            width: 300px;
        }
        .totals table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals td {
            padding: 8px;
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
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 9pt;
            color: #666;
            text-align: center;
        }
        .notes {
            margin-top: 30px;
            padding: 15px;
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            font-size: 9pt;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rechnung</h1>
        <div class="organizer-info">
            <strong>{{ $organizer['company_name'] ?? 'Veranstalter' }}</strong><br>
            {{ $organizer['address'] ?? '' }}<br>
            {{ $organizer['postal_code'] ?? '' }} {{ $organizer['city'] ?? '' }}<br>
            @if(!empty($organizer['tax_id']))
                Steuer-Nr: {{ $organizer['tax_id'] }}<br>
            @endif
            @if(!empty($organizer['email']))
                E-Mail: {{ $organizer['email'] }}
            @endif
        </div>
    </div>

    <div class="addresses">
        <table>
            <tr>
                <td style="width: 60%;">
                    <div class="customer">
                        <strong>Rechnungsempfänger:</strong><br>
                        {{ $customer['name'] }}<br>
                        {{ $customer['address'] ?? '' }}
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
                <td>{{ $booking->invoice_number }}</td>
            </tr>
            <tr>
                <td>Rechnungsdatum:</td>
                <td>{{ optional($booking->invoice_date)->format('d.m.Y') ?? now()->format('d.m.Y') }}</td>
            </tr>
            <tr>
                <td>Buchungsnummer:</td>
                <td>{{ $booking->booking_number }}</td>
            </tr>
            <tr>
                <td>Veranstaltung:</td>
                <td>{{ $event->title }}</td>
            </tr>
            <tr>
                <td>Datum:</td>
                <td>{{ $event->start_date->format('d.m.Y H:i') }} Uhr</td>
            </tr>
        </table>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th>Position</th>
                <th class="text-right">Anzahl</th>
                <th class="text-right">Einzelpreis</th>
                <th class="text-right">Gesamt</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item['description'] }}</td>
                <td class="text-right">{{ $item['quantity'] }}</td>
                <td class="text-right">{{ number_format($item['unit_price'], 2, ',', '.') }} €</td>
                <td class="text-right">{{ number_format($item['total'], 2, ',', '.') }} €</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td>Zwischensumme:</td>
                <td>{{ number_format($booking->subtotal, 2, ',', '.') }} €</td>
            </tr>
            @if($booking->discount > 0)
            <tr>
                <td>Rabatt:</td>
                <td>-{{ number_format($booking->discount, 2, ',', '.') }} €</td>
            </tr>
            @endif
            <tr>
                <td>MwSt. (19%):</td>
                <td>{{ number_format($booking->total * 0.19 / 1.19, 2, ',', '.') }} €</td>
            </tr>
            <tr class="total-row">
                <td>Gesamtbetrag:</td>
                <td>{{ number_format($booking->total, 2, ',', '.') }} €</td>
            </tr>
        </table>
    </div>

    @if($booking->payment_status !== 'paid')
    <div class="notes">
        <strong>Zahlungshinweise:</strong><br>
        Bitte überweisen Sie den Betrag bis zum {{ $event->start_date->subDays(7)->format('d.m.Y') }}.<br>
        <br>
        <strong>Bankverbindung:</strong><br>
        @if(!empty($organizer['bank_account']))
            @php $bankAccount = $organizer['bank_account']; @endphp
            Kontoinhaber: {{ $bankAccount['account_holder'] ?? $organizer['company_name'] }}<br>
            @if(!empty($bankAccount['bank_name']))
                Bank: {{ $bankAccount['bank_name'] }}<br>
            @endif
            IBAN: {{ $bankAccount['iban'] ?? 'Wird noch bekannt gegeben' }}<br>
            @if(!empty($bankAccount['bic']))
                BIC: {{ $bankAccount['bic'] }}<br>
            @endif
        @else
            Bankverbindung: Wird noch bekannt gegeben<br>
        @endif
        <br>
        Verwendungszweck: {{ $booking->invoice_number ?? $booking->booking_number }}
    </div>
    @else
    <div class="notes" style="background: #d1fae5; border-color: #10b981;">
        <strong>Zahlung erhalten</strong><br>
        Diese Rechnung wurde bereits bezahlt. Vielen Dank!
    </div>
    @endif

    <div class="footer">
        Vielen Dank für Ihre Buchung!<br>
        Bei Fragen wenden Sie sich bitte an: {{ $organizer['email']  }}
    </div>
</body>
</html>

