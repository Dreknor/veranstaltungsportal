@extends('emails.layout')

@php
    $label = 'padding: 4px 16px 4px 0; width: 150px; vertical-align: top; font-size: 14px; line-height: 1.5; color: #55676A;';
    $value = 'padding: 4px 0; vertical-align: top; font-size: 15px; line-height: 1.5; color: #1E3135;';
@endphp

@section('title', 'Veranstaltung abgesagt')

@if($event->organization?->logo)
    @section('header_logo')
        <img src="{{ asset('storage/' . $event->organization->logo) }}"
             alt="{{ $event->organization->name }} Logo"
             style="display: inline-block; max-height: 40px; max-width: 140px; height: auto; border: 0;">
    @endsection
@endif

@section('heading', 'Veranstaltung abgesagt')

@section('content')
    @if($booking)
        <p style="margin: 0 0 16px 0;">Hallo {{ $booking->customer_name }},</p>
    @else
        <p style="margin: 0 0 16px 0;">Hallo,</p>
    @endif

    @component('emails.partials.notice', ['type' => 'error', 'title' => 'Wichtige Mitteilung'])
        <p style="margin: 0;">Die folgende Veranstaltung wurde leider abgesagt.</p>
    @endcomponent

    @component('emails.partials.notice', ['type' => 'info'])
        <p style="margin: 0 0 12px 0; font-size: 18px; line-height: 1.3; font-weight: 600; color: #1E3135;">{{ $event->title }}</p>
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td style="{{ $label }}">Datum</td>
                <td style="{{ $value }}">{{ $event->start_date->format('d.m.Y') }}</td>
            </tr>
            <tr>
                <td style="{{ $label }}">Uhrzeit</td>
                <td style="{{ $value }}">{{ $event->start_date->format('H:i') }}–{{ $event->end_date->format('H:i') }} Uhr</td>
            </tr>
            @if($event->venue_name)
                <tr>
                    <td style="{{ $label }}">Veranstaltungsort</td>
                    <td style="{{ $value }}">{{ $event->venue_name }}</td>
                </tr>
            @endif
            @if($event->cancelled_at)
                <tr>
                    <td style="{{ $label }}">Abgesagt am</td>
                    <td style="{{ $value }}">{{ $event->cancelled_at->format('d.m.Y H:i') }} Uhr</td>
                </tr>
            @endif
            @if($booking)
                <tr>
                    <td style="{{ $label }}">Ihre Buchungsnummer</td>
                    <td style="{{ $value }}">{{ $booking->booking_number }}</td>
                </tr>
            @endif
        </table>
    @endcomponent

    @if($event->cancellation_reason)
        @component('emails.partials.notice', ['type' => 'warning', 'title' => 'Grund der Absage'])
            <p style="margin: 0;">{{ $event->cancellation_reason }}</p>
        @endcomponent
    @endif

    @if($booking)
        <h2 style="margin: 8px 0 8px 0; font-size: 18px; line-height: 1.3; font-weight: 600; color: #1E3135;">Was passiert jetzt?</h2>
        <ul style="margin: 0 0 24px 0; padding-left: 20px;">
            <li style="margin: 0 0 4px 0;">Ihre Buchung wurde automatisch storniert – Ihre Tickets bzw. Zugangsdaten sind nicht mehr gültig.</li>
            @if($booking->payment_status === 'paid' && (float) $booking->total > 0)
                <li style="margin: 0 0 4px 0;">Bereits gezahlte Beträge ({{ number_format($booking->total, 2, ',', '.') }} €) werden vom Veranstalter erstattet.</li>
            @elseif((float) $booking->total > 0)
                <li style="margin: 0 0 4px 0;">Eine noch offene Rechnung müssen Sie nicht mehr bezahlen.</li>
            @endif
            <li style="margin: 0 0 4px 0;">Sie müssen keine weiteren Schritte unternehmen.</li>
        </ul>
    @endif

    <p style="margin: 0;">Wir entschuldigen uns für die entstandenen Unannehmlichkeiten und hoffen, Sie bald bei einer anderen Veranstaltung begrüßen zu dürfen.</p>

    @include('emails.partials.button', ['url' => route('events.index'), 'label' => 'Weitere Veranstaltungen entdecken'])

    <p style="margin: 0 0 8px 0;">Bei Fragen stehen wir Ihnen gerne zur Verfügung:</p>
    <p style="margin: 0;">
        @if($event->organization?->email)
            <strong>E-Mail:</strong> {{ $event->organization->email }}<br>
        @endif
        @if($event->organization?->phone)
            <strong>Telefon:</strong> {{ $event->organization->phone }}<br>
        @endif
    </p>
@endsection

@section('footer_note', 'Diese E-Mail wurde automatisch generiert.')
