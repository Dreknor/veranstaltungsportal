<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veranstaltung abgesagt</title>
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
            padding: 30px 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            background-color: #f9fafb;
            padding: 30px 20px;
        }
        .alert-box {
            background-color: #fef2f2;
            border-left: 4px solid #dc2626;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .info-box {
            background-color: white;
            border: 1px solid #e5e7eb;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
        }
        .info-box h2 {
            margin-top: 0;
            color: #1f2937;
        }
        .info-row {
            margin: 10px 0;
            padding: 8px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: bold;
            color: #6b7280;
            display: inline-block;
            width: 150px;
        }
        .reason-box {
            background-color: #fff7ed;
            border: 1px solid #fed7aa;
            padding: 15px;
            margin: 15px 0;
            border-radius: 6px;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            color: #6b7280;
            font-size: 0.9em;
            margin-top: 30px;
            padding: 20px;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>⚠️ Veranstaltung abgesagt</h1>
    </div>

    <div class="content">
        @if($booking)
            <p>Hallo {{ $booking->customer_name }},</p>
        @else
            <p>Hallo,</p>
        @endif

        <div class="alert-box">
            <strong>Wichtige Mitteilung:</strong> Die folgende Veranstaltung wurde leider abgesagt.
        </div>

        <div class="info-box">
            <h2>{{ $event->title }}</h2>

            <div class="info-row">
                <span class="info-label">Datum:</span>
                {{ $event->start_date->format('d.m.Y') }}
            </div>

            <div class="info-row">
                <span class="info-label">Uhrzeit:</span>
                {{ $event->start_date->format('H:i') }} - {{ $event->end_date->format('H:i') }} Uhr
            </div>

            @if($event->venue_name)
            <div class="info-row">
                <span class="info-label">Veranstaltungsort:</span>
                {{ $event->venue_name }}
            </div>
            @endif

            @if($event->cancelled_at)
            <div class="info-row">
                <span class="info-label">Abgesagt am:</span>
                {{ $event->cancelled_at->format('d.m.Y H:i') }} Uhr
            </div>
            @endif

            @if($booking)
            <div class="info-row">
                <span class="info-label">Ihre Buchungsnummer:</span>
                {{ $booking->booking_number }}
            </div>
            @endif
        </div>

        @if($event->cancellation_reason)
        <div class="reason-box">
            <strong>Grund der Absage:</strong><br>
            {{ $event->cancellation_reason }}
        </div>
        @endif

        @if($booking)
        <p><strong>Was passiert jetzt?</strong></p>
        <ul>
            <li>Ihre Buchung wurde automatisch storniert</li>
            <li>Falls Sie bereits bezahlt haben, wird der Betrag in den nächsten 5-7 Werktagen erstattet</li>
            <li>Sie müssen keine weiteren Schritte unternehmen</li>
        </ul>
        @endif

        <p>Wir entschuldigen uns für die entstandenen Unannehmlichkeiten und hoffen, Sie bald bei einer anderen Veranstaltung begrüßen zu dürfen.</p>

        <center>
            <a href="{{ route('events.index') }}" class="button">Weitere Veranstaltungen entdecken</a>
        </center>

        <p>Bei Fragen stehen wir Ihnen gerne zur Verfügung:</p>
        <p>
            @if($event->organizer_email)
                <strong>E-Mail:</strong> {{ $event->organizer_email }}<br>
            @endif
            @if($event->organizer_phone)
                <strong>Telefon:</strong> {{ $event->organizer_phone }}<br>
            @endif
        </p>
    </div>

    <div class="footer">
        <p>Diese E-Mail wurde automatisch generiert.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Alle Rechte vorbehalten.</p>
    </div>
</body>
</html>

