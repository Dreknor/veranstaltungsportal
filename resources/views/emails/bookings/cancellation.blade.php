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
        <p>Hallo {{ $booking->customer_name }},</p>

        <p>Ihre Buchung wurde erfolgreich storniert.</p>

        <div class="info-box">
            <h2>{{ $booking->event->title }}</h2>
            <p>
                <strong>Buchungsnummer:</strong> {{ $booking->booking_number }}<br>
                <strong>Storniert am:</strong> {{ $booking->cancelled_at->format('d.m.Y H:i') }} Uhr
            </p>
        </div>

        <p>Falls Sie eine Rückerstattung erwarten, wird diese in den nächsten 5-7 Werktagen bearbeitet.</p>

        <p>Bei Fragen wenden Sie sich bitte an {{ $booking->event->organizer_email ?? config('mail.from.address') }}</p>

        <p>Wir hoffen, Sie bald wieder bei einer unserer Veranstaltungen begrüßen zu dürfen!</p>
    </div>

    <div class="footer">
        <p>Diese E-Mail wurde automatisch generiert. Bitte antworten Sie nicht auf diese E-Mail.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Alle Rechte vorbehalten.</p>
    </div>
</body>
</html>

