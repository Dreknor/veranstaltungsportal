<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rechnung {{ $invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }
        .header {
            margin-bottom: 40px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #2563eb;
            font-size: 24pt;
            margin-bottom: 10px;
        }
        .company-info {
            font-size: 9pt;
            color: #666;
        }
        .invoice-details {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .invoice-details .left,
        .invoice-details .right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .invoice-details .right {
            text-align: right;
        }
        .customer-info {
            margin-bottom: 30px;
        }
        .customer-info h3 {
            font-size: 12pt;
            margin-bottom: 10px;
            color: #2563eb;
        }
        .info-row {
            margin-bottom: 5px;
        }
        .info-row strong {
            display: inline-block;
            width: 120px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table thead {
            background-color: #f3f4f6;
        }
        table th {
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            border-bottom: 2px solid #2563eb;
        }
        table td {
            padding: 10px 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        table th:last-child,
        table td:last-child {
            text-align: right;
        }
        .totals {
            float: right;
            width: 300px;
            margin-top: 20px;
        }
        .totals table {
            margin-bottom: 0;
        }
        .totals table td {
            border: none;
            padding: 5px 8px;
        }
        .totals .total-row {
            font-weight: bold;
            font-size: 12pt;
            border-top: 2px solid #333;
        }
        .totals .total-row td {
            padding-top: 10px;
        }
        .footer {
            clear: both;
            margin-top: 60px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 9pt;
            color: #666;
            text-align: center;
        }
        .payment-info {
            background-color: #f9fafb;
            padding: 15px;
            margin: 20px 0;
            border-left: 4px solid #2563eb;
        }
        .payment-info h4 {
            margin-bottom: 10px;
            color: #2563eb;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 9pt;
            font-weight: bold;
        }
        .status-paid {
            background-color: #d1fae5;
            color: #065f46;
        }
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
    </style>
</head>
<body>
    <div class="header">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div style="flex: 1;">
                <h1>RECHNUNG</h1>
            </div>
            @if($event->organization?->logo)
                <div style="text-align: right;">
                    <img src="{{ public_path('storage/' . $event->organization->logo) }}"
                         alt="{{ $event->organization->name }} Logo"
                         style="max-height: 60px; max-width: 150px; object-fit: contain;">
                </div>
            @endif
        </div>
        <div class="company-info">
            Veranstaltungsportal | {{ config('app.name') }}<br>
            {{ $event->organization?->name ?? 'Musterstraße 123, 12345 Musterstadt' }}<br>
            @if($event->organization?->email)
                E-Mail: {{ $event->organization->email }} |
            @endif
            @if($event->organization?->phone)
                Tel: {{ $event->organization->phone }}
            @endif
        </div>
    </div>

    <div class="invoice-details">
        <div class="left">
            <div class="info-row"><strong>Rechnungsnr.:</strong> {{ $invoice_number }}</div>
            <div class="info-row"><strong>Rechnungsdatum:</strong> {{ $invoice_date->format('d.m.Y') }}</div>
            <div class="info-row"><strong>Buchungsnr.:</strong> {{ $booking->booking_number }}</div>
        </div>
        <div class="right">
            <div class="info-row">
                <strong>Zahlungsstatus:</strong>
                @if($booking->payment_status === 'paid')
                    <span class="status-badge status-paid">BEZAHLT</span>
                @else
                    <span class="status-badge status-pending">AUSSTEHEND</span>
                @endif
            </div>
        </div>
    </div>

    <div class="customer-info">
        <h3>Rechnungsempfänger</h3>
        <div>{{ $booking->customer_name }}</div>
        <div>{{ $booking->customer_email }}</div>
        @if($booking->customer_phone)
            <div>{{ $booking->customer_phone }}</div>
        @endif
    </div>

    <div class="customer-info">
        <h3>Veranstaltung</h3>
        <div class="info-row"><strong>Event:</strong> {{ $event->title }}</div>
        <div class="info-row"><strong>Datum:</strong> {{ $event->start_date->format('d.m.Y H:i') }} Uhr</div>
        <div class="info-row"><strong>Ort:</strong> {{ $event->venue_name }}, {{ $event->venue_city }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 50%">Beschreibung</th>
                <th style="width: 15%; text-align: center">Menge</th>
                <th style="width: 20%; text-align: right">Einzelpreis</th>
                <th style="width: 15%; text-align: right">Gesamt</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grouped = $items->groupBy('ticket_type_id');
            @endphp

            @foreach($grouped as $ticketTypeId => $groupedItems)
                @php
                    $firstItem = $groupedItems->first();
                    $quantity = $groupedItems->count();
                    $totalPrice = $groupedItems->sum('price');
                @endphp
                <tr>
                    <td>
                        <strong>{{ $firstItem->ticketType->name }}</strong>
                        @if($firstItem->ticketType->description)
                            <br><small style="color: #666;">{{ $firstItem->ticketType->description }}</small>
                        @endif
                    </td>
                    <td style="text-align: center">{{ $quantity }}</td>
                    <td style="text-align: right">{{ number_format($firstItem->price, 2, ',', '.') }} €</td>
                    <td style="text-align: right">{{ number_format($totalPrice, 2, ',', '.') }} €</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td>Zwischensumme:</td>
                <td style="text-align: right">{{ number_format($booking->subtotal, 2, ',', '.') }} €</td>
            </tr>
            @if($booking->discount > 0)
                <tr>
                    <td>
                        Rabatt
                        @if($booking->discountCode)
                            <br><small>({{ $booking->discountCode->code }})</small>
                        @endif
                    </td>
                    <td style="text-align: right; color: green">-{{ number_format($booking->discount, 2, ',', '.') }} €</td>
                </tr>
            @endif
            <tr class="total-row">
                <td>Gesamtbetrag:</td>
                <td style="text-align: right">{{ number_format($booking->total, 2, ',', '.') }} €</td>
            </tr>
        </table>
    </div>

    <div style="clear: both;"></div>

    @if($booking->payment_status !== 'paid')
        <div class="payment-info">
            <h4>Zahlungsinformationen</h4>
            <p>Bitte überweisen Sie den Betrag von <strong>{{ number_format($booking->total, 2, ',', '.') }} €</strong> unter Angabe der Buchungsnummer <strong>{{ $booking->booking_number }}</strong> auf folgendes Konto:</p>
            <p style="margin-top: 10px;">
                <strong>Kontoinhaber:</strong> Veranstaltungsportal<br>
                <strong>IBAN:</strong> DE89 3704 0044 0532 0130 00<br>
                <strong>BIC:</strong> COBADEFFXXX<br>
                <strong>Verwendungszweck:</strong> {{ $booking->booking_number }}
            </p>
        </div>
    @else
        <div class="payment-info">
            <h4>Zahlungsbestätigung</h4>
            <p>Der Betrag von <strong>{{ number_format($booking->total, 2, ',', '.') }} €</strong> wurde erfolgreich bezahlt.</p>
            <p>Vielen Dank für Ihre Zahlung!</p>
        </div>
    @endif

    <div class="footer">
        <p><strong>Wichtige Hinweise:</strong></p>
        <p>Bitte bringen Sie diese Rechnung oder Ihre Buchungsnummer zum Event mit.</p>
        <p>Bei Fragen zur Rechnung wenden Sie sich bitte an {{ $event->organization?->email ?? config('mail.from.address') }}</p>
        <p style="margin-top: 20px;">
            <small>
                Dies ist eine maschinell erstellte Rechnung und bedarf keiner Unterschrift.<br>
                {{ config('app.name') }} | USt-IdNr.: DE123456789 | Steuernummer: 12/345/67890
            </small>
        </p>
    </div>
</body>
</html>

