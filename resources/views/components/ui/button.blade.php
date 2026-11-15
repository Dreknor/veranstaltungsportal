{{--
    Button und Textlink. Mit href ein <a>, sonst ein <button type="button">.
    Formulare absenden: type="submit" angeben.
--}}
@props([
    'variant' => 'primary', // primary | secondary | danger | link
    'size' => 'md', // sm (36 px, nur Tabellenzeilen) | md (44 px) | lg (48 px) | xl (52 px)
    'href' => null,
    'type' => 'button',
    'full' => false,
    'disabled' => false,
    'arrow' => false, // Pfeil hinter dem Text (v. a. variant="link")
    'icon' => null, // Slot: Icon vor dem Text
])

@php
    $isLink = $variant === 'link';

    $classes = \Illuminate\Support\Arr::toCssClasses([
        'inline-flex items-center justify-center gap-2 font-medium no-underline transition-colors duration-150 cursor-pointer',
        'disabled:cursor-not-allowed aria-disabled:cursor-not-allowed',
        'w-full' => $full,

        // Flächen-Buttons
        'rounded-control border whitespace-nowrap' => ! $isLink,
        'disabled:border-line disabled:bg-zone disabled:text-disabled aria-disabled:border-line aria-disabled:bg-zone aria-disabled:text-disabled' => ! $isLink,
        'h-9 px-3 text-small' => ! $isLink && $size === 'sm',
        'h-11 px-4 text-ui' => ! $isLink && $size === 'md',
        'h-12 px-5 text-base' => ! $isLink && $size === 'lg',
        'h-13 px-5 text-base font-semibold' => ! $isLink && $size === 'xl',
        'border-primary bg-primary text-on-primary hover:border-ink hover:bg-ink' => $variant === 'primary',
        'border-line-strong bg-surface text-ink hover:border-ink hover:bg-zone' => $variant === 'secondary',
        'border-danger bg-danger text-on-primary hover:border-danger-hover hover:bg-danger-hover' => $variant === 'danger',

        // Textlink
        'text-ink underline-offset-[3px] hover:underline disabled:text-disabled disabled:no-underline aria-disabled:text-disabled aria-disabled:no-underline' => $isLink,
        'min-h-9 text-small' => $isLink && $size === 'sm',
        'min-h-11 text-ui' => $isLink && $size === 'md',
        'min-h-11 text-base font-semibold' => $isLink && in_array($size, ['lg', 'xl'], true),
    ]);
@endphp

@if ($href !== null && ! $disabled)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>
        @if ($icon)<span class="shrink-0 [&>svg]:h-4 [&>svg]:w-4">{{ $icon }}</span>@endif
        {{ $slot }}
        @if ($arrow)<x-icon.arrow-right class="h-4 w-4 shrink-0" />@endif
    </a>
@elseif ($href !== null)
    <a role="link" aria-disabled="true" {{ $attributes->class($classes) }}>
        @if ($icon)<span class="shrink-0 [&>svg]:h-4 [&>svg]:w-4">{{ $icon }}</span>@endif
        {{ $slot }}
        @if ($arrow)<x-icon.arrow-right class="h-4 w-4 shrink-0" />@endif
    </a>
@else
    <button type="{{ $type }}" @disabled($disabled) {{ $attributes->class($classes) }}>
        @if ($icon)<span class="shrink-0 [&>svg]:h-4 [&>svg]:w-4">{{ $icon }}</span>@endif
        {{ $slot }}
        @if ($arrow)<x-icon.arrow-right class="h-4 w-4 shrink-0" />@endif
    </button>
@endif
