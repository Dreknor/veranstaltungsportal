<!DOCTYPE html>
<html>
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
        <p>&copy; {{ date('Y') }} Veranstaltungsportal. Alle Rechte vorbehalten.</p>
    </div>
</body>
</html>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buchungsbestätigung</title>
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
            background-color: #2563eb;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .content {
            background-color: #f9fafb;
            padding: 20px;
        }
        .ticket-info {
            background-color: white;
            border: 1px solid #e5e7eb;
            padding: 15px;
            margin: 15px 0;
            border-radius: 8px;
        }
        .price-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }
        .total {
            font-size: 1.2em;
            font-weight: bold;
            border-top: 2px solid #333;
            padding-top: 10px;
            margin-top: 10px;
        }
        .button {
            display: inline-block;
            background-color: #2563eb;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 15px 0;
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
        <h1>Buchungsbestätigung</h1>
    </div>

    <div class="content">
        <p>Hallo {{ $booking->customer_name }},</p>

        <p>Vielen Dank für Ihre Buchung! Ihre Tickets wurden erfolgreich reserviert.</p>

        <div class="ticket-info">
            <h2>{{ $booking->event->title }}</h2>
            <p>
                <strong>Datum:</strong> {{ $booking->event->start_date->format('d.m.Y H:i') }} Uhr<br>
                <strong>Ort:</strong> {{ $booking->event->venue_name }}, {{ $booking->event->venue_address }}, {{ $booking->event->venue_city }}<br>
                <strong>Buchungsnummer:</strong> {{ $booking->booking_number }}
            </p>
        </div>

        <div class="ticket-info">
            <h3>Ihre Tickets:</h3>
            @foreach($booking->items as $item)
                <div class="price-row">
                    <span>{{ $item->ticketType?->name ?? 'Ticket' }}</span>
                    <span>{{ number_format($item->price, 2, ',', '.') }} €</span>
                </div>
            @endforeach

            <div class="price-row">
                <span>Zwischensumme:</span>
                <span>{{ number_format($booking->subtotal, 2, ',', '.') }} €</span>
            </div>

            @if($booking->discount > 0)
                <div class="price-row" style="color: green;">
                    <span>Rabatt:</span>
                    <span>-{{ number_format($booking->discount, 2, ',', '.') }} €</span>
                </div>
            @endif

            <div class="price-row total">
                <span>Gesamt:</span>
                <span>{{ number_format($booking->total, 2, ',', '.') }} €</span>
            </div>
        </div>

        <p style="text-align: center;">
            <a href="{{ route('bookings.show', $booking->booking_number) }}" class="button">
                Buchung anzeigen
            </a>
        </p>

        <p><strong>Wichtige Informationen:</strong></p>
        <ul>
            <li>Bitte bringen Sie diese E-Mail oder Ihre Buchungsnummer zum Event mit</li>
            <li>Der Einlass beginnt 30 Minuten vor Veranstaltungsbeginn</li>
            <li>Bei Fragen kontaktieren Sie uns unter {{ $booking->event->organizer_email ?? config('mail.from.address') }}</li>
        </ul>
    </div>

    <div class="footer">
        <p>Diese E-Mail wurde automatisch generiert. Bitte antworten Sie nicht auf diese E-Mail.</p>
        <p>&copy; {{ date('Y') }} Veranstaltungsportal. Alle Rechte vorbehalten.</p>
    </div>
</body>
</html>

