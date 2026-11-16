@extends('emails.layout')

@php
    $label = 'padding: 8px 16px 8px 0; width: 140px; vertical-align: top; border-bottom: 1px solid #DCE3E2; font-size: 14px; line-height: 1.5; color: #55676A;';
    $value = 'padding: 8px 0; vertical-align: top; border-bottom: 1px solid #DCE3E2; font-size: 15px; line-height: 1.5; color: #1E3135;';
@endphp

@section('title', 'Anmeldung eingegangen')

@if($booking->event->organization?->logo)
    @section('header_logo')
        <img src="{{ asset('storage/' . $booking->event->organization->logo) }}"
             alt="{{ $booking->event->organization->name }} Logo"
             style="display: inline-block; max-height: 40px; max-width: 140px; height: auto; border: 0;">
    @endsection
@endif

@section('heading', 'Anmeldung eingegangen')

@section('content')
    <p style="margin: 0 0 16px 0;">
        Liebe/r {{ $booking->customer_name }},
    </p>
    <p style="margin: 0 0 24px 0;">
        vielen Dank für Ihre Anmeldung! Wir haben Ihre Anmeldung erfolgreich erhalten und werden sie in Kürze bearbeiten.
    </p>

    @component('emails.partials.notice', ['type' => 'warning', 'title' => 'Ihre Anmeldung wird geprüft'])
        <p style="margin: 0;">
            Ihre Anmeldung wurde erfolgreich entgegengenommen und wird derzeit vom Veranstalter geprüft.
            Sobald Ihre Teilnahme bestätigt wurde, erhalten Sie eine weitere E-Mail mit allen relevanten Informationen.
        </p>
    @endcomponent

    <h2 style="margin: 8px 0 8px 0; font-size: 18px; line-height: 1.3; font-weight: 600; color: #1E3135;">Anmeldedetails</h2>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-top: 1px solid #DCE3E2;">
        <tr>
            <td style="{{ $label }}">Veranstaltung</td>
            <td style="{{ $value }} font-weight: 600;">{{ $booking->event->title }}</td>
        </tr>
        <tr>
            <td style="{{ $label }}">Datum</td>
            <td style="{{ $value }}">
                {{ $booking->event->start_date->format('d.m.Y') }} um {{ $booking->event->start_date->format('H:i') }} Uhr
            </td>
        </tr>
        @if(!$booking->event->isOnline() && $booking->event->venue_city)
            <tr>
                <td style="{{ $label }}">Ort</td>
                <td style="{{ $value }}">
                    {{ $booking->event->venue_name }}, {{ $booking->event->venue_city }}
                </td>
            </tr>
        @endif
        <tr>
            <td style="{{ $label }}">Buchungsnummer</td>
            <td style="{{ $value }} font-weight: 600;">{{ $booking->booking_number }}</td>
        </tr>
    </table>

    @include('emails.partials.button', ['url' => $booking->manageUrl(), 'label' => 'Anmeldung ansehen'])

    <p style="margin: 0 0 24px 0; font-size: 14px; color: #55676A;">
        Bei Fragen wenden Sie sich bitte an den Veranstalter:
        <strong style="color: #1E3135;">{{ $booking->event->getOrganizerName() }}</strong>
        @if($booking->event->getOrganizerEmail())
            (<a href="mailto:{{ $booking->event->getOrganizerEmail() }}" style="color: #1E3135; text-decoration: underline;">{{ $booking->event->getOrganizerEmail() }}</a>)
        @endif
    </p>

    <p style="margin: 0;">
        Viele Grüße<br>
        Ihr {{ config('app.name') }}-Team
    </p>
@endsection

@section('footer_note', 'Diese E-Mail wurde automatisch generiert. Bitte antworten Sie nicht direkt auf diese E-Mail.')
