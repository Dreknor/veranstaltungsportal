@extends('emails.layout')

@section('title', 'E-Mail-Adresse bestätigen')

@section('preheader', 'Bitte bestätigen Sie Ihre E-Mail-Adresse, um Ihr Konto zu aktivieren.')

@section('heading')
    Willkommen bei {{ config('app.name') }}
@endsection

@section('content')
    <p style="margin: 0 0 16px 0;">
        Hallo{{ $user->fullName() !== '' ? ' ' . $user->fullName() : '' }},
    </p>

    <p style="margin: 0 0 16px 0;">
        vielen Dank für Ihre Registrierung! Wir freuen uns, Sie bei {{ config('app.name') }} begrüßen zu dürfen.
    </p>

    <p style="margin: 0 0 16px 0;">
        Um Ihr Konto zu aktivieren und alle Funktionen nutzen zu können, bestätigen Sie bitte Ihre E-Mail-Adresse:
    </p>

    @include('emails.partials.button', ['url' => $verificationUrl, 'label' => 'E-Mail-Adresse bestätigen'])

    @component('emails.partials.notice', ['type' => 'info', 'title' => 'Hinweis'])
        <p style="margin: 0;">Dieser Bestätigungslink ist aus Sicherheitsgründen 60 Minuten gültig.</p>
    @endcomponent

    <p style="margin: 0 0 8px 0; font-size: 14px; color: #55676A;">
        Falls der Button nicht funktioniert, kopieren Sie bitte den folgenden Link in Ihren Browser:
    </p>
    <p style="margin: 0 0 24px 0; font-size: 14px; word-break: break-all;">
        <a href="{{ $verificationUrl }}" style="color: #1E3135; text-decoration: underline;">{{ $verificationUrl }}</a>
    </p>

    <p style="margin: 0 0 24px 0; font-size: 14px; color: #55676A;">
        Sie haben sich nicht bei {{ config('app.name') }} registriert? Dann können Sie diese E-Mail einfach ignorieren. Ihr
        Konto wird nicht aktiviert.
    </p>

    <p style="margin: 0;">
        Viele Grüße<br>
        Ihr {{ config('app.name') }}-Team
    </p>
@endsection

@section('footer_note', 'Diese E-Mail wurde automatisch generiert. Bitte antworten Sie nicht direkt auf diese E-Mail.')
