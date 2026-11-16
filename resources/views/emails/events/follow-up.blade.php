@extends('emails.layout')

@section('title', 'Danke für Ihre Teilnahme')

@section('heading', 'Danke für Ihre Teilnahme')

@section('subheading')
    {{ $event->title }}
@endsection

@section('content')
    <p style="margin: 0 0 16px 0;">Hallo {{ $recipientName }},</p>
    <p style="margin: 0 0 24px 0;">vielen Dank, dass Sie bei <strong>{{ $event->title }}</strong> dabei waren. Wir hoffen,
        die Fortbildung hat Ihnen neue Impulse für Ihre Arbeit gegeben.</p>

    @if ($certificateItems->isNotEmpty())
        @component('emails.partials.notice', [
            'type' => 'success',
            'title' => 'Teilnahmebescheinigung' . ($certificateItems->count() > 1 ? 'en' : ''),
        ])
            <p style="margin: 0;">
                Im Anhang finden Sie die
                Bescheinigung{{ $certificateItems->count() > 1 ? 'en für ' . $certificateItems->map->participantName()->implode(', ') : '' }}
                @if ($event->duration)
                    über {{ $event->getFormattedDuration() }} Fortbildungszeit
                @endif.
            </p>
        @endcomponent
    @elseif(!$isAttendee)
        <p style="margin: 0 0 24px 0; font-size: 14px; color: #55676A;">Ihre Teilnahmebescheinigung können Sie jederzeit über
            Ihre Buchung abrufen.</p>
    @endif

    @if ($canGiveFeedback)
        <h2 style="margin: 8px 0 8px 0; font-size: 18px; line-height: 1.3; font-weight: 600; color: #1E3135;">Wie hat es
            Ihnen gefallen?</h2>
        <p style="margin: 0;">Ihr Feedback dauert nur eine Minute und hilft dem Veranstalter, zukünftige Angebote zu
            verbessern.</p>
        @include('emails.partials.button', [
            'url' => $booking->manageUrl() . '#feedback',
            'label' => 'Feedback geben',
        ])
    @endif

    <p style="margin: 0 0 16px 0; font-size: 14px; color: #55676A;">
        Weitere Fortbildungen finden Sie unter <a href="{{ route('events.index') }}"
            style="color: #1E3135; text-decoration: underline;">{{ route('events.index') }}</a>.
    </p>
    <p style="margin: 0;">Herzliche Grüße<br>{{ $event->getOrganizerName() }}</p>
@endsection

@section('footer_note', 'Sie erhalten diese E-Mail einmalig nach der Veranstaltung.')
