@php
    $event = $booking->event;
    $isFree = (float) $booking->total <= 0;
    $invoiceLater = $booking->payment_status === 'extern' || $booking->ticketsReleasedBeforePayment();
    $deliverable = $booking->hasTicketDocument() ? 'Tickets' : 'Zugangsdaten';
    $otherDates = $event->hasMultipleDates()
        ? $event->dates->where('is_cancelled', false)->filter(fn ($d) => !$d->start_date->equalTo($event->start_date))->values()
        : collect();
    $venue = collect([$event->venue_name, trim(($event->venue_postal_code ?? '') . ' ' . ($event->venue_city ?? ''))])->filter()->implode(', ');
    $contactEmail = $event->getOrganizerEmail() ?? config('mail.from.address');
    $cell = 'padding: 12px 20px; border-bottom: 1px solid #f3f4f6; font-size: 14px;';
@endphp
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buchung bestätigt</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: #f3f4f6;">
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #f3f4f6; padding: 24px 0;">
    <tr>
        <td align="center" style="padding: 0 12px;">
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 12px; overflow: hidden;">

                {{-- Kopf --}}
                <tr>
                    <td style="background-color: #16a34a; background: linear-gradient(135deg, #16a34a 0%, #15803d 100%); padding: 32px 24px; text-align: center;">
                        @if($event->organization?->logo)
                            <div style="margin-bottom: 16px;">
                                <img src="{{ asset('storage/' . $event->organization->logo) }}"
                                     alt="{{ $event->organization->name }} Logo"
                                     style="max-height: 50px; max-width: 160px; object-fit: contain; filter: brightness(0) invert(1);">
                            </div>
                        @endif
                        <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: 700;">✅ {{ $isFree ? 'Anmeldung bestätigt' : 'Buchung bestätigt' }}</h1>
                        <p style="margin: 8px 0 0 0; color: rgba(255,255,255,0.85); font-size: 15px;">{{ config('app.name') }}</p>
                    </td>
                </tr>

                {{-- Inhalt --}}
                <tr>
                    <td style="padding: 32px 24px;">
                        <p style="margin: 0 0 16px 0; font-size: 16px; color: #374151; line-height: 1.6;">
                            Liebe/r {{ $booking->customer_name }},
                        </p>
                        <p style="margin: 0 0 24px 0; font-size: 16px; color: #374151; line-height: 1.6;">
                            @if($isFree)
                                Ihre Anmeldung ist verbindlich bestätigt.
                            @elseif($invoiceLater)
                                Ihre Buchung ist verbindlich bestätigt. Die Rechnung erhalten Sie separat vom Veranstalter{{ $booking->ticketsReleasedBeforePayment() ? ' – gegebenenfalls auch erst nach der Veranstaltung' : '' }}.
                            @else
                                Ihre Zahlung ist eingegangen – Ihre Buchung ist damit verbindlich bestätigt. Vielen Dank!
                            @endif
                            @if($event->requiresOnlineInfo() && $booking->hasTicketDocument() && $booking->canSendTickets())
                                Unten finden Sie Ihre Online-Zugangsdaten, Ihre Tickets für die Teilnahme vor Ort sind angehängt.
                            @elseif($event->requiresOnlineInfo())
                                Unten finden Sie Ihre Zugangsdaten zur Online-Veranstaltung.
                            @endif
                        </p>

                        {{-- Veranstaltung --}}
                        <div style="background-color: #f0fdf4; border-left: 4px solid #16a34a; padding: 16px 20px; margin: 0 0 24px 0; border-radius: 4px;">
                            <p style="margin: 0; font-size: 17px; font-weight: 700; color: #374151; line-height: 1.4;">{{ $event->title }}</p>
                            <p style="margin: 6px 0 0 0; font-size: 14px; color: #6b7280;">
                                📅 {{ $event->start_date->format('d.m.Y') }} um {{ $event->start_date->format('H:i') }} Uhr
                                @if($otherDates->isNotEmpty())
                                    <br>Weitere Termine: {{ $otherDates->map(fn ($d) => $d->start_date->format('d.m.Y H:i'))->implode(', ') }}
                                @endif
                            </p>
                            @if($event->requiresVenue() && $venue !== '')
                                <p style="margin: 4px 0 0 0; font-size: 14px; color: #6b7280;">📍 {{ $venue }}</p>
                            @endif
                        </div>

                        {{-- Buchungsdetails --}}
                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
                               style="border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; margin-bottom: 24px;">
                            <tr>
                                <td colspan="2" style="background-color: #f9fafb; padding: 12px 20px; border-bottom: 1px solid #e5e7eb;">
                                    <strong style="font-size: 14px; color: #374151;">📋 Buchungsdetails</strong>
                                </td>
                            </tr>
                            <tr>
                                <td style="{{ $cell }} color: #6b7280;">Buchungsnummer</td>
                                <td style="{{ $cell }} color: #374151; font-weight: 600;">{{ $booking->booking_number }}</td>
                            </tr>
                            @foreach($booking->items->loadMissing('ticketType')->groupBy('ticket_type_id') as $items)
                                @php
                                    $qty = $items->sum('quantity');
                                    $unitPrice = (float) $items->first()->price;
                                @endphp
                                <tr>
                                    <td style="{{ $cell }} color: #6b7280; vertical-align: top;">{{ $items->first()->ticketType?->name ?? 'Ticket' }}</td>
                                    <td style="{{ $cell }} color: #374151;">
                                        @if($unitPrice > 0)
                                            {{ $qty }}&times;&nbsp;{{ number_format($unitPrice, 2, ',', '.') }}&nbsp;€@if($qty > 1) = <strong>{{ number_format($unitPrice * $qty, 2, ',', '.') }}&nbsp;€</strong>@endif
                                        @else
                                            {{ $qty }}&times; kostenfrei
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            @if(!$isFree)
                                @if((float) $booking->discount > 0)
                                    <tr>
                                        <td style="{{ $cell }} color: #6b7280;">Rabatt</td>
                                        <td style="{{ $cell }} color: #16a34a;">&minus;&nbsp;{{ number_format((float) $booking->discount, 2, ',', '.') }}&nbsp;€</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td style="{{ $cell }} color: #6b7280;">Gesamtbetrag</td>
                                    <td style="{{ $cell }} color: #374151; font-weight: 700;">{{ number_format((float) $booking->total, 2, ',', '.') }}&nbsp;€</td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 20px; font-size: 14px; color: #6b7280;">Zahlung</td>
                                    <td style="padding: 12px 20px; font-size: 14px; font-weight: 700; color: {{ $invoiceLater ? '#1d4ed8' : '#16a34a' }};">
                                        {{ $invoiceLater ? 'Rechnung folgt separat' : '✓ Bezahlt' }}
                                    </td>
                                </tr>
                            @endif
                        </table>

                        {{-- Online-Zugangsdaten (Online und Hybrid) --}}
                        @if($event->requiresOnlineInfo() && $event->online_url)
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
                                   style="border: 1px solid #bfdbfe; border-radius: 8px; overflow: hidden; margin-bottom: 24px;">
                                <tr>
                                    <td style="background-color: #eff6ff; padding: 12px 20px; border-bottom: 1px solid #bfdbfe;">
                                        <strong style="font-size: 14px; color: #1d4ed8;">🌐 Online-Zugangsdaten</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 16px 20px; font-size: 14px; color: #374151; line-height: 1.8;">
                                        <strong>Zugangslink:</strong><br>
                                        <a href="{{ $event->online_url }}" style="color: #1d4ed8; word-break: break-all;">{{ $event->online_url }}</a>
                                        @if($event->online_access_code)
                                            <br><br><strong>Zugangscode:</strong>&nbsp;
                                            <code style="background: #f0f0f0; padding: 2px 8px; border-radius: 4px; font-family: monospace;">{{ $event->online_access_code }}</code>
                                        @endif
                                        <br><br><span style="color: #6b7280; font-size: 13px;">Sie erhalten die Zugangsdaten zusätzlich mit der Erinnerung kurz vor Beginn.</span>
                                    </td>
                                </tr>
                            </table>
                        @endif

                        {{-- Teilnehmende eintragen (auch bei reinen Online-Veranstaltungen) --}}
                        @if($booking->needsPersonalization())
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
                                   style="border: 1px solid #fde68a; border-radius: 8px; overflow: hidden; margin-bottom: 24px;">
                                <tr>
                                    <td style="background-color: #fffbeb; padding: 12px 20px; border-bottom: 1px solid #fde68a;">
                                        <strong style="font-size: 14px; color: #92400e;">👥 Bitte Teilnehmende eintragen</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 16px 20px; font-size: 14px; color: #374151; line-height: 1.8;">
                                        Sie haben {{ $booking->itemCount() }} Plätze gebucht. Bitte tragen Sie für jeden Platz Name und E-Mail-Adresse der teilnehmenden Person ein.
                                        @if($booking->hasTicketDocument())
                                            Danach erhalten Sie die personalisierten Tickets – und jede Person mit eigener E-Mail-Adresse ihr Ticket direkt.
                                        @else
                                            Danach erhält jede Person mit eigener E-Mail-Adresse die Zugangsdaten direkt.
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 0 20px 20px 20px;">
                                        <a href="{{ route('bookings.personalize', $booking->booking_number) }}"
                                           style="display: inline-block; padding: 10px 24px; background-color: #f59e0b; color: #ffffff; text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: 600;">
                                            Teilnehmende eintragen
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        @elseif($booking->hasTicketDocument() && $booking->canSendTickets())
                            {{-- Tickets sind als Anhang beigefügt --}}
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
                                   style="border: 1px solid #bbf7d0; border-radius: 8px; overflow: hidden; margin-bottom: 24px;">
                                <tr>
                                    <td style="background-color: #f0fdf4; padding: 12px 20px; border-bottom: 1px solid #bbf7d0;">
                                        <strong style="font-size: 14px; color: #15803d;">🎫 Ihre Tickets</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 16px 20px; font-size: 14px; color: #374151; line-height: 1.8;">
                                        Ihre Tickets sind dieser E-Mail als <strong>PDF-Anhang</strong> beigefügt.
                                        Bitte drucken Sie sie aus <strong>oder</strong> zeigen Sie sie auf dem Smartphone vor.
                                        Jedes Ticket enthält einen <strong>QR-Code</strong> für den Check-in.
                                    </td>
                                </tr>
                            </table>
                        @endif

                        {{-- Hinweise des Veranstalters --}}
                        @if($event->ticket_notes)
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
                                   style="border: 1px solid #fde68a; border-radius: 8px; overflow: hidden; margin-bottom: 24px;">
                                <tr>
                                    <td style="background-color: #fffbeb; padding: 12px 20px; border-bottom: 1px solid #fde68a;">
                                        <strong style="font-size: 14px; color: #92400e;">⚠️ Hinweise des Veranstalters</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 16px 20px; font-size: 14px; color: #374151; line-height: 1.7;">
                                        {!! nl2br(e($event->ticket_notes)) !!}
                                    </td>
                                </tr>
                            </table>
                        @endif

                        {{-- Stornierung: nur solange sie noch möglich ist --}}
                        @if($event->canCancelBooking())
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
                                   style="border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 16px 20px; font-size: 14px; color: #374151; line-height: 1.7;">
                                        <strong>🔄 Stornierung:</strong>
                                        @if($event->cancellation_days_before !== null)
                                            möglich bis zum
                                            <strong>{{ $event->start_date->copy()->subDays($event->cancellation_days_before)->format('d.m.Y') }}</strong>
                                            ({{ $event->cancellation_days_before }} Tag(e) vor Veranstaltungsbeginn).
                                        @else
                                            jederzeit bis zum Beginn der Veranstaltung möglich.
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        @endif

                        {{-- Button --}}
                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                            <tr>
                                <td align="center" style="padding: 8px 0 24px 0;">
                                    <a href="{{ $booking->manageUrl() }}"
                                       style="display: inline-block; padding: 14px 32px; background-color: #16a34a; color: #ffffff; text-decoration: none; font-size: 16px; font-weight: 600; border-radius: 8px;">
                                        Buchungsdetails ansehen
                                    </a>
                                </td>
                            </tr>
                        </table>

                        {{-- Kontakt --}}
                        <p style="margin: 0; padding-top: 20px; border-top: 1px solid #e5e7eb; text-align: center; font-size: 15px; color: #374151;">
                            <strong>Wir freuen uns auf Sie!</strong><br>
                            <span style="font-size: 14px; color: #6b7280;">Bei Fragen: {{ $event->getOrganizerName() }} ·
                                <a href="mailto:{{ $contactEmail }}" style="color: #16a34a;">{{ $contactEmail }}</a></span>
                        </p>
                    </td>
                </tr>

                {{-- Fußzeile --}}
                <tr>
                    <td style="padding: 16px 24px; background-color: #f9fafb; text-align: center; color: #9ca3af; font-size: 12px; line-height: 1.6;">
                        Buchungsnummer: <strong>{{ $booking->booking_number }}</strong><br>
                        Diese E-Mail wurde automatisch generiert. Bitte antworten Sie nicht direkt auf diese E-Mail.<br>
                        © {{ date('Y') }} {{ config('app.name') }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
