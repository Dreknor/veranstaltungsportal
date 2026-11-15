<x-layouts.app title="Teilnahmezertifikate">
    <x-ui.page-header
        title="Teilnahmezertifikate"
        :subtitle="$event->title"
        :back-href="route('organizer.events.edit', $event)"
        back-label="Zur Veranstaltung"
    >
        @if ($statistics['event_completed'])
            <x-slot:actions>
                <form method="POST" action="{{ route('organizer.events.certificates.bulk', $event) }}">
                    @csrf
                    <x-ui.button type="submit">Alle fehlenden Zertifikate erstellen</x-ui.button>
                </form>
            </x-slot:actions>
        @endif
    </x-ui.page-header>

    @unless ($statistics['event_completed'])
        <x-ui.alert tone="info" class="mb-8">
            Zertifikate können erst erstellt werden, wenn die Veranstaltung beendet ist
            ({{ $event->end_date->format('d.m.Y') }}).
        </x-ui.alert>
    @endunless

    <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <x-ui.stat label="Bestätigte Buchungen" :value="$statistics['total_attendees']" />
        <x-ui.stat label="Zertifikate auf dieser Seite erstellt" :value="$statistics['certificates_generated']" />
    </div>

    <x-ui.table label="Buchungen mit Zertifikatsstatus">
        <thead>
            <tr>
                <th scope="col">Name</th>
                <th scope="col">Buchung</th>
                <th scope="col" class="num">Tickets</th>
                <th scope="col">Zertifikat</th>
                <th scope="col" class="actions"><span class="sr-only">Aktionen</span></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($bookings as $booking)
                <tr>
                    <th scope="row">
                        {{ $booking->customer_name }}
                        <span class="block text-small font-normal text-muted">{{ $booking->customer_email }}</span>
                    </th>
                    <td class="tabular-nums">{{ $booking->booking_number }}</td>
                    <td class="num">{{ $booking->items->sum('quantity') }}</td>
                    <td>
                        @if ($booking->certificate_generated_at)
                            <x-ui.status tone="success">Erstellt am {{ $booking->certificate_generated_at->format('d.m.Y') }}</x-ui.status>
                        @else
                            <x-ui.status tone="neutral">Noch nicht erstellt</x-ui.status>
                        @endif
                    </td>
                    <td class="actions">
                        @if ($booking->certificate_generated_at)
                            <x-ui.button size="sm" variant="secondary" :href="route('organizer.events.certificates.download', [$event, $booking])">
                                Herunterladen
                            </x-ui.button>
                            <x-ui.dropdown label="Weitere Aktionen für {{ $booking->customer_name }}" icon-only>
                                <x-ui.dropdown-item type="submit" form="certificate-email-{{ $booking->id }}">Per E-Mail senden</x-ui.dropdown-item>
                                <x-ui.dropdown-item type="submit" form="certificate-delete-{{ $booking->id }}" danger>Zertifikat löschen</x-ui.dropdown-item>
                            </x-ui.dropdown>
                            <form id="certificate-email-{{ $booking->id }}" method="POST" action="{{ route('organizer.events.certificates.email', [$event, $booking]) }}" class="hidden">
                                @csrf
                            </form>
                            <form id="certificate-delete-{{ $booking->id }}" method="POST" action="{{ route('organizer.events.certificates.destroy', [$event, $booking]) }}" class="hidden"
                                onsubmit="return confirm('Zertifikat wirklich löschen?')">
                                @csrf
                                @method('DELETE')
                            </form>
                        @elseif ($statistics['event_completed'])
                            <form method="POST" action="{{ route('organizer.events.certificates.generate', [$event, $booking]) }}">
                                @csrf
                                <x-ui.button type="submit" size="sm" variant="secondary">Erstellen</x-ui.button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr class="hover:bg-transparent">
                    <td colspan="5" class="p-0">
                        <x-ui.empty :bordered="false">Für diese Veranstaltung gibt es noch keine bestätigten Buchungen.</x-ui.empty>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-ui.table>

    <div class="mt-6">
        {{ $bookings->links() }}
    </div>
</x-layouts.app>
