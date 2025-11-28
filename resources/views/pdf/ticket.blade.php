<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket - {{ $event->title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
        }

        .container {
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #2563eb;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
        }

        .ticket-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #1f2937;
        }

        .ticket-subtitle {
            font-size: 14px;
            color: #6b7280;
        }

        .ticket-body {
            margin-bottom: 30px;
        }

        .section {
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e5e7eb;
        }

        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            font-weight: bold;
            color: #4b5563;
            padding: 8px 15px 8px 0;
            width: 35%;
        }

        .info-value {
            display: table-cell;
            padding: 8px 0;
            color: #1f2937;
        }

        .qr-section {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            background-color: #f9fafb;
            border-radius: 8px;
        }

        .qr-code {
            margin: 15px auto;
        }

        .qr-code img {
            width: 200px;
            height: 200px;
        }

        .booking-reference {
            font-size: 18px;
            font-weight: bold;
            color: #2563eb;
            margin: 15px 0;
            letter-spacing: 2px;
        }

        .tickets-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .tickets-table th {
            background-color: #f3f4f6;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            color: #374151;
            border-bottom: 2px solid #d1d5db;
        }

        .tickets-table td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        .total-row {
            font-weight: bold;
            background-color: #f9fafb;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
        }

        .important-info {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
        }

        .important-info p {
            margin: 5px 0;
            font-size: 11px;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            background-color: #10b981;
            color: white;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
        }

        .status-cancelled {
            background-color: #ef4444;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            @if($event->organization?->logo)
                <div style="text-align: center; margin-bottom: 15px;">
                    <img src="{{ public_path('storage/' . $event->organization->logo) }}"
                         alt="{{ $event->organization->name }} Logo"
                         style="max-height: 50px; max-width: 200px; object-fit: contain;">
                </div>
            @else
                <div class="logo">Bildungsportal</div>
            @endif
            <div class="ticket-title">Veranstaltungs-Ticket</div>
            <div class="ticket-subtitle">{{ $event->title }}</div>
        </div>

        <!-- Booking Reference -->
        <div class="qr-section">
            <div class="section-title">Buchungsreferenz</div>
            <div class="booking-reference">{{ $booking->booking_number }}</div>
            <div style="margin-top: 10px;">
                @if($booking->status === 'confirmed')
                    <span class="status-badge">Bestätigt</span>
                @elseif($booking->status === 'cancelled')
                    <span class="status-badge status-cancelled">Storniert</span>
                @else
                    <span class="status-badge" style="background-color: #f59e0b;">{{ ucfirst($booking->status) }}</span>
                @endif
            </div>
        </div>

        <!-- Event Information -->
        <div class="section">
            <div class="section-title">Veranstaltungsinformationen</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Veranstaltung:</div>
                    <div class="info-value">{{ $event->title }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Datum:</div>
                    <div class="info-value">{{ $event->start_date->format('d.m.Y') }} - {{ $event->end_date->format('d.m.Y') }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Uhrzeit:</div>
                    <div class="info-value">{{ $event->start_date->format('H:i') }} Uhr</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Ort:</div>
                    <div class="info-value">{{ $event->location }}</div>
                </div>
                @if($event->venue_name)
                <div class="info-row">
                    <div class="info-label">Veranstaltungsort:</div>
                    <div class="info-value">{{ $event->venue_name }}</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Attendee Information -->
        <div class="section">
            <div class="section-title">Teilnehmerinformationen</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Name:</div>
                    <div class="info-value">{{ $booking->customer_name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">E-Mail:</div>
                    <div class="info-value">{{ $booking->customer_email }}</div>
                </div>
                @if($booking->customer_phone)
                <div class="info-row">
                    <div class="info-label">Telefon:</div>
                    <div class="info-value">{{ $booking->customer_phone }}</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Ticket Details -->
        <div class="section">
            <div class="section-title">Ticket-Details</div>
            <table class="tickets-table">
                <thead>
                    <tr>
                        <th>Ticket-Typ</th>
                        <th>Anzahl</th>
                        <th style="text-align: right;">Einzelpreis</th>
                        <th style="text-align: right;">Gesamt</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td>{{ $item->ticketType->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td style="text-align: right;">{{ number_format($item->price, 2, ',', '.') }} €</td>
                        <td style="text-align: right;">{{ number_format($item->subtotal, 2, ',', '.') }} €</td>
                    </tr>
                    @endforeach
                    @if($booking->discount_amount > 0)
                    <tr>
                        <td colspan="3" style="text-align: right;">Rabatt ({{ $booking->discount_code }}):</td>
                        <td style="text-align: right; color: #10b981;">-{{ number_format($booking->discount_amount, 2, ',', '.') }} €</td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td colspan="3" style="text-align: right;">Gesamtbetrag:</td>
                        <td style="text-align: right;">{{ number_format($booking->total_amount, 2, ',', '.') }} €</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- QR Code -->
        <div class="qr-section">
            <div class="section-title">Check-In QR-Code</div>
            <p style="font-size: 11px; color: #6b7280; margin-bottom: 10px;">
                Bitte zeigen Sie diesen QR-Code beim Check-In vor
            </p>
            <div class="qr-code">
                <img src="{{ $qrCode }}" alt="QR Code">
            </div>
        </div>

        <!-- Important Information -->
        <div class="important-info">
            <p><strong>Wichtige Hinweise:</strong></p>
            <p>• Bitte bringen Sie dieses Ticket (ausgedruckt oder digital) zur Veranstaltung mit.</p>
            <p>• Der Check-In erfolgt über den QR-Code am Eingang.</p>
            <p>• Bei Fragen wenden Sie sich bitte an: {{ $event->organization?->email ?? 'support@bildungsportal.de' }}</p>
            @if($event->special_instructions)
            <p>• {{ $event->special_instructions }}</p>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Bildungsportal für Fort- und Weiterbildungen</p>
            <p>Ticket generiert am {{ now()->format('d.m.Y H:i') }} Uhr</p>
            <p>Buchungs-ID: {{ $booking->id }} | Referenz: {{ $booking->booking_number }}</p>
        </div>
    </div>
</body>
</html>

