{{--
    Mengenwahl: Minus, Zahlenfeld, Plus. Alle Attribute außer class landen am <input>
    (name, id, min, max, step, data-*, x-model, eigene Klassen über input-class).
    Die Buttons ändern den Wert des Feldes und lösen daran "input" und "change" aus (bubbling) –
    bestehendes JavaScript hört also wie gewohnt auf das Feld. min/max/step werden bei jedem Klick
    frisch vom Feld gelesen und dürfen sich zur Laufzeit ändern.
    Setzt fremdes Skript den Wert direkt, muss es danach selbst "input" auslösen, damit die Buttons
    ihren Zustand (deaktiviert bei min/max) nachziehen.
--}}
@props([
    'name' => null,
    'id' => null,
    'value' => 0,
    'min' => 0,
    'max' => null,
    'step' => 1,
    'label' => 'Anzahl', // Name der Sache, z. B. "Teilnahme" – für aria-label der Buttons und des Feldes
    'unit' => 'Ticket',
    'disabled' => false,
    'old' => true,
    'inputClass' => '',
])

@php
    $id ??= \App\Support\Ui::id($name) ?? 'stepper-'.\Illuminate\Support\Str::random(6);
    $value = $old ? \App\Support\Ui::old($name, $value) : $value;
    $value = is_numeric($value) ? $value + 0 : $min;
@endphp

<div
    role="group"
    aria-label="Anzahl {{ $label }}"
    x-data="{
        value: {{ \Illuminate\Support\Js::from($value) }},
        low: {{ \Illuminate\Support\Js::from($min) }},
        high: {{ \Illuminate\Support\Js::from($max) }},
        limits() {
            const el = this.$refs.input;
            const num = (name, fallback) => {
                const raw = el.getAttribute(name);
                return raw === null || raw === '' || isNaN(Number(raw)) ? fallback : Number(raw);
            };
            this.low = num('min', 0);
            this.high = num('max', null);
            return { step: num('step', 1) || 1 };
        },
        clamp(n) {
            if (isNaN(n)) n = this.low;
            if (n < this.low) n = this.low;
            if (this.high !== null && n > this.high) n = this.high;
            return n;
        },
        sync() {
            this.limits();
            this.value = parseInt(this.$refs.input.value, 10) || 0;
        },
        commit() {
            this.limits();
            const el = this.$refs.input;
            const n = this.clamp(parseInt(el.value, 10));
            if (String(n) !== el.value) {
                el.value = n;
                el.dispatchEvent(new Event('input', { bubbles: true }));
            }
            this.value = n;
        },
        change(direction) {
            const el = this.$refs.input;
            if (el.disabled) return;
            const { step } = this.limits();
            const n = this.clamp((parseInt(el.value, 10) || 0) + direction * step);
            if (String(n) === el.value) return;
            el.value = n;
            this.value = n;
            el.dispatchEvent(new Event('input', { bubbles: true }));
            el.dispatchEvent(new Event('change', { bubbles: true }));
        },
    }"
    x-init="sync()"
    {{ $attributes->only('class')->class('inline-flex h-[46px] items-center rounded-control border border-line-strong bg-surface text-ink') }}
>
    <button
        type="button"
        aria-label="{{ $label }}: ein {{ $unit }} weniger"
        class="focus-inset inline-flex h-11 w-11 cursor-pointer items-center justify-center rounded-[7px] bg-surface text-ink transition-colors duration-150 hover:bg-zone disabled:cursor-not-allowed disabled:bg-surface disabled:text-disabled"
        @disabled($disabled || $value <= $min)
        x-bind:disabled="$refs.input.disabled || value <= low"
        x-on:click="change(-1)"
    >
        <x-icon.minus class="h-4 w-4" />
    </button>
    <label class="sr-only" for="{{ $id }}">Anzahl {{ $label }}</label>
    <input
        type="text"
        inputmode="numeric"
        pattern="[0-9]*"
        autocomplete="off"
        @if ($name) name="{{ $name }}" @endif
        id="{{ $id }}"
        value="{{ $value }}"
        min="{{ $min }}"
        @if ($max !== null) max="{{ $max }}" @endif
        step="{{ $step }}"
        @disabled($disabled)
        x-ref="input"
        x-on:input="sync()"
        x-on:change="commit()"
        x-bind:class="value > 0 ? 'text-ink' : 'text-muted'"
        {{ $attributes->except('class')->class(['focus-inset h-11 min-h-0 w-10 rounded-[7px] border-0 bg-transparent p-0 text-center text-base font-semibold tabular-nums disabled:cursor-not-allowed disabled:bg-transparent disabled:text-disabled', $inputClass]) }}
    >
    <button
        type="button"
        aria-label="{{ $label }}: ein {{ $unit }} mehr"
        class="focus-inset inline-flex h-11 w-11 cursor-pointer items-center justify-center rounded-[7px] bg-surface text-ink transition-colors duration-150 hover:bg-zone disabled:cursor-not-allowed disabled:bg-surface disabled:text-disabled"
        @disabled($disabled || ($max !== null && $value >= $max))
        x-bind:disabled="$refs.input.disabled || (high !== null && value >= high)"
        x-on:click="change(1)"
    >
        <x-icon.plus class="h-4 w-4" />
    </button>
</div>
