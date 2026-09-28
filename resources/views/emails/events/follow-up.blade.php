<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danke für Ihre Teilnahme</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif; background-color: #f3f4f6; color: #333; line-height: 1.6;">
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #f3f4f6; padding: 24px 0;">
    <tr>
        <td align="center" style="padding: 0 12px;">
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 12px; overflow: hidden;">
                <tr>
                    <td style="background-color: #059669; color: #ffffff; padding: 28px 24px; text-align: center;">
                        <h1 style="margin: 0; font-size: 22px;">🙏 Danke für Ihre Teilnahme!</h1>
                        <p style="margin: 8px 0 0 0; opacity: 0.9;">{{ $event->title }}</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 24px;">
                        <p style="margin-top: 0;">Hallo {{ $recipientName }},</p>
                        <p>vielen Dank, dass Sie bei <strong>{{ $event->title }}</strong> dabei waren. Wir hoffen, die Fortbildung hat Ihnen neue Impulse für Ihre Arbeit gegeben.</p>

                        @if($certificateItems->isNotEmpty())
                            <div style="background: #f0fdf4; padding: 16px; border-radius: 8px; border-left: 4px solid #16a34a; margin: 16px 0;">
                                <p style="margin: 0 0 6px 0; font-weight: bold;">📜 Teilnahmebescheinigung{{ $certificateItems->count() > 1 ? 'en' : '' }}</p>
                                <p style="margin: 0;">
                                    Im Anhang finden Sie die Bescheinigung{{ $certificateItems->count() > 1 ? 'en für ' . $certificateItems->map->participantName()->implode(', ') : '' }}
                                    @if($event->duration) über {{ $event->getFormattedDuration() }} Fortbildungszeit @endif.
                                </p>
                            </div>
                        @elseif(!$isAttendee)
                            <p style="font-size: 14px; color: #4b5563;">Ihre Teilnahmebescheinigung können Sie jederzeit über Ihre Buchung abrufen.</p>
                        @endif

                        @if($canGiveFeedback)
                            <div style="background: #eff6ff; padding: 16px; border-radius: 8px; border-left: 4px solid #2563eb; margin: 16px 0;">
                                <p style="margin: 0 0 8px 0; font-weight: bold;">⭐ Wie hat es Ihnen gefallen?</p>
                                <p style="margin: 0 0 12px 0;">Ihr Feedback dauert nur eine Minute und hilft dem Veranstalter, zukünftige Angebote zu verbessern.</p>
                                <a href="{{ $booking->manageUrl() }}#feedback"
                                   style="display: inline-block; background: #2563eb; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold;">
                                    Feedback geben
                                </a>
                            </div>
                        @endif

                        <p style="font-size: 14px; color: #4b5563;">
                            Weitere Fortbildungen finden Sie unter <a href="{{ route('events.index') }}" style="color: #059669;">{{ route('events.index') }}</a>.
                        </p>
                        <p style="font-size: 14px; color: #4b5563;">Herzliche Grüße<br>{{ $event->getOrganizerName() }}</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 16px 24px; border-top: 1px solid #e5e7eb; text-align: center; font-size: 12px; color: #9ca3af;">
                        Sie erhalten diese E-Mail einmalig nach der Veranstaltung. © {{ date('Y') }} {{ config('app.name') }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
