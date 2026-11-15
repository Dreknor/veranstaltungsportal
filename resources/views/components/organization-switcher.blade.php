{{--
    Organisationswechsler im Kopf des angemeldeten Bereichs (B4). Aufklappmenü über <x-ui.dropdown>:
    Escape und Klick daneben schließen, der Fokus kehrt zum Button zurück.
    Unter 640 px richtet sich das Menü am Kopf aus, damit es nicht über den Rand läuft.
--}}
@php($org = isset($currentOrganization) ? $currentOrganization : auth()->user()->currentOrganization())
<x-ui.dropdown width="w-[calc(100vw-32px)] mr-4 sm:mr-0 sm:w-72" class="flex-none max-sm:static">
    <x-slot:trigger>
        <button type="button" x-ref="button" x-on:click="toggle()" x-bind:aria-expanded="open.toString()"
            aria-expanded="false"
            class="inline-flex h-11 max-w-[240px] cursor-pointer items-center gap-2 rounded-control border border-line-strong bg-surface px-3 text-ui font-medium text-ink transition-colors duration-150 hover:border-ink hover:bg-zone">
            <x-icon.building-office class="h-4 w-4 flex-none text-muted [&_*]:[stroke-width:1.6]" aria-hidden="true" />
            <span class="sr-only">Organisation: {{ $org ? $org->name : 'Keine Organisation' }}</span>
            @if ($org)
                <span class="hidden truncate sm:inline" aria-hidden="true">{{ $org->name }}</span>
            @else
                <span class="hidden truncate text-muted sm:inline" aria-hidden="true">Keine Organisation</span>
            @endif
            <x-icon.chevron-down class="h-4 w-4 flex-none text-muted" />
        </button>
    </x-slot:trigger>

    <p class="m-0 px-4 pt-2 pb-1 text-caption text-muted">Organisation wechseln</p>
    <div class="max-h-60 overflow-y-auto">
        @foreach (auth()->user()->activeOrganizations as $o)
            <form method="POST" action="{{ route('organizer.organizations.switch', $o) }}" class="m-0">
                @csrf
                <x-ui.dropdown-item type="submit" :aria-current="$org && $org->id === $o->id ? 'true' : null">
                    <span class="min-w-0 flex-1 truncate">{{ $o->name }}</span>
                    @if ($org && $org->id === $o->id)
                        <x-icon.check class="h-4 w-4 flex-none" />
                        <span class="sr-only">(aktiv)</span>
                    @endif
                </x-ui.dropdown-item>
            </form>
        @endforeach
    </div>
    <div class="mt-1 border-t border-line pt-1">
        <x-ui.dropdown-item :href="route('organizer.organizations.create')">
            <x-slot:icon><x-icon.plus /></x-slot:icon>
            Neue Organisation
        </x-ui.dropdown-item>
    </div>
</x-ui.dropdown>
