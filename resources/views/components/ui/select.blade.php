{{--
    Auswahlfeld mit Chevron. Mit label inklusive Beschriftung, Hinweis und Fehler; ohne label nur das Feld.
    Optionen im Slot oder über :options (Wert => Text). class landet am äußeren <div>,
    alle übrigen Attribute am <select>.
--}}
@props([
    'label' => null,
    'name' => null,
    'id' => null,
    'options' => null, // ['wert' => 'Text', …]
    'selected' => null,
    'placeholder' => null, // leere erste Option
    'required' => false,
    'optional' => false,
    'hint' => null, // Text; HTML über <x-slot:hint>
    'error' => null,
    'size' => 'md',
    'old' => true,
    'bag' => 'default',
    'selectClass' => '',
])

@php
    $id ??= \App\Support\Ui::id($name) ?? 'feld-' . \Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(6));
    $error ??= \App\Support\Ui::error($errors ?? null, $name, $bag);
    // Hinweis als Prop (Text) oder als Slot <x-slot:hint> (HTML, z. B. mit Link)
    $hasHint = $hint instanceof \Illuminate\View\ComponentSlot ? $hint->hasActualContent() : filled($hint);
    $describedBy = trim(($hasHint ? $id . '-hint ' : '') . ($error ? $id . '-error' : ''));
    $current = $old ? \App\Support\Ui::old($name, $selected) : $selected;

    $classes = \Illuminate\Support\Arr::toCssClasses([
        'block w-full cursor-pointer appearance-none truncate rounded-control border border-line-strong bg-surface py-0 pr-10 pl-3.5 text-base text-ink transition-colors duration-150',
        'hover:border-ink focus:border-ink',
        'disabled:cursor-not-allowed disabled:border-line disabled:bg-zone disabled:text-muted',
        'aria-invalid:border-danger-field',
        'h-12' => $size !== 'sm',
        'h-11' => $size === 'sm',
        $selectClass,
    ]);
@endphp

<div {{ $attributes->only('class') }}>
    @if ($label)
        <x-ui.label :for="$id" :required="$required" :optional="$optional">{{ $label }}</x-ui.label>
    @endif

    <div class="relative">
        <select @if ($name) name="{{ $name }}" @endif
            @if ($id) id="{{ $id }}" @endif @required($required)
            @if ($error) aria-invalid="true" @endif
            @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
            {{ $attributes->except('class')->class($classes) }}>
            @if ($placeholder !== null)
                <option value="">{{ $placeholder }}</option>
            @endif
            @if ($options !== null)
                @foreach ($options as $optionValue => $optionLabel)
                    <option value="{{ $optionValue }}" @selected($current !== null && (string) $current === (string) $optionValue)>{{ $optionLabel }}</option>
                @endforeach
            @endif
            {{ $slot }}
        </select>
        <x-icon.chevron-down class="pointer-events-none absolute top-1/2 right-3.5 h-4 w-4 -translate-y-1/2 text-ink" />
    </div>

    @if ($hasHint)
        <x-ui.hint :id="$id . '-hint'">{{ $hint }}</x-ui.hint>
    @endif
    <x-ui.field-error :message="$error" :id="$id . '-error'" />
</div>
