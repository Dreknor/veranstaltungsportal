<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anmeldung nicht bestätigt</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        @if($booking->event->organization?->logo)
            <div style="text-align: center; margin-bottom: 15px;">
                <img src="{{ asset('storage/' . $booking->event->organization->logo) }}"
                     alt="{{ $booking->event->organization->name }} Logo"
                     style="max-height: 40px; max-width: 150px; object-fit: contain; filter: brightness(0) invert(1);">
            </div>
        @endif
        <h1 style="color: white; margin: 0; font-size: 28px;">Anmeldung nicht bestätigt</h1>
        <p style="color: rgba(255,255,255,0.9); margin: 10px 0 0 0;">{{ config('app.name') }}</p>
    </div>

    <div style="background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px;">
        <p style="font-size: 16px; margin-bottom: 20px;">
            Liebe/r {{ $booking->customer_name }},
        </p>

        <p style="font-size: 16px; margin-bottom: 20px;">
            wir bedauern Ihnen mitteilen zu müssen, dass Ihre Anmeldung zur folgenden Veranstaltung leider nicht bestätigt werden konnte:
        </p>

        <!-- Event Information -->
        <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #ef4444;">
            <h2 style="color: #dc2626; margin: 0 0 15px 0; font-size: 20px;">{{ $booking->event->title }}</h2>

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
                        <strong>📋 Buchungsnr.:</strong>
                    </td>
                    <td style="padding: 8px 0; font-weight: bold;">
                        {{ $booking->booking_number }}
                    </td>
                </tr>
            </table>
        </div>

        @if($rejectionReason)
            <div style="background: #fee2e2; border: 1px solid #fca5a5; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <p style="margin: 0; color: #991b1b; font-weight: bold;">Begründung:</p>
                <p style="margin: 8px 0 0 0; color: #7f1d1d;">{{ $rejectionReason }}</p>
            </div>
        @endif

        <p style="font-size: 14px; color: #666; margin-bottom: 20px;">
            Bei Fragen zu dieser Entscheidung wenden Sie sich bitte direkt an den Veranstalter:
        </p>

        <p style="font-size: 14px; color: #666; margin-bottom: 20px;">
            <strong>{{ $booking->event->getOrganizerName() }}</strong>
            @if($booking->event->getOrganizerEmail())
                <br><a href="mailto:{{ $booking->event->getOrganizerEmail() }}" style="color: #ef4444;">{{ $booking->event->getOrganizerEmail() }}</a>
            @endif
        </p>

        <p style="font-size: 12px; color: #999; text-align: center; margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px;">
            © {{ date('Y') }} {{ config('app.name') }}. Alle Rechte vorbehalten.<br>
            Diese E-Mail wurde automatisch generiert. Bitte antworten Sie nicht direkt auf diese E-Mail.
        </p>
    </div>
</body>
</html>

