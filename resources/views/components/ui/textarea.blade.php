{{--
    Mehrzeiliges Feld. Inhalt über value oder den Slot. class landet am äußeren <div>,
    alle übrigen Attribute am <textarea>.
--}}
@props([
    'label' => null,
    'name' => null,
    'id' => null,
    'value' => null,
    'rows' => 4,
    'required' => false,
    'optional' => false,
    'hint' => null,
    'error' => null,
    'old' => true,
    'bag' => 'default',
    'textareaClass' => '',
])

@php
    $id ??= \App\Support\Ui::id($name) ?? 'feld-' . \Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(6));
    $error ??= \App\Support\Ui::error($errors ?? null, $name, $bag);
    $describedBy = trim(($hint ? $id . '-hint ' : '') . ($error ? $id . '-error' : ''));
    $content = $value ?? ($slot->isNotEmpty() ? trim($slot->toHtml()) : null);
    $content = $old ? \App\Support\Ui::old($name, $content) : $content;
    $fromSlot = $value === null && $content !== null && $slot->isNotEmpty() && $content === trim($slot->toHtml());

    $classes = \Illuminate\Support\Arr::toCssClasses([
        'block min-h-30 w-full rounded-control border border-line-strong bg-surface px-3.5 py-3 text-base text-ink transition-colors duration-150',
        'placeholder:text-muted hover:border-ink focus:border-ink',
        'disabled:cursor-not-allowed disabled:border-line disabled:bg-zone disabled:text-muted',
        'read-only:bg-zone',
        'aria-invalid:border-danger-field',
        $textareaClass,
    ]);
@endphp

<div {{ $attributes->only('class') }}>
    @if ($label)
        <x-ui.label :for="$id" :required="$required" :optional="$optional">{{ $label }}</x-ui.label>
    @endif

    <textarea @if ($name) name="{{ $name }}" @endif
        @if ($id) id="{{ $id }}" @endif rows="{{ $rows }}" @required($required)
        @if ($error) aria-invalid="true" @endif
        @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
        {{ $attributes->except('class')->class($classes) }}>
@if ($fromSlot)
{!! $content !!}@else{{ $content }}
@endif
</textarea>

    @if ($hint)
        <x-ui.hint :id="$id . '-hint'">{{ $hint }}</x-ui.hint>
    @endif
    <x-ui.field-error :message="$error" :id="$id . '-error'" />
</div>
