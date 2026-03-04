<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buchungsbestätigung</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        @if($booking->event->organization?->logo)
            <div style="text-align: center; margin-bottom: 15px;">
                <img src="{{ asset('storage/' . $booking->event->organization->logo) }}"
                     alt="{{ $booking->event->organization->name }} Logo"
                     style="max-height: 40px; max-width: 150px; object-fit: contain; filter: brightness(0) invert(1);">
            </div>
        @endif
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

        <!-- Booking Confirmation Link -->
        <div style="background: #e7f3ff; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #0066cc;">
            <p style="margin: 0 0 10px 0; color: #333; font-size: 14px;">
                📋 <strong>Ihre Buchungsbestätigung:</strong>
            </p>
            <a href="{{ route('bookings.show', $booking->booking_number) }}"
               style="display: inline-block; padding: 10px 20px; background-color: #0066cc; color: white;
                      text-decoration: none; border-radius: 5px; font-weight: bold;">
                Buchungsdetails ansehen
            </a>
            <p style="margin: 10px 0 0 0; font-size: 12px; color: #666;">
                Über diesen Link können Sie jederzeit Ihre vollständigen Buchungsdetails einsehen.
            </p>
        </div>

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
                    @php
                        $isFreeOnlineNoTicket = $booking->event->isOnline()
                            && !$booking->event->requires_ticket
                            && (is_null($booking->event->price_from) || $booking->event->price_from == 0);
                        $showOnlineAccess = $booking->payment_status === 'paid' || $isFreeOnlineNoTicket;
                    @endphp
                    @if($showOnlineAccess && $booking->event->online_url)
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
                    @endif
                    @if($showOnlineAccess && $booking->event->online_access_code)
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
                    @if(!$showOnlineAccess)
                    <tr>
                        <td colspan="2" style="padding: 12px 0;">
                            <div style="background: #fff3cd; padding: 12px; border-radius: 4px; border-left: 3px solid #ffc107;">
                                <p style="margin: 0; color: #856404; font-size: 14px;">
                                    ℹ️ Bitte beachten Sie: Die Zugangsdaten werden nach Zahlungseingang freigeschaltet.
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
                    @php
                        $isFreeHybrid = !$booking->event->requires_ticket
                            && (is_null($booking->event->price_from) || $booking->event->price_from == 0);
                        $showHybridOnlineAccess = $booking->payment_status === 'paid' || $isFreeHybrid;
                    @endphp
                    @if($showHybridOnlineAccess && $booking->event->online_url)
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
                    @endif
                    @if(!$showHybridOnlineAccess)
                    <tr>
                        <td colspan="2" style="padding: 12px 0;">
                            <div style="background: #fff3cd; padding: 12px; border-radius: 4px; border-left: 3px solid #ffc107;">
                                <p style="margin: 0; color: #856404; font-size: 14px;">
                                    ℹ️ Die Online-Zugangsdaten werden nach Zahlungseingang freigeschaltet.
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
                    <td style="padding: 10px 0;">{{ $item->ticketType?->name ?? 'Ticket' }}</td>
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

        <!-- Organizer Information -->
        @php
            $organization = $booking->event->organization;
            $bankAccount = $organization?->bank_account ?? [];
            $billingData = $organization?->billing_data ?? [];
            $organizerName = $organization?->name ?? $booking->event->getOrganizerName();
            $organizerEmail = $organization?->email ?? $booking->event->getOrganizerEmail();
            $organizerPhone = $organization?->phone ?? $booking->event->getOrganizerPhone();
            $organizerTaxId = $organization?->tax_id ?? null;
        @endphp

        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #6c757d;">
            <h3 style="color: #495057; margin: 0 0 10px 0; font-size: 16px;">🏢 Veranstalter</h3>
            <p style="margin: 0; color: #333; line-height: 1.6;">
                <strong>{{ $organizerName }}</strong><br>
                @if(!empty($billingData['company_address']))
                    {{ $billingData['company_address'] }}<br>
                    {{ $billingData['company_postal_code'] }} {{ $billingData['company_city'] }}<br>
                @endif
                @if(!empty($billingData['company_email']) || $organizerEmail)
                    E-Mail: {{ $billingData['company_email'] ?? $organizerEmail }}<br>
                @endif
                @if(!empty($billingData['company_phone']) || $organizerPhone)
                    Telefon: {{ $billingData['company_phone'] ?? $organizerPhone }}<br>
                @endif
                @if($organizerTaxId)
                    Steuernummer: {{ $organizerTaxId }}<br>
                @endif
            </p>
        </div>

        @if($booking->payment_status !== 'paid')
        <!-- Payment Instructions -->
        <div style="background: #e7f3ff; padding: 20px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #0066cc;">
            <h3 style="color: #0066cc; margin: 0 0 10px 0; font-size: 16px;">💳 Zahlungshinweise</h3>
            <p style="margin: 0; color: #333;">
                Bitte überweisen Sie den Betrag von <strong>{{ number_format($booking->total, 2, ',', '.') }} €</strong>
                unter Angabe der Buchungsnummer <strong>{{ $booking->booking_number }}</strong> auf folgendes Konto:
            </p>
                <table style="width: 100%;">
                    <tr>
                        <td style="padding: 5px 0; color: #666; width: 140px;">Empfänger:</td>
                        <td style="padding: 5px 0;"><strong>{{ $bankAccount['account_holder'] ?? $organizerName }}</strong></td>
                    </tr>
                    @if(!empty($bankAccount['bank_name']))
                    <tr>
                        <td style="padding: 5px 0; color: #666;">Bank:</td>
                        <td style="padding: 5px 0;"><strong>{{ $bankAccount['bank_name'] }}</strong></td>
                    </tr>
                    @endif
                    <tr>
                        <td style="padding: 5px 0; color: #666;">IBAN:</td>
                        <td style="padding: 5px 0;"><strong>{{ $bankAccount['iban'] ?? 'Wird noch bekannt gegeben' }}</strong></td>
                    </tr>
                    @if(!empty($bankAccount['bic']))
                    <tr>
                        <td style="padding: 5px 0; color: #666;">BIC:</td>
                        <td style="padding: 5px 0;"><strong>{{ $bankAccount['bic'] }}</strong></td>
                    </tr>
                    @endif
                    <tr>
                        <td style="padding: 5px 0; color: #666;">Verwendungszweck:</td>
                        <td style="padding: 5px 0;"><strong>{{ $booking->booking_number }}</strong></td>
                    </tr>
                </table>
            <p style="margin: 15px 0 0 0; font-size: 14px; color: #666;">
                Nach Zahlungseingang erhalten Sie Ihre Tickets per E-Mail.
            </p>
        </div>
        @elseif($booking->needsPersonalization())
        <!-- Personalization Required -->
        <div style="background: #fff3cd; padding: 20px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #ffc107;">
            <h3 style="color: #856404; margin: 0 0 10px 0; font-size: 16px;">👥 Ticket-Personalisierung erforderlich</h3>
            <p style="margin: 0 0 15px 0; color: #333;">
                Sie haben mehrere Tickets gebucht. Bitte personalisieren Sie Ihre Tickets, damit diese versendet werden können.
            </p>
            <div style="text-align: center;">
                <a href="{{ route('bookings.personalize', $booking->booking_number) }}"
                   style="display: inline-block; padding: 12px 30px; background-color: #ffc107; color: #000;
                          text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 10px;">
                    Jetzt personalisieren
                </a>
            </div>
        </div>
        @endif

        <!-- Attachments Info -->
        <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
            <h3 style="color: #333; margin: 0 0 15px 0; font-size: 18px;">📎 Anlagen</h3>
            <ul style="margin: 0; padding-left: 20px;">
                <li style="margin-bottom: 8px;">
                    <strong>Rechnung</strong> - Rechnung_{{ $booking->invoice_number ?? $booking->booking_number }}.pdf
                </li>
                @if($booking->event->requires_ticket && $booking->payment_status === 'paid' && !$booking->event->isOnline())
                <li style="margin-bottom: 8px;">
                    <strong>Tickets</strong> - Ticket_{{ $booking->booking_number }}.pdf
                    <span style="color: #28a745; font-size: 12px;">(QR-Code für Check-In)</span>
                </li>
                @elseif(!$booking->event->requires_ticket)
                <li style="margin-bottom: 8px; color: #666;">
                    <em>Für diese Veranstaltung ist kein separates Ticket erforderlich.</em>
                </li>
                @endif
            </ul>
        </div>

        <!-- Next Steps -->
        <div style="background: #f0f0f0; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
            <h3 style="color: #333; margin: 0 0 15px 0; font-size: 18px;">📋 Nächste Schritte</h3>
            <ol style="margin: 0; padding-left: 20px; color: #666;">
                @php
                    $isFreeOnlineNoTicketSteps = $booking->event->isOnline()
                        && !$booking->event->requires_ticket
                        && (is_null($booking->event->price_from) || $booking->event->price_from == 0);
                @endphp
                @if($booking->payment_status !== 'paid' && !$isFreeOnlineNoTicketSteps)
                <li style="margin-bottom: 10px;">Überweisung des Betrags mit Angabe der Buchungsnummer</li>
                <li style="margin-bottom: 10px;">
                    Nach Zahlungseingang erhalten Sie
                    @if($booking->event->isOnline())
                        die Zugangsdaten zur Online-Veranstaltung
                    @elseif($booking->event->requires_ticket)
                        Ihre Tickets
                    @else
                        eine Zahlungsbestätigung
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
                    @if($booking->event->requires_ticket)
                    <li style="margin-bottom: 10px;">Bei Präsenz-Teilnahme: Bringen Sie Ihre Tickets (ausgedruckt oder digital) mit</li>
                    @else
                    <li style="margin-bottom: 10px;">Bei Präsenz-Teilnahme: Ihre Buchungsnummer dient als Zugangsnachweis</li>
                    @endif
                    @else
                    @if($booking->event->requires_ticket)
                    <li style="margin-bottom: 10px;">Bewahren Sie Ihre Tickets sicher auf</li>
                    <li style="margin-bottom: 10px;">Bringen Sie Ihre Tickets (ausgedruckt oder digital) zur Veranstaltung mit</li>
                    <li style="margin-bottom: 10px;">Der QR-Code wird beim Check-In gescannt</li>
                    @else
                    <li style="margin-bottom: 10px;">Notieren Sie sich Ihre Buchungsnummer: <strong>{{ $booking->booking_number }}</strong></li>
                    <li style="margin-bottom: 10px;">Ihre Buchungsnummer dient als Zugangsnachweis zur Veranstaltung</li>
                    <li style="margin-bottom: 10px;">Sie können die Buchungsbestätigung ausgedruckt oder digital vorzeigen</li>
                    @endif
                    @endif
                @endif
            </ol>
        </div>

        <!-- Contact Info -->
        <div style="text-align: center; padding: 20px 0; border-top: 2px solid #eee; margin-top: 30px;">
            <p style="margin: 0 0 10px 0; color: #666;">
                Bei Fragen zur Veranstaltung erreichen Sie den Veranstalter unter:
            </p>
            <p style="margin: 0; font-size: 14px;">
                <strong>{{ $billingData['company_email'] ?? $organizerEmail }}</strong>
                @if(!empty($billingData['company_phone']) || $organizerPhone)
                    <br>Tel: {{ $billingData['company_phone'] ?? $organizerPhone }}
                @endif
            </p>
            @if(!empty(settings('contact_email')))
            <p style="margin: 15px 0 0 0; color: #999; font-size: 12px;">
                Allgemeine Fragen an die Plattform: {{ settings('contact_email') }}
            </p>
            @endif
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

