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

        .qr-code img {
            width: 200px;
            height: 200px;
        }

        .ticket-reference {
            font-size: 18px;
            font-weight: bold;
            color: #2563eb;
            margin: 15px 0;
            letter-spacing: 2px;
        }

        .attendee-highlight {
            background-color: #dbeafe;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #2563eb;
            margin: 20px 0;
        }

        .attendee-name {
            font-size: 18px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 5px;
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
            <div class="ticket-title">Persönliches Ticket</div>
            <div class="ticket-subtitle">{{ $event->title }}</div>
        </div>

        <!-- Ticket Number & QR -->
        <div class="qr-section">
            <div class="section-title">Ticket-Nummer</div>
            <div class="ticket-reference">{{ $item->ticket_number }}</div>

            @if($event->show_qr_code_on_ticket && isset($qrCode))
                <div class="qr-code">
                    <img src="{{ $qrCode }}" alt="QR Code">
                </div>
                <p style="font-size: 10px; color: #6b7280; margin-top: 10px;">
                    Bitte zeigen Sie diesen QR-Code beim Check-In vor
                </p>
            @endif
        </div>

        <!-- Attendee Information (Highlighted) -->
        <div class="attendee-highlight">
            <div class="section-title" style="border-bottom: none; margin-bottom: 5px;">Ausgestellt für</div>
            <div class="attendee-name">{{ $item->attendee_name ?? $booking->customer_name }}</div>
            @if($item->attendee_email)
                <div style="color: #4b5563; font-size: 12px;">{{ $item->attendee_email }}</div>
            @endif
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
                @if($event->isOnline())
                <div class="info-row">
                    <div class="info-label">Format:</div>
                    <div class="info-value">Online-Veranstaltung</div>
                </div>
                @if($event->online_url)
                <div class="info-row">
                    <div class="info-label">Online-Zugang:</div>
                    <div class="info-value" style="word-break: break-all; color: #2563eb;">{{ $event->online_url }}</div>
                </div>
                @endif
                @if($event->online_access_code)
                <div class="info-row">
                    <div class="info-label">Zugangscode:</div>
                    <div class="info-value" style="font-family: monospace; background-color: #f3f4f6; padding: 4px 8px; border-radius: 4px;">{{ $event->online_access_code }}</div>
                </div>
                @endif
                @elseif($event->isHybrid())
                <div class="info-row">
                    <div class="info-label">Format:</div>
                    <div class="info-value">Hybrid-Veranstaltung (Präsenz & Online)</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Präsenz-Ort:</div>
                    <div class="info-value">
                        @if($event->venue_name){{ $event->venue_name }}<br>@endif
                        {{ $event->venue_address }}, {{ $event->venue_postal_code }} {{ $event->venue_city }}
                    </div>
                </div>
                @if($event->online_url)
                <div class="info-row">
                    <div class="info-label">Online-Zugang:</div>
                    <div class="info-value" style="word-break: break-all; color: #2563eb;">{{ $event->online_url }}</div>
                </div>
                @endif
                @if($event->online_access_code)
                <div class="info-row">
                    <div class="info-label">Zugangscode:</div>
                    <div class="info-value" style="font-family: monospace; background-color: #f3f4f6; padding: 4px 8px; border-radius: 4px;">{{ $event->online_access_code }}</div>
                </div>
                @endif
                @else
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
                @endif
            </div>
        </div>

        <!-- Ticket Type & Booking -->
        <div class="section">
            <div class="section-title">Ticket-Details</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Ticket-Typ:</div>
                    <div class="info-value">{{ $item->ticketType->name }}</div>
                </div>
                @if($item->ticketType->description)
                <div class="info-row">
                    <div class="info-label">Beschreibung:</div>
                    <div class="info-value">{{ $item->ticketType->description }}</div>
                </div>
                @endif
                <div class="info-row">
                    <div class="info-label">Buchungsnummer:</div>
                    <div class="info-value">{{ $booking->booking_number }}</div>
                </div>
            </div>
        </div>

        <!-- Important Info -->
        <div class="important-info">
            <p><strong>Wichtige Hinweise:</strong></p>
            @if($event->ticket_notes)
                {!! nl2br(e($event->ticket_notes)) !!}
            @else
                <p>• Bringen Sie dieses Ticket (ausgedruckt oder digital) zur Veranstaltung mit</p>
                @if($event->show_qr_code_on_ticket)
                    <p>• Der QR-Code wird beim Check-In gescannt</p>
                @endif
                <p>• Dieses Ticket ist personalisiert und nicht übertragbar</p>
            @endif
            <p>• Bei Fragen wenden Sie sich bitte an den Veranstalter</p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Buchung: {{ $booking->booking_number }}</p>
            <p style="margin-top: 10px;">
                © {{ date('Y') }} {{ config('app.name') }}. Alle Rechte vorbehalten.
            </p>
        </div>
    </div>
</body>
</html>
