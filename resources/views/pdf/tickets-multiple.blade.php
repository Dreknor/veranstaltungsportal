<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tickets</title>
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

        .page-break {
            page-break-after: always;
        }

        .ticket-container {
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

        .ticket-number {
            font-size: 16px;
            color: #6b7280;
            margin-bottom: 10px;
        }

        .ticket-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #1f2937;
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

        .ticket-type-badge {
            display: inline-block;
            padding: 8px 20px;
            background-color: #dbeafe;
            color: #1e40af;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            margin: 10px 0;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    @foreach($tickets as $index => $ticket)
    <div class="ticket-container">
        <!-- Header -->
        <div class="header">
            <div class="logo">Bildungsportal</div>
            <div class="ticket-number">Ticket {{ $ticket['ticketNumber'] }} von {{ $totalTickets }}</div>
            <div class="ticket-title">{{ $ticket['event']->title }}</div>
        </div>

        <!-- Booking Reference -->
        <div class="qr-section">
            <div class="section-title">Buchungsreferenz</div>
            <div class="booking-reference">{{ $ticket['booking']->booking_number }}</div>
            <div class="ticket-type-badge">{{ $ticket['ticketType']->name }}</div>
        </div>

        <!-- Event Information -->
        <div class="section">
            <div class="section-title">Veranstaltungsinformationen</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Veranstaltung:</div>
                    <div class="info-value">{{ $ticket['event']->title }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Datum:</div>
                    <div class="info-value">{{ $ticket['event']->start_date->format('d.m.Y') }} - {{ $ticket['event']->end_date->format('d.m.Y') }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Uhrzeit:</div>
                    <div class="info-value">{{ $ticket['event']->start_date->format('H:i') }} Uhr</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Ort:</div>
                    <div class="info-value">{{ $ticket['event']->location }}</div>
                </div>
            </div>
        </div>

        <!-- Attendee Information -->
        <div class="section">
            <div class="section-title">Teilnehmerinformationen</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Name:</div>
                    <div class="info-value">{{ $ticket['booking']->attendee_name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">E-Mail:</div>
                    <div class="info-value">{{ $ticket['booking']->attendee_email }}</div>
                </div>
            </div>
        </div>

        <!-- QR Code -->
        @if($ticket['event']->show_qr_code_on_ticket)
        <div class="qr-section">
            <div class="section-title">Check-In QR-Code</div>
            <p style="font-size: 11px; color: #6b7280; margin-bottom: 10px;">
                Bitte zeigen Sie diesen QR-Code beim Check-In vor
            </p>
            <div class="qr-code">
                <img src="{{ $ticket['qrCode'] }}" alt="QR Code">
            </div>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>Bildungsportal für Fort- und Weiterbildungen</p>
            <p>Ticket generiert am {{ now()->format('d.m.Y H:i') }} Uhr</p>
        </div>
    </div>

    @if($index < count($tickets) - 1)
    <div class="page-break"></div>
    @endif
    @endforeach
</body>
</html>

