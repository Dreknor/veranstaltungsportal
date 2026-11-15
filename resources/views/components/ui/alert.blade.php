{{--
    Hinweis / Meldung: Fläche + Kontur + Text + Icon in der Meldungsfarbe (A2). Ersetzt alle farbigen
    Meldungskästen. Text im Slot (Absätze, Listen, Links erlaubt). dismissible nur für flüchtige Meldungen.
    role: danger/warning "alert", sonst "status" – überschreibbar mit role="…".
--}}
@props([
    'tone' => 'info', // info | success | warning | danger
    'title' => null,
    'dismissible' => false,
    'size' => 'small', // small (14 px) | base (16 px)
])

@php
    $toneClasses = match ($tone) {
        'success' => 'border-success-line bg-success-zone text-success',
        'warning' => 'border-warning-line bg-warning-zone text-warning',
        'danger' => 'border-danger-line bg-danger-zone text-danger',
        default => 'border-line bg-zone text-ink',
    };
    $icon = match ($tone) {
        'success' => 'icon.check-circle',
        'warning' => 'icon.warning',
        'danger' => 'icon.error',
        default => 'icon.info',
    };
    $role = in_array($tone, ['danger', 'warning'], true) ? 'alert' : 'status';
@endphp

<div
    role="{{ $role }}"
    @if ($dismissible) x-data="{ open: true }" x-show="open" @endif
    {{ $attributes->class(['flex items-start gap-3 rounded-control border px-4 py-3', $toneClasses, 'text-small' => $size !== 'base', 'text-base' => $size === 'base']) }}
>
    <x-dynamic-component :component="$icon" class="mt-px h-5 w-5 flex-none" />
    <div class="min-w-0 flex-1 [&_a]:font-medium [&_a]:underline [&_a]:underline-offset-[3px] [&_p]:m-0 [&_ul]:m-0 [&_ul]:list-disc [&_ul]:pl-5">
        @if ($title)
            <p class="m-0 font-semibold">{{ $title }}</p>
        @endif
        {{ $slot }}
    </div>
    @if ($dismissible)
        <button
            type="button"
            x-on:click="open = false"
            aria-label="Meldung schließen"
            class="-my-2.5 -mr-3 inline-flex h-11 w-11 flex-none cursor-pointer items-center justify-center rounded-control text-current transition-colors duration-150 hover:bg-surface/60"
        >
            <x-icon.x class="h-4 w-4" />
        </button>
    @endif
</div>
