<x-layouts.app title="Mein Dashboard">
    <x-ui.page-header title="Willkommen zurück, {{ $user->name }}" subtitle="Verwalten Sie Ihre Buchungen und Fortbildungen." />

    <div class="mb-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-ui.stat label="Buchungen gesamt" :value="$stats['total_bookings']" :href="route('user.bookings')" />
        <x-ui.stat label="Anstehende Veranstaltungen" :value="$stats['upcoming_events']" :href="route('user.events.upcoming')" />
        <x-ui.stat label="Vergangene Veranstaltungen" :value="$stats['past_events']" :href="route('user.events.past')" />
        <x-ui.stat label="Ungelesene Benachrichtigungen" :value="$unreadNotificationsCount" :href="route('notifications.index')" />
    </div>

    <nav aria-label="Schnellzugriff" class="mb-12">
        <ul class="m-0 grid list-none grid-cols-1 gap-x-8 p-0 sm:grid-cols-2 lg:grid-cols-5">
            @php
                $pendingRequests = $user->getPendingRequestsCount();
                $quickLinks = [
                    ['Fortbildungen finden', route('events.index'), null],
                    ['Meine Buchungen', route('user.bookings'), null],
                    ['Netzwerk', route('connections.index'), $pendingRequests > 0 ? $pendingRequests . ' offene Anfragen' : null],
                    ['Favoriten', route('favorites.index'), null],
                    ['Statistiken', route('user.statistics'), null],
                ];
            @endphp
            @foreach ($quickLinks as [$label, $url, $note])
                <li class="border-t border-line">
                    <a href="{{ $url }}"
                        class="flex min-h-15 items-center justify-between gap-3 text-ui font-medium text-ink no-underline underline-offset-[3px] hover:underline">
                        <span>
                            {{ $label }}
                            @if ($note)
                                <span class="block text-small font-normal text-muted">{{ $note }}</span>
                            @endif
                        </span>
                        <x-icon.arrow-right class="h-4 w-4 shrink-0" />
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>

    <x-ui.section title="Anstehende Veranstaltungen" :link-href="route('user.events.upcoming')" link-label="Alle anzeigen" class="mb-12">
        @if ($upcomingBookings->count() > 0)
            <x-ui.row-list>
                @foreach ($upcomingBookings as $booking)
                    @if ($booking->event)
                        <x-ui.event-row :event="$booking->event" :href="route('bookings.show', $booking->booking_number)">
                            <span class="text-small text-muted tabular-nums">Buchung {{ $booking->booking_number }}</span>
                            <x-slot:aside>
                                <x-ui.status :tone="\App\Support\Ui::bookingTone($booking->status)">{{ $booking->statusLabel() }}</x-ui.status>
                            </x-slot:aside>
                            <x-slot:action>
                                <x-ui.button variant="secondary" :href="route('bookings.show', $booking->booking_number)" full>Buchung ansehen</x-ui.button>
                                <x-ui.button variant="link" size="sm" :href="route('bookings.ticket', $booking->booking_number)">Ticket herunterladen</x-ui.button>
                            </x-slot:action>
                        </x-ui.event-row>
                    @endif
                @endforeach
            </x-ui.row-list>
        @else
            <x-ui.empty title="Keine anstehenden Veranstaltungen" :action-href="route('events.index')" action-label="Fortbildungen entdecken">
                Sobald Sie eine Veranstaltung buchen, erscheint sie hier.
            </x-ui.empty>
        @endif
    </x-ui.section>

    <x-ui.section title="Letzte Buchungen" :link-href="route('user.bookings')" link-label="Alle anzeigen" class="mb-12">
        @if ($bookings->count() > 0)
            <x-ui.row-list>
                @foreach ($bookings->take(5) as $booking)
                    @if ($booking->event)
                        <x-ui.event-row :event="$booking->event" :href="route('bookings.show', $booking->booking_number)">
                            <span class="text-small text-muted tabular-nums">Gebucht am {{ $booking->created_at->format('d.m.Y') }} · {{ $booking->booking_number }}</span>
                            <x-slot:aside>
                                <span class="text-base font-semibold whitespace-nowrap">{{ \App\Support\Ui::money($booking->total) }}</span>
                                <x-ui.status :tone="\App\Support\Ui::bookingTone($booking->status)">{{ $booking->statusLabel() }}</x-ui.status>
                            </x-slot:aside>
                            <x-slot:action>
                                <x-ui.button variant="secondary" :href="route('bookings.show', $booking->booking_number)" full>Buchung ansehen</x-ui.button>
                            </x-slot:action>
                        </x-ui.event-row>
                    @endif
                @endforeach
            </x-ui.row-list>
        @else
            <x-ui.empty title="Noch keine Buchungen" :action-href="route('events.index')" action-label="Veranstaltungen entdecken" action-variant="primary">
                Entdecken Sie Fortbildungen und sichern Sie sich Ihren Platz.
            </x-ui.empty>
        @endif
    </x-ui.section>

    <x-ui.section title="Benachrichtigungen" :link-href="route('notifications.index')" link-label="Alle Benachrichtigungen" class="mb-12">
        @if ($notifications->count() > 0)
            <x-ui.row-list>
                @foreach ($notifications as $notification)
                    <x-ui.row>
                        <p @class(['m-0 flex items-center gap-2 text-base', 'font-semibold' => !$notification->read_at, 'font-normal' => $notification->read_at])>
                            @unless ($notification->read_at)
                                <span aria-hidden="true" class="h-2 w-2 flex-none rounded-full bg-leaf"></span>
                                <span class="sr-only">Ungelesen:</span>
                            @endunless
                            {{ $notification->data['title'] ?? 'Neue Benachrichtigung' }}
                        </p>
                        @if (!empty($notification->data['message']))
                            <p class="m-0 text-small text-muted">{{ $notification->data['message'] }}</p>
                        @endif
                        <x-slot:aside>
                            <span class="text-small text-muted">{{ $notification->created_at->diffForHumans() }}</span>
                        </x-slot:aside>
                        @unless ($notification->read_at)
                            <x-slot:action>
                                <form action="{{ route('notifications.mark-read', $notification->id) }}" method="POST" class="m-0">
                                    @csrf
                                    @method('PATCH')
                                    <x-ui.button type="submit" variant="link" size="sm">Als gelesen markieren</x-ui.button>
                                </form>
                            </x-slot:action>
                        @endunless
                    </x-ui.row>
                @endforeach
            </x-ui.row-list>
        @else
            <x-ui.empty>Keine Benachrichtigungen.</x-ui.empty>
        @endif
    </x-ui.section>

    @if (isset($recommendedEvents) && $recommendedEvents->count() > 0)
        <x-ui.section title="Empfohlene Fortbildungen" :link-href="route('settings.interests.edit')" link-label="Interessen anpassen"
            description="Basierend auf Ihren Interessen haben wir diese Veranstaltungen für Sie ausgewählt.">
            <x-ui.row-list>
                @foreach ($recommendedEvents as $event)
                    <x-ui.event-row :event="$event" />
                @endforeach
            </x-ui.row-list>
        </x-ui.section>
    @endif
</x-layouts.app>
