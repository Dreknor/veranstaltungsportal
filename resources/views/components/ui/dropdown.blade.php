{{--
    Aufklappmenü, z. B. "Mehr" in Tabellenzeilen ab drei Aktionen. Einträge: <x-ui.dropdown-item>.
    Bedienung: Klick öffnet/schließt, Escape und Klick daneben schließen, Fokus kehrt zum Button zurück.
    Eigener Auslöser über Slot trigger (muss ein <button> sein; x-on:click="toggle()" setzen).
--}}
@props([
    'label' => 'Mehr',
    'align' => 'right', // right | left
    'size' => 'sm', // sm (36 px, Tabellenzeilen) | md (44 px)
    'iconOnly' => false, // nur Punkte-Icon; label wird zum aria-label
    'width' => 'w-56',
    'trigger' => null,
])

@php
    $menuId = 'menu-' . \Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(8));
@endphp

<div x-data="{
    open: false,
    top: 0,
    left: 0,
    // Das Menü steht fest positioniert (fixed), damit es nicht im Scrollbereich von Tabellen abgeschnitten wird.
    place() {
        const anchor = (this.$refs.button || this.$el).getBoundingClientRect();
        const menu = this.$refs.menu;
        const w = menu.offsetWidth, h = menu.offsetHeight, gap = 4, edge = 8;
        let top = anchor.bottom + gap;
        if (top + h > window.innerHeight - edge && anchor.top - h - gap > edge) top = anchor.top - h - gap;
        let left = {{ $align === 'left' ? 'anchor.left' : 'anchor.right - w' }};
        left = Math.min(Math.max(left, edge), window.innerWidth - w - edge);
        this.top = top; this.left = left;
    },
    toggle() { this.open = !this.open; if (this.open) this.$nextTick(() => this.place()); },
    close(focus = true) { if (!this.open) return;
        this.open = false; if (focus) this.$refs.button?.focus(); },
}" x-on:keydown.escape.prevent.stop="close()" x-on:click.outside="close(false)"
    x-on:focusout="if (! $el.contains($event.relatedTarget)) close(false)"
    x-on:scroll.window.capture="open && place()" x-on:resize.window="open && place()"
    {{ $attributes->class('relative inline-block text-left') }}>
    @if ($trigger)
        {{ $trigger }}
    @else
        <x-ui.button variant="secondary" :size="$size" x-ref="button" x-on:click="toggle()"
            x-bind:aria-expanded="open.toString()" aria-expanded="false" aria-controls="{{ $menuId }}"
            :aria-label="$iconOnly ? $label : null" @class([
                'w-11 px-0' => $iconOnly && $size !== 'sm',
                'w-9 px-0' => $iconOnly && $size === 'sm',
            ])>
            @if ($iconOnly)
                <x-icon.more class="h-5 w-5" />
            @else
                {{ $label }}
                <x-icon.chevron-down class="h-4 w-4 transition-transform duration-150"
                    x-bind:class="open && 'rotate-180'" />
            @endif
        </x-ui.button>
    @endif

    <div id="{{ $menuId }}" x-ref="menu" x-show="open" x-cloak
        x-bind:style="`top: ${top}px; left: ${left}px`" @class([
        'fixed z-50 flex flex-col rounded-box border border-line bg-surface py-1 text-ink shadow-overlay',
        $width,
    ])>
        {{ $slot }}
    </div>
</div>
