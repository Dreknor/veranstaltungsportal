@extends('emails.layout')

@section('title', 'Ihr Teilnahmezertifikat')

@section('heading', 'Ihr Teilnahmezertifikat')

@section('subheading')
    {{ $event->title }}
@endsection

@section('content')
    <p style="margin: 0 0 16px 0;">Hallo {{ $booking->customer_name }},</p>
    <p style="margin: 0 0 24px 0;">
        vielen Dank für Ihre Teilnahme an „{{ $event->title }}“ am {{ $event->start_date->format('d.m.Y') }}.
        Ihr Teilnahmezertifikat finden Sie im Anhang dieser E-Mail.
    </p>

    @component('emails.partials.notice', ['type' => 'success', 'title' => 'Zertifikat als PDF'])
        <p style="margin: 0;">Bitte bewahren Sie das Zertifikat für Ihre Unterlagen auf.</p>
    @endcomponent
@endsection
