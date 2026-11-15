{{--
    "In den Kalender": Sekundärbutton über die volle Breite, öffnet ein Menü mit
    Google Kalender, Outlook und iCal-Datei (.ics).
--}}
@props(['event'])

@php
    $calendarService = app(\App\Services\CalendarService::class);
    $googleUrl = $calendarService->getGoogleCalendarUrl($event);
    $outlookUrl = $calendarService->getOutlookCalendarUrl($event);
    $icalUrl = route('events.calendar.export', $event->slug);
@endphp

<x-ui.dropdown align="left" width="w-full" {{ $attributes->class('w-full') }}>
    <x-slot:trigger>
        <x-ui.button variant="secondary" full x-ref="button" x-on:click="toggle()" x-bind:aria-expanded="open.toString()"
            aria-expanded="false" aria-haspopup="true">
            <x-slot:icon>
                <x-icon.calendar />
            </x-slot:icon>
            In den Kalender
            <x-icon.chevron-down class="h-4 w-4 transition-transform duration-150" x-bind:class="open && 'rotate-180'" />
        </x-ui.button>
    </x-slot:trigger>

    <x-ui.dropdown-item :href="$googleUrl" target="_blank" rel="noopener noreferrer">
        Google Kalender
        <x-icon.external-link class="ml-auto h-4 w-4 text-muted" />
        <span class="sr-only">(öffnet eine externe Seite)</span>
    </x-ui.dropdown-item>
    <x-ui.dropdown-item :href="$outlookUrl" target="_blank" rel="noopener noreferrer">
        Outlook
        <x-icon.external-link class="ml-auto h-4 w-4 text-muted" />
        <span class="sr-only">(öffnet eine externe Seite)</span>
    </x-ui.dropdown-item>
    <x-ui.dropdown-item :href="$icalUrl">
        iCal-Datei (.ics)
        <x-icon.download class="ml-auto h-4 w-4 text-muted" />
    </x-ui.dropdown-item>
</x-ui.dropdown>
