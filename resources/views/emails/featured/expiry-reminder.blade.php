@extends('emails.layout')

@php
    $label = 'padding: 8px 16px 8px 0; width: 160px; vertical-align: top; border-bottom: 1px solid #DCE3E2; font-size: 14px; line-height: 1.5; color: #55676A;';
    $value = 'padding: 8px 0; vertical-align: top; border-bottom: 1px solid #DCE3E2; font-size: 15px; line-height: 1.5; color: #1E3135; font-variant-numeric: tabular-nums;';
    $h2 = 'margin: 32px 0 8px 0; font-size: 18px; line-height: 1.3; font-weight: 600; color: #1E3135;';
@endphp

@section('title', 'Hervorhebungs-Status läuft ab')

@section('heading', 'Hervorhebungs-Status läuft bald ab')

@section('content')
    <p style="margin: 0 0 16px 0;">Hallo {{ $user->name }},</p>

    @component('emails.partials.notice', ['type' => 'warning', 'title' => 'Wichtiger Hinweis'])
        <p style="margin: 0;">Der Hervorhebungs-Status Ihrer Veranstaltung läuft in <strong>3 Tagen</strong> ab!</p>
    @endcomponent

    <p style="margin: 0;">Ihre Veranstaltung wird aktuell als „Empfohlen" prominent auf der Startseite angezeigt. Diese Hervorhebung endet am <strong>{{ $fee->featured_end_date->format('d.m.Y') }}</strong>.</p>

    <h2 style="{{ $h2 }}">Veranstaltungsdetails</h2>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-top: 1px solid #DCE3E2;">
        <tr>
            <td style="{{ $label }}">Veranstaltung</td>
            <td style="{{ $value }}">{{ $event->title }}</td>
        </tr>
        <tr>
            <td style="{{ $label }}">Hervorgehoben seit</td>
            <td style="{{ $value }}">{{ $fee->featured_start_date->format('d.m.Y') }}</td>
        </tr>
        <tr>
            <td style="{{ $label }}">Hervorgehoben bis</td>
            <td style="{{ $value }}">{{ $fee->featured_end_date->format('d.m.Y') }}</td>
        </tr>
        <tr>
            <td style="{{ $label }}">Verbleibende Tage</td>
            <td style="{{ $value }} font-weight: 600;">3 Tage</td>
        </tr>
    </table>

    <h2 style="{{ $h2 }}">Möchten Sie die Hervorhebung verlängern?</h2>
    <p style="margin: 0;">Um die Sichtbarkeit Ihrer Veranstaltung weiterhin zu maximieren, können Sie den Hervorhebungs-Status jetzt verlängern:</p>

    @include('emails.partials.button', ['url' => route('organizer.featured-events.extend', $event), 'label' => 'Jetzt verlängern', 'margin' => '24px 0 8px 0'])
    @include('emails.partials.button', ['url' => route('organizer.featured-events.history'), 'label' => 'Hervorhebungs-Historie ansehen', 'variant' => 'secondary', 'margin' => '8px 0 0 0'])

    <h2 style="{{ $h2 }}">Was passiert nach Ablauf?</h2>
    <p style="margin: 0 0 8px 0;">Nach dem Ablaufdatum wird Ihre Veranstaltung:</p>
    <ul style="margin: 0 0 24px 0; padding-left: 20px;">
        <li style="margin: 0 0 4px 0;">weiterhin veröffentlicht und buchbar bleiben</li>
        <li style="margin: 0 0 4px 0;">in den normalen Suchergebnissen erscheinen</li>
        <li style="margin: 0 0 4px 0;">nicht mehr prominent auf der Startseite angezeigt werden</li>
        <li style="margin: 0 0 4px 0;">kein „Empfohlen"-Abzeichen mehr haben</li>
    </ul>

    <p style="margin: 0 0 16px 0;">Bei Fragen stehen wir Ihnen gerne zur Verfügung!</p>

    <p style="margin: 0;">
        Mit freundlichen Grüßen<br>
        Ihr {{ config('app.name') }}-Team
    </p>
@endsection

@section('footer_note')
    Diese E-Mail wurde automatisch versendet.<br>
    Sie können Benachrichtigungen in Ihren <a href="{{ route('settings.notifications.edit') }}" style="color: #55676A; text-decoration: underline;">Einstellungen</a> verwalten.
@endsection
