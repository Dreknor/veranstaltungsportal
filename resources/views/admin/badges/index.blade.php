<x-layouts.app title="Abzeichen verwalten">
    @php
        $typeLabels = ['attendance' => 'Teilnahme', 'achievement' => 'Erfolge', 'special' => 'Spezial'];
    @endphp

    <x-ui.page-header title="Abzeichen verwalten">
        <x-slot:actions>
            <x-ui.button :href="route('admin.badges.create')">
                <x-slot:icon><x-icon.plus /></x-slot:icon>
                Neues Abzeichen anlegen
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <!-- Statistics -->
    <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <x-ui.stat label="Abzeichen gesamt" :value="$stats['total']" />
        <x-ui.stat label="Teilnahme" :value="$stats['attendance']" />
        <x-ui.stat label="Erfolge" :value="$stats['achievement']" />
        <x-ui.stat label="Spezial" :value="$stats['special']" />
        <x-ui.stat label="Vergeben" :value="$stats['total_awarded']" />
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('admin.badges.index') }}"
        class="mb-6 grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] items-end gap-x-4 gap-y-5">
        <x-ui.field label="Suche" name="search" :old="false" :value="request('search')" placeholder="Abzeichen suchen" />
        <x-ui.select label="Typ" name="type" :old="false" :selected="request('type')" :options="[
            '' => 'Alle Typen',
            'attendance' => 'Teilnahme',
            'achievement' => 'Erfolge',
            'special' => 'Spezial',
        ]" />
        <div class="flex flex-wrap gap-3">
            <x-ui.button type="submit" variant="secondary" size="lg">Filtern</x-ui.button>
            @if (request()->hasAny(['search', 'type']))
                <x-ui.button variant="link" :href="route('admin.badges.index')">Zurücksetzen</x-ui.button>
            @endif
        </div>
    </form>

    <x-ui.table label="Abzeichen">
        <thead>
            <tr>
                <th scope="col">Abzeichen</th>
                <th scope="col">Typ</th>
                <th scope="col" class="num">Punkte</th>
                <th scope="col" class="num">Vergeben</th>
                <th scope="col">Anforderung</th>
                <th scope="col" class="actions"><span class="sr-only">Aktionen</span></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($badges as $badge)
                <tr>
                    <th scope="row">
                        <div class="flex items-center gap-4">
                            <x-badge-icon :badge="$badge" size="sm" class="flex-none" />
                            <div class="min-w-0">
                                <a href="{{ route('admin.badges.show', $badge) }}"
                                    class="text-ink underline underline-offset-[3px]">{{ $badge->name }}</a>
                                <span
                                    class="block text-small font-normal text-muted">{{ Str::limit($badge->description, 80) }}</span>
                            </div>
                        </div>
                    </th>
                    <td><x-ui.status
                            tone="neutral">{{ $typeLabels[$badge->type] ?? ucfirst($badge->type) }}</x-ui.status></td>
                    <td class="num">{{ $badge->points }}</td>
                    <td class="num">{{ $badge->users_count }}</td>
                    <td>{{ $badge->requirement_value }}
                        {{ \App\Models\Badge::REQUIREMENT_TYPES[\App\Models\Badge::normalizeRequirement((string) $badge->requirement_type)] ?? str_replace('_', ' ', $badge->requirement_type) }}
                    </td>
                    <td class="actions">
                        <x-ui.button size="sm" variant="secondary" :href="route('admin.badges.show', $badge)">Details</x-ui.button>
                        <x-ui.button size="sm" variant="secondary" :href="route('admin.badges.edit', $badge)">Bearbeiten</x-ui.button>
                    </td>
                </tr>
            @empty
                <tr class="hover:bg-transparent">
                    <td colspan="6" class="p-0">
                        <x-ui.empty :bordered="false" title="Keine Abzeichen gefunden" :action-href="route('admin.badges.create')"
                            action-label="Erstes Abzeichen anlegen" action-variant="primary">
                            Zu diesem Filter gibt es kein Abzeichen.
                        </x-ui.empty>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-ui.table>

    @if ($badges->hasPages())
        <div class="mt-6">
            {{ $badges->links() }}
        </div>
    @endif
</x-layouts.app>
