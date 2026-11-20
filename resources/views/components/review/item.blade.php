{{--
    Eine veröffentlichte Bewertung: Name (gekürzt, außer freigegeben), Sterne, Monat, optional die
    bewertete Veranstaltung („Teilnahme an … am …“), Kommentar und Antwort des Veranstalters.
    Gehört in eine <ul> mit Trennlinien (siehe <x-review.list>).
--}}
@props([
    'review',
    'showEvent' => false, // bewertete Veranstaltung nennen (Veranstalter-Profil, frühere Termine)
    'linkEvent' => true, // Veranstaltungstitel verlinken
    'organizationName' => null, // für „Antwort von …“; Standard: Veranstalter der Veranstaltung
])

@php
    $event = $review->event;
    $organizationName ??= $event?->organization?->name ?? 'Veranstalter';
@endphp

<li {{ $attributes->class('flex flex-col gap-2 py-5') }}>
    <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
        <span class="text-ui font-medium text-ink">{{ $review->publicName() }}</span>
        <x-ui.stars :value="$review->rating" size="sm" />
        <span class="text-small text-muted tabular-nums">
            <time datetime="{{ $review->created_at->toDateString() }}">{{ $review->created_at->format('d.m.Y') }}</time>
        </span>
    </div>

    @if ($showEvent && $event)
        <p class="m-0 text-small text-muted">
            Teilnahme an
            @if ($linkEvent)
                <a href="{{ route('events.show', $event->slug) }}"
                    class="text-ink-soft underline underline-offset-[3px] hover:text-ink">{{ $event->title }}</a>
            @else
                <span class="text-ink-soft">{{ $event->title }}</span>
            @endif
            am <span class="tabular-nums">{{ $event->start_date->format('d.m.Y') }}</span>
        </p>
    @endif

    @if ($review->comment)
        <p class="m-0 max-w-prose text-base text-ink-soft">{!! nl2br(e($review->comment)) !!}</p>
    @endif

    @if ($review->hasOrganizerReply())
        <div class="mt-2 flex max-w-prose flex-col gap-1 rounded-control border border-line bg-zone px-4 py-3">
            <p class="m-0 text-small font-medium text-ink">
                Antwort von {{ $organizationName }}
                @if ($review->organizer_replied_at)
                    <span class="font-normal text-muted tabular-nums">
                        · <time
                            datetime="{{ $review->organizer_replied_at->toDateString() }}">{{ $review->organizer_replied_at->format('d.m.Y') }}</time>
                    </span>
                @endif
            </p>
            <p class="m-0 text-base text-ink-soft">{!! nl2br(e($review->organizer_reply)) !!}</p>
        </div>
    @endif
</li>
