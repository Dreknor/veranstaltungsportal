<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $messageSubject }}</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif; background-color: #f3f4f6; color: #333; line-height: 1.6;">
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #f3f4f6; padding: 24px 0;">
    <tr>
        <td align="center" style="padding: 0 12px;">
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 12px; overflow: hidden;">
                <tr>
                    <td style="background-color: #4f46e5; color: #ffffff; padding: 20px 24px;">
                        <p style="margin: 0; font-size: 13px; opacity: 0.85;">Nachricht des Veranstalters zu</p>
                        <p style="margin: 4px 0 0 0; font-size: 18px; font-weight: bold;">{{ $event->title }}</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 24px;">
                        <p style="margin-top: 0;">Hallo {{ $recipientName }},</p>
                        <div style="font-size: 15px;">{!! nl2br(e($messageBody)) !!}</div>
                        <p style="margin-top: 24px; font-size: 14px; color: #4b5563;">
                            Viele Grüße<br>{{ $event->getOrganizerName() }}
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 16px 24px; border-top: 1px solid #e5e7eb; font-size: 13px; color: #6b7280;">
                        <strong>{{ $event->title }}</strong> · {{ $event->start_date->format('d.m.Y H:i') }} Uhr
                        @if($event->requiresVenue() && $event->venue_name) · {{ $event->venue_name }} @endif
                        <br>Buchungsnummer {{ $booking->booking_number }} ·
                        <a href="{{ $booking->manageUrl() }}" style="color: #4f46e5;">Buchung ansehen</a>
                        <br>Antworten auf diese E-Mail gehen direkt an den Veranstalter.
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
