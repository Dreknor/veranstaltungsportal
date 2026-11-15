{{-- Eintrag der dritten Ebene in der Seitenleiste (ohne Icon). Derzeit nicht in Verwendung. --}}
@props(['active' => false, 'href' => '#'])

<li>
    <x-ui.sidebar-item :href="$href" :active="$active" {{ $attributes }}>
        {{ $slot }}
    </x-ui.sidebar-item>
</li>
