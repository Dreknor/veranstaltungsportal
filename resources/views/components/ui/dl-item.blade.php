{{-- Ein Paar in <x-ui.dl>. Wert im Slot; leere Werte zeigen "–". num: tabellarische Ziffern. --}}
@props(['label', 'num' => false])

<div
    {{ $attributes->class('flex flex-col gap-1 py-3 in-data-[layout=rows]:md:grid in-data-[layout=rows]:md:grid-cols-[200px_minmax(0,1fr)] in-data-[layout=rows]:md:gap-6 in-data-[layout=stack]:py-0') }}>
    <dt class="text-small text-muted">{{ $label }}</dt>
    <dd @class(['m-0 min-w-0 text-base break-words', 'tabular-nums' => $num])>{{ $slot->isEmpty() ? '–' : $slot }}</dd>
</div>
