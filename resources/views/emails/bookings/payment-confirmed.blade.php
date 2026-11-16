@extends('emails.layout')

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
    $label = 'padding: 8px 16px 8px 0; width: 140px; vertical-align: top; border-bottom: 1px solid #DCE3E2; font-size: 14px; line-height: 1.5; color: #55676A;';
    $value = 'padding: 8px 0; vertical-align: top; border-bottom: 1px solid #DCE3E2; font-size: 15px; line-height: 1.5; color: #1E3135; font-variant-numeric: tabular-nums;';
@endphp

@section('title', 'Buchung bestätigt')

@if($event->organization?->logo)
    @section('header_logo')
        <img src="{{ asset('storage/' . $event->organization->logo) }}"
             alt="{{ $event->organization->name }} Logo"
             style="display: inline-block; max-height: 40px; max-width: 140px; height: auto; border: 0;">
    @endsection
@endif

@section('heading')
    {{ $isFree ? 'Anmeldung bestätigt' : 'Buchung bestätigt' }}
@endsection

@section('content')
    <p style="margin: 0 0 16px 0;">
        Liebe/r {{ $booking->customer_name }},
    </p>
    <p style="margin: 0 0 24px 0;">
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
    @component('emails.partials.notice', ['type' => 'info'])
        <p style="margin: 0; font-size: 18px; line-height: 1.3; font-weight: 600; color: #1E3135;">{{ $event->title }}</p>
        <p style="margin: 4px 0 0 0; font-size: 14px; color: #55676A;">
            {{ $event->start_date->format('d.m.Y') }} um {{ $event->start_date->format('H:i') }} Uhr
            @if($otherDates->isNotEmpty())
                <br>Weitere Termine: {{ $otherDates->map(fn ($d) => $d->start_date->format('d.m.Y H:i'))->implode(', ') }}
            @endif
        </p>
        @if($event->requiresVenue() && $venue !== '')
            <p style="margin: 4px 0 0 0; font-size: 14px; color: #55676A;">{{ $venue }}</p>
        @endif
    @endcomponent

    {{-- Buchungsdetails --}}
    <h2 style="margin: 8px 0 8px 0; font-size: 18px; line-height: 1.3; font-weight: 600; color: #1E3135;">Buchungsdetails</h2>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-top: 1px solid #DCE3E2; margin: 0 0 24px 0;">
        <tr>
            <td style="{{ $label }}">Buchungsnummer</td>
            <td style="{{ $value }} font-weight: 600;">{{ $booking->booking_number }}</td>
        </tr>
        @foreach($booking->items->loadMissing('ticketType')->groupBy('ticket_type_id') as $items)
            @php
                $qty = $items->sum('quantity');
                $unitPrice = (float) $items->first()->price;
            @endphp
            <tr>
                <td style="{{ $label }}">{{ $items->first()->ticketType?->name ?? 'Ticket' }}</td>
                <td style="{{ $value }}">
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
                    <td style="{{ $label }}">Rabatt</td>
                    <td style="{{ $value }}">&minus;&nbsp;{{ number_format((float) $booking->discount, 2, ',', '.') }}&nbsp;€</td>
                </tr>
            @endif
            <tr>
                <td style="{{ $label }}">Gesamtbetrag</td>
                <td style="{{ $value }} font-weight: 600;">{{ number_format((float) $booking->total, 2, ',', '.') }}&nbsp;€</td>
            </tr>
            <tr>
                <td style="{{ $label }}">Zahlung</td>
                <td style="{{ $value }} font-weight: 600; color: {{ $invoiceLater ? '#1E3135' : '#2C5E1A' }};">
                    {{ $invoiceLater ? 'Rechnung folgt separat' : 'Bezahlt' }}
                </td>
            </tr>
        @endif
    </table>

    {{-- Online-Zugangsdaten (Online und Hybrid) --}}
    @if($event->requiresOnlineInfo() && $event->online_url)
        @component('emails.partials.notice', ['type' => 'info', 'title' => 'Online-Zugangsdaten'])
            <p style="margin: 0 0 8px 0; font-size: 15px;">
                <strong>Zugangslink:</strong><br>
                <a href="{{ $event->online_url }}" style="color: #1E3135; text-decoration: underline; word-break: break-all;">{{ $event->online_url }}</a>
            </p>
            @if($event->online_access_code)
                <p style="margin: 0 0 8px 0; font-size: 15px;"><strong>Zugangscode:</strong>&nbsp;
                    <code style="padding: 2px 8px; border: 1px solid #DCE3E2; border-radius: 4px; background-color: #FFFFFF; font-family: Consolas, Menlo, monospace; font-size: 15px;">{{ $event->online_access_code }}</code></p>
            @endif
            <p style="margin: 0; font-size: 14px; color: #55676A;">Sie erhalten die Zugangsdaten zusätzlich mit der Erinnerung kurz vor Beginn.</p>
        @endcomponent
    @endif

    {{-- Teilnehmende eintragen (auch bei reinen Online-Veranstaltungen) --}}
    @if($booking->needsPersonalization())
        @component('emails.partials.notice', ['type' => 'warning', 'title' => 'Bitte Teilnehmende eintragen'])
            <p style="margin: 0;">
                Sie haben {{ $booking->itemCount() }} Plätze gebucht. Bitte tragen Sie für jeden Platz Name und E-Mail-Adresse der teilnehmenden Person ein.
                @if($booking->hasTicketDocument())
                    Danach erhalten Sie die personalisierten Tickets – und jede Person mit eigener E-Mail-Adresse ihr Ticket direkt.
                @else
                    Danach erhält jede Person mit eigener E-Mail-Adresse die Zugangsdaten direkt.
                @endif
            </p>
            @include('emails.partials.button', ['url' => route('bookings.personalize', $booking->booking_number), 'label' => 'Teilnehmende eintragen', 'margin' => '16px 0 0 0'])
        @endcomponent
    @elseif($booking->hasTicketDocument() && $booking->canSendTickets())
        {{-- Tickets sind als Anhang beigefügt --}}
        @component('emails.partials.notice', ['type' => 'success', 'title' => 'Ihre Tickets'])
            <p style="margin: 0;">
                Ihre Tickets sind dieser E-Mail als <strong>PDF-Anhang</strong> beigefügt.
                Bitte drucken Sie sie aus <strong>oder</strong> zeigen Sie sie auf dem Smartphone vor.
                Jedes Ticket enthält einen <strong>QR-Code</strong> für den Check-in.
            </p>
        @endcomponent
    @endif

    {{-- Hinweise des Veranstalters --}}
    @if($event->ticket_notes)
        @component('emails.partials.notice', ['type' => 'warning', 'title' => 'Hinweise des Veranstalters'])
            <p style="margin: 0;">{!! nl2br(e($event->ticket_notes)) !!}</p>
        @endcomponent
    @endif

    {{-- Stornierung: nur solange sie noch möglich ist --}}
    @if($event->canCancelBooking())
        <p style="margin: 0 0 16px 0; font-size: 15px;">
            <strong>Stornierung:</strong>
            @if($event->cancellation_days_before !== null)
                möglich bis zum
                <strong>{{ $event->start_date->copy()->subDays($event->cancellation_days_before)->format('d.m.Y') }}</strong>
                ({{ $event->cancellation_days_before }} Tag(e) vor Veranstaltungsbeginn).
            @else
                jederzeit bis zum Beginn der Veranstaltung möglich.
            @endif
        </p>
    @endif

    @include('emails.partials.button', ['url' => $booking->manageUrl(), 'label' => 'Buchungsdetails ansehen', 'margin' => '8px 0 24px 0'])

    {{-- Kontakt --}}
    <p style="margin: 0;">
        <strong>Wir freuen uns auf Sie!</strong><br>
        <span style="font-size: 14px; color: #55676A;">Bei Fragen: {{ $event->getOrganizerName() }} ·
            <a href="mailto:{{ $contactEmail }}" style="color: #1E3135; text-decoration: underline;">{{ $contactEmail }}</a></span>
    </p>
@endsection

@section('footer_note')
    Buchungsnummer: <strong>{{ $booking->booking_number }}</strong><br>
    Diese E-Mail wurde automatisch generiert. Bitte antworten Sie nicht direkt auf diese E-Mail.
@endsection
