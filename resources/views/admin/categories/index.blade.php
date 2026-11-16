<x-layouts.app title="Kategorien verwalten" :header="false">
    <x-ui.page-header title="Kategorien verwalten" subtitle="Verwalten Sie die Veranstaltungskategorien.">
        <x-slot:actions>
            <x-ui.button :href="route('admin.categories.create')">
                <x-slot:icon><x-icon.plus /></x-slot:icon>
                Neue Kategorie
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <form method="GET" action="{{ route('admin.categories.index') }}"
        class="mb-6 grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] items-end gap-x-4 gap-y-5">
        <x-ui.field label="Suche" name="search" :old="false" :value="request('search')" placeholder="Kategorie suchen" />
        <div class="flex flex-wrap items-center gap-3">
            <x-ui.button type="submit" variant="secondary" size="lg">Suchen</x-ui.button>
            @if (request('search'))
                <x-ui.button variant="link" :href="route('admin.categories.index')">Zurücksetzen</x-ui.button>
            @endif
        </div>
    </form>

    <x-ui.table label="Kategorien">
        <thead>
            <tr>
                <th scope="col">Name</th>
                <th scope="col">Slug</th>
                <th scope="col">Icon</th>
                <th scope="col" class="num">Veranstaltungen</th>
                <th scope="col">Status</th>
                <th scope="col" class="actions"><span class="sr-only">Aktionen</span></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($categories as $category)
                <tr>
                    <th scope="row">{{ $category->name }}</th>
                    <td class="text-muted">{{ $category->slug }}</td>
                    <td class="text-muted">
                        <span class="inline-flex items-center gap-2">
                            <x-ui.icon :name="$category->icon" fallback="calendar" class="h-5 w-5 flex-none text-ink" />
                            {{ $category->icon }}
                        </span>
                    </td>
                    <td class="num">{{ $category->events_count }}</td>
                    <td>
                        @if ($category->is_active)
                            <x-ui.status tone="success">Aktiv</x-ui.status>
                        @else
                            <x-ui.status tone="neutral">Inaktiv</x-ui.status>
                        @endif
                    </td>
                    <td class="actions">
                        <x-ui.dropdown label="Aktionen für {{ $category->name }}" icon-only>
                            <x-ui.dropdown-item :href="route('admin.categories.edit', $category)">Bearbeiten</x-ui.dropdown-item>
                            <x-ui.dropdown-item type="submit" form="toggle-{{ $category->id }}">{{ $category->is_active ? 'Deaktivieren' : 'Aktivieren' }}</x-ui.dropdown-item>
                            @if ($category->events_count === 0)
                                <x-ui.dropdown-item danger type="submit" form="destroy-{{ $category->id }}">Löschen</x-ui.dropdown-item>
                            @endif
                        </x-ui.dropdown>
                        <form id="toggle-{{ $category->id }}" method="POST" action="{{ route('admin.categories.toggle-active', $category) }}" class="hidden">@csrf</form>
                        @if ($category->events_count === 0)
                            <form id="destroy-{{ $category->id }}" method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                                onsubmit="return confirm('Sind Sie sicher, dass Sie diese Kategorie löschen möchten?');" class="hidden">@csrf @method('DELETE')</form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr class="hover:bg-transparent">
                    <td colspan="6" class="p-0">
                        <x-ui.empty :bordered="false">Keine Kategorien gefunden.</x-ui.empty>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-ui.table>

    @if ($categories->hasPages())
        <div class="mt-6">
            {{ $categories->links() }}
        </div>
    @endif
</x-layouts.app>
