<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tickets</title>
    <style>
        @include('pdf.partials.styles')
    </style>
</head>
<body>
    @foreach($tickets as $index => $ticket)
    <div class="ticket-container">
        <!-- Header -->
        <div class="header">
            @include('pdf.partials.wordmark')
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
            <p class="qr-hint">
                Bitte zeigen Sie diesen QR-Code beim Check-In vor
            </p>
            <div class="qr-code">
                <img src="{{ $ticket['qrCode'] }}" alt="QR Code">
            </div>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>{{ config('app.name') }} · Hauptfach Mensch</p>
            <p>Ticket generiert am {{ now()->format('d.m.Y H:i') }} Uhr</p>
        </div>
    </div>

    @if($index < count($tickets) - 1)
    <div class="page-break"></div>
    @endif
    @endforeach
</body>
</html>

