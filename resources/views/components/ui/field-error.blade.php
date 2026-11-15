{{--
    Fehlertext unter einem Feld. Ohne message wird der erste Fehler zu name aus $errors gelesen.
    Rendert nichts, wenn es keinen Fehler gibt.
--}}
@props([
    'name' => null,
    'message' => null,
    'id' => null,
    'bag' => 'default',
])

@php
    $message ??= \App\Support\Ui::error($errors ?? null, $name, $bag);
    $id ??= $name ? \App\Support\Ui::id($name) . '-error' : null;
@endphp

@if ($message)
    <p @if ($id) id="{{ $id }}" @endif
        {{ $attributes->class('mt-1.5 flex items-start gap-1.5 text-small text-danger') }}>
        <x-icon.error class="mt-0.5 h-4 w-4 shrink-0" />
        <span>{{ $message }}</span>
    </p>
@endif
