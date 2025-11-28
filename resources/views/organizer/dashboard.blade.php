<x-layouts.app title="Veranstalter Dashboard">
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Veranstalter Dashboard</h1>
                <p class="text-gray-600 mt-2">Willkommen zurück, {{ auth()->user()->name }}!</p>
            </div>

            <!-- Statistiken -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Gesamt Events</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_events'] }}</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <x-icon.calendar class="w-8 h-8 text-blue-600" />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Veröffentlicht</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['published_events'] }}</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <x-icon.check class="w-8 h-8 text-green-600" />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Buchungen</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_bookings'] }}</p>
                        </div>
                        <div class="p-3 bg-purple-100 rounded-full">
                            <x-icon.ticket class="w-8 h-8 text-purple-600" />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Umsatz</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['total_revenue'], 2, ',', '.') }} €</p>
                        </div>
                        <div class="p-3 bg-yellow-100 rounded-full">
                            <x-icon.currency class="w-8 h-8 text-yellow-600" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Kommende Events -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6 border-b">
                        <div class="flex justify-between items-center">
                            <h2 class="text-xl font-bold text-gray-900">Kommende Events</h2>
                            <a href="{{ route('organizer.events.create') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                + Neues Event
                            </a>
                        </div>
                    </div>
                    <div class="divide-y">
                        @forelse($upcomingEvents as $event)
                            <div class="p-6 hover:bg-gray-50">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-900">
                                            <a href="{{ route('organizer.events.edit', $event) }}" class="hover:text-blue-600">
                                                {{ $event->title }}
                                            </a>
                                        </h3>
                                        <div class="flex items-center gap-4 mt-2 text-sm text-gray-600">
                                            <span class="flex items-center">
                                                <x-icon.calendar class="w-4 h-4 mr-1" />
                                                {{ $event->start_date->format('d.m.Y H:i') }}
                                            </span>
                                            <span class="flex items-center">
                                                <x-icon.location class="w-4 h-4 mr-1" />
                                                {{ $event->venue_city }}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-2 mt-2">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $event->bookings->count() }} Buchungen
                                            </span>
                                            @if($event->is_published)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Veröffentlicht
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    Entwurf
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <a href="{{ route('organizer.events.edit', $event) }}" class="ml-4 text-gray-400 hover:text-gray-600">
                                        <x-icon.edit class="w-5 h-5" />
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="p-6 text-center text-gray-600">
                                <p>Keine kommenden Events</p>
                                <a href="{{ route('organizer.events.create') }}" class="text-blue-600 hover:text-blue-800 text-sm mt-2 inline-block">
                                    Erstellen Sie Ihr erstes Event
                                </a>
                            </div>
                        @endforelse
                    </div>
                    @if($upcomingEvents->count() > 0)
                        <div class="p-4 border-t text-center">
                            <a href="{{ route('organizer.events.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                Alle Events anzeigen →
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Aktuelle Buchungen -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6 border-b">
                        <div class="flex justify-between items-center">
                            <h2 class="text-xl font-bold text-gray-900">Aktuelle Buchungen</h2>
                            <a href="{{ route('organizer.bookings.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                Alle anzeigen
                            </a>
                        </div>
                    </div>
                    <div class="divide-y">
                        @forelse($recentBookings as $booking)
                            <div class="p-6 hover:bg-gray-50">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="font-semibold text-gray-900">{{ $booking->customer_name }}</span>
                                            @if($booking->status === 'confirmed')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Bestätigt
                                                </span>
                                            @elseif($booking->status === 'pending')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    Ausstehend
                                                </span>
                                            @elseif($booking->status === 'cancelled')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    Storniert
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-600">{{ $booking->event->title }}</p>
                                        <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                                            <span>{{ $booking->items->count() }} Tickets</span>
                                            <span>{{ number_format($booking->total, 2, ',', '.') }} €</span>
                                            <span>{{ $booking->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                    <a href="{{ route('organizer.bookings.show', $booking) }}" class="ml-4 text-gray-400 hover:text-gray-600">
                                        <x-icon.arrow-right class="w-5 h-5" />
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="p-6 text-center text-gray-600">
                                <p>Keine Buchungen vorhanden</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                <a href="{{ route('organizer.events.create') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-full">
                            <x-icon.plus class="w-6 h-6 text-blue-600" />
                        </div>
                        <div class="ml-4">
                            <h3 class="font-semibold text-gray-900">Neues Event erstellen</h3>
                            <p class="text-sm text-gray-600">Event anlegen und veröffentlichen</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('organizer.bookings.index') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-100 rounded-full">
                            <x-icon.list class="w-6 h-6 text-purple-600" />
                        </div>
                        <div class="ml-4">
                            <h3 class="font-semibold text-gray-900">Buchungen verwalten</h3>
                            <p class="text-sm text-gray-600">Buchungen ansehen und bearbeiten</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('organizer.bookings.export') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-full">
                            <x-icon.download class="w-6 h-6 text-green-600" />
                        </div>
                        <div class="ml-4">
                            <h3 class="font-semibold text-gray-900">Daten exportieren</h3>
                            <p class="text-sm text-gray-600">Buchungsdaten als CSV</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('organizer.featured-events.history') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
                    <div class="flex items-center">
                        <div class="p-3 bg-yellow-100 rounded-full">
                            <x-icon.star class="w-6 h-6 text-yellow-600" />
                        </div>
                        <div class="ml-4">
                            <h3 class="font-semibold text-gray-900">Featured Events</h3>
                            <p class="text-sm text-gray-600">Anträge, Zahlungen & Verlängerungen</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('help.category', 'organizer') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
                    <div class="flex items-center">
                        <div class="p-3 bg-gray-100 rounded-full">
                            <x-icon.help class="w-6 h-6 text-gray-700" />
                        </div>
                        <div class="ml-4">
                            <h3 class="font-semibold text-gray-900">Hilfe für Veranstalter</h3>
                            <p class="text-sm text-gray-600">Erste Schritte, Events, Buchungen</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Hilfe-Links -->
            <div class="mt-8 bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Hilfe & Ressourcen</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a class="text-blue-600 hover:text-blue-800" href="{{ route('help.article', ['organizer', 'getting-started']) }}">Erste Schritte als Organisator</a>
                    <a class="text-blue-600 hover:text-blue-800" href="{{ route('help.article', ['organizer', 'creating-events']) }}">Events erstellen & verwalten</a>
                    <a class="text-blue-600 hover:text-blue-800" href="{{ route('help.article', ['organizer', 'managing-bookings']) }}">Buchungen & Teilnehmer verwalten</a>
                    <a class="text-blue-600 hover:text-blue-800" href="{{ route('help.article', ['organizer', 'tickets-pricing']) }}">Tickets & Preisgestaltung</a>
                    <a class="text-blue-600 hover:text-blue-800" href="{{ route('help.article', ['organizer', 'analytics-reports']) }}">Statistiken & Berichte</a>
                    <a class="text-blue-600 hover:text-blue-800" href="{{ route('help.article', ['organizer', 'communication']) }}">Kommunikation mit Teilnehmern</a>
                    <a class="text-blue-600 hover:text-blue-800" href="{{ route('help.article', ['organizer', 'marketing-promotion']) }}">Marketing & Promotion</a>
                    <a class="text-blue-600 hover:text-blue-800" href="{{ route('help.article', ['organizer', 'settings-preferences']) }}">Einstellungen & Präferenzen</a>
                    <a class="text-blue-600 hover:text-blue-800" href="{{ route('help.article', ['organizer', 'troubleshooting']) }}">Häufige Probleme & FAQ</a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
