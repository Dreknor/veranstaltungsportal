@extends('emails.layout')

@php
    $session = $session ?? null;
    $start = $session?->start_date ?? $event->start_date;
    $end = $session?->end_date ?? ($session ? null : $event->end_date);
    $venueSource = $session ?? $event;
    $startsIn = now()->diff($start);
    $startsInText = $start->isPast()
        ? 'jetzt'
        : ($startsIn->days >= 1
            ? ($start->isTomorrow()
                ? 'morgen'
                : 'in ' . $startsIn->days . ' Tagen')
            : ($startsIn->h >= 1
                ? 'in ' . $startsIn->h . ' Stunde' . ($startsIn->h === 1 ? '' : 'n')
                : 'in ' . max(1, $startsIn->i) . ' Minuten'));
    $label =
        'padding: 4px 16px 4px 0; width: 120px; vertical-align: top; font-size: 14px; line-height: 1.5; color: #55676A;';
    $value = 'padding: 4px 0; vertical-align: top; font-size: 15px; line-height: 1.5; color: #1E3135;';
@endphp

@section('title', 'Veranstaltungserinnerung')

@if ($event->organization?->logo)
    @section('header_logo')
        <img src="{{ asset('storage/' . $event->organization->logo) }}" alt="{{ $event->organization->name }} Logo"
            style="display: inline-block; max-height: 40px; max-width: 140px; height: auto; border: 0;">
    @endsection
@endif

@section('heading')
    Ihre Veranstaltung beginnt {{ $startsInText }}
@endsection

@section('content')
    <p style="margin: 0 0 16px 0;">Hallo {{ $recipientName }},</p>

    <p style="margin: 0 0 24px 0;">
        wir möchten Sie an Ihre Teilnahme an folgender Veranstaltung erinnern:
    </p>

    @component('emails.partials.notice', ['type' => 'info'])
        <p style="margin: 0 0 12px 0; font-size: 18px; line-height: 1.3; font-weight: 600; color: #1E3135;">{{ $event->title }}
        </p>
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td style="{{ $label }}">Datum</td>
                <td style="{{ $value }}"><strong>{{ $start->translatedFormat('l, d.m.Y') }}</strong></td>
            </tr>
            <tr>
                <td style="{{ $label }}">Uhrzeit</td>
                <td style="{{ $value }}">{{ $start->format('H:i') }} Uhr @if ($end)
                        – {{ $end->format('H:i') }} Uhr
                    @endif
                </td>
            </tr>
            @if ($session)
                @php $allDates = $event->dates->where('is_cancelled', false)->values(); @endphp
                <tr>
                    <td style="{{ $label }}">Termin</td>
                    <td style="{{ $value }}">Termin {{ $allDates->search(fn($d) => $d->id === $session->id) + 1 }} von
                        {{ $allDates->count() }}</td>
                </tr>
            @endif
            @if ($event->requiresVenue() && $venueSource->venue_name)
                <tr>
                    <td style="{{ $label }}">Ort</td>
                    <td style="{{ $value }}">
                        {{ collect([$venueSource->venue_name, $venueSource->venue_address, trim($venueSource->venue_postal_code . ' ' . $venueSource->venue_city)])->filter()->implode(', ') }}
                    </td>
                </tr>
            @endif
            @if ($event->isOnline())
                <tr>
                    <td style="{{ $label }}">Format</td>
                    <td style="{{ $value }}">Online-Veranstaltung</td>
                </tr>
            @elseif($event->isHybrid())
                <tr>
                    <td style="{{ $label }}">Format</td>
                    <td style="{{ $value }}">Hybrid – vor Ort oder online</td>
                </tr>
            @endif
            <tr>
                <td style="{{ $label }}">Buchungsnummer</td>
                <td style="{{ $value }}">{{ $booking->booking_number }}</td>
            </tr>
        </table>
    @endcomponent

    @if ($event->requiresOnlineInfo())
        @if ($showOnlineAccess)
            <h2 style="margin: 8px 0 8px 0; font-size: 18px; line-height: 1.3; font-weight: 600; color: #1E3135;">Ihre
                Zugangsdaten</h2>
            <p style="margin: 0 0 8px 0; word-break: break-all;">
                <a href="{{ $event->online_url }}"
                    style="color: #1E3135; text-decoration: underline;">{{ $event->online_url }}</a>
            </p>
            @if ($event->online_access_code)
                <p style="margin: 0 0 8px 0;">Zugangscode: <code
                        style="padding: 2px 8px; border: 1px solid #DCE3E2; border-radius: 4px; background-color: #F3F6F5; font-family: Consolas, Menlo, monospace; font-size: 15px;">{{ $event->online_access_code }}</code>
                </p>
            @endif
            @include('emails.partials.button', [
                'url' => $event->online_url,
                'label' => 'Zur Online-Veranstaltung',
                'margin' => '16px 0',
            ])
            <p style="margin: 0 0 24px 0; font-size: 14px; color: #55676A;">Tipp: Wählen Sie sich einige Minuten vor Beginn
                ein, um Ton und Kamera zu prüfen.</p>
        @else
            @component('emails.partials.notice', ['type' => 'warning', 'title' => 'Zugangsdaten noch nicht freigeschaltet'])
                <p style="margin: 0;">Die Online-Zugangsdaten werden freigeschaltet, sobald Ihre Buchung bestätigt und bezahlt
                    ist.
                    Bitte wenden Sie sich bei Fragen an den Veranstalter.</p>
            @endcomponent
        @endif
    @endif

    @if ($event->requiresVenue())
        @component('emails.partials.notice', ['type' => 'info', 'title' => 'Einlass'])
            @if ($event->requires_ticket)
                <p style="margin: 0;">Bitte bringen Sie Ihr Ticket (ausgedruckt oder auf dem Smartphone) zur Veranstaltung mit.
                    @if (!$isAttendee)
                        Sie können es jederzeit über Ihre Buchung erneut herunterladen.
                    @endif
                </p>
            @else
                <p style="margin: 0;">Für den Einlass genügt Ihre Buchungsnummer
                    <strong>{{ $booking->booking_number }}</strong>.</p>
            @endif
            @if ($event->directions)
                <p style="margin: 8px 0 0 0; font-size: 14px;"><strong>Anfahrt:</strong>
                    {{ Str::limit(strip_tags($event->directions), 300) }}</p>
            @endif
        @endcomponent
    @endif

    @if ($event->ticket_notes)
        @component('emails.partials.notice', ['type' => 'info', 'title' => 'Hinweise des Veranstalters'])
            <p style="margin: 0; font-size: 14px;">{!! nl2br(e($event->ticket_notes)) !!}</p>
        @endcomponent
    @endif

    @unless ($isAttendee)
        @include('emails.partials.button', [
            'url' => $booking->manageUrl(),
            'label' => 'Buchung anzeigen',
            'margin' => '8px 0 8px 0',
        ])
    @endunless
    @include('emails.partials.button', [
        'url' => route('events.show', $event->slug),
        'label' => 'Veranstaltungsdetails',
        'variant' => $isAttendee ? 'primary' : 'secondary',
        'margin' => '8px 0 24px 0',
    ])

    <p style="margin: 0; font-size: 14px; color: #55676A;">
        Wir freuen uns auf Ihre Teilnahme!<br>
        {{ $event->getOrganizerName() }}
        @if ($event->getOrganizerEmail())
            · <a href="mailto:{{ $event->getOrganizerEmail() }}"
                style="color: #1E3135; text-decoration: underline;">{{ $event->getOrganizerEmail() }}</a>
        @endif
    </p>
@endsection

@section('footer_note')
    @if ($isAttendee)
        Sie erhalten diese E-Mail, weil Sie als Teilnehmer:in für diese Veranstaltung angemeldet wurden.
    @else
        Sie erhalten diese E-Mail, weil Sie diese Veranstaltung gebucht haben.
        @if ($booking->user_id)
            <br><a href="{{ route('settings.notifications.edit') }}"
                style="color: #55676A; text-decoration: underline;">E-Mail-Einstellungen verwalten</a>
        @endif
    @endif
@endsection
