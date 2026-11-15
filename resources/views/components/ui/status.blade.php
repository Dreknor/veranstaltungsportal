{{--
    Status als Punkt plus Text – nie Farbe allein, nie gefüllte Pille.
    free: Blattgrün-Punkt ("noch 6 frei"); waitlist: hohler Ring ("Warteliste");
    success | warning | danger | info | neutral: Buchungs- und Zahlungsstatus im Backend.
--}}
@props([
    'tone' => 'neutral',
])

@php
    $text = match ($tone) {
        'success' => 'text-success',
        'warning' => 'text-warning',
        'danger' => 'text-danger',
        'info' => 'text-ink',
        default => 'text-ink-soft',
    };
    $dot = match ($tone) {
        'free' => 'bg-leaf',
        'waitlist' => 'border-[1.5px] border-muted',
        'success' => 'bg-success',
        'warning' => 'bg-warning',
        'danger' => 'bg-danger',
        'info' => 'bg-primary',
        default => 'bg-muted',
    };
@endphp

<span {{ $attributes->class(['inline-flex items-center gap-1.5 text-small whitespace-nowrap', $text]) }}>
    <span aria-hidden="true" class="h-2 w-2 flex-none rounded-full {{ $dot }}"></span>
    {{ $slot }}
</span>
