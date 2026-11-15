<x-layouts.app title="Warteliste">
    <x-ui.page-header
        title="Warteliste"
        :subtitle="$event->title"
        :back-href="route('organizer.events.edit', $event)"
        back-label="Zurück zur Veranstaltung"
    />

    <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-ui.stat label="Gesamt" :value="$statistics['total']" />
        <x-ui.stat label="Wartend" :value="$statistics['waiting']" />
        <x-ui.stat label="Benachrichtigt" :value="$statistics['notified']" />
        <x-ui.stat label="Gewünschte Tickets" :value="$statistics['total_requested_tickets']" />
    </div>

    {{-- Manuelle Benachrichtigung --}}
    @if ($statistics['waiting'] > 0)
        <x-ui.box tone="zone" padding="md" class="mb-8">
            <h2 class="m-0 mb-2 text-h3">Manuelle Benachrichtigung</h2>
            <p class="m-0 mb-4 max-w-prose text-base text-ink-soft">
                Benachrichtigen Sie die nächsten Personen auf der Warteliste, dass Tickets verfügbar sind.
            </p>
            <form method="POST" action="{{ route('organizer.events.waitlist.notify', $event) }}" class="flex flex-col gap-4 sm:flex-row sm:items-end">
                @csrf
                <x-ui.field label="Anzahl verfügbarer Tickets" name="quantity" type="number" min="1" value="1" required class="sm:w-72" />
                <x-ui.button type="submit">Nächste benachrichtigen</x-ui.button>
            </form>
        </x-ui.box>
    @endif

    {{-- Warteliste --}}
    @if ($waitlist->count() > 0)
        <x-ui.table label="Warteliste">
            <thead>
                <tr>
                    <th scope="col">Anmeldung</th>
                    <th scope="col">Name / E-Mail</th>
                    <th scope="col">Telefon</th>
                    <th scope="col" class="num">Tickets</th>
                    <th scope="col">Ticketart</th>
                    <th scope="col">Status</th>
                    <th scope="col" class="actions"><span class="sr-only">Aktionen</span></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($waitlist as $entry)
                    <tr>
                        <td class="tabular-nums whitespace-nowrap">{{ $entry->created_at->format('d.m.Y H:i') }}</td>
                        <th scope="row">
                            {{ $entry->name }}
                            <span class="block text-small font-normal text-muted">{{ $entry->email }}</span>
                        </th>
                        <td class="tabular-nums whitespace-nowrap">{{ $entry->phone ?? '–' }}</td>
                        <td class="num">{{ $entry->quantity }}</td>
                        <td>{{ $entry->ticketType ? $entry->ticketType->name : 'Beliebig' }}</td>
                        <td>
                            @if ($entry->status === 'waiting')
                                <x-ui.status tone="waitlist">Wartend</x-ui.status>
                            @elseif ($entry->status === 'notified')
                                <x-ui.status tone="info">Benachrichtigt</x-ui.status>
                                @if ($entry->expires_at)
                                    <span class="block text-small text-muted tabular-nums">Läuft ab: {{ $entry->expires_at->format('d.m.Y H:i') }}</span>
                                @endif
                            @elseif ($entry->status === 'converted')
                                <x-ui.status tone="success">Konvertiert</x-ui.status>
                            @else
                                <x-ui.status tone="neutral">{{ $entry->status }}</x-ui.status>
                            @endif
                        </td>
                        <td class="actions">
                            <form method="POST" action="{{ route('organizer.events.waitlist.remove', [$event, $entry]) }}" class="inline-block"
                                  onsubmit="return confirm('Möchten Sie diesen Eintrag wirklich entfernen?');">
                                @csrf
                                @method('DELETE')
                                <x-ui.button type="submit" size="sm" variant="secondary">Entfernen</x-ui.button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-ui.table>

        <div class="mt-6">
            {{ $waitlist->links() }}
        </div>
    @else
        <x-ui.empty>Keine Einträge auf der Warteliste.</x-ui.empty>
    @endif
</x-layouts.app>
