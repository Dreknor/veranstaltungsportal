<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zahlung bestätigt</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: #f3f4f6;">
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #f3f4f6; padding: 40px 0;">
        <tr>
            <td align="center">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="600" style="max-width: 600px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">

                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #16a34a 0%, #15803d 100%); padding: 40px 30px; text-align: center;">
                            @if($booking->event->organization?->logo)
                                <div style="margin-bottom: 16px;">
                                    <img src="{{ asset('storage/' . $booking->event->organization->logo) }}"
                                         alt="{{ $booking->event->organization->name }} Logo"
                                         style="max-height: 50px; max-width: 160px; object-fit: contain; filter: brightness(0) invert(1);">
                                </div>
                            @endif
                            <h1 style="margin: 0; color: #ffffff; font-size: 26px; font-weight: 700; letter-spacing: -0.5px;">
                                ✅ Zahlung bestätigt
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
                            <p style="margin: 0 0 30px 0; font-size: 16px; color: #374151; line-height: 1.6;">
                                Ihre Zahlung wurde erfolgreich verarbeitet. Vielen Dank!
                            </p>

                            <!-- Event Info -->
                            <div style="background-color: #f0fdf4; border-left: 4px solid #16a34a; padding: 16px 20px; margin: 0 0 24px 0; border-radius: 4px;">
                                <p style="margin: 0; font-size: 17px; font-weight: 700; color: #374151; line-height: 1.4;">
                                    {{ $booking->event->title }}
                                </p>
                                <p style="margin: 6px 0 0 0; font-size: 14px; color: #6b7280;">
                                    📅 {{ $booking->event->start_date->format('d.m.Y') }} um {{ $booking->event->start_date->format('H:i') }} Uhr
                                </p>
                                @if(!$booking->event->isOnline() && $booking->event->venue_city)
                                <p style="margin: 4px 0 0 0; font-size: 14px; color: #6b7280;">
                                    📍 {{ $booking->event->venue_name }}, {{ $booking->event->venue_postal_code }} {{ $booking->event->venue_city }}
                                </p>
                                @endif
                            </div>

                            <!-- Buchungsdetails -->
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
                                @php
                                    $groupedItems = $booking->items->load('ticketType')->groupBy('ticket_type_id');
                                @endphp
                                @foreach($groupedItems as $ticketTypeId => $items)
                                @php
                                    $firstItem = $items->first();
                                    $qty       = $items->sum('quantity');
                                    $unitPrice = (float) $firstItem->price;
                                    $subtotal  = $unitPrice * $qty;
                                @endphp
                                <tr>
                                    <td style="padding: 12px 20px; border-bottom: 1px solid #f3f4f6; color: #6b7280; font-size: 14px; vertical-align: top;">
                                        {{ $firstItem->ticketType?->name ?? 'Ticket' }}
                                    </td>
                                    <td style="padding: 12px 20px; border-bottom: 1px solid #f3f4f6; color: #374151; font-size: 14px;">
                                        {{ $qty }}&times;&nbsp;{{ number_format($unitPrice, 2, ',', '.') }}&nbsp;€@if($qty > 1) = <strong>{{ number_format($subtotal, 2, ',', '.') }}&nbsp;€</strong>@endif
                                    </td>
                                </tr>
                                @endforeach
                                @if($booking->discount > 0)
                                <tr>
                                    <td style="padding: 12px 20px; border-bottom: 1px solid #f3f4f6; color: #6b7280; font-size: 14px;">Rabatt</td>
                                    <td style="padding: 12px 20px; border-bottom: 1px solid #f3f4f6; color: #16a34a; font-size: 14px;">&minus;&nbsp;{{ number_format($booking->discount, 2, ',', '.') }}&nbsp;€</td>
                                </tr>
                                @endif
                                <tr>
                                    <td style="padding: 12px 20px; border-bottom: 1px solid #f3f4f6; color: #6b7280; font-size: 14px;">Gesamtbetrag</td>
                                    <td style="padding: 12px 20px; border-bottom: 1px solid #f3f4f6; color: #374151; font-size: 14px; font-weight: 700;">{{ number_format($booking->total, 2, ',', '.') }}&nbsp;€</td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 20px; color: #6b7280; font-size: 14px;">Zahlungsstatus</td>
                                    <td style="padding: 12px 20px; color: #16a34a; font-size: 14px; font-weight: 700;">✓ Bezahlt</td>
                                </tr>
                            </table>

                            {{-- Tickets / Zugangsdaten --}}
                            @if($booking->event->isOnline())
                                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
                                       style="border: 1px solid #bfdbfe; border-radius: 8px; overflow: hidden; margin-bottom: 24px;">
                                    <tr>
                                        <td style="background-color: #eff6ff; padding: 12px 20px; border-bottom: 1px solid #bfdbfe;">
                                            <strong style="font-size: 14px; color: #1d4ed8;">🌐 Online-Zugangsdaten</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 16px 20px; font-size: 14px; color: #374151; line-height: 1.8;">
                                            @if($booking->event->online_url)
                                                <strong>Zugangslink:</strong><br>
                                                <a href="{{ $booking->event->online_url }}" style="color: #1d4ed8; word-break: break-all;">{{ $booking->event->online_url }}</a>
                                            @endif
                                            @if($booking->event->online_access_code)
                                                <br><br><strong>Zugangscode:</strong>&nbsp;
                                                <code style="background: #f0f0f0; padding: 2px 8px; border-radius: 4px; font-family: monospace;">{{ $booking->event->online_access_code }}</code>
                                            @endif
                                        </td>
                                    </tr>
                                </table>

                            @elseif($booking->event->requires_ticket)
                                @if($booking->canSendTickets())
                                    {{-- Tickets sind tatsächlich als Anhang beigefügt --}}
                                    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
                                           style="border: 1px solid #bbf7d0; border-radius: 8px; overflow: hidden; margin-bottom: 24px;">
                                        <tr>
                                            <td style="background-color: #f0fdf4; padding: 12px 20px; border-bottom: 1px solid #bbf7d0;">
                                                <strong style="font-size: 14px; color: #15803d;">🎫 Ihre Tickets</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 16px 20px; font-size: 14px; color: #374151; line-height: 1.8;">
                                                Ihre Tickets sind dieser E-Mail als <strong>PDF-Anhang</strong> beigefügt.<br>
                                                Bitte drucken Sie diese aus <strong>oder</strong> zeigen Sie sie auf Ihrem Smartphone vor.<br>
                                                Jedes Ticket enthält einen <strong>QR-Code</strong> für den Check-In.
                                            </td>
                                        </tr>
                                    </table>
                                @elseif($booking->needsPersonalization())
                                    {{-- Personalisierung steht noch aus –– keine Tickets angehängt! --}}
                                    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
                                           style="border: 1px solid #fde68a; border-radius: 8px; overflow: hidden; margin-bottom: 24px;">
                                        <tr>
                                            <td style="background-color: #fffbeb; padding: 12px 20px; border-bottom: 1px solid #fde68a;">
                                                <strong style="font-size: 14px; color: #92400e;">👥 Personalisierung erforderlich</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 16px 20px; font-size: 14px; color: #374151; line-height: 1.8;">
                                                Sie haben mehrere Tickets gebucht. Bitte geben Sie für jedes Ticket die Daten des jeweiligen Teilnehmers an.<br>
                                                Die Tickets werden Ihnen <strong>nach der Personalisierung</strong> per E-Mail zugesandt.
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 0 20px 20px 20px;">
                                                <a href="{{ route('bookings.personalize', $booking->booking_number) }}"
                                                   style="display: inline-block; padding: 10px 24px; background-color: #f59e0b; color: #ffffff; text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: 600;">
                                                    Jetzt personalisieren
                                                </a>
                                            </td>
                                        </tr>
                                    </table>
                                @endif
                            @endif

                            {{-- Hinweise des Veranstalters --}}
                            @if($booking->event->ticket_notes)
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
                                   style="border: 1px solid #fde68a; border-radius: 8px; overflow: hidden; margin-bottom: 24px;">
                                <tr>
                                    <td style="background-color: #fffbeb; padding: 12px 20px; border-bottom: 1px solid #fde68a;">
                                        <strong style="font-size: 14px; color: #92400e;">⚠️ Hinweise des Veranstalters</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 16px 20px; font-size: 14px; color: #374151; line-height: 1.7;">
                                        {!! nl2br(e($booking->event->ticket_notes)) !!}
                                    </td>
                                </tr>
                            </table>
                            @endif

                            {{-- Stornierungsrichtlinie --}}
                            @if($booking->event->cancellation_allowed)
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
                                   style="border: 1px solid #bbf7d0; border-radius: 8px; overflow: hidden; margin-bottom: 24px;">
                                <tr>
                                    <td style="background-color: #f0fdf4; padding: 12px 20px; border-bottom: 1px solid #bbf7d0;">
                                        <strong style="font-size: 14px; color: #15803d;">🔄 Stornierung</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 16px 20px; font-size: 14px; color: #374151; line-height: 1.7;">
                                        @if($booking->event->cancellation_days_before !== null)
                                            Sie können Ihre Buchung bis zum
                                            <strong>{{ $booking->event->start_date->copy()->subDays($booking->event->cancellation_days_before)->format('d.m.Y') }}</strong>
                                            ({{ $booking->event->cancellation_days_before }} Tag(e) vor Veranstaltungsbeginn) stornieren.
                                        @else
                                            Sie können Ihre Buchung jederzeit bis zum Beginn der Veranstaltung stornieren.
                                        @endif
                                        <a href="{{ route('bookings.show', $booking->booking_number) }}" style="color: #16a34a; display: block; margin-top: 8px;">
                                            Buchungsdetails aufrufen →
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            @endif


                            <!-- CTA Button -->
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                                <tr>
                                    <td align="center" style="padding: 10px 0 30px 0;">
                                        <a href="{{ route('bookings.show', $booking->booking_number) }}"
                                           style="display: inline-block; padding: 14px 36px; background: linear-gradient(135deg, #16a34a 0%, #15803d 100%); color: #ffffff; text-decoration: none; font-size: 16px; font-weight: 600; border-radius: 8px; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.4);">
                                            Buchungsdetails ansehen
                                        </a>
                                    </td>
                                </tr>
                            </table>
        <!-- Contact Info -->
        <div style="text-align: center; padding: 20px 0; border-top: 2px solid #eee; margin-top: 30px;">
            <p style="margin: 0 0 15px 0; font-size: 16px; color: #333;">
                <strong>Wir freuen uns auf Sie!</strong>
            </p>
            <p style="margin: 0 0 10px 0; color: #666;">
                Bei Fragen erreichen Sie uns unter:
            </p>
            <p style="margin: 0; font-size: 14px;">
                <strong>{{ $booking->event->organization?->email ?? config('mail.from.address') }}</strong>
            </p>
        </div>

        <!-- Footer -->
        <div style="text-align: center; padding: 20px; color: #999; font-size: 12px;">
            <p style="margin: 0;">
                © {{ date('Y') }} {{ config('app.name') }}. Alle Rechte vorbehalten.
            </p>
            <p style="margin: 10px 0 0 0;">
                Buchungsnummer: <strong>{{ $booking->booking_number }}</strong>
            </p>
            <p style="margin: 10px 0 0 0;">
                Diese E-Mail wurde automatisch generiert. Bitte antworten Sie nicht direkt auf diese E-Mail.
            </p>
        </div>
    </div>
</body>
</html>

