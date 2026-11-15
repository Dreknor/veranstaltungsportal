{{--
    Dialog auf Basis von <dialog> (showModal): Fokus bleibt im Dialog, Escape schließt, der Fokus kehrt
    zum auslösenden Element zurück, der Rest der Seite ist inert. Weiß, Radius 12, bis 560 px, Polster 24.

    Öffnen:   x-on:click="$dispatch('open-modal', 'name')"   (oder Slot trigger)
    Schließen: x-on:click="$dispatch('close-modal', 'name')" (oder ein Button mit x-on:click="close()" im Dialog)
    show=true öffnet den Dialog beim Laden (z. B. nach Validierungsfehlern).
    Slots: Standard (Inhalt), footer (Aktionen unten rechts: sekundär links, primär rechts), trigger.
--}}
@props([
    'name',
    'title',
    'show' => false,
    'maxWidth' => '560px',
    'footer' => null,
    'trigger' => null,
])

@php
    $titleId = 'modal-'.\Illuminate\Support\Str::slug($name).'-title';
@endphp

<div
    x-data="{
        open() { this.$refs.dialog.showModal(); },
        close() { this.$refs.dialog.close(); },
    }"
    x-on:open-modal.window="if ($event.detail === @js($name)) open()"
    x-on:close-modal.window="if ($event.detail === @js($name)) close()"
    @if ($show) x-init="$nextTick(() => open())" @endif
    class="contents"
>
    @if ($trigger)
        <span x-on:click="open()" class="contents">{{ $trigger }}</span>
    @endif

    <dialog
        x-ref="dialog"
        aria-modal="true"
        aria-labelledby="{{ $titleId }}"
        x-on:click.self="close()"
        {{ $attributes->class('m-auto w-[calc(100%-32px)] rounded-box border border-line bg-surface p-0 text-ink shadow-overlay backdrop:bg-ink/50') }}
        style="max-width: {{ $maxWidth }}"
    >
        <div class="flex flex-col gap-4 p-6">
            <div class="flex items-start justify-between gap-4">
                <h2 id="{{ $titleId }}" class="m-0 pt-2 text-h3">{{ $title }}</h2>
                <button type="button" x-on:click="close()" aria-label="Dialog schließen" class="-mt-1 -mr-3 inline-flex h-11 w-11 flex-none cursor-pointer items-center justify-center rounded-control text-ink transition-colors duration-150 hover:bg-zone">
                    <x-icon.x class="h-5 w-5" />
                </button>
            </div>
            <div class="text-base">{{ $slot }}</div>
            @if ($footer)
                <div class="mt-2 flex flex-wrap justify-end gap-3">{{ $footer }}</div>
            @endif
        </div>
    </dialog>
</div>
