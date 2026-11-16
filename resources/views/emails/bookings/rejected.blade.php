@extends('emails.layout')

@php
    $label =
        'padding: 4px 16px 4px 0; width: 120px; vertical-align: top; font-size: 14px; line-height: 1.5; color: #55676A;';
    $value = 'padding: 4px 0; vertical-align: top; font-size: 15px; line-height: 1.5; color: #1E3135;';
@endphp

@section('title', 'Anmeldung nicht bestätigt')

@if ($booking->event->organization?->logo)
    @section('header_logo')
        <img src="{{ asset('storage/' . $booking->event->organization->logo) }}"
            alt="{{ $booking->event->organization->name }} Logo"
            style="display: inline-block; max-height: 40px; max-width: 140px; height: auto; border: 0;">
    @endsection
@endif

@section('heading', 'Anmeldung nicht bestätigt')

@section('content')
    <p style="margin: 0 0 16px 0;">
        Liebe/r {{ $booking->customer_name }},
    </p>

    <p style="margin: 0 0 24px 0;">
        wir bedauern Ihnen mitteilen zu müssen, dass Ihre Anmeldung zur folgenden Veranstaltung leider nicht bestätigt
        werden konnte:
    </p>

    @component('emails.partials.notice', ['type' => 'info'])
        <p style="margin: 0 0 12px 0; font-size: 18px; line-height: 1.3; font-weight: 600; color: #1E3135;">
            {{ $booking->event->title }}</p>
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td style="{{ $label }}">Datum</td>
                <td style="{{ $value }}">{{ $booking->event->start_date->format('d.m.Y, H:i') }} Uhr</td>
            </tr>
            <tr>
                <td style="{{ $label }}">Buchungsnr.</td>
                <td style="{{ $value }} font-weight: 600;">{{ $booking->booking_number }}</td>
            </tr>
        </table>
    @endcomponent

    @if ($rejectionReason)
        @component('emails.partials.notice', ['type' => 'error', 'title' => 'Begründung'])
            <p style="margin: 0;">{{ $rejectionReason }}</p>
        @endcomponent
    @endif

    <p style="margin: 0 0 8px 0; font-size: 14px; color: #55676A;">
        Bei Fragen zu dieser Entscheidung wenden Sie sich bitte direkt an den Veranstalter:
    </p>

    <p style="margin: 0;">
        <strong>{{ $booking->event->getOrganizerName() }}</strong>
        @if ($booking->event->getOrganizerEmail())
            <br><a href="mailto:{{ $booking->event->getOrganizerEmail() }}"
                style="color: #1E3135; text-decoration: underline;">{{ $booking->event->getOrganizerEmail() }}</a>
        @endif
    </p>
@endsection

@section('footer_note', 'Diese E-Mail wurde automatisch generiert. Bitte antworten Sie nicht direkt auf diese E-Mail.')
