<x-layouts.app title="Veranstaltungsverwaltung" :header="false">
    <x-ui.page-header title="Veranstaltungsverwaltung" subtitle="Verwalten Sie alle Veranstaltungen im System." />

    <form action="{{ route('admin.events.index') }}" method="GET"
        class="mb-6 grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] items-end gap-x-4 gap-y-5">
        <x-ui.field label="Suche" name="search" :old="false" :value="request('search')" placeholder="Titel oder Beschreibung" />
        <x-ui.select label="Status" name="status" :old="false"
            :options="['' => 'Alle', 'published' => 'Veröffentlicht', 'draft' => 'Entwurf', 'featured' => 'Featured']"
            :selected="request('status')" />
        <x-ui.select label="Kategorie" name="category" :old="false"
            :options="['' => 'Alle'] + $categories->pluck('name', 'id')->all()" :selected="request('category')" />
        <div class="flex flex-wrap items-center gap-3">
            <x-ui.button type="submit" variant="secondary" size="lg">Filtern</x-ui.button>
            <x-ui.button variant="link" :href="route('admin.events.index')">Zurücksetzen</x-ui.button>
        </div>
    </form>

    <x-ui.table label="Veranstaltungen">
        <thead>
            <tr>
                <th scope="col">Veranstaltung</th>
                <th scope="col">Veranstalter</th>
                <th scope="col">Kategorie</th>
                <th scope="col">Status</th>
                <th scope="col">Datum</th>
                <th scope="col" class="num">Buchungen</th>
                <th scope="col" class="actions"><span class="sr-only">Aktionen</span></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($events as $event)
                <tr>
                    <th scope="row" class="min-w-56">
                        <span class="block">{{ Str::limit($event->title, 40) }}</span>
                        <span class="block text-small font-normal text-muted">{{ $event->slug }}</span>
                    </th>
                    <td>
                        <span class="block">{{ $event->getOrganizerName() }}</span>
                        <span class="block text-small text-muted">{{ $event->getOrganizerEmail() ?? 'N/A' }}</span>
                    </td>
                    <td class="text-muted">{{ $event->category->name }}</td>
                    <td>
                        <span class="flex flex-col items-start gap-1">
                            @if ($event->is_published)
                                <x-ui.status tone="success">Veröffentlicht</x-ui.status>
                            @else
                                <x-ui.status tone="warning">Entwurf</x-ui.status>
                            @endif
                            @if ($event->is_featured)
                                <x-ui.status tone="info">Featured</x-ui.status>
                            @endif
                        </span>
                    </td>
                    <td class="whitespace-nowrap tabular-nums">{{ $event->start_date->format('d.m.Y H:i') }}</td>
                    <td class="num">{{ $event->bookings_count }}</td>
                    <td class="actions">
                        <x-ui.dropdown label="Aktionen für {{ $event->title }}" icon-only>
                            <x-ui.dropdown-item :href="route('events.show', $event->slug)" target="_blank" rel="noopener">Anzeigen</x-ui.dropdown-item>
                            <x-ui.dropdown-item type="submit" form="publish-{{ $event->id }}">{{ $event->is_published ? 'Verstecken' : 'Veröffentlichen' }}</x-ui.dropdown-item>
                            <x-ui.dropdown-item type="submit" form="feature-{{ $event->id }}">{{ $event->is_featured ? 'Unfeaturen' : 'Featuren' }}</x-ui.dropdown-item>
                            <x-ui.dropdown-item danger type="submit" form="destroy-{{ $event->id }}">Löschen</x-ui.dropdown-item>
                        </x-ui.dropdown>
                        <form id="publish-{{ $event->id }}" action="{{ route('admin.events.toggle-publish', $event) }}" method="POST" class="hidden">@csrf</form>
                        <form id="feature-{{ $event->id }}" action="{{ route('admin.events.toggle-featured', $event) }}" method="POST" class="hidden">@csrf</form>
                        <form id="destroy-{{ $event->id }}" action="{{ route('admin.events.destroy', $event) }}" method="POST"
                            onsubmit="return confirm('Veranstaltung wirklich löschen?');" class="hidden">@csrf @method('DELETE')</form>
                    </td>
                </tr>
            @empty
                <tr class="hover:bg-transparent">
                    <td colspan="7" class="p-0">
                        <x-ui.empty :bordered="false">Keine Veranstaltungen gefunden.</x-ui.empty>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-ui.table>

    @if ($events->hasPages())
        <div class="mt-6">
            {{ $events->links() }}
        </div>
    @endif
</x-layouts.app>
