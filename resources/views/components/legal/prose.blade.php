{{--
    Lesetext für Rechtstexte aus der Datenbank (LegalPage, HTML aus dem Editor).
    Lokaler Behelf, bis app.css eine Klasse .richtext bereitstellt (B6): Schriftregeln aus A3,
    Lesebreite 760 px. Überschriften linksbündig, Listen mit normalem Aufzählungszeichen.
--}}
<div
    {{ $attributes->class([
        'max-w-prose text-base leading-[1.6] text-ink',
        '[&>:first-child]:mt-0',
        '[&_h2]:mt-12 [&_h2]:mb-3 [&_h2]:text-h2',
        '[&_h3]:mt-8 [&_h3]:mb-2 [&_h3]:text-h3',
        '[&_h4]:mt-6 [&_h4]:mb-2 [&_h4]:text-base [&_h4]:font-semibold',
        '[&_p]:mt-0 [&_p]:mb-4',
        '[&_ul]:mt-0 [&_ul]:mb-4 [&_ul]:list-disc [&_ul]:pl-6',
        '[&_ol]:mt-0 [&_ol]:mb-4 [&_ol]:list-decimal [&_ol]:pl-6',
        '[&_li]:mb-1',
        '[&_a]:font-medium [&_a]:text-ink [&_a]:underline [&_a]:underline-offset-[3px]',
        '[&_strong]:font-semibold',
        '[&_blockquote]:my-6 [&_blockquote]:border-l [&_blockquote]:border-line-strong [&_blockquote]:pl-4 [&_blockquote]:text-ink-soft',
        '[&_hr]:my-8 [&_hr]:border-0 [&_hr]:border-t [&_hr]:border-line',
    ]) }}>
    {{ $slot }}
</div>
