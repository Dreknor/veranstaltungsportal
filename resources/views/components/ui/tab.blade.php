{{--
    Ein Reiter in <x-ui.tabs>. count: Zahl hinter dem Text (z. B. offene Buchungen).
    Mit href ein Link (aktiv: aria-current="page"). Ohne href ein <button type="button"> für
    <x-ui.tabs variant="toggle">: aktiv ist er bei aria-pressed="true" – active setzt den Startwert,
    Alpine hält ihn aktuell (x-on:click="tab = 'a'" :aria-pressed="tab === 'a'").
--}}
@props(['href' => null, 'active' => false, 'count' => null])

@php
    $base =
        'focus-inset -mb-px inline-flex min-h-11 items-center gap-2 border-b-2 px-3 text-ui whitespace-nowrap no-underline transition-colors duration-150';
@endphp

<li class="flex-none">
    @if ($href !== null)
        <a href="{{ $href }}" @if ($active) aria-current="page" @endif
            {{ $attributes->class([
                $base,
                'border-ink font-semibold text-ink' => $active,
                'border-transparent font-medium text-muted hover:border-line-strong hover:text-ink' => !$active,
            ]) }}>
            {{ $slot }}
            @if ($count !== null)
                <span class="text-small font-normal text-muted tabular-nums">{{ $count }}</span>
            @endif
        </a>
    @else
        <button type="button" aria-pressed="{{ $active ? 'true' : 'false' }}"
            {{ $attributes->class([
                $base,
                'cursor-pointer border-transparent bg-transparent font-medium text-muted hover:border-line-strong hover:text-ink',
                'aria-pressed:border-ink aria-pressed:font-semibold aria-pressed:text-ink',
            ]) }}>
            {{ $slot }}
            @if ($count !== null)
                <span class="text-small font-normal text-muted tabular-nums">{{ $count }}</span>
            @endif
        </button>
    @endif
</li>
