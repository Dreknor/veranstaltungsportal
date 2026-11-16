@extends('emails.layout')

@php
    $label =
        'padding: 8px 16px 8px 0; width: 140px; vertical-align: top; border-bottom: 1px solid #DCE3E2; font-size: 14px; line-height: 1.5; color: #55676A;';
    $value =
        'padding: 8px 0; vertical-align: top; border-bottom: 1px solid #DCE3E2; font-size: 15px; line-height: 1.5; color: #1E3135; font-variant-numeric: tabular-nums;';
@endphp

@section('title', 'Neue Buchung eingegangen')

@if ($booking->event->organization?->logo)
    @section('header_logo')
        <img src="{{ asset('storage/' . $booking->event->organization->logo) }}"
            alt="{{ $booking->event->organization->name }} Logo"
            style="display: inline-block; max-height: 40px; max-width: 140px; height: auto; border: 0;">
    @endsection
@endif

@section('heading', 'Neue Buchung eingegangen')

@section('content')
    <p style="margin: 0 0 16px 0;">
        Hallo {{ $organizer->fullName() }},
    </p>

    <p style="margin: 0 0 24px 0;">
        Sie haben eine neue Buchung für Ihre Veranstaltung erhalten:
    </p>

    @component('emails.partials.notice', ['type' => 'info'])
        <p style="margin: 0; font-size: 18px; line-height: 1.3; font-weight: 600; color: #1E3135;">
            {{ $booking->event->title }}
        </p>
        <p style="margin: 4px 0 0 0; font-size: 14px; color: #55676A;">
            {{ $booking->event->start_date->format('d.m.Y') }} um {{ $booking->event->start_date->format('H:i') }} Uhr
        </p>
    @endcomponent

    <h2 style="margin: 8px 0 8px 0; font-size: 18px; line-height: 1.3; font-weight: 600; color: #1E3135;">Buchungsdetails
    </h2>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
        style="border-top: 1px solid #DCE3E2;">
        <tr>
            <td style="{{ $label }}">Buchungsnummer</td>
            <td style="{{ $value }} font-weight: 600;">{{ $booking->booking_number }}</td>
        </tr>
        <tr>
            <td style="{{ $label }}">Teilnehmer</td>
            <td style="{{ $value }}">{{ $booking->customer_name }}</td>
        </tr>
        <tr>
            <td style="{{ $label }}">E-Mail</td>
            <td style="{{ $value }}">
                <a href="mailto:{{ $booking->customer_email }}"
                    style="color: #1E3135; text-decoration: underline;">{{ $booking->customer_email }}</a>
            </td>
        </tr>
        @php
            $groupedItems = $booking->items->load('ticketType')->groupBy('ticket_type_id');
        @endphp
        @foreach ($groupedItems as $ticketTypeId => $items)
            @php
                $firstItem = $items->first();
                $qty = $items->sum('quantity');
                $unitPrice = (float) $firstItem->price;
                $subtotal = $unitPrice * $qty;
                $ticketName = $firstItem->ticketType?->name ?? 'Ticket';
            @endphp
            <tr>
                <td style="{{ $label }}">{{ $ticketName }}</td>
                <td style="{{ $value }}">
                    {{ $qty }}&times;&nbsp;{{ number_format($unitPrice, 2, ',', '.') }}&nbsp;€
                    @if ($qty > 1)
                        <span style="color: #55676A;">=</span>
                        <strong>{{ number_format($subtotal, 2, ',', '.') }}&nbsp;€</strong>
                    @endif
                </td>
            </tr>
        @endforeach
        @if ($booking->discount > 0)
            <tr>
                <td style="{{ $label }}">Rabatt</td>
                <td style="{{ $value }}">&minus;&nbsp;{{ number_format($booking->discount, 2, ',', '.') }}&nbsp;€
                </td>
            </tr>
        @endif
        <tr>
            <td style="{{ $label }}">Gesamtbetrag</td>
            <td style="{{ $value }} font-weight: 600;">{{ number_format($booking->total, 2, ',', '.') }}&nbsp;€
            </td>
        </tr>
    </table>

    @if ($booking->event->organization?->hasExternalInvoicing() && $booking->total > 0)
        <h2 style="margin: 32px 0 8px 0; font-size: 18px; line-height: 1.3; font-weight: 600; color: #1E3135;">
            Rechnungsdaten (für externe Fakturierung)</h2>
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
            style="border-top: 1px solid #DCE3E2;">
            <tr>
                <td style="{{ $label }}">Firma</td>
                <td style="{{ $value }}">{{ $booking->billing_company ?: '-' }}</td>
            </tr>
            <tr>
                <td style="{{ $label }}">USt-IdNr.</td>
                <td style="{{ $value }}">{{ $booking->billing_vat_id ?: '-' }}</td>
            </tr>
            @if ($booking->billing_address)
                <tr>
                    <td style="{{ $label }}">Adresse</td>
                    <td style="{{ $value }}">
                        {{ $booking->billing_address }}<br>
                        {{ $booking->billing_postal_code }}
                        {{ $booking->billing_city }}{{ $booking->billing_country ? ', ' . $booking->billing_country : '' }}
                    </td>
                </tr>
            @endif
            <tr>
                <td style="{{ $label }}">E-Mail</td>
                <td style="{{ $value }}">{{ $booking->customer_email }}</td>
            </tr>
            @if ($booking->customer_phone)
                <tr>
                    <td style="{{ $label }}">Telefon</td>
                    <td style="{{ $value }}">{{ $booking->customer_phone }}</td>
                </tr>
            @endif
            <tr>
                <td style="{{ $label }}">Betrag</td>
                <td style="{{ $value }} font-weight: 600;">{{ number_format($booking->total, 2, ',', '.') }} €</td>
            </tr>
        </table>
        @include('emails.partials.button', [
            'url' => route('organizer.billing-data.index'),
            'label' => 'Rechnungsdaten exportieren',
            'variant' => 'secondary',
            'margin' => '16px 0 0 0',
        ])
    @endif

    @include('emails.partials.button', [
        'url' => route('organizer.bookings.show', $booking),
        'label' => 'Buchung ansehen',
    ])

    <p style="margin: 0 0 24px 0; font-size: 14px; color: #55676A;">
        Diese Benachrichtigung wurde automatisch versandt, nachdem eine Buchung für Ihre Veranstaltung eingegangen ist.
        Sie können Benachrichtigungseinstellungen in Ihrem Profil anpassen.
    </p>

    <p style="margin: 0;">
        Viele Grüße<br>
        Ihr {{ config('app.name') }}-Team
    </p>
@endsection

@section('footer_note', 'Diese E-Mail wurde automatisch generiert. Bitte antworten Sie nicht direkt auf diese E-Mail.')
