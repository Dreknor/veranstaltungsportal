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
            font-size: 11pt;
            line-height: 1.6;
            color: #333;
        }
        .container {
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 3px solid #3b82f6;
        }
        .company-info h1 {
            color: #3b82f6;
            font-size: 20pt;
            margin-bottom: 5px;
        }
        .company-info p {
            color: #64748b;
            font-size: 9pt;
            line-height: 1.4;
        }
        .invoice-info {
            text-align: right;
        }
        .invoice-info h2 {
            color: #1e293b;
            font-size: 24pt;
            margin-bottom: 10px;
        }
        .invoice-info p {
            color: #64748b;
            font-size: 10pt;
        }
        .addresses {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
        }
        .address-block {
            width: 48%;
        }
        .address-block h3 {
            color: #1e293b;
            font-size: 12pt;
            margin-bottom: 10px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 5px;
        }
        .address-block p {
            color: #475569;
            font-size: 10pt;
            line-height: 1.5;
        }
        .invoice-details {
            background: #f8fafc;
            padding: 15px;
            margin-bottom: 30px;
            border-radius: 4px;
        }
        .invoice-details table {
            width: 100%;
        }
        .invoice-details td {
            padding: 5px 0;
            font-size: 10pt;
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
            margin-bottom: 20px;
        }
        .items-table thead {
            background: #3b82f6;
            color: white;
        }
        .items-table th {
            padding: 12px;
            text-align: left;
            font-size: 10pt;
        }
        .items-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10pt;
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
            margin-bottom: 30px;
        }
        .totals table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals td {
            padding: 8px 12px;
            font-size: 10pt;
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
            font-size: 12pt;
        }
        .totals .total-row td {
            color: white;
            padding: 12px;
        }
        .tax-info {
            background: #f1f5f9;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .tax-info h4 {
            color: #1e293b;
            font-size: 11pt;
            margin-bottom: 10px;
        }
        .tax-info table {
            width: 100%;
            font-size: 9pt;
        }
        .tax-info td {
            padding: 5px;
            color: #475569;
        }
        .payment-info {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin-bottom: 20px;
        }
        .payment-info h4 {
            color: #92400e;
            font-size: 11pt;
            margin-bottom: 10px;
        }
        .payment-info p {
            color: #78350f;
            font-size: 9pt;
            line-height: 1.5;
        }
        .notes {
            margin-bottom: 20px;
        }
        .notes h4 {
            color: #1e293b;
            font-size: 11pt;
            margin-bottom: 10px;
        }
        .notes p {
            color: #64748b;
            font-size: 9pt;
            line-height: 1.5;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e2e8f0;
            text-align: center;
            color: #94a3b8;
            font-size: 8pt;
        }
        .footer p {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <h1>🎓 Bildungsportal</h1>
                <p>
                    Evangelische Schulen in Sachsen<br>
                    Fort- und Weiterbildungen<br>
                    {{ config('app.name') }}
                </p>
            </div>
            <div class="invoice-info">
                <h2>RECHNUNG</h2>
                <p>Nr. {{ $invoiceNumber }}</p>
                <p>Datum: {{ $invoiceDate }}</p>
            </div>
        </div>

        <!-- Addresses -->
        <div class="addresses">
            <div class="address-block">
                <h3>Rechnungsempfänger</h3>
                <p>
                    <strong>{{ $booking->attendee_name }}</strong><br>
                    {{ $booking->attendee_email }}<br>
                    @if($booking->attendee_phone)
                    Tel: {{ $booking->attendee_phone }}<br>
                    @endif
                </p>
            </div>
            <div class="address-block">
                <h3>Veranstalter</h3>
                <p>
                    <strong>{{ $event->organizer->name }}</strong><br>
                    {{ $event->organizer->email }}<br>
                    @if($event->organizer->phone)
                    Tel: {{ $event->organizer->phone }}<br>
                    @endif
                </p>
            </div>
        </div>

        <!-- Invoice Details -->
        <div class="invoice-details">
            <table>
                <tr>
                    <td>Buchungsnummer:</td>
                    <td><strong>{{ $booking->booking_number }}</strong></td>
                </tr>
                <tr>
                    <td>Buchungsdatum:</td>
                    <td>{{ $booking->created_at->format('d.m.Y H:i') }} Uhr</td>
                </tr>
                <tr>
                    <td>Veranstaltung:</td>
                    <td><strong>{{ $event->title }}</strong></td>
                </tr>
                <tr>
                    <td>Veranstaltungsdatum:</td>
                    <td>{{ \Carbon\Carbon::parse($event->start_date)->format('d.m.Y H:i') }} Uhr</td>
                </tr>
                @if($event->location)
                <tr>
                    <td>Veranstaltungsort:</td>
                    <td>{{ $event->location }}</td>
                </tr>
                @endif
            </table>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Position</th>
                    <th>Beschreibung</th>
                    <th class="text-center">Menge</th>
                    <th class="text-right">Einzelpreis</th>
                    <th class="text-right">MwSt. %</th>
                    <th class="text-right">Gesamt</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item['description'] }}</strong><br>
                        <small style="color: #64748b;">Ticket-Typ: {{ $item['ticket_type'] }}</small>
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
                <tr>
                    <td>Zwischensumme (Netto):</td>
                    <td>{{ number_format($netTotal, 2, ',', '.') }} €</td>
                </tr>
                @if($discountAmount > 0)
                <tr>
                    <td>Rabatt:</td>
                    <td>-{{ number_format($discountAmount, 2, ',', '.') }} €</td>
                </tr>
                @endif
                <tr>
                    <td>MwSt. ({{ $taxRate }}%):</td>
                    <td>{{ number_format($taxAmount, 2, ',', '.') }} €</td>
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
                <tr>
                    <td style="width: 25%;"><strong>MwSt.-Satz</strong></td>
                    <td style="width: 25%; text-align: right;"><strong>Netto</strong></td>
                    <td style="width: 25%; text-align: right;"><strong>MwSt.</strong></td>
                    <td style="width: 25%; text-align: right;"><strong>Brutto</strong></td>
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
            <p>
                <strong>Zahlungsstatus:</strong> {{ ucfirst($booking->payment_status) }}<br>
                <strong>Zahlungsmethode:</strong> {{ ucfirst($booking->payment_method ?? 'Überweisung') }}<br>
                <br>
                Bitte überweisen Sie den Betrag von <strong>{{ number_format($totalAmount, 2, ',', '.') }} €</strong> unter Angabe der Buchungsnummer <strong>{{ $booking->booking_number }}</strong> auf folgendes Konto:<br>
                <br>
                <em>Bankverbindung wird vom Veranstalter bereitgestellt.</em>
            </p>
        </div>
        @else
        <div class="payment-info" style="background: #d1fae5; border-left-color: #10b981;">
            <h4 style="color: #065f46;">✅ Zahlungsinformationen</h4>
            <p style="color: #064e3b;">
                <strong>Zahlungsstatus:</strong> Bezahlt<br>
                <strong>Zahlungsmethode:</strong> {{ ucfirst($booking->payment_method ?? 'Überweisung') }}<br>
                <strong>Zahlungsdatum:</strong> {{ $booking->updated_at->format('d.m.Y H:i') }} Uhr<br>
                <br>
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
            <h4>Allgemeine Hinweise</h4>
            <p>
                • Diese Rechnung wurde elektronisch erstellt und ist ohne Unterschrift gültig.<br>
                • Bei Fragen zur Rechnung wenden Sie sich bitte an den Veranstalter.<br>
                • Stornierungen gemäß den Geschäftsbedingungen des Veranstalters.<br>
                • Kleinunternehmerregelung gemäß § 19 UStG (falls zutreffend).<br>
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Bildungsportal für Fort- und Weiterbildungen</p>
            <p>Evangelische Schulen in Sachsen | {{ config('app.url') }}</p>
            <p>Erstellt am {{ now()->format('d.m.Y H:i') }} Uhr</p>
        </div>
    </div>
</body>
</html>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket - {{ $booking->booking_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #333;
        }
        .container {
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #3b82f6;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #3b82f6;
            font-size: 28pt;
            margin-bottom: 10px;
        }
        .header .subtitle {
            color: #64748b;
            font-size: 12pt;
        }
        .ticket-info {
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .ticket-info h2 {
            color: #1e293b;
            font-size: 16pt;
            margin-bottom: 15px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .info-label {
            font-weight: bold;
            color: #475569;
        }
        .info-value {
            color: #1e293b;
        }
        .event-details {
            margin-bottom: 30px;
        }
        .event-details h2 {
            color: #1e293b;
            font-size: 16pt;
            margin-bottom: 15px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 10px;
        }
        .event-info {
            margin-bottom: 15px;
        }
        .event-info .label {
            font-weight: bold;
            color: #64748b;
            display: block;
            margin-bottom: 5px;
        }
        .event-info .value {
            color: #1e293b;
            font-size: 12pt;
        }
        .tickets-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .tickets-table th {
            background: #3b82f6;
            color: white;
            padding: 12px;
            text-align: left;
            font-size: 11pt;
        }
        .tickets-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e2e8f0;
        }
        .tickets-table tr:nth-child(even) {
            background: #f8fafc;
        }
        .qr-section {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            background: white;
            border: 2px dashed #cbd5e1;
            border-radius: 8px;
        }
        .qr-section h3 {
            color: #1e293b;
            margin-bottom: 15px;
        }
        .qr-code {
            margin: 0 auto;
        }
        .verification-code {
            font-size: 14pt;
            font-weight: bold;
            color: #3b82f6;
            margin-top: 15px;
            letter-spacing: 2px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e2e8f0;
            text-align: center;
            color: #64748b;
            font-size: 9pt;
        }
        .total {
            font-size: 14pt;
            font-weight: bold;
            color: #3b82f6;
            text-align: right;
            margin-top: 10px;
        }
        .important-info {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
        }
        .important-info h4 {
            color: #92400e;
            margin-bottom: 10px;
        }
        .important-info p {
            color: #78350f;
            font-size: 10pt;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🎓 Bildungsportal Ticket</h1>
            <div class="subtitle">Fort- und Weiterbildungen für Pädagog:innen</div>
        </div>

        <!-- Ticket Information -->
        <div class="ticket-info">
            <h2>Ticket-Information</h2>
            <div class="info-row">
                <span class="info-label">Buchungsnummer:</span>
                <span class="info-value">{{ $booking->booking_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Status:</span>
                <span class="info-value">{{ ucfirst($booking->status) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Buchungsdatum:</span>
                <span class="info-value">{{ $booking->created_at->format('d.m.Y H:i') }} Uhr</span>
            </div>
        </div>

        <!-- Event Details -->
        <div class="event-details">
            <h2>Veranstaltung</h2>
            <div class="event-info">
                <span class="label">Titel:</span>
                <span class="value">{{ $event->title }}</span>
            </div>
            <div class="event-info">
                <span class="label">Datum:</span>
                <span class="value">{{ \Carbon\Carbon::parse($event->start_date)->format('d.m.Y') }}</span>
                @if($event->end_date && $event->end_date !== $event->start_date)
                    - {{ \Carbon\Carbon::parse($event->end_date)->format('d.m.Y') }}
                @endif
            </div>
            <div class="event-info">
                <span class="label">Uhrzeit:</span>
                <span class="value">
                    {{ \Carbon\Carbon::parse($event->start_date)->format('H:i') }} Uhr
                    @if($event->end_date)
                        - {{ \Carbon\Carbon::parse($event->end_date)->format('H:i') }} Uhr
                    @endif
                </span>
            </div>
            @if($event->location)
            <div class="event-info">
                <span class="label">Ort:</span>
                <span class="value">{{ $event->location }}</span>
            </div>
            @endif
        </div>

        <!-- Attendee Information -->
        <div class="event-details">
            <h2>Teilnehmer:in</h2>
            <div class="event-info">
                <span class="label">Name:</span>
                <span class="value">{{ $booking->attendee_name }}</span>
            </div>
            <div class="event-info">
                <span class="label">E-Mail:</span>
                <span class="value">{{ $booking->attendee_email }}</span>
            </div>
            @if($booking->attendee_phone)
            <div class="event-info">
                <span class="label">Telefon:</span>
                <span class="value">{{ $booking->attendee_phone }}</span>
            </div>
            @endif
        </div>

        <!-- Tickets -->
        <table class="tickets-table">
            <thead>
                <tr>
                    <th>Ticket-Typ</th>
                    <th style="text-align: center;">Anzahl</th>
                    <th style="text-align: right;">Einzelpreis</th>
                    <th style="text-align: right;">Gesamt</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr>
                    <td>{{ $item->ticketType->name }}</td>
                    <td style="text-align: center;">{{ $item->quantity }}</td>
                    <td style="text-align: right;">{{ number_format($item->price, 2, ',', '.') }} €</td>
                    <td style="text-align: right;">{{ number_format($item->subtotal, 2, ',', '.') }} €</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total">
            Gesamtbetrag: {{ number_format($booking->total_amount, 2, ',', '.') }} €
        </div>

        <!-- QR Code -->
        <div class="qr-section">
            <h3>Check-In QR-Code</h3>
            <div class="qr-code">
                <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code" width="200" height="200">
            </div>
            <div class="verification-code">
                Code: {{ $booking->verification_code }}
            </div>
        </div>

        <!-- Important Info -->
        <div class="important-info">
            <h4>⚠️ Wichtige Hinweise</h4>
            <p>
                • Bitte bringen Sie dieses Ticket ausgedruckt oder auf Ihrem Smartphone zum Event mit.<br>
                • Der QR-Code wird beim Check-In gescannt.<br>
                • Bei Fragen wenden Sie sich bitte an: {{ $event->organizer->email }}<br>
                • Stornierungen sind bis zu {{ $event->cancellation_deadline ? \Carbon\Carbon::parse($event->cancellation_deadline)->format('d.m.Y') : '48 Stunden vor Veranstaltungsbeginn' }} möglich.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Bildungsportal für Fort- und Weiterbildungen</p>
            <p>Evangelische Schulen in Sachsen</p>
            <p>Erstellt am {{ now()->format('d.m.Y H:i') }} Uhr</p>
        </div>
    </div>
</body>
</html>

