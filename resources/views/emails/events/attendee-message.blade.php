@extends('emails.layout')

@section('title')
    {{ $messageSubject }}
@endsection

@section('content')
    <p style="margin: 0 0 4px 0; font-size: 14px; color: #55676A;">Nachricht des Veranstalters zu</p>
    <p style="margin: 0 0 24px 0; font-size: 18px; line-height: 1.3; font-weight: 600; color: #1E3135;">{{ $event->title }}</p>

    <p style="margin: 0 0 16px 0;">Hallo {{ $recipientName }},</p>
    <div style="margin: 0 0 24px 0;">{!! nl2br(e($messageBody)) !!}</div>
    <p style="margin: 0;">
        Viele Grüße<br>{{ $event->getOrganizerName() }}
    </p>
@endsection

@section('footer_note')
    <strong style="color: #1E3135;">{{ $event->title }}</strong> · {{ $event->start_date->format('d.m.Y H:i') }} Uhr
    @if($event->requiresVenue() && $event->venue_name) · {{ $event->venue_name }} @endif
    <br>Buchungsnummer {{ $booking->booking_number }} ·
    <a href="{{ $booking->manageUrl() }}" style="color: #55676A; text-decoration: underline;">Buchung ansehen</a>
    <br>Antworten auf diese E-Mail gehen direkt an den Veranstalter.
@endsection
