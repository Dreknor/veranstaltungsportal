@extends('emails.layout')

@section('title', 'Passwort zurücksetzen')

@section('preheader', 'Legen Sie ein neues Passwort für Ihr Konto fest.')

@section('heading', 'Passwort zurücksetzen')

@section('content')
    <p style="margin: 0 0 16px 0;">
        Hallo{{ isset($user) && $user->fullName() !== '' ? ' ' . $user->fullName() : '' }},
    </p>

    <p style="margin: 0 0 16px 0;">
        wir haben eine Anfrage erhalten, das Passwort für Ihr Konto bei <strong>{{ config('app.name') }}</strong>
        zurückzusetzen.
        Klicken Sie auf den folgenden Button, um ein neues Passwort festzulegen:
    </p>

    @include('emails.partials.button', ['url' => $resetUrl, 'label' => 'Neues Passwort festlegen'])

    @component('emails.partials.notice', ['type' => 'info', 'title' => 'Hinweis'])
        <p style="margin: 0;">
            Dieser Link ist aus Sicherheitsgründen
            <strong>{{ config('auth.passwords.' . config('auth.defaults.passwords') . '.expire', 60) }} Minuten</strong> gültig.
            Danach müssen Sie erneut eine Passwort-Zurücksetzen-Anfrage stellen.
        </p>
    @endcomponent

    <p style="margin: 0 0 8px 0; font-size: 14px; color: #55676A;">
        Falls der Button nicht funktioniert, kopieren Sie bitte den folgenden Link in Ihren Browser:
    </p>
    <p style="margin: 0 0 24px 0; font-size: 14px; word-break: break-all;">
        <a href="{{ $resetUrl }}" style="color: #1E3135; text-decoration: underline;">{{ $resetUrl }}</a>
    </p>

    <p style="margin: 0 0 24px 0; font-size: 14px; color: #55676A;">
        Sie haben keine Passwort-Zurücksetzen-Anfrage gestellt? Dann ignorieren Sie diese E-Mail bitte –
        Ihr Passwort bleibt unverändert und Ihr Konto ist sicher.
    </p>

    <p style="margin: 0;">
        Viele Grüße<br>
        Ihr {{ config('app.name') }}-Team
    </p>
@endsection

@section('footer_note', 'Diese E-Mail wurde automatisch generiert. Bitte antworten Sie nicht direkt auf diese E-Mail.')
