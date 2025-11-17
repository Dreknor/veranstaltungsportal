<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buchungsbestätigung</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="color: white; margin: 0; font-size: 28px;">Buchungsbestätigung</h1>
        <p style="color: rgba(255,255,255,0.9); margin: 10px 0 0 0;">{{ config('app.name') }}</p>
    </div>

    <div style="background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px;">
        <p style="font-size: 16px; margin-bottom: 20px;">
            Liebe/r {{ $booking->customer_name }},
        </p>

        <p style="font-size: 16px; margin-bottom: 20px;">
            vielen Dank für Ihre Buchung! Wir freuen uns, Sie bei folgender Veranstaltung begrüßen zu dürfen:
        </p>

        <!-- Event Information -->
        <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #667eea;">
            <h2 style="color: #667eea; margin: 0 0 15px 0; font-size: 20px;">{{ $booking->event->title }}</h2>

            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; color: #666; width: 140px;">
                        <strong>📅 Datum:</strong>
                    </td>
                    <td style="padding: 8px 0;">
                        {{ $booking->event->start_date->format('d.m.Y, H:i') }} Uhr
                    </td>
                </tr>

                @if($booking->event->isOnline())
                    <tr>
                        <td style="padding: 8px 0; color: #666;">
                            <strong>🌐 Format:</strong>
                        </td>
                        <td style="padding: 8px 0;">
                            <span style="background: #e7f3ff; padding: 4px 12px; border-radius: 4px; color: #0066cc; font-weight: 600;">
                                Online-Veranstaltung
                            </span>
                        </td>
                    </tr>
                    @if($booking->payment_status === 'paid')
                    <tr>
                        <td style="padding: 8px 0; color: #666;">
                            <strong>🔗 Zugangslink:</strong>
                        </td>
                        <td style="padding: 8px 0;">
                            <a href="{{ $booking->event->online_url }}" style="color: #667eea; text-decoration: none; word-break: break-all;">
                                {{ $booking->event->online_url }}
                            </a>
                        </td>
                    </tr>
                    @if($booking->event->online_access_code)
                    <tr>
                        <td style="padding: 8px 0; color: #666;">
                            <strong>🔑 Zugangscode:</strong>
                        </td>
                        <td style="padding: 8px 0;">
                            <code style="background: #f0f0f0; padding: 4px 8px; border-radius: 4px; font-family: monospace;">
                                {{ $booking->event->online_access_code }}
                            </code>
                        </td>
                    </tr>
                    @endif
                    @else
                    <tr>
                        <td colspan="2" style="padding: 12px 0;">
                            <div style="background: #fff3cd; padding: 12px; border-radius: 4px; border-left: 3px solid #ffc107;">
                                <p style="margin: 0; color: #856404; font-size: 14px;">
                                    ℹ️ Die Zugangsdaten zur Online-Veranstaltung werden Ihnen nach Zahlungseingang zugesendet.
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endif
                @elseif($booking->event->isHybrid())
                    <tr>
                        <td style="padding: 8px 0; color: #666;">
                            <strong>🎯 Format:</strong>
                        </td>
                        <td style="padding: 8px 0;">
                            <span style="background: #e7f3ff; padding: 4px 12px; border-radius: 4px; color: #0066cc; font-weight: 600;">
                                Hybrid-Veranstaltung
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #666;">
                            <strong>📍 Präsenz:</strong>
                        </td>
                        <td style="padding: 8px 0;">
                            {{ $booking->event->venue_name }}<br>
                            {{ $booking->event->venue_address }}<br>
                            {{ $booking->event->venue_postal_code }} {{ $booking->event->venue_city }}
                        </td>
                    </tr>
                    @if($booking->payment_status === 'paid')
                    <tr>
                        <td style="padding: 8px 0; color: #666;">
                            <strong>🔗 Online-Zugang:</strong>
                        </td>
                        <td style="padding: 8px 0;">
                            <a href="{{ $booking->event->online_url }}" style="color: #667eea; text-decoration: none; word-break: break-all;">
                                {{ $booking->event->online_url }}
                            </a>
                            @if($booking->event->online_access_code)
                            <br><br>
                            <strong style="color: #666;">Code:</strong>
                            <code style="background: #f0f0f0; padding: 4px 8px; border-radius: 4px; font-family: monospace; margin-left: 5px;">
                                {{ $booking->event->online_access_code }}
                            </code>
                            @endif
                        </td>
                    </tr>
                    @else
                    <tr>
                        <td colspan="2" style="padding: 12px 0;">
                            <div style="background: #fff3cd; padding: 12px; border-radius: 4px; border-left: 3px solid #ffc107;">
                                <p style="margin: 0; color: #856404; font-size: 14px;">
                                    ℹ️ Die Online-Zugangsdaten werden Ihnen nach Zahlungseingang zugesendet.
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endif
                @else
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
                @endif

                <tr>
                    <td style="padding: 8px 0; color: #666;">
                        <strong>🎫 Buchungsnr.:</strong>
                    </td>
                    <td style="padding: 8px 0;">
                        <strong>{{ $booking->booking_number }}</strong>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;">
                        <strong>🧾 Rechnungsnr.:</strong>
                    </td>
                    <td style="padding: 8px 0;">
                        <strong>{{ $booking->invoice_number ?? 'wird erstellt' }}</strong>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Booking Details -->
        <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
            <h3 style="color: #333; margin: 0 0 15px 0; font-size: 18px;">Buchungsdetails</h3>

            <table style="width: 100%; border-collapse: collapse;">
                @foreach($booking->items as $item)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 10px 0;">{{ $item->ticketType->name }}</td>
                    <td style="padding: 10px 0; text-align: center;">{{ $item->quantity }}x</td>
                    <td style="padding: 10px 0; text-align: right;">{{ number_format($item->price, 2, ',', '.') }} €</td>
                </tr>
                @endforeach

                @if($booking->discount > 0)
                <tr>
                    <td colspan="2" style="padding: 10px 0; color: #28a745;">Rabatt</td>
                    <td style="padding: 10px 0; text-align: right; color: #28a745;">-{{ number_format($booking->discount, 2, ',', '.') }} €</td>
                </tr>
                @endif

                <tr style="border-top: 2px solid #667eea;">
                    <td colspan="2" style="padding: 10px 0;"><strong>Gesamt</strong></td>
                    <td style="padding: 10px 0; text-align: right;"><strong>{{ number_format($booking->total, 2, ',', '.') }} €</strong></td>
                </tr>
            </table>
        </div>

        <!-- Payment Status -->
        <div style="background: {{ $booking->payment_status === 'paid' ? '#d4edda' : '#fff3cd' }};
                    padding: 15px; border-radius: 8px; margin-bottom: 20px;
                    border-left: 4px solid {{ $booking->payment_status === 'paid' ? '#28a745' : '#ffc107' }};">
            <strong>Zahlungsstatus:</strong>
            @if($booking->payment_status === 'paid')
                <span style="color: #28a745;">✓ Bezahlt</span>
            @elseif($booking->payment_status === 'pending')
                <span style="color: #ffc107;">⏳ Ausstehend</span>
            @else
                <span style="color: #666;">{{ ucfirst($booking->payment_status) }}</span>
            @endif
        </div>

        @if($booking->payment_status !== 'paid')
        <!-- Payment Instructions -->
        <div style="background: #e7f3ff; padding: 20px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #0066cc;">
            <h3 style="color: #0066cc; margin: 0 0 10px 0; font-size: 16px;">💳 Zahlungshinweise</h3>
            <p style="margin: 0; color: #333;">
                Bitte überweisen Sie den Betrag von <strong>{{ number_format($booking->total, 2, ',', '.') }} €</strong>
                unter Angabe der Buchungsnummer <strong>{{ $booking->booking_number }}</strong> auf folgendes Konto:
            </p>
            <div style="margin-top: 15px; padding: 15px; background: white; border-radius: 4px;">
                <table style="width: 100%;">
                    <tr>
                        <td style="padding: 5px 0; color: #666; width: 140px;">Empfänger:</td>
                        <td style="padding: 5px 0;"><strong>{{ settings('site_name', 'Bildungsportal') }}</strong></td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0; color: #666;">IBAN:</td>
                        <td style="padding: 5px 0;"><strong>DE89 3704 0044 0532 0130 00</strong></td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0; color: #666;">Verwendungszweck:</td>
                        <td style="padding: 5px 0;"><strong>{{ $booking->booking_number }}</strong></td>
                    </tr>
                </table>
            </div>
            <p style="margin: 15px 0 0 0; font-size: 14px; color: #666;">
                Nach Zahlungseingang erhalten Sie Ihre Tickets per E-Mail.
            </p>
        </div>
        @endif

        <!-- Attachments Info -->
        <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
            <h3 style="color: #333; margin: 0 0 15px 0; font-size: 18px;">📎 Anlagen</h3>
            <ul style="margin: 0; padding-left: 20px;">
                <li style="margin-bottom: 8px;">
                    <strong>Rechnung</strong> - Rechnung_{{ $booking->invoice_number ?? $booking->booking_number }}.pdf
                </li>
                @if($booking->payment_status === 'paid' && !$booking->event->isOnline())
                <li style="margin-bottom: 8px;">
                    <strong>Tickets</strong> - Ticket_{{ $booking->booking_number }}.pdf
                    <span style="color: #28a745; font-size: 12px;">(QR-Code für Check-In)</span>
                </li>
                @endif
            </ul>
        </div>

        <!-- Next Steps -->
        <div style="background: #f0f0f0; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
            <h3 style="color: #333; margin: 0 0 15px 0; font-size: 18px;">📋 Nächste Schritte</h3>
            <ol style="margin: 0; padding-left: 20px; color: #666;">
                @if($booking->payment_status !== 'paid')
                <li style="margin-bottom: 10px;">Überweisung des Betrags mit Angabe der Buchungsnummer</li>
                <li style="margin-bottom: 10px;">
                    Nach Zahlungseingang erhalten Sie
                    @if($booking->event->isOnline())
                        die Zugangsdaten zur Online-Veranstaltung
                    @else
                        Ihre Tickets
                    @endif
                </li>
                @else
                    @if($booking->event->isOnline())
                    <li style="margin-bottom: 10px;">Notieren Sie sich die Zugangsdaten zur Online-Veranstaltung</li>
                    <li style="margin-bottom: 10px;">Testen Sie vorab Ihre Internetverbindung und Ihr Endgerät</li>
                    <li style="margin-bottom: 10px;">Wählen Sie sich einige Minuten vor Beginn in die Online-Veranstaltung ein</li>
                    @elseif($booking->event->isHybrid())
                    <li style="margin-bottom: 10px;">Entscheiden Sie, ob Sie vor Ort oder online teilnehmen</li>
                    <li style="margin-bottom: 10px;">Bei Online-Teilnahme: Nutzen Sie die angegebenen Zugangsdaten</li>
                    <li style="margin-bottom: 10px;">Bei Präsenz-Teilnahme: Bringen Sie Ihre Tickets (ausgedruckt oder digital) mit</li>
                    @else
                    <li style="margin-bottom: 10px;">Bewahren Sie Ihre Tickets sicher auf</li>
                    <li style="margin-bottom: 10px;">Bringen Sie Ihre Tickets (ausgedruckt oder digital) zur Veranstaltung mit</li>
                    <li style="margin-bottom: 10px;">Der QR-Code wird beim Check-In gescannt</li>
                    @endif
                @endif
            </ol>
        </div>

        <!-- Contact Info -->
        <div style="text-align: center; padding: 20px 0; border-top: 2px solid #eee; margin-top: 30px;">
            <p style="margin: 0 0 10px 0; color: #666;">
                Bei Fragen erreichen Sie uns unter:
            </p>
            <p style="margin: 0; font-size: 14px;">
                <strong>{{ settings('contact_email', 'info@bildungsportal.de') }}</strong>
            </p>
        </div>

        <!-- Footer -->
        <div style="text-align: center; padding: 20px; color: #999; font-size: 12px;">
            <p style="margin: 0;">
                © {{ date('Y') }} {{ config('app.name') }}. Alle Rechte vorbehalten.
            </p>
            <p style="margin: 10px 0 0 0;">
                Diese E-Mail wurde automatisch generiert. Bitte antworten Sie nicht direkt auf diese E-Mail.
            </p>
        </div>
    </div>
</body>
</html>

