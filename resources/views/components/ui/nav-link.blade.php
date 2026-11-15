{{--
    Navigationslink in Kopfzeilen (öffentlicher Kopf, Veranstalter-Leiste): 44 px hoch, Text ui.
    Aktiv: Gewicht 600 und aria-current="page"; sonst 500. Hover: Unterstreichung (3 px Abstand).
    block: volle Breite (Einträge in Aufklappmenüs auf dem Handy).
--}}
@props([
    'href',
    'active' => false,
    'block' => false,
])

<a href="{{ $href }}" @if ($active) aria-current="page" @endif
    {{ $attributes->class([
        'items-center px-3 text-ui text-ink no-underline underline-offset-[3px] hover:underline',
        'inline-flex min-h-11 whitespace-nowrap' => !$block,
        'flex min-h-11 w-full' => $block,
        'font-semibold' => $active,
        'font-medium' => !$active,
    ]) }}>{{ $slot }}</a>
