<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anmeldung eingegangen</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        @if($booking->event->organization?->logo)
            <div style="text-align: center; margin-bottom: 15px;">
                <img src="{{ asset('storage/' . $booking->event->organization->logo) }}"
                     alt="{{ $booking->event->organization->name }} Logo"
                     style="max-height: 40px; max-width: 150px; object-fit: contain; filter: brightness(0) invert(1);">
            </div>
        @endif
        <h1 style="color: white; margin: 0; font-size: 28px;">Anmeldung eingegangen</h1>
        <p style="color: rgba(255,255,255,0.9); margin: 10px 0 0 0;">{{ config('app.name') }}</p>
    </div>

    <div style="background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px;">
        <p style="font-size: 16px; margin-bottom: 20px;">
            Liebe/r {{ $booking->customer_name }},
        </p>

        <p style="font-size: 16px; margin-bottom: 20px;">
            vielen Dank für Ihre Anmeldung! Wir haben Ihre Anmeldung erfolgreich erhalten und werden sie in Kürze bearbeiten.
        </p>

        <!-- Status-Box -->
        <div style="background: #fef3c7; border: 1px solid #f59e0b; padding: 20px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #f59e0b;">
            <p style="margin: 0; color: #92400e; font-weight: bold; font-size: 15px;">
                ⏳ Ihre Anmeldung wird geprüft
            </p>
            <p style="margin: 10px 0 0 0; color: #92400e; font-size: 14px;">
                Ihre Anmeldung wurde erfolgreich entgegengenommen und wird derzeit vom Veranstalter geprüft.
                Sobald Ihre Teilnahme bestätigt wurde, erhalten Sie eine weitere E-Mail mit allen relevanten
                Informationen und Zugangsdaten.
            </p>
        </div>

        <!-- Event Information -->
        <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #f59e0b;">
            <h2 style="color: #92400e; margin: 0 0 15px 0; font-size: 20px;">{{ $booking->event->title }}</h2>

            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; color: #666; width: 140px;">
                        <strong>📅 Datum:</strong>
                    </td>
                    <td style="padding: 8px 0;">
                        {{ $booking->event->start_date->format('d.m.Y, H:i') }} Uhr
                    </td>
                </tr>
                @if(!$booking->event->isOnline() && $booking->event->venue_city)
                    <tr>
                        <td style="padding: 8px 0; color: #666;">
                            <strong>📍 Ort:</strong>
                        </td>
                        <td style="padding: 8px 0;">
                            {{ $booking->event->venue_name }}, {{ $booking->event->venue_city }}
                        </td>
                    </tr>
                @endif
                <tr>
                    <td style="padding: 8px 0; color: #666;">
                        <strong>📋 Buchungsnr.:</strong>
                    </td>
                    <td style="padding: 8px 0; font-weight: bold;">
                        {{ $booking->booking_number }}
                    </td>
                </tr>
            </table>
        </div>

        <p style="font-size: 14px; color: #666; margin-bottom: 20px;">
            Sie können den Status Ihrer Anmeldung jederzeit über folgenden Link einsehen:
        </p>

        <div style="text-align: center; margin-bottom: 20px;">
            <a href="{{ route('bookings.show', $booking->booking_number) }}"
               style="display: inline-block; padding: 12px 24px; background-color: #f59e0b; color: white;
                      text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 15px;">
                Anmeldung ansehen
            </a>
        </div>

        <p style="font-size: 14px; color: #666; border-top: 1px solid #eee; padding-top: 20px; margin-top: 20px;">
            Bei Fragen wenden Sie sich bitte an den Veranstalter:
            {{ $booking->event->getOrganizerName() }}
            @if($booking->event->getOrganizerEmail())
                (<a href="mailto:{{ $booking->event->getOrganizerEmail() }}" style="color: #f59e0b;">{{ $booking->event->getOrganizerEmail() }}</a>)
            @endif
        </p>

        <p style="font-size: 12px; color: #999; text-align: center; margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px;">
            © {{ date('Y') }} {{ config('app.name') }}. Alle Rechte vorbehalten.<br>
            Diese E-Mail wurde automatisch generiert. Bitte antworten Sie nicht direkt auf diese E-Mail.
        </p>
    </div>
</body>
</html>

