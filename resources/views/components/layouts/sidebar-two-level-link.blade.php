{{-- Eintrag der zweiten Ebene in der Seitenleiste. Derzeit nicht in Verwendung. --}}
@props(['active' => false, 'href' => '#', 'icon' => null])

<li>
    <x-ui.sidebar-item :href="$href" :active="$active" :icon="$icon" {{ $attributes }}>
        {{ $slot }}
    </x-ui.sidebar-item>
</li>
