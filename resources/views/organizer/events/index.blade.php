<x-layouts.app title="Meine Veranstaltungen">
    <x-ui.page-header title="Meine Veranstaltungen" subtitle="Verwalten Sie Ihre Veranstaltungen.">
        <x-slot:actions>
            <x-ui.button :href="route('organizer.events.create')">
                <x-slot:icon><x-icon.plus /></x-slot:icon>
                Veranstaltung anlegen
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.tabs label="Veranstaltungen filtern" class="mb-8">
        <x-ui.tab :href="route('organizer.events.index')" :active="!$isArchive" :count="$upcomingCount > 0 ? $upcomingCount : null">
            Aktuelle und kommende
        </x-ui.tab>
        <x-ui.tab :href="route('organizer.events.index', ['archive' => 1])" :active="$isArchive" :count="$archiveCount > 0 ? $archiveCount : null">
            Archiv
        </x-ui.tab>
    </x-ui.tabs>

    @if ($isArchive)
        <x-ui.alert tone="info" class="mb-6">
            Sie sehen das <strong>Archiv</strong> – abgeschlossene Veranstaltungen der Vergangenheit.
        </x-ui.alert>
    @endif

    @if ($events->count() > 0)
        <x-ui.row-list>
            @foreach ($events as $event)
                <x-ui.event-row :event="$event" :href="route('organizer.events.edit', $event)">
                    <x-slot:aside>
                        @if ($event->is_cancelled)
                            <x-ui.status tone="danger">Abgesagt</x-ui.status>
                        @elseif ($event->is_published)
                            <x-ui.status tone="success">Veröffentlicht</x-ui.status>
                        @else
                            <x-ui.status tone="neutral">Entwurf</x-ui.status>
                        @endif
                        <span class="text-small text-muted whitespace-nowrap">{{ $event->bookings->count() }} Buchungen</span>
                    </x-slot:aside>
                    <x-slot:action>
                        <div class="flex gap-2">
                            <x-ui.button variant="secondary" :href="route('organizer.events.edit', $event)" class="min-w-0 flex-1">Bearbeiten</x-ui.button>
                            <x-ui.dropdown label="Weitere Aktionen für {{ $event->title }}" icon-only size="md" width="w-64">
                                <x-ui.dropdown-item :href="route('events.show', $event->slug)" target="_blank" rel="noopener">
                                    <x-slot:icon><x-icon.external-link /></x-slot:icon>
                                    Öffentlich ansehen
                                </x-ui.dropdown-item>
                                <x-ui.dropdown-item :href="route('organizer.events.ticket-types.index', $event)">Ticketarten</x-ui.dropdown-item>
                                <x-ui.dropdown-item :href="route('organizer.events.discount-codes.index', $event)">Rabattcodes</x-ui.dropdown-item>
                                <x-ui.dropdown-item :href="route('organizer.events.waitlist.index', $event)">Warteliste</x-ui.dropdown-item>
                                <x-ui.dropdown-item :href="route('organizer.bookings.index', ['event_id' => $event->id])">Buchungen</x-ui.dropdown-item>
                                @if ($event->hasAttendees())
                                    <x-ui.dropdown-item :href="route('organizer.check-in.index', $event)">Check-in</x-ui.dropdown-item>
                                @endif
                                <x-ui.dropdown-item :href="route('organizer.statistics.event', $event)">Statistik</x-ui.dropdown-item>
                                <x-ui.dropdown-item :href="route('organizer.events.certificates.index', $event)">Zertifikate</x-ui.dropdown-item>
                                @if (!$event->is_cancelled && !$isArchive)
                                    <x-ui.dropdown-item :href="route('organizer.featured-events.create', $event)">Hervorheben</x-ui.dropdown-item>
                                @endif
                                @if (!$event->is_cancelled)
                                    <x-ui.dropdown-item type="submit" form="duplicate-{{ $event->id }}">Duplizieren</x-ui.dropdown-item>
                                    @if ($event->hasAttendees())
                                        <x-ui.dropdown-item danger x-on:click="$dispatch('open-modal', 'cancel-event-{{ $event->id }}')">Absagen</x-ui.dropdown-item>
                                    @else
                                        <x-ui.dropdown-item type="submit" form="delete-{{ $event->id }}" danger>Löschen</x-ui.dropdown-item>
                                    @endif
                                @endif
                            </x-ui.dropdown>
                        </div>
                    </x-slot:action>
                </x-ui.event-row>
            @endforeach
        </x-ui.row-list>

        {{-- Formulare außerhalb des Menüs, angesprochen über form="…" --}}
        @foreach ($events as $event)
            @if (!$event->is_cancelled)
                <form id="duplicate-{{ $event->id }}" action="{{ route('organizer.events.duplicate', $event) }}" method="POST" class="hidden">
                    @csrf
                </form>

                @if ($event->hasAttendees())
                    {{-- Dialog für die Absage --}}
                    <x-ui.modal name="cancel-event-{{ $event->id }}" title="Veranstaltung absagen">
                        <form id="cancel-form-{{ $event->id }}" action="{{ route('organizer.events.cancel', $event) }}" method="POST" class="flex flex-col gap-4">
                            @csrf
                            <p class="m-0">
                                Diese Veranstaltung hat {{ $event->getAttendeesCount() }} Teilnehmende.
                                Alle Teilnehmenden werden per E-Mail über die Absage informiert.
                            </p>
                            <x-ui.textarea label="Grund der Absage" optional name="cancellation_reason" id="cancellation_reason{{ $event->id }}" rows="3"
                                :old="false" :error="false"
                                placeholder="Teilen Sie den Teilnehmenden mit, warum die Veranstaltung abgesagt wird …" />
                        </form>
                        <x-slot:footer>
                            <x-ui.button variant="secondary" x-on:click="close()">Abbrechen</x-ui.button>
                            <x-ui.button variant="danger" type="submit" form="cancel-form-{{ $event->id }}">Veranstaltung absagen</x-ui.button>
                        </x-slot:footer>
                    </x-ui.modal>
                @else
                    <form id="delete-{{ $event->id }}" action="{{ route('organizer.events.destroy', $event) }}" method="POST" class="hidden"
                        onsubmit="return confirm('Veranstaltung wirklich löschen?');">
                        @csrf
                        @method('DELETE')
                    </form>
                @endif
            @endif
        @endforeach

        @if ($events->hasPages())
            <div class="mt-8">
                {{ $events->appends(request()->query())->links() }}
            </div>
        @endif
    @else
        @if ($isArchive)
            <x-ui.empty title="Keine archivierten Veranstaltungen vorhanden" :action-href="route('organizer.events.index')" action-label="Zu den aktuellen Veranstaltungen">
                Abgeschlossene Veranstaltungen erscheinen hier im Archiv.
            </x-ui.empty>
        @else
            <x-ui.empty title="Keine Veranstaltungen vorhanden" :action-href="route('organizer.events.create')" action-label="Veranstaltung anlegen" action-variant="primary">
                Legen Sie Ihre erste Veranstaltung an, um loszulegen.
            </x-ui.empty>
        @endif
    @endif
</x-layouts.app>
