<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticketart geändert</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td {
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }
        td.num {
            text-align: right;
            white-space: nowrap;
        }
        .muted {
            color: #6b7280;
        }
        .button {
            display: inline-block;
            background-color: #2563eb;
            color: white !important;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 6px;
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
    @php
        $contactEmail = $booking->event->getOrganizerEmail() ?? config('mail.from.address');
        $showPrices = !$booking->isFree() || $totalChanged;
        $money = fn ($amount) => number_format((float) $amount, 2, ',', '.') . ' €';
    @endphp

    <div class="header">
        @if($booking->event->organization?->logo)
            <img src="{{ asset('storage/' . $booking->event->organization->logo) }}"
                 alt="{{ $booking->event->organization->name }} Logo">
        @endif
        <h1>Ticketart geändert</h1>
    </div>

    <div class="content">
        <p>Hallo {{ $booking->customer_name }},</p>

        <p>der Veranstalter hat in Ihrer Buchung <strong>{{ $booking->booking_number }}</strong> für
            <strong>{{ $booking->event->title }}</strong> die Ticketart geändert.</p>

        <div class="info-box">
            <p><strong>Begründung des Veranstalters:</strong> {{ $reason }}</p>
        </div>

        <h2>Änderungen</h2>
        <table>
            @foreach($changes as $change)
                <tr>
                    <td>
                        <strong>{{ $change['attendee'] }}</strong><br>
                        <span class="muted">{{ $change['old_ticket_type'] }} →</span> {{ $change['new_ticket_type'] }}
                    </td>
                    @if($showPrices)
                        <td class="num">
                            @if((float) $change['old_price'] !== (float) $change['new_price'])
                                <span class="muted" style="text-decoration: line-through;">{{ $money($change['old_price']) }}</span><br>
                            @endif
                            {{ $money($change['new_price']) }}
                        </td>
                    @endif
                </tr>
            @endforeach
            @if($totalChanged)
                <tr>
                    <td><strong>Neuer Gesamtbetrag</strong></td>
                    <td class="num"><strong>{{ $money($booking->total) }}</strong></td>
                </tr>
            @endif
        </table>

        @if($priceLocked)
            <p>Der Preis Ihrer bereits bezahlten Buchung bleibt unverändert. Bei Fragen zur Abrechnung wenden Sie sich bitte direkt an den Veranstalter.</p>
        @elseif($totalChanged && $booking->isFree())
            <p>Für Ihre Buchung ist keine Zahlung mehr nötig.</p>
        @elseif($totalChanged && $booking->payment_status === 'extern')
            <p>Die Abrechnung erfolgt weiterhin direkt über den Veranstalter.</p>
        @elseif($totalChanged && (float) $oldTotal == 0.0)
            <p>Ihre Buchung ist damit kostenpflichtig. Die Rechnung und die Zahlungshinweise erhalten Sie in einer separaten E-Mail.</p>
        @elseif($totalChanged && $booking->payment_status === 'pending')
            <p>Bitte zahlen Sie den neuen Gesamtbetrag von <strong>{{ $money($booking->total) }}</strong>{{ $invoiceAttached ? '. Die korrigierte Rechnung finden Sie im Anhang' : '' }}.</p>
        @endif

        @if($booking->hasTicketDocument() && $booking->canSendTickets())
            <p>Im Anhang finden Sie Ihre neu ausgestellten Tickets. Ticketnummern und QR-Codes bleiben gültig, bitte verwenden Sie dennoch die neuen Tickets.</p>
        @endif

        <p><a href="{{ $booking->manageUrl() }}" class="button">Buchungsdetails ansehen</a></p>

        <p>Bei Fragen wenden Sie sich bitte an <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>.</p>

        <p>Viele Grüße<br>Ihr {{ config('app.name') }}-Team</p>
    </div>

    <div class="footer">
        <p>Diese E-Mail wurde automatisch generiert. Bitte antworten Sie nicht auf diese E-Mail.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Alle Rechte vorbehalten.</p>
    </div>
</body>
</html>
