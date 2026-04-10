<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anmeldung eingegangen</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: #f3f4f6;">
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #f3f4f6; padding: 40px 0;">
        <tr>
            <td align="center">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="600" style="max-width: 600px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">

                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); padding: 40px 30px; text-align: center;">
                            @if($booking->event->organization?->logo)
                                <div style="margin-bottom: 16px;">
                                    <img src="{{ asset('storage/' . $booking->event->organization->logo) }}"
                                         alt="{{ $booking->event->organization->name }} Logo"
                                         style="max-height: 50px; max-width: 160px; object-fit: contain; filter: brightness(0) invert(1);">
                                </div>
                            @endif
                            <h1 style="margin: 0; color: #ffffff; font-size: 26px; font-weight: 700; letter-spacing: -0.5px;">
                                ⏳ Anmeldung eingegangen
                            </h1>
                            <p style="margin: 10px 0 0 0; color: rgba(255,255,255,0.85); font-size: 16px;">
                                {{ config('app.name') }}
                            </p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <p style="margin: 0 0 20px 0; font-size: 16px; color: #374151; line-height: 1.6;">
                                Liebe/r {{ $booking->customer_name }},
                            </p>
                            <p style="margin: 0 0 24px 0; font-size: 16px; color: #374151; line-height: 1.6;">
                                vielen Dank für Ihre Anmeldung! Wir haben Ihre Anmeldung erfolgreich erhalten und werden sie in Kürze bearbeiten.
                            </p>

                            <!-- Status-Box -->
                            <div style="background-color: #fffbeb; border-left: 4px solid #f59e0b; padding: 16px 20px; margin: 0 0 24px 0; border-radius: 4px;">
                                <p style="margin: 0; font-weight: 700; color: #92400e; font-size: 15px;">
                                    ⏳ Ihre Anmeldung wird geprüft
                                </p>
                                <p style="margin: 8px 0 0 0; color: #92400e; font-size: 14px; line-height: 1.6;">
                                    Ihre Anmeldung wurde erfolgreich entgegengenommen und wird derzeit vom Veranstalter geprüft.
                                    Sobald Ihre Teilnahme bestätigt wurde, erhalten Sie eine weitere E-Mail mit allen relevanten Informationen.
                                </p>
                            </div>

                            <!-- Event Info -->
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
                                   style="background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; margin-bottom: 24px;">
                                <tr>
                                    <td colspan="2" style="background-color: #f9fafb; padding: 12px 20px; border-bottom: 1px solid #e5e7eb;">
                                        <strong style="font-size: 14px; color: #374151;">📋 Anmeldedetails</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 20px; border-bottom: 1px solid #f3f4f6; color: #6b7280; font-size: 14px; width: 160px;">Veranstaltung</td>
                                    <td style="padding: 12px 20px; border-bottom: 1px solid #f3f4f6; color: #374151; font-size: 14px; font-weight: 600;">{{ $booking->event->title }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 20px; border-bottom: 1px solid #f3f4f6; color: #6b7280; font-size: 14px;">Datum</td>
                                    <td style="padding: 12px 20px; border-bottom: 1px solid #f3f4f6; color: #374151; font-size: 14px;">
                                        {{ $booking->event->start_date->format('d.m.Y') }} um {{ $booking->event->start_date->format('H:i') }} Uhr
                                    </td>
                                </tr>
                                @if(!$booking->event->isOnline() && $booking->event->venue_city)
                                <tr>
                                    <td style="padding: 12px 20px; border-bottom: 1px solid #f3f4f6; color: #6b7280; font-size: 14px;">Ort</td>
                                    <td style="padding: 12px 20px; border-bottom: 1px solid #f3f4f6; color: #374151; font-size: 14px;">
                                        {{ $booking->event->venue_name }}, {{ $booking->event->venue_city }}
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <td style="padding: 12px 20px; color: #6b7280; font-size: 14px;">Buchungsnummer</td>
                                    <td style="padding: 12px 20px; color: #374151; font-size: 14px; font-weight: 600;">{{ $booking->booking_number }}</td>
                                </tr>
                            </table>

                            <!-- CTA Button -->
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                                <tr>
                                    <td align="center" style="padding: 10px 0 30px 0;">
                                        <a href="{{ route('bookings.show', $booking->booking_number) }}"
                                           style="display: inline-block; padding: 14px 36px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: #ffffff; text-decoration: none; font-size: 16px; font-weight: 600; border-radius: 8px; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);">
                                            Anmeldung ansehen
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 10px 0 30px 0;">

                            <p style="margin: 0; font-size: 14px; color: #6b7280; line-height: 1.6;">
                                Bei Fragen wenden Sie sich bitte an den Veranstalter:
                                <strong>{{ $booking->event->getOrganizerName() }}</strong>
                                @if($booking->event->getOrganizerEmail())
                                    (<a href="mailto:{{ $booking->event->getOrganizerEmail() }}" style="color: #f59e0b; text-decoration: none;">{{ $booking->event->getOrganizerEmail() }}</a>)
                                @endif
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

