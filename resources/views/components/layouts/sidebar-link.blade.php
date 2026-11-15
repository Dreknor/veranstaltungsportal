{{-- Eintrag der Seitenleiste als Listenpunkt; Gestaltung und Icon-Zuordnung in <x-ui.sidebar-item>. --}}
@props(['active' => false, 'href' => '#', 'icon' => null, 'count' => null, 'countLabel' => 'neu'])

<li>
    <x-ui.sidebar-item :href="$href" :active="$active" :icon="$icon" :count="$count" :count-label="$countLabel"
        {{ $attributes }}>
        {{ $slot }}
    </x-ui.sidebar-item>
</li>
