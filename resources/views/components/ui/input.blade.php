{{--
    Reines Eingabeelement ohne Label (Tabellenfilter, Sonderfälle). Für Formulare: <x-ui.field>.
    Ein Label muss der Aufrufer selbst setzen (<x-ui.label> oder aria-label).
--}}
@props([
    'type' => 'text',
    'name' => null,
    'id' => null,
    'value' => null,
    'size' => 'md', // md (48 px) | sm (44 px, Filterzeilen)
    'invalid' => null, // null = automatisch aus $errors
    'old' => true, // false: old() nicht berücksichtigen
    'bag' => 'default',
])

@php
    $id ??= \App\Support\Ui::id($name);
    $invalid ??= \App\Support\Ui::error($errors ?? null, $name, $bag) !== null;

    if ($type === 'password' || $type === 'file') {
        $value = null;
    } elseif ($old) {
        $value = \App\Support\Ui::old($name, $value);
    }

    $classes = \Illuminate\Support\Arr::toCssClasses([
        'block w-full rounded-control border border-line-strong bg-surface px-3.5 text-base text-ink transition-colors duration-150',
        'placeholder:text-muted hover:border-ink focus:border-ink',
        'disabled:cursor-not-allowed disabled:border-line disabled:bg-zone disabled:text-muted',
        'read-only:bg-zone',
        'aria-invalid:border-danger-field',
        'h-12' => $size !== 'sm',
        'h-11' => $size === 'sm',
        'py-2.5 file:mr-3 file:border-0 file:bg-transparent file:p-0 file:text-small file:font-medium file:text-ink' => $type === 'file',
    ]);
@endphp

<input
    type="{{ $type }}"
    @if ($name) name="{{ $name }}" @endif
    @if ($id) id="{{ $id }}" @endif
    @if ($value !== null && ! is_array($value)) value="{{ $value }}" @endif
    @if ($invalid) aria-invalid="true" @endif
    {{ $attributes->class($classes) }}
>
