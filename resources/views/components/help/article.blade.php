{{--
    Rahmen eines Hilfeartikels (Muster siehe Kommentar oben in help/HELP_ARTICLE_TEMPLATE.blade.php).
    Bringt das Seitengerüst (Layout app) selbst mit – Artikel dürfen nicht zusätzlich darin stehen.
    Seitenkopf mit Zurück-Link und H1, Lesetext höchstens 760 px mit Schriftregeln für h2/h3, Absätze,
    Listen, Links und <details>, darunter optional eine Linkliste "Weiterführende Artikel".
    Der Artikel schreibt nur schlichtes HTML (section, h2, h3, p, ul, ol, details) und Bausteine.
    Die Typografie nutzt :where(), damit Bausteine im Inhalt (z. B. ui.alert) ihre eigenen Abstände behalten.
--}}
@props([
    'title',
    'subtitle' => null,
    'backHref' => null, // Standard: Hilfe-Übersicht
    'backLabel' => 'Zur Hilfe-Übersicht',
    'related' => null, // Slot: Linkliste verwandter Artikel
    'relatedTitle' => 'Weiterführende Artikel',
])

<x-layouts.app :title="$title">
    <article {{ $attributes }}>
        <x-ui.page-header :title="$title" :subtitle="$subtitle" :back-href="$backHref ?? route('help.index')" :back-label="$backLabel" />

        <div
            class="max-w-prose text-base text-ink [&_:where(a:not([class]))]:underline [&_:where(a:not([class]))]:underline-offset-[3px] [&_:where(li)]:my-1 [&_:where(ol)]:my-3 [&_:where(ol)]:list-decimal [&_:where(ol)]:pl-6 [&_:where(p)]:my-3 [&_:where(strong)]:font-semibold [&_:where(ul)]:my-3 [&_:where(ul)]:list-disc [&_:where(ul)]:pl-6 [&>:first-child]:mt-0 [&_section+[role]]:mt-12 [&_:where([role=alert],[role=status])]:my-6 [&_details]:border-b [&_details]:border-line [&_details]:py-2 [&_details:first-of-type]:border-t [&_h2]:m-0 [&_h2]:mb-3 [&_h2]:text-h2 [&_section>h3:first-child]:mt-0 [&_h3]:mt-6 [&_h3]:mb-2 [&_h3]:text-h3 [&_section]:mt-12 [&_summary]:min-h-11 [&_summary]:cursor-pointer [&_summary]:py-2 [&_summary]:font-semibold">
            {{ $slot }}
        </div>

        @if ($related)
            <nav aria-labelledby="verwandte-artikel" class="mt-12 max-w-prose border-t border-line pt-8">
                <h2 id="verwandte-artikel" class="m-0 mb-3 text-h3 text-ink">{{ $relatedTitle }}</h2>
                <div class="flex flex-col items-start">
                    {{ $related }}
                </div>
            </nav>
        @endif
    </article>
</x-layouts.app>
