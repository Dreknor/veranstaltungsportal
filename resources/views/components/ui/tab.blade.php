{{-- Ein Reiter in <x-ui.tabs>. count: Zahl hinter dem Text (z. B. offene Buchungen). --}}
@props([
    'href',
    'active' => false,
    'count' => null,
])

<li class="flex-none">
    <a
        href="{{ $href }}"
        @if ($active) aria-current="page" @endif
        {{ $attributes->class([
            'focus-inset -mb-px inline-flex min-h-11 items-center gap-2 border-b-2 px-3 text-ui whitespace-nowrap no-underline transition-colors duration-150',
            'border-ink font-semibold text-ink' => $active,
            'border-transparent font-medium text-muted hover:border-line-strong hover:text-ink' => ! $active,
        ]) }}
    >
        {{ $slot }}
        @if ($count !== null)
            <span class="text-small font-normal text-muted tabular-nums">{{ $count }}</span>
        @endif
    </a>
</li>
