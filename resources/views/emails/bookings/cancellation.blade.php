<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stornierungsbestätigung</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #dc2626;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .header img {
            max-height: 40px;
            max-width: 150px;
            object-fit: contain;
            filter: brightness(0) invert(1);
            margin-bottom: 10px;
        }
        .content {
            background-color: #f9fafb;
            padding: 20px;
        }
        .info-box {
            background-color: white;
            border: 1px solid #e5e7eb;
            padding: 15px;
            margin: 15px 0;
            border-radius: 8px;
        }
        .footer {
            text-align: center;
            color: #6b7280;
            font-size: 0.9em;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="header">
        @if($booking->event->organization?->logo)
            <img src="{{ asset('storage/' . $booking->event->organization->logo) }}"
                 alt="{{ $booking->event->organization->name }} Logo">
        @endif
        <h1>Stornierungsbestätigung</h1>
    </div>

    <div class="content">
        @php
            $cancelledByOrganizer = in_array($booking->cancelled_by, ['organizer', 'event']);
            $contactEmail = $booking->event->getOrganizerEmail() ?? config('mail.from.address');
        @endphp
        <p>Hallo {{ $booking->customer_name }},</p>

        @if($booking->cancelled_by === 'system')
            <p>Ihre Buchung wurde automatisch storniert.</p>
        @elseif($cancelledByOrganizer)
            <p>Ihre Buchung wurde vom Veranstalter storniert.</p>
        @else
            <p>Ihre Buchung wurde wie gewünscht storniert.</p>
        @endif

        <div class="info-box">
            <h2>{{ $booking->event->title }}</h2>
            <p>
                <strong>Termin:</strong> {{ $booking->event->start_date->format('d.m.Y H:i') }} Uhr<br>
                <strong>Buchungsnummer:</strong> {{ $booking->booking_number }}<br>
                <strong>Storniert am:</strong> {{ ($booking->cancelled_at ?? now())->format('d.m.Y H:i') }} Uhr
            </p>
            @if($booking->cancellation_reason)
                <p><strong>Grund:</strong> {{ $booking->cancellation_reason }}</p>
            @endif
        </div>

        <p>Ihre Tickets bzw. Zugangsdaten sind damit nicht mehr gültig.</p>

        @if((float) $booking->total > 0)
            @if($booking->payment_status === 'paid')
                <p>Bereits gezahlte Beträge werden vom Veranstalter gemäß den Stornobedingungen erstattet.</p>
            @elseif($booking->payment_status === 'pending')
                <p>Eine noch offene Rechnung zu dieser Buchung müssen Sie nicht mehr bezahlen.</p>
            @endif
        @endif

        <p>Bei Fragen wenden Sie sich bitte an <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>.</p>

        <p>Wir hoffen, Sie bald wieder bei einer unserer Veranstaltungen begrüßen zu dürfen!</p>
    </div>

    <div class="footer">
        <p>Diese E-Mail wurde automatisch generiert. Bitte antworten Sie nicht auf diese E-Mail.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Alle Rechte vorbehalten.</p>
    </div>
</body>
</html>

