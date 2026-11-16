@extends('emails.layout')

@section('title')
    {{ $booking->hasTicketDocument() ? 'Ihr Ticket' : 'Ihre Zugangsdaten' }}
@endsection

@section('heading', 'Sie sind angemeldet')

@section('subheading')
    {{ $event->title }}
@endsection

@section('content')
    <p style="margin: 0 0 16px 0;">
        Hallo {{ $item->participantName() }},</p>
    <p style="margin: 0 0 24px 0;">
        {{ $booking->customer_name }}
        @if ($booking->customer_organization)
            ({{ $booking->customer_organization }})
        @endif
        hat Sie für folgende Veranstaltung angemeldet:
    </p>

    @component('emails.partials.notice', ['type' => 'info'])
        <p style="margin: 0 0 8px 0; font-size: 18px; line-height: 1.3; font-weight: 600; color: #1E3135;">{{ $event->title }}
        </p>
        <p style="margin: 0 0 4px 0;">{{ $event->start_date->format('d.m.Y') }}, {{ $event->start_date->format('H:i') }} Uhr
            @if ($event->end_date)
                – {{ $event->end_date->format('H:i') }} Uhr
            @endif
        </p>
        @if ($event->hasMultipleDates())
            <p style="margin: 0 0 4px 0; font-size: 14px; color: #55676A;">Weitere Termine:
                {{ $event->dates->where('is_cancelled', false)->skip(1)->map(fn($d) => $d->start_date->format('d.m.Y H:i'))->implode(', ') }}
            </p>
        @endif
        @if ($event->requiresVenue() && $event->venue_name)
            <p style="margin: 0 0 4px 0;">{{ $event->venue_name }}@if ($event->venue_address)
                    , {{ $event->venue_address }}
                @endif, {{ trim($event->venue_postal_code . ' ' . $event->venue_city) }}</p>
        @endif
        <p style="margin: 0; font-size: 13px; color: #55676A;">Ticket-Nr. {{ $item->ticket_number }} · Buchung
            {{ $booking->booking_number }}</p>
    @endcomponent

    @if ($booking->hasTicketDocument())
        @component('emails.partials.notice', ['type' => 'success', 'title' => 'Ihr Ticket'])
            <p style="margin: 0;">Ihr persönliches Ticket ist als <strong>PDF angehängt</strong>. Bitte zeigen Sie es beim
                Einlass vor (ausgedruckt oder auf dem Smartphone).</p>
        @endcomponent
    @endif

    @if ($showOnlineAccess)
        <h2 style="margin: 8px 0 8px 0; font-size: 18px; line-height: 1.3; font-weight: 600; color: #1E3135;">Online-Zugang
        </h2>
        <p style="margin: 0 0 8px 0; word-break: break-all;"><a href="{{ $event->online_url }}"
                style="color: #1E3135; text-decoration: underline;">{{ $event->online_url }}</a></p>
        @if ($event->online_access_code)
            <p style="margin: 0 0 8px 0;">Zugangscode: <code
                    style="padding: 2px 8px; border: 1px solid #DCE3E2; border-radius: 4px; background-color: #F3F6F5; font-family: Consolas, Menlo, monospace; font-size: 15px;">{{ $event->online_access_code }}</code>
            </p>
        @endif
        <div style="height: 16px; line-height: 16px; font-size: 16px;">&nbsp;</div>
    @endif

    @if ($event->ticket_notes)
        <p style="margin: 0 0 16px 0; font-size: 14px;"><strong>Hinweise des
                Veranstalters:</strong><br>{!! nl2br(e($event->ticket_notes)) !!}</p>
    @endif

    <p style="margin: 0; font-size: 14px; color: #55676A;">
        Kurz vor Beginn erhalten Sie eine Erinnerung{{ $event->requiresOnlineInfo() ? ' mit den Zugangsdaten' : '' }}.
        Fragen zur Anmeldung richten Sie bitte an {{ $booking->customer_name }} oder an den Veranstalter
        {{ $event->getOrganizerName() }}@if ($event->getOrganizerEmail())
            (<a href="mailto:{{ $event->getOrganizerEmail() }}"
                style="color: #1E3135; text-decoration: underline;">{{ $event->getOrganizerEmail() }}</a>)
        @endif.
    </p>
@endsection

@section('footer_note', 'Sie erhalten diese E-Mail, weil Sie als Teilnehmer:in für diese Veranstaltung angemeldet
    wurden.')
