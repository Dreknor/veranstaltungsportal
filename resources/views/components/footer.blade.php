{{--
    Fuß (B4, Referenz startseite.html): Zone mit Linie oben, Wortmarke, Fußnavigation,
    darunter „© Jahr Bildungsportal · Ein Angebot der ESdi GmbH", Cookie-Einstellungen und
    „Mehr zur Aktion Hauptfach Mensch".
    compact: schmale Zeile für den angemeldeten Bereich (unter dem Inhalt neben der Seitenleiste).
--}}
@props(['compact' => false])

@php
    $footerLinks = [
        ['label' => 'Veranstaltungen', 'url' => route('events.index')],
        ['label' => 'Kalender', 'url' => route('events.calendar')],
        ['label' => 'FAQ', 'url' => route('faq')],
        ['label' => 'Hilfe', 'url' => route('help.index')],
        ['label' => 'Kontakt', 'url' => route('contact.show')],
        ['label' => 'Impressum', 'url' => route('impressum')],
        ['label' => 'Datenschutz', 'url' => route('datenschutz')],
        ['label' => 'AGB', 'url' => route('agb')],
    ];
    $linkClass = 'inline-flex min-h-11 items-center text-ink no-underline underline-offset-[3px] hover:underline';
@endphp

@if ($compact)
    <footer {{ $attributes->class('border-t border-line bg-surface') }}>
        <div
            class="mx-auto flex max-w-page flex-wrap items-center justify-between gap-x-8 gap-y-1 px-page py-2 text-small text-muted">
            <span class="py-3">© {{ date('Y') }} {{ config('app.name') }} · Ein Angebot der
                <a href="https://www.esdigmbh.de/" target="_blank" rel="noopener"
                    class="text-muted underline-offset-[3px] hover:underline">ESdi GmbH</a></span>
            <nav aria-label="Fußnavigation" class="flex flex-wrap gap-x-6">
                @foreach (array_slice($footerLinks, 4) as $link)
                    <a href="{{ $link['url'] }}" class="{{ $linkClass }} font-medium">{{ $link['label'] }}</a>
                @endforeach
                <button type="button" onclick="window.showCookiePreferences()"
                    class="{{ $linkClass }} cursor-pointer border-0 bg-transparent p-0 font-medium">Cookie-Einstellungen</button>
            </nav>
        </div>
    </footer>
@else
    <x-ui.zone as="footer" border="top" {{ $attributes }}>
        <div class="flex flex-col gap-6 pt-10 pb-8">
            <div class="flex flex-wrap items-center justify-between gap-x-12 gap-y-4">
                <x-wordmark size="sm" />
                <nav aria-label="Fußnavigation" class="flex flex-wrap gap-x-7">
                    @foreach ($footerLinks as $link)
                        <a href="{{ $link['url'] }}"
                            class="{{ $linkClass }} text-ui font-medium">{{ $link['label'] }}</a>
                    @endforeach
                </nav>
            </div>
            <div
                class="flex flex-wrap items-center justify-between gap-x-8 gap-y-1 border-t border-line pt-4 text-small text-muted">
                <span>© {{ date('Y') }} {{ config('app.name') }} · Ein Angebot der
                    <a href="https://www.esdigmbh.de/" target="_blank" rel="noopener"
                        class="text-muted underline-offset-[3px] hover:underline">ESdi GmbH</a></span>
                <div class="flex flex-wrap items-center gap-x-6">
                    <button type="button" onclick="window.showCookiePreferences()"
                        class="{{ $linkClass }} cursor-pointer border-0 bg-transparent p-0 text-small font-medium">Cookie-Einstellungen</button>
                    <a href="https://www.ev-schulen-sachsen.de/hauptfach-mensch-1" target="_blank" rel="noopener"
                        class="{{ $linkClass }} gap-2 text-small font-medium">
                        Mehr zur Aktion Hauptfach Mensch
                        <x-icon.external-link class="h-4 w-4" />
                        <span class="sr-only">(öffnet in neuem Fenster)</span>
                    </a>
                </div>
            </div>
        </div>
    </x-ui.zone>
@endif
