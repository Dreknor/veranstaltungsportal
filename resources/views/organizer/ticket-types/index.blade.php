<x-layouts.app title="Ticketarten">
    <x-ui.page-header title="Ticketarten" :subtitle="$event->title" :back-href="route('organizer.events.edit', $event)" back-label="Zurück zur Veranstaltung">
        <x-slot:actions>
            <x-ui.button :href="route('organizer.events.ticket-types.create', $event)">
                <x-slot:icon><x-icon.plus /></x-slot:icon>
                Neue Ticketart
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    @if ($ticketTypes->count() > 0)
        <x-ui.table label="Ticketarten">
            <thead>
                <tr>
                    <th scope="col">Name</th>
                    <th scope="col" class="num max-sm:hidden">Preis</th>
                    <th scope="col" class="num max-sm:hidden">Verfügbar</th>
                    <th scope="col" class="num max-sm:hidden">Verkauft</th>
                    <th scope="col" class="max-sm:hidden">Status</th>
                    <th scope="col" class="actions"><span class="sr-only">Aktionen</span></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ticketTypes as $ticketType)
                    <tr>
                        <th scope="row">
                            {{ $ticketType->name }}
                            @if ($ticketType->description)
                                <span
                                    class="block text-small font-normal text-muted">{{ Str::limit($ticketType->description, 50) }}</span>
                            @endif
                            <span class="mt-1 block text-small font-normal text-muted tabular-nums sm:hidden">
                                {{ \App\Support\Ui::money($ticketType->price) }} · {{ $ticketType->quantity_sold }}
                                verkauft
                            </span>
                            <span class="mt-1 block font-normal sm:hidden">
                                @if ($ticketType->is_available)
                                    <x-ui.status tone="success">Aktiv</x-ui.status>
                                @else
                                    <x-ui.status tone="neutral">Inaktiv</x-ui.status>
                                @endif
                            </span>
                        </th>
                        <td class="num max-sm:hidden">{{ \App\Support\Ui::money($ticketType->price) }}</td>
                        <td class="num max-sm:hidden">
                            @if ($ticketType->quantity_available)
                                {{ $ticketType->quantity_available - $ticketType->quantity_sold }}
                            @else
                                Unbegrenzt
                            @endif
                        </td>
                        <td class="num max-sm:hidden">{{ $ticketType->quantity_sold }}</td>
                        <td class="max-sm:hidden">
                            @if ($ticketType->is_available)
                                <x-ui.status tone="success">Aktiv</x-ui.status>
                            @else
                                <x-ui.status tone="neutral">Inaktiv</x-ui.status>
                            @endif
                        </td>
                        <td class="actions">
                            <x-ui.button size="sm" variant="secondary" :href="route('organizer.events.ticket-types.edit', [$event, $ticketType])">Bearbeiten</x-ui.button>
                            @if (!$ticketType->hasActiveSales())
                                <x-ui.dropdown label="Weitere Aktionen für {{ $ticketType->name }}" icon-only>
                                    <x-ui.dropdown-item type="submit" form="ticket-type-delete-{{ $ticketType->id }}"
                                        danger>Löschen</x-ui.dropdown-item>
                                </x-ui.dropdown>
                                <form id="ticket-type-delete-{{ $ticketType->id }}"
                                    action="{{ route('organizer.events.ticket-types.destroy', [$event, $ticketType]) }}"
                                    method="POST" class="hidden"
                                    onsubmit="return confirm('Sind Sie sicher, dass Sie diese Ticketart löschen möchten?');">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            @else
                                <span class="inline-flex items-center gap-1 text-small text-muted"
                                    title="Löschen nicht möglich: Es gibt noch aktive Buchungen für diese Ticketart.">
                                    <x-icon.lock class="h-4 w-4" />
                                    <span class="max-sm:sr-only">Aktive Buchungen</span>
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-ui.table>
    @else
        <x-ui.empty title="Keine Ticketarten" :action-href="route('organizer.events.ticket-types.create', $event)" action-label="Ticketart erstellen"
            action-variant="primary">
            Erstellen Sie Ihre erste Ticketart für diese Veranstaltung.
        </x-ui.empty>
    @endif
</x-layouts.app>
