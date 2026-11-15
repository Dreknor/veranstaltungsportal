{{-- Gruppe von Posten in der Zusammenfassung, mit Linie darüber. Kinder: <x-ui.summary-line>. --}}
<div {{ $attributes->class('flex flex-col gap-2 border-t border-line pt-4') }}>
    {{ $slot }}
</div>
