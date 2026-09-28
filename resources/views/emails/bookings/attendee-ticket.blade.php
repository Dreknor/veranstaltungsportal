<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $booking->hasTicketDocument() ? 'Ihr Ticket' : 'Ihre Zugangsdaten' }}</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif; background-color: #f3f4f6; color: #333; line-height: 1.6;">
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #f3f4f6; padding: 24px 0;">
    <tr>
        <td align="center" style="padding: 0 12px;">
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 12px; overflow: hidden;">
                <tr>
                    <td style="background-color: #4f46e5; color: #ffffff; padding: 28px 24px; text-align: center;">
                        <h1 style="margin: 0; font-size: 22px;">🎟️ Sie sind angemeldet</h1>
                        <p style="margin: 8px 0 0 0; opacity: 0.9;">{{ $event->title }}</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 24px;">
                        <p style="margin-top: 0;">Hallo {{ $item->participantName() }},</p>
                        <p>
                            {{ $booking->customer_name }}
                            @if($booking->customer_organization) ({{ $booking->customer_organization }}) @endif
                            hat Sie für folgende Veranstaltung angemeldet:
                        </p>

                        <div style="background: #f9fafb; padding: 16px; border-radius: 8px; border: 1px solid #e5e7eb; margin: 16px 0;">
                            <p style="margin: 0 0 6px 0; font-weight: bold; font-size: 17px;">{{ $event->title }}</p>
                            <p style="margin: 2px 0;">📅 {{ $event->start_date->format('d.m.Y') }}, {{ $event->start_date->format('H:i') }} Uhr @if($event->end_date) – {{ $event->end_date->format('H:i') }} Uhr @endif</p>
                            @if($event->hasMultipleDates())
                                <p style="margin: 2px 0; font-size: 14px; color: #4b5563;">Weitere Termine:
                                    {{ $event->dates->where('is_cancelled', false)->skip(1)->map(fn ($d) => $d->start_date->format('d.m.Y H:i'))->implode(', ') }}
                                </p>
                            @endif
                            @if($event->requiresVenue() && $event->venue_name)
                                <p style="margin: 2px 0;">📍 {{ $event->venue_name }}@if($event->venue_address), {{ $event->venue_address }}@endif, {{ trim($event->venue_postal_code . ' ' . $event->venue_city) }}</p>
                            @endif
                            <p style="margin: 2px 0; font-size: 13px; color: #6b7280;">Ticket-Nr. {{ $item->ticket_number }} · Buchung {{ $booking->booking_number }}</p>
                        </div>

                        @if($booking->hasTicketDocument())
                            <div style="background: #f0fdf4; padding: 16px; border-radius: 8px; border-left: 4px solid #16a34a; margin: 16px 0;">
                                Ihr persönliches Ticket ist als <strong>PDF angehängt</strong>. Bitte zeigen Sie es beim Einlass vor (ausgedruckt oder auf dem Smartphone).
                            </div>
                        @endif

                        @if($showOnlineAccess)
                            <div style="background: #eff6ff; padding: 16px; border-radius: 8px; border-left: 4px solid #2563eb; margin: 16px 0;">
                                <p style="margin: 0 0 8px 0; font-weight: bold;">🔗 Online-Zugang</p>
                                <p style="margin: 0 0 8px 0;"><a href="{{ $event->online_url }}" style="color: #1d4ed8; word-break: break-all;">{{ $event->online_url }}</a></p>
                                @if($event->online_access_code)
                                    <p style="margin: 0;">Zugangscode: <code style="background: #ffffff; padding: 2px 8px; border-radius: 4px; border: 1px solid #bfdbfe;">{{ $event->online_access_code }}</code></p>
                                @endif
                            </div>
                        @endif

                        @if($event->ticket_notes)
                            <p style="font-size: 14px;"><strong>Hinweise des Veranstalters:</strong><br>{!! nl2br(e($event->ticket_notes)) !!}</p>
                        @endif

                        <p style="font-size: 14px; color: #4b5563;">
                            Kurz vor Beginn erhalten Sie eine Erinnerung{{ $event->requiresOnlineInfo() ? ' mit den Zugangsdaten' : '' }}.
                            Fragen zur Anmeldung richten Sie bitte an {{ $booking->customer_name }} oder an den Veranstalter
                            {{ $event->getOrganizerName() }}@if($event->getOrganizerEmail()) (<a href="mailto:{{ $event->getOrganizerEmail() }}" style="color: #4f46e5;">{{ $event->getOrganizerEmail() }}</a>)@endif.
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 16px 24px; border-top: 1px solid #e5e7eb; text-align: center; font-size: 12px; color: #9ca3af;">
                        Sie erhalten diese E-Mail, weil Sie als Teilnehmer:in für diese Veranstaltung angemeldet wurden.<br>© {{ date('Y') }} {{ config('app.name') }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
