<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event-Erinnerung</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 10px 10px 0 0; text-align: center;">
        @if($event->organization?->logo)
            <div style="text-align: center; margin-bottom: 15px;">
                <img src="{{ asset('storage/' . $event->organization->logo) }}"
                     alt="{{ $event->organization->name }} Logo"
                     style="max-height: 40px; max-width: 150px; object-fit: contain; filter: brightness(0) invert(1);">
            </div>
        @endif
        <h1 style="margin: 0; font-size: 28px;">⏰ Event-Erinnerung</h1>
        <p style="margin: 10px 0 0 0; font-size: 16px; opacity: 0.9;">Ihr Event startet bald!</p>
    </div>

    <div style="background: #f9fafb; padding: 30px; border-radius: 0 0 10px 10px; border: 1px solid #e5e7eb; border-top: none;">
        <p style="font-size: 16px; margin-top: 0;">Hallo {{ $booking->customer_name }},</p>

        <p style="font-size: 16px;">
            Dies ist eine freundliche Erinnerung, dass Ihr gebuchtes Event
            <strong>in {{ $hoursUntilEvent }} Stunden</strong> beginnt:
        </p>

        <!-- Event Details Card -->
        <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border: 2px solid #667eea;">
            <h2 style="margin: 0 0 15px 0; color: #667eea; font-size: 22px;">
                {{ $event->title }}
            </h2>

            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; vertical-align: top; width: 100px;">
                        <strong style="color: #6b7280;">📅 Datum:</strong>
                    </td>
                    <td style="padding: 8px 0;">
                        {{ $event->start_date->format('d.m.Y') }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; vertical-align: top;">
                        <strong style="color: #6b7280;">🕐 Uhrzeit:</strong>
                    </td>
                    <td style="padding: 8px 0;">
                        {{ $event->start_date->format('H:i') }} Uhr
                        @if($event->end_date)
                            - {{ $event->end_date->format('H:i') }} Uhr
                        @endif
                    </td>
                </tr>
                @if($event->location)
                <tr>
                    <td style="padding: 8px 0; vertical-align: top;">
                        <strong style="color: #6b7280;">📍 Ort:</strong>
                    </td>
                    <td style="padding: 8px 0;">
                        {{ $event->location }}
                    </td>
                </tr>
                @endif
                <tr>
                    <td style="padding: 8px 0; vertical-align: top;">
                        <strong style="color: #6b7280;">🎫 Tickets:</strong>
                    </td>
                    <td style="padding: 8px 0;">
                        {{ $booking->total_tickets }} Ticket(s)
                    </td>
                </tr>
            </table>
        </div>

        <!-- Booking Reference -->
        <div style="background: #fef3c7; padding: 15px; border-radius: 6px; margin: 20px 0; border-left: 4px solid #f59e0b;">
            <p style="margin: 0; font-size: 14px;">
                <strong>Buchungsreferenz:</strong> {{ $booking->booking_number }}
            </p>
        </div>

        <!-- Action Buttons -->
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('bookings.show', $booking) }}"
               style="display: inline-block; background: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; font-weight: bold; margin: 5px;">
                📄 Buchung anzeigen
            </a>
            <a href="{{ route('events.show', $event) }}"
               style="display: inline-block; background: #10b981; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; font-weight: bold; margin: 5px;">
                ℹ️ Event-Details
            </a>
        </div>

        @if($event->description)
        <div style="margin: 20px 0;">
            <h3 style="color: #374151; font-size: 16px; margin-bottom: 10px;">Event-Beschreibung:</h3>
            <div style="color: #6b7280; font-size: 14px; line-height: 1.6;">
                {{ Str::limit(strip_tags($event->description), 200) }}
            </div>
        </div>
        @endif

        <!-- Tips Section -->
        <div style="background: #e0e7ff; padding: 15px; border-radius: 6px; margin: 20px 0;">
            <h3 style="margin: 0 0 10px 0; color: #4338ca; font-size: 16px;">💡 Tipps für Ihr Event:</h3>
            <ul style="margin: 0; padding-left: 20px; color: #4338ca; font-size: 14px;">
                <li>Planen Sie ausreichend Zeit für die Anreise ein</li>
                <li>Halten Sie Ihr Ticket bereit (digital oder ausgedruckt)</li>
                <li>Überprüfen Sie die aktuellen Teilnahmebedingungen</li>
                @if($event->location)
                <li>Speichern Sie die Veranstaltungsadresse in Ihrem Navi</li>
                @endif
            </ul>
        </div>

        <div style="border-top: 2px solid #e5e7eb; margin-top: 30px; padding-top: 20px;">
            <p style="font-size: 14px; color: #6b7280; margin: 0;">
                Wir freuen uns auf Ihre Teilnahme!<br>
                Ihr {{ config('app.name') }}-Team
            </p>
        </div>

        <!-- Footer -->
        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb; text-align: center;">
            <p style="font-size: 12px; color: #9ca3af; margin: 5px 0;">
                Sie erhalten diese E-Mail, weil Sie ein Event gebucht haben.
            </p>
            <p style="font-size: 12px; color: #9ca3af; margin: 5px 0;">
                <a href="{{ route('settings.notifications.edit') }}" style="color: #667eea; text-decoration: none;">
                    E-Mail-Einstellungen verwalten
                </a>
            </p>
        </div>
    </div>
</body>
</html>

