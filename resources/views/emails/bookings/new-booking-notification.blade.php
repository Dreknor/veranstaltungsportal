<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Neue Buchung eingegangen</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: #f3f4f6;">
    <!-- Wrapper -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #f3f4f6; padding: 40px 0;">
        <tr>
            <td align="center">
                <!-- Main Container -->
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="600" style="max-width: 600px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">

                    <!-- Header with Gradient -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px 30px; text-align: center;">
                            @if($booking->event->organization?->logo)
                                <div style="margin-bottom: 16px;">
                                    <img src="{{ asset('storage/' . $booking->event->organization->logo) }}"
                                         alt="{{ $booking->event->organization->name }} Logo"
                                         style="max-height: 50px; max-width: 160px; object-fit: contain; filter: brightness(0) invert(1);">
                                </div>
                            @endif
                            <h1 style="margin: 0; color: #ffffff; font-size: 26px; font-weight: 700; letter-spacing: -0.5px;">
                                🎟️ Neue Buchung eingegangen
                            </h1>
                            <p style="margin: 10px 0 0 0; color: rgba(255,255,255,0.85); font-size: 16px;">
                                {{ config('app.name') }}
                            </p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <!-- Greeting -->
                            <p style="margin: 0 0 20px 0; font-size: 16px; color: #374151; line-height: 1.6;">
                                Hallo {{ $organizer->first_name ?? $organizer->name }},
                            </p>

                            <p style="margin: 0 0 30px 0; font-size: 16px; color: #374151; line-height: 1.6;">
                                Sie haben eine neue Buchung für Ihre Veranstaltung erhalten:
                            </p>

                            <!-- Event Info -->
                            <div style="background-color: #f0f4ff; border-left: 4px solid #667eea; padding: 16px 20px; margin: 0 0 24px 0; border-radius: 4px;">
                                <p style="margin: 0; font-size: 17px; font-weight: 700; color: #374151; line-height: 1.4;">
                                    {{ $booking->event->title }}
                                </p>
                                <p style="margin: 6px 0 0 0; font-size: 14px; color: #6b7280;">
                                    📅 {{ $booking->event->start_date->format('d.m.Y') }} um {{ $booking->event->start_date->format('H:i') }} Uhr
                                </p>
                            </div>

                            <!-- Booking Details Table -->
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
                                   style="background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; margin-bottom: 24px;">
                                <tr>
                                    <td colspan="2" style="background-color: #f9fafb; padding: 12px 20px; border-bottom: 1px solid #e5e7eb;">
                                        <strong style="font-size: 14px; color: #374151;">📋 Buchungsdetails</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 20px; border-bottom: 1px solid #f3f4f6; color: #6b7280; font-size: 14px; width: 160px;">
                                        Buchungsnummer
                                    </td>
                                    <td style="padding: 12px 20px; border-bottom: 1px solid #f3f4f6; color: #374151; font-size: 14px; font-weight: 600;">
                                        {{ $booking->booking_number }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 20px; border-bottom: 1px solid #f3f4f6; color: #6b7280; font-size: 14px;">
                                        Teilnehmer
                                    </td>
                                    <td style="padding: 12px 20px; border-bottom: 1px solid #f3f4f6; color: #374151; font-size: 14px;">
                                        {{ $booking->customer_name }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 20px; border-bottom: 1px solid #f3f4f6; color: #6b7280; font-size: 14px;">
                                        E-Mail
                                    </td>
                                    <td style="padding: 12px 20px; border-bottom: 1px solid #f3f4f6; color: #374151; font-size: 14px;">
                                        <a href="mailto:{{ $booking->customer_email }}" style="color: #667eea; text-decoration: none;">
                                            {{ $booking->customer_email }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 20px; border-bottom: 1px solid #f3f4f6; color: #6b7280; font-size: 14px;">
                                        Anzahl Tickets
                                    </td>
                                    <td style="padding: 12px 20px; border-bottom: 1px solid #f3f4f6; color: #374151; font-size: 14px;">
                                        {{ $booking->items->sum('quantity') }} Ticket(s)
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 20px; color: #6b7280; font-size: 14px;">
                                        Gesamtbetrag
                                    </td>
                                    <td style="padding: 12px 20px; color: #374151; font-size: 14px; font-weight: 700;">
                                        {{ number_format($booking->total, 2, ',', '.') }} €
                                    </td>
                                </tr>
                            </table>

                            <!-- CTA Button -->
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                                <tr>
                                    <td align="center" style="padding: 10px 0 30px 0;">
                                        <a href="{{ route('organizer.bookings.show', $booking) }}"
                                           style="display: inline-block; padding: 14px 36px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #ffffff; text-decoration: none; font-size: 16px; font-weight: 600; border-radius: 8px; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);">
                                            Buchung ansehen
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Divider -->
                            <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 10px 0 30px 0;">

                            <p style="margin: 0; font-size: 14px; color: #6b7280; line-height: 1.6;">
                                Diese Benachrichtigung wurde automatisch versandt, nachdem eine Buchung für Ihre Veranstaltung eingegangen ist.
                                Sie können Benachrichtigungseinstellungen in Ihrem Profil anpassen.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 30px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0 0 10px 0; font-size: 14px; color: #374151; line-height: 1.6;">
                                Viele Grüße<br>
                                <strong>Ihr {{ config('app.name') }}-Team</strong>
                            </p>
                            <p style="margin: 20px 0 0 0; font-size: 12px; color: #9ca3af; line-height: 1.5;">
                                © {{ date('Y') }} {{ config('app.name') }}. Alle Rechte vorbehalten.
                            </p>
                            <p style="margin: 10px 0 0 0; font-size: 12px; color: #9ca3af; line-height: 1.5;">
                                Diese E-Mail wurde automatisch generiert. Bitte antworten Sie nicht direkt auf diese E-Mail.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

