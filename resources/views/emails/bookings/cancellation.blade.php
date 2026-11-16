@extends('emails.layout')

@php
    $cancelledByOrganizer = in_array($booking->cancelled_by, ['organizer', 'event']);
    $contactEmail = $booking->event->getOrganizerEmail() ?? config('mail.from.address');
@endphp

@section('title', 'Stornierungsbestätigung')

@if($booking->event->organization?->logo)
    @section('header_logo')
        <img src="{{ asset('storage/' . $booking->event->organization->logo) }}"
             alt="{{ $booking->event->organization->name }} Logo"
             style="display: inline-block; max-height: 40px; max-width: 140px; height: auto; border: 0;">
    @endsection
@endif

@section('heading', 'Stornierungsbestätigung')

@section('content')
    <p style="margin: 0 0 16px 0;">Hallo {{ $booking->customer_name }},</p>

    @if($booking->cancelled_by === 'system')
        <p style="margin: 0 0 24px 0;">Ihre Buchung wurde automatisch storniert.</p>
    @elseif($cancelledByOrganizer)
        <p style="margin: 0 0 24px 0;">Ihre Buchung wurde vom Veranstalter storniert.</p>
    @else
        <p style="margin: 0 0 24px 0;">Ihre Buchung wurde wie gewünscht storniert.</p>
    @endif

    @component('emails.partials.notice', ['type' => 'info'])
        <p style="margin: 0 0 8px 0; font-size: 18px; line-height: 1.3; font-weight: 600; color: #1E3135;">{{ $booking->event->title }}</p>
        <p style="margin: 0;">
            <strong>Termin:</strong> {{ $booking->event->start_date->format('d.m.Y H:i') }} Uhr<br>
            <strong>Buchungsnummer:</strong> {{ $booking->booking_number }}<br>
            <strong>Storniert am:</strong> {{ ($booking->cancelled_at ?? now())->format('d.m.Y H:i') }} Uhr
        </p>
        @if($booking->cancellation_reason)
            <p style="margin: 8px 0 0 0;"><strong>Grund:</strong> {{ $booking->cancellation_reason }}</p>
        @endif
    @endcomponent

    <p style="margin: 0 0 16px 0;">Ihre Tickets bzw. Zugangsdaten sind damit nicht mehr gültig.</p>

    @if((float) $booking->total > 0)
        @if($booking->payment_status === 'paid')
            <p style="margin: 0 0 16px 0;">Bereits gezahlte Beträge werden vom Veranstalter gemäß den Stornobedingungen erstattet.</p>
        @elseif($booking->payment_status === 'pending')
            <p style="margin: 0 0 16px 0;">Eine noch offene Rechnung zu dieser Buchung müssen Sie nicht mehr bezahlen.</p>
        @endif
    @endif

    <p style="margin: 0 0 16px 0;">Bei Fragen wenden Sie sich bitte an <a href="mailto:{{ $contactEmail }}" style="color: #1E3135; text-decoration: underline;">{{ $contactEmail }}</a>.</p>

    <p style="margin: 0;">Wir hoffen, Sie bald wieder bei einer unserer Veranstaltungen begrüßen zu dürfen!</p>
@endsection

@section('footer_note', 'Diese E-Mail wurde automatisch generiert. Bitte antworten Sie nicht auf diese E-Mail.')
