<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zahlung bestätigt</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        @if($booking->event->organization?->logo)
            <div style="text-align: center; margin-bottom: 15px;">
                <img src="{{ asset('storage/' . $booking->event->organization->logo) }}"
                     alt="{{ $booking->event->organization->name }} Logo"
                     style="max-height: 40px; max-width: 150px; object-fit: contain; filter: brightness(0) invert(1);">
            </div>
        @endif
        <h1 style="color: white; margin: 0; font-size: 28px;">✓ Zahlung bestätigt!</h1>
        <p style="color: rgba(255,255,255,0.9); margin: 10px 0 0 0;">Ihre Tickets sind bereit</p>
    </div>

    <div style="background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px;">
        <p style="font-size: 16px; margin-bottom: 20px;">
            Liebe/r {{ $booking->customer_name }},
        </p>

        <p style="font-size: 16px; margin-bottom: 20px;">
            großartig! Wir haben Ihre Zahlung erhalten und bestätigt. Ihre Tickets für die folgende Veranstaltung sind nun verfügbar:
        </p>

        <!-- Event Information -->
        <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #28a745;">
            <h2 style="color: #28a745; margin: 0 0 15px 0; font-size: 20px;">{{ $booking->event->title }}</h2>

            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; color: #666; width: 140px;">
                        <strong>📅 Datum:</strong>
                    </td>
                    <td style="padding: 8px 0;">
                        {{ $booking->event->start_date->format('d.m.Y, H:i') }} Uhr
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;">
                        <strong>📍 Ort:</strong>
                    </td>
                    <td style="padding: 8px 0;">
                        {{ $booking->event->venue_name }}<br>
                        {{ $booking->event->venue_address }}<br>
                        {{ $booking->event->venue_postal_code }} {{ $booking->event->venue_city }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;">
                        <strong>🎫 Buchungsnr.:</strong>
                    </td>
                    <td style="padding: 8px 0;">
                        <strong>{{ $booking->booking_number }}</strong>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Payment Confirmation -->
        <div style="background: #d4edda; padding: 20px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #28a745;">
            <h3 style="color: #28a745; margin: 0 0 10px 0; font-size: 18px;">💚 Zahlung erfolgreich</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 5px 0; color: #666; width: 140px;">Betrag:</td>
                    <td style="padding: 5px 0;"><strong>{{ number_format($booking->total, 2, ',', '.') }} €</strong></td>
                </tr>
                <tr>
                    <td style="padding: 5px 0; color: #666;">Status:</td>
                    <td style="padding: 5px 0;"><strong style="color: #28a745;">✓ Bezahlt</strong></td>
                </tr>
            </table>
        </div>

        <!-- Tickets Info -->
        <div style="background: #e7f3ff; padding: 20px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #0066cc;">
            <h3 style="color: #0066cc; margin: 0 0 15px 0; font-size: 18px;">🎫 Ihre Tickets</h3>
            <p style="margin: 0 0 15px 0; color: #333;">
                Ihre Tickets sind dieser E-Mail als PDF-Datei beigefügt. Bitte beachten Sie:
            </p>
            <ul style="margin: 0; padding-left: 20px; color: #666;">
                <li style="margin-bottom: 8px;">
                    Drucken Sie die Tickets aus <strong>ODER</strong> zeigen Sie sie auf Ihrem Smartphone vor
                </li>
                <li style="margin-bottom: 8px;">
                    Jedes Ticket enthält einen eindeutigen <strong>QR-Code</strong> für den Check-In
                </li>
                <li style="margin-bottom: 8px;">
                    Bewahren Sie Ihre Tickets sicher auf
                </li>
                <li style="margin-bottom: 8px;">
                    Bei Verlust wenden Sie sich bitte an uns unter Angabe Ihrer Buchungsnummer
                </li>
            </ul>
        </div>

        <!-- Tickets Overview -->
        <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
            <h3 style="color: #333; margin: 0 0 15px 0; font-size: 18px;">📋 Ticket-Übersicht</h3>

            <table style="width: 100%; border-collapse: collapse;">
                @foreach($booking->items as $item)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 10px 0;">{{ $item->ticketType?->name ?? 'Ticket' }}</td>
                    <td style="padding: 10px 0; text-align: center;"><strong>{{ $item->quantity }}x</strong></td>
                </tr>
                @endforeach
            </table>

            <p style="margin: 15px 0 0 0; padding-top: 15px; border-top: 2px solid #eee; color: #666; font-size: 14px;">
                <strong>Gesamt:</strong> {{ array_sum(array_column($booking->items->toArray(), 'quantity')) }} Ticket(s)
            </p>
        </div>

        <!-- Important Notes -->
        <div style="background: #fff3cd; padding: 20px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #ffc107;">
            <h3 style="color: #856404; margin: 0 0 10px 0; font-size: 16px;">⚠️ Wichtige Hinweise</h3>
            @if($booking->event->ticket_notes)
                <div style="color: #856404; font-size: 14px;">{!! nl2br(e($booking->event->ticket_notes)) !!}</div>
            @else
                <ul style="margin: 0; padding-left: 20px; color: #856404; font-size: 14px;">
                    <li style="margin-bottom: 8px;">
                        Erscheinen Sie bitte ca. 15 Minuten vor Veranstaltungsbeginn
                    </li>
                    @if($booking->event->show_qr_code_on_ticket)
                    <li style="margin-bottom: 8px;">
                        Der QR-Code wird beim Einlass gescannt
                    </li>
                    @endif
                    @if($booking->event->directions)
                    <li style="margin-bottom: 8px;">
                        Anfahrtshinweise: {{ $booking->event->directions }}
                    </li>
                    @endif
                </ul>
            @endif
        </div>

        <!-- Stornierungsrichtlinie -->
        @if($booking->event->cancellation_allowed)
        <div style="background: #f0fff4; padding: 20px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #28a745;">
            <h3 style="color: #155724; margin: 0 0 10px 0; font-size: 16px;">🔄 Stornierungsrichtlinie</h3>
            @if($booking->event->cancellation_days_before !== null)
                <p style="margin: 0; color: #333;">
                    Sie können Ihre Buchung bis
                    <strong>{{ $booking->event->start_date->copy()->subDays($booking->event->cancellation_days_before)->format('d.m.Y') }}</strong>
                    ({{ $booking->event->cancellation_days_before }} Tag(e) vor Veranstaltungsbeginn)
                    selbst stornieren.
                </p>
            @else
                <p style="margin: 0; color: #333;">
                    Sie können Ihre Buchung jederzeit bis zum Beginn der Veranstaltung selbst stornieren.
                </p>
            @endif
            <p style="margin: 10px 0 0 0; font-size: 13px; color: #555;">
                Zur Stornierung rufen Sie bitte Ihre
                <a href="{{ route('bookings.show', $booking->booking_number) }}" style="color: #0066cc;">Buchungsdetails</a>
                auf.
            </p>
        </div>
        @endif

        <!-- Contact Info -->
        <div style="text-align: center; padding: 20px 0; border-top: 2px solid #eee; margin-top: 30px;">
            <p style="margin: 0 0 15px 0; font-size: 16px; color: #333;">
                <strong>Wir freuen uns auf Sie!</strong>
            </p>
            <p style="margin: 0 0 10px 0; color: #666;">
                Bei Fragen erreichen Sie uns unter:
            </p>
            <p style="margin: 0; font-size: 14px;">
                <strong>{{ $booking->event->organization?->email ?? config('mail.from.address') }}</strong>
            </p>
        </div>

        <!-- Footer -->
        <div style="text-align: center; padding: 20px; color: #999; font-size: 12px;">
            <p style="margin: 0;">
                © {{ date('Y') }} {{ config('app.name') }}. Alle Rechte vorbehalten.
            </p>
            <p style="margin: 10px 0 0 0;">
                Buchungsnummer: <strong>{{ $booking->booking_number }}</strong>
            </p>
        </div>
    </div>
</body>
</html>

