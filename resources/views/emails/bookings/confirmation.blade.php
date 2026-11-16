@extends('emails.layout')

@php
    $isFreBooking = $booking->total == 0;
    $isExternalInvoicing = $booking->event->organization?->hasExternalInvoicing() ?? false;
    $label =
        'padding: 4px 16px 4px 0; width: 130px; vertical-align: top; font-size: 14px; line-height: 1.5; color: #55676A;';
    $value = 'padding: 4px 0; vertical-align: top; font-size: 15px; line-height: 1.5; color: #1E3135;';
    $row =
        'padding: 8px 0; vertical-align: top; border-bottom: 1px solid #DCE3E2; font-size: 15px; line-height: 1.5; color: #1E3135; font-variant-numeric: tabular-nums;';
    $code =
        'padding: 2px 8px; border: 1px solid #DCE3E2; border-radius: 4px; background-color: #FFFFFF; font-family: Consolas, Menlo, monospace; font-size: 15px;';
    $h2 = 'margin: 8px 0 8px 0; font-size: 18px; line-height: 1.3; font-weight: 600; color: #1E3135;';
    $link = 'color: #1E3135; text-decoration: underline;';
@endphp

@section('title', 'Buchungsbestätigung')

@if ($booking->event->organization?->logo)
    @section('header_logo')
        <img src="{{ asset('storage/' . $booking->event->organization->logo) }}"
            alt="{{ $booking->event->organization->name }} Logo"
            style="display: inline-block; max-height: 40px; max-width: 140px; height: auto; border: 0;">
    @endsection
@endif

@section('heading', 'Buchungsbestätigung')

@section('content')
    <p style="margin: 0 0 16px 0;">
        Liebe/r {{ $booking->customer_name }},
    </p>

    <p style="margin: 0 0 16px 0;">
        vielen Dank für Ihre Buchung! Wir freuen uns, Sie bei folgender Veranstaltung begrüßen zu dürfen:
    </p>

    {{-- Link zur Buchungsbestätigung --}}
    <p style="margin: 0;"><strong>Ihre Buchungsbestätigung:</strong></p>
    @include('emails.partials.button', [
        'url' => $booking->manageUrl(),
        'label' => 'Buchungsdetails ansehen',
        'margin' => '12px 0 8px 0',
    ])
    <p style="margin: 0 0 24px 0; font-size: 14px; color: #55676A;">
        Über diesen Link können Sie jederzeit Ihre vollständigen Buchungsdetails einsehen.
    </p>

    {{-- Veranstaltung --}}
    @component('emails.partials.notice', ['type' => 'info'])
        <p style="margin: 0 0 12px 0; font-size: 18px; line-height: 1.3; font-weight: 600; color: #1E3135;">
            {{ $booking->event->title }}</p>

        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td style="{{ $label }}">Datum</td>
                <td style="{{ $value }}">{{ $booking->event->start_date->format('d.m.Y, H:i') }} Uhr</td>
            </tr>

            @if ($booking->event->isOnline())
                <tr>
                    <td style="{{ $label }}">Format</td>
                    <td style="{{ $value }}">Online-Veranstaltung</td>
                </tr>
                @php
                    $showOnlineAccess = $booking->canAccessOnlineContent();
                @endphp
                @if ($showOnlineAccess && $booking->event->online_url)
                    <tr>
                        <td style="{{ $label }}">Zugangslink</td>
                        <td style="{{ $value }} word-break: break-all;">
                            <a href="{{ $booking->event->online_url }}"
                                style="{{ $link }}">{{ $booking->event->online_url }}</a>
                        </td>
                    </tr>
                @endif
                @if ($showOnlineAccess && $booking->event->online_access_code)
                    <tr>
                        <td style="{{ $label }}">Zugangscode</td>
                        <td style="{{ $value }}"><code
                                style="{{ $code }}">{{ $booking->event->online_access_code }}</code></td>
                    </tr>
                @endif
                @if (!$showOnlineAccess)
                    <tr>
                        <td colspan="2" style="padding: 8px 0 0 0; font-size: 14px; line-height: 1.5; color: #6B4A0A;">
                            Bitte beachten Sie: Die Zugangsdaten werden nach Zahlungseingang freigeschaltet.
                        </td>
                    </tr>
                @endif
            @elseif($booking->event->isHybrid())
                <tr>
                    <td style="{{ $label }}">Format</td>
                    <td style="{{ $value }}">Hybrid-Veranstaltung</td>
                </tr>
                <tr>
                    <td style="{{ $label }}">Präsenz</td>
                    <td style="{{ $value }}">
                        {{ $booking->event->venue_name }}<br>
                        {{ $booking->event->venue_address }}<br>
                        {{ $booking->event->venue_postal_code }} {{ $booking->event->venue_city }}
                    </td>
                </tr>
                @php
                    $showHybridOnlineAccess = $booking->canAccessOnlineContent();
                @endphp
                @if ($showHybridOnlineAccess && $booking->event->online_url)
                    <tr>
                        <td style="{{ $label }}">Online-Zugang</td>
                        <td style="{{ $value }} word-break: break-all;">
                            <a href="{{ $booking->event->online_url }}"
                                style="{{ $link }}">{{ $booking->event->online_url }}</a>
                            @if ($booking->event->online_access_code)
                                <br>
                                <span style="color: #55676A;">Code:</span>
                                <code style="{{ $code }}">{{ $booking->event->online_access_code }}</code>
                            @endif
                        </td>
                    </tr>
                @endif
                @if (!$showHybridOnlineAccess)
                    <tr>
                        <td colspan="2" style="padding: 8px 0 0 0; font-size: 14px; line-height: 1.5; color: #6B4A0A;">
                            Die Online-Zugangsdaten werden nach Zahlungseingang freigeschaltet.
                        </td>
                    </tr>
                @endif
            @else
                <tr>
                    <td style="{{ $label }}">Ort</td>
                    <td style="{{ $value }}">
                        {{ $booking->event->venue_name }}<br>
                        {{ $booking->event->venue_address }}<br>
                        {{ $booking->event->venue_postal_code }} {{ $booking->event->venue_city }}
                    </td>
                </tr>
            @endif

            <tr>
                <td style="{{ $label }}">Buchungsnr.</td>
                <td style="{{ $value }} font-weight: 600;">{{ $booking->booking_number }}</td>
            </tr>
            @if (!$isFreBooking && !$isExternalInvoicing)
                <tr>
                    <td style="{{ $label }}">Rechnungsnr.</td>
                    <td style="{{ $value }} font-weight: 600;">{{ $booking->invoice_number ?? 'wird erstellt' }}</td>
                </tr>
            @endif
        </table>
    @endcomponent

    {{-- Buchungsdetails --}}
    <h2 style="{{ $h2 }}">Buchungsdetails</h2>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
        style="border-top: 1px solid #DCE3E2; margin: 0 0 24px 0;">
        @foreach ($booking->items as $item)
            <tr>
                <td style="{{ $row }}">{{ $item->ticketType?->name ?? 'Ticket' }}</td>
                <td style="{{ $row }} text-align: center; white-space: nowrap;">{{ $item->quantity }}x</td>
                @if (!$isFreBooking)
                    <td style="{{ $row }} text-align: right; white-space: nowrap;">
                        {{ number_format($item->price, 2, ',', '.') }} €</td>
                @endif
            </tr>
        @endforeach

        @if (!$isFreBooking)
            @if ($booking->discount > 0)
                <tr>
                    <td colspan="2" style="{{ $row }}">Rabatt</td>
                    <td style="{{ $row }} text-align: right; white-space: nowrap;">
                        -{{ number_format($booking->discount, 2, ',', '.') }} €</td>
                </tr>
            @endif

            <tr>
                <td colspan="2" style="{{ $row }} font-weight: 600;">Gesamt</td>
                <td style="{{ $row }} text-align: right; white-space: nowrap; font-weight: 600;">
                    {{ number_format($booking->total, 2, ',', '.') }} €</td>
            </tr>
        @else
            <tr>
                <td style="{{ $row }} font-weight: 600;">Kostenfreie Veranstaltung</td>
                <td colspan="2" style="{{ $row }} text-align: right; font-weight: 600;">kostenfrei</td>
            </tr>
        @endif
    </table>

    {{-- Zahlungsstatus nur bei kostenpflichtigen Buchungen anzeigen, aber nicht bei externer Rechnungsstellung --}}
    @if (!$isFreBooking && $booking->payment_status !== 'extern')
        @php
            $paymentNoticeType = match (true) {
                $booking->payment_status === 'paid' => 'success',
                $booking->payment_status === 'pending' && $booking->ticketsReleasedBeforePayment() => 'info',
                $booking->payment_status === 'pending' => 'warning',
                default => 'info',
            };
        @endphp
        @component('emails.partials.notice', ['type' => $paymentNoticeType])
            <p style="margin: 0;">
                <strong>Zahlungsstatus:</strong>
                @if ($booking->payment_status === 'paid')
                    Bezahlt
                @elseif($booking->payment_status === 'pending' && $booking->ticketsReleasedBeforePayment())
                    Rechnung folgt separat
                @elseif($booking->payment_status === 'pending')
                    Ausstehend
                @else
                    {{ ucfirst($booking->payment_status) }}
                @endif
            </p>
        @endcomponent
    @endif

    {{-- Veranstalter --}}
    @php
        $organization = $booking->event->organization;
        $bankAccount = $organization?->bank_account ?? [];
        $billingData = $organization?->billing_data ?? [];
        $organizerName = $organization?->name ?? $booking->event->getOrganizerName();
        $organizerEmail = $organization?->email ?? $booking->event->getOrganizerEmail();
        $organizerPhone = $organization?->phone ?? $booking->event->getOrganizerPhone();
        $organizerTaxId = $organization?->tax_id ?? null;
    @endphp

    <h2 style="{{ $h2 }}">Veranstalter</h2>
    <p style="margin: 0 0 24px 0;">
        <strong>{{ $organizerName }}</strong><br>
        @if (!empty($billingData['company_address']))
            {{ $billingData['company_address'] }}<br>
            {{ $billingData['company_postal_code'] }} {{ $billingData['company_city'] }}<br>
        @endif
        @if (!empty($billingData['company_email']) || $organizerEmail)
            E-Mail: {{ $billingData['company_email'] ?? $organizerEmail }}<br>
        @endif
        @if (!empty($billingData['company_phone']) || $organizerPhone)
            Telefon: {{ $billingData['company_phone'] ?? $organizerPhone }}<br>
        @endif
        @if ($organizerTaxId)
            Steuernummer: {{ $organizerTaxId }}<br>
        @endif
    </p>

    @if ($booking->payment_status !== 'paid' && $booking->payment_status !== 'extern' && !$isFreBooking)
        @if ($isExternalInvoicing)
            @component('emails.partials.notice', ['type' => 'info', 'title' => 'Zahlungshinweise'])
                <p style="margin: 0;">
                    @if ($booking->ticketsReleasedBeforePayment())
                        Ihre Teilnahme ist bereits bestätigt. Die Rechnung erhalten Sie separat vom Veranstalter –
                        gegebenenfalls auch erst nach der Veranstaltung.
                    @else
                        Zur Begleichung Ihrer Buchung erhalten Sie in Kürze eine Rechnung mit allen Zahlungsinformationen direkt
                        vom Veranstalter.
                    @endif
                </p>
                <p style="margin: 8px 0 0 0;">
                    Bitte überweisen Sie den Betrag von
                    <strong>{{ number_format($booking->total, 2, ',', '.') }} €</strong>
                    erst nach Rechnungserhalt. Bankverbindung und Zahlungsfrist entnehmen Sie bitte der Rechnung.
                </p>
                <p style="margin: 8px 0 0 0; font-size: 14px; color: #55676A;">
                    Ihre Buchungsnummer für Rückfragen: <strong style="color: #1E3135;">{{ $booking->booking_number }}</strong>
                </p>
            @endcomponent
        @else
            @component('emails.partials.notice', ['type' => 'info', 'title' => 'Zahlungshinweise'])
                <p style="margin: 0 0 8px 0;">
                    Bitte überweisen Sie den Betrag von <strong>{{ number_format($booking->total, 2, ',', '.') }} €</strong>
                    unter Angabe der Buchungsnummer <strong>{{ $booking->booking_number }}</strong> auf folgendes Konto:
                </p>
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td style="{{ $label }} width: 110px;">Empfänger:</td>
                        <td style="{{ $value }} font-weight: 600; word-break: break-word;">
                            {{ $bankAccount['account_holder'] ?? $organizerName }}</td>
                    </tr>
                    @if (!empty($bankAccount['bank_name']))
                        <tr>
                            <td style="{{ $label }} width: 110px;">Bank:</td>
                            <td style="{{ $value }} font-weight: 600; word-break: break-word;">
                                {{ $bankAccount['bank_name'] }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td style="{{ $label }} width: 110px;">IBAN:</td>
                        <td style="{{ $value }} font-weight: 600; word-break: break-all;">
                            {{ $bankAccount['iban'] ?? 'Wird noch bekannt gegeben' }}</td>
                    </tr>
                    @if (!empty($bankAccount['bic']))
                        <tr>
                            <td style="{{ $label }} width: 110px;">BIC:</td>
                            <td style="{{ $value }} font-weight: 600; word-break: break-word;">
                                {{ $bankAccount['bic'] }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td style="{{ $label }} width: 110px;">Verwendungszweck:</td>
                        <td style="{{ $value }} font-weight: 600; word-break: break-all;">
                            {{ $booking->booking_number }}</td>
                    </tr>
                </table>
                <p style="margin: 12px 0 0 0; font-size: 14px; color: #55676A;">
                    Nach Zahlungseingang erhalten Sie Ihre Tickets per E-Mail.
                </p>
            @endcomponent
        @endif
    @endif

    @if ($booking->needsPersonalization())
        @component('emails.partials.notice', ['type' => 'warning', 'title' => 'Ticket-Personalisierung erforderlich'])
            <p style="margin: 0;">
                Sie haben mehrere Tickets gebucht. Bitte personalisieren Sie Ihre Tickets, damit diese versendet werden können.
            </p>
            @include('emails.partials.button', [
                'url' => route('bookings.personalize', $booking->booking_number),
                'label' => 'Jetzt personalisieren',
                'margin' => '16px 0 0 0',
            ])
        @endcomponent
    @endif

    {{-- Anlagen --}}
    <h2 style="{{ $h2 }}">Anlagen</h2>
    <ul style="margin: 0 0 24px 0; padding-left: 20px;">
        @if (!$isFreBooking && !$isExternalInvoicing)
            <li style="margin: 0 0 8px 0;">
                <strong>Rechnung</strong> – Rechnung_{{ $booking->invoice_number ?? $booking->booking_number }}.pdf
            </li>
        @elseif(!$isFreBooking && $isExternalInvoicing)
            <li style="margin: 0 0 8px 0; color: #55676A;">
                Die Rechnung wird Ihnen separat vom Veranstalter zugestellt.
            </li>
        @endif
        @if ($booking->hasTicketDocument() && $booking->isReadyForParticipation() && $booking->canSendTickets())
            <li style="margin: 0 0 8px 0;">
                <strong>Tickets</strong> – Ticket_{{ $booking->booking_number }}.pdf
                <span style="color: #55676A; font-size: 14px;">(QR-Code für Check-In)</span>
            </li>
        @elseif($booking->hasTicketDocument())
            <li style="margin: 0 0 8px 0; color: #55676A;">
                Ihre Tickets erhalten Sie in einer separaten E-Mail, sobald die Buchung bestätigt, bezahlt und ggf.
                personalisiert ist.
            </li>
        @elseif(!$booking->event->requires_ticket)
            <li style="margin: 0 0 8px 0; color: #55676A;">
                Für diese Veranstaltung ist kein separates Ticket erforderlich.
            </li>
        @endif
    </ul>

    {{-- Nächste Schritte --}}
    <h2 style="{{ $h2 }}">Nächste Schritte</h2>
    <ol style="margin: 0 0 24px 0; padding-left: 20px;">
        @if (!$booking->isReadyForParticipation() && !$isFreBooking)
            @if ($isExternalInvoicing)
                <li style="margin: 0 0 8px 0;">Warten Sie auf die Rechnung vom Veranstalter</li>
                <li style="margin: 0 0 8px 0;">Überweisen Sie den Betrag nach Rechnungserhalt</li>
            @else
                <li style="margin: 0 0 8px 0;">Überweisung des Betrags mit Angabe der Buchungsnummer</li>
                <li style="margin: 0 0 8px 0;">
                    Nach Zahlungseingang erhalten Sie
                    @if ($booking->event->isOnline())
                        die Zugangsdaten zur Online-Veranstaltung
                    @elseif($booking->event->requires_ticket)
                        Ihre Tickets
                    @else
                        eine Zahlungsbestätigung
                    @endif
                </li>
            @endif
        @else
            @if ($booking->event->isOnline())
                <li style="margin: 0 0 8px 0;">Notieren Sie sich die Zugangsdaten zur Online-Veranstaltung</li>
                <li style="margin: 0 0 8px 0;">Testen Sie vorab Ihre Internetverbindung und Ihr Endgerät</li>
                <li style="margin: 0 0 8px 0;">Wählen Sie sich einige Minuten vor Beginn in die Online-Veranstaltung ein
                </li>
            @elseif($booking->event->isHybrid())
                <li style="margin: 0 0 8px 0;">Entscheiden Sie, ob Sie vor Ort oder online teilnehmen</li>
                <li style="margin: 0 0 8px 0;">Bei Online-Teilnahme: Nutzen Sie die angegebenen Zugangsdaten</li>
                @if ($booking->event->requires_ticket)
                    <li style="margin: 0 0 8px 0;">Bei Präsenz-Teilnahme: Bringen Sie Ihre Tickets (ausgedruckt oder
                        digital) mit</li>
                @else
                    <li style="margin: 0 0 8px 0;">Bei Präsenz-Teilnahme: Ihre Buchungsnummer dient als Zugangsnachweis
                    </li>
                @endif
            @else
                @if ($booking->event->requires_ticket)
                    <li style="margin: 0 0 8px 0;">Bewahren Sie Ihre Tickets sicher auf</li>
                    <li style="margin: 0 0 8px 0;">Bringen Sie Ihre Tickets (ausgedruckt oder digital) zur Veranstaltung
                        mit</li>
                    <li style="margin: 0 0 8px 0;">Der QR-Code wird beim Check-In gescannt</li>
                @else
                    <li style="margin: 0 0 8px 0;">Notieren Sie sich Ihre Buchungsnummer:
                        <strong>{{ $booking->booking_number }}</strong>
                    </li>
                    <li style="margin: 0 0 8px 0;">Ihre Buchungsnummer dient als Zugangsnachweis zur Veranstaltung</li>
                    <li style="margin: 0 0 8px 0;">Sie können die Buchungsbestätigung ausgedruckt oder digital vorzeigen
                    </li>
                @endif
            @endif
        @endif
    </ol>

    {{-- Stornierungsrichtlinie --}}
    @if ($booking->event->cancellation_allowed)
        @component('emails.partials.notice', ['type' => 'info', 'title' => 'Stornierungsrichtlinie'])
            @if ($booking->event->cancellation_days_before !== null)
                <p style="margin: 0;">
                    Sie können Ihre Buchung bis
                    <strong>{{ $booking->event->start_date->copy()->subDays($booking->event->cancellation_days_before)->format('d.m.Y') }}</strong>
                    ({{ $booking->event->cancellation_days_before }} Tag(e) vor Veranstaltungsbeginn)
                    selbst stornieren.
                </p>
            @else
                <p style="margin: 0;">
                    Sie können Ihre Buchung jederzeit bis zum Beginn der Veranstaltung selbst stornieren.
                </p>
            @endif
            <p style="margin: 8px 0 0 0; font-size: 14px;">
                Zur Stornierung rufen Sie bitte Ihre
                <a href="{{ $booking->manageUrl() }}" style="{{ $link }}">Buchungsdetails</a>
                auf.
            </p>
        @endcomponent
    @endif

    {{-- Kontakt --}}
    <p style="margin: 0 0 8px 0; font-size: 14px; color: #55676A;">
        Bei Fragen zur Veranstaltung erreichen Sie den Veranstalter unter:
    </p>
    <p style="margin: 0 0 24px 0;">
        <strong>{{ $billingData['company_email'] ?? $organizerEmail }}</strong>
        @if (!empty($billingData['company_phone']) || $organizerPhone)
            <br>Tel: {{ $billingData['company_phone'] ?? $organizerPhone }}
        @endif
    </p>
    @if (!empty(settings('contact_email')))
        <p style="margin: 0 0 24px 0; font-size: 14px; color: #55676A;">
            Allgemeine Fragen an die Plattform: {{ settings('contact_email') }}
        </p>
    @endif

    <p style="margin: 0;">
        Viele Grüße<br>
        Ihr {{ config('app.name') }}-Team
    </p>
@endsection

@section('footer_note', 'Diese E-Mail wurde automatisch generiert. Bitte antworten Sie nicht direkt auf diese E-Mail.')
