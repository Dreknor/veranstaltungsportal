{{-- Aufklappbarer Eintrag der dritten Ebene in der Seitenleiste. Derzeit nicht in Verwendung. --}}
@props(['active' => false, 'title' => '', 'icon' => 'list'])

<li x-data="{ subOpen: {{ $active ? 'true' : 'false' }} }">
    <x-ui.sidebar-item :icon="$icon" :active="$active" x-on:click="subOpen = !subOpen"
        x-bind:aria-expanded="subOpen.toString()" :aria-expanded="($active ? 'true' : 'false')">
        <span class="flex items-center justify-between gap-2">
            {{ $title }}
            <x-icon.chevron-down class="h-4 w-4 flex-none transition-transform duration-150"
                x-bind:class="subOpen && 'rotate-180'" />
        </span>
    </x-ui.sidebar-item>

    <ul x-show="subOpen" x-cloak class="m-0 mt-0.5 flex list-none flex-col gap-0.5 p-0 pl-8">
        {{ $slot }}
    </ul>
</li>
