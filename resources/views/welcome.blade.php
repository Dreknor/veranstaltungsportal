<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Fort- und Weiterbildungen für Bildungseinrichtungen</title>
    <meta name="description" content="Das zentrale Portal für Fort- und Weiterbildungen an evangelischen Schulen. Entdecken Sie Angebote im Rahmen der Aktion Hauptfach Mensch und weitere pädagogische Veranstaltungen.">

    <x-meta-tags
        :title="config('app.name') . ' - Fort- und Weiterbildungen für Bildungseinrichtungen'"
        :description="'Das zentrale Portal für Fort- und Weiterbildungen an evangelischen Schulen. Entdecken Sie Angebote im Rahmen der Aktion Hauptfach Mensch und weitere pädagogische Veranstaltungen.'"
    />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="flex items-center gap-3">
                        <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }} Logo" class="h-14 w-14 object-contain">
                        <span class="text-xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                            {{ config('app.name') }}
                        </span>
                    </a>
                </div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('events.index') }}" class="text-gray-700 hover:text-blue-600 transition">Veranstaltungen</a>
                    <a href="{{ route('events.calendar') }}" class="text-gray-700 hover:text-blue-600 transition">Kalender</a>
                    @auth
                        @if(auth()->user()->hasRole('organizer'))
                            <a href="{{ route('organizer.dashboard') }}" class="text-gray-700 hover:text-blue-600 transition">Dashboard</a>
                        @endif
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg hover:shadow-lg transition">
                            Mein Konto
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600 transition">Anmelden</a>
                        <a href="{{ route('register') }}" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg hover:shadow-lg transition">
                            Registrieren
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative bg-gradient-to-br from-blue-600 via-indigo-700 to-purple-800 text-white py-24 overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 40px 40px;"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-4xl mx-auto">
                <div class="mb-6">
                    <span class="inline-block px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full text-sm font-medium">
                        Für evangelische Schulen & Bildungseinrichtungen
                    </span>
                </div>
                <h1 class="text-5xl md:text-6xl font-bold mb-6 animate-fade-in">
                    Fort- und Weiterbildungen für Bildungseinrichtungen
                </h1>
                <p class="text-xl md:text-2xl mb-4 text-white/90">
                    Entdecken Sie qualifizierte Fortbildungsangebote und Veranstaltungen
                </p>
                <p class="text-lg mb-10 text-white/80">
                    Mit Schwerpunkt auf der Aktion <strong>Hauptfach Mensch</strong> und pädagogischer Exzellenz
                </p>

                <!-- Search Bar -->
                <div class="max-w-3xl mx-auto mb-8">
                    <form action="{{ route('events.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                        <input type="text" name="search" placeholder="Fortbildung, Thema oder Referent suchen..."
                               class="flex-1 px-6 py-4 rounded-lg text-gray-900 focus:outline-none focus:ring-4 focus:ring-blue-300 shadow-lg">
                        <button type="submit" class="px-8 py-4 bg-yellow-500 text-gray-900 rounded-lg hover:bg-yellow-400 font-semibold shadow-lg hover:shadow-xl transition transform hover:scale-105">
                            🔍 Suchen
                        </button>
                    </form>
                </div>

                <!-- Quick Stats -->
                <div class="grid grid-cols-3 gap-6 max-w-2xl mx-auto">
                    @php
                        $eventCount = \App\Models\Event::published()->count();
                        $categoryCount = \App\Models\EventCategory::where('is_active', true)->count();
                        $bookingCount = \App\Models\Booking::where('status', 'confirmed')->count();
                    @endphp
                    <div class="text-center">
                        <div class="text-3xl font-bold">{{ $eventCount }}+</div>
                        <div class="text-sm text-white/80">Veranstaltungen</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold">{{ $categoryCount }}+</div>
                        <div class="text-sm text-white/80">Themengebiete</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold">{{ $bookingCount }}+</div>
                        <div class="text-sm text-white/80">Teilnahmen</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kategorien -->
    <div class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Fortbildungsthemen</h2>
                <p class="text-lg text-gray-600">Finden Sie passende Weiterbildungen für Ihre pädagogische Praxis</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
                @php
                    $categories = \App\Models\EventCategory::where('is_active', true)->get();
                    $icons = ['👨‍🏫', '📖', '🧠', '🤝', '🎨', '💡', '🌱', '🎯', '🔬', '📊'];
                @endphp
                @foreach($categories as $index => $category)
                    <a href="{{ route('events.index', ['category' => $category->id]) }}"
                       class="group flex flex-col items-center p-6 rounded-xl border-2 border-gray-200 hover:border-blue-500 hover:shadow-lg transition transform hover:scale-105 bg-white">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center mb-3 group-hover:scale-110 transition"
                             style="background: linear-gradient(135deg, {{ $category->color }}40, {{ $category->color }}80);">
                            <span class="text-3xl">{{ $icons[$index % count($icons)] }}</span>
                        </div>
                        <span class="font-semibold text-gray-900 text-center">{{ $category->name }}</span>
                        <span class="text-xs text-gray-500 mt-1">
                            {{ $category->events()->published()->count() }} Veranstaltungen
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Featured Events -->
    <div class="py-20 bg-gradient-to-br from-gray-50 to-blue-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-12">
                <div>
                    <h2 class="text-4xl font-bold text-gray-900 mb-2">⭐ Empfohlene Fortbildungen</h2>
                    <p class="text-lg text-gray-600">Besonders relevante Weiterbildungsangebote für Sie</p>
                </div>
                <a href="{{ route('events.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold text-lg hover:underline">
                    Alle Veranstaltungen ansehen →
                </a>
            </div>

            @php
                $featuredEvents = \App\Models\Event::published()->featured()->with('category')->limit(3)->get();
            @endphp

            @if($featuredEvents->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($featuredEvents as $event)
                        <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-2xl transition transform hover:scale-105">
                            <div class="h-56 bg-gradient-to-br from-blue-400 to-purple-600 relative">
                                @if($event->featured_image)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($event->featured_image) }}" alt="{{ $event->title }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <span class="text-6xl opacity-80">🎉</span>
                                    </div>
                                @endif
                                @if($event->is_featured)
                                    <span class="absolute top-4 right-4 bg-yellow-400 text-yellow-900 px-3 py-1 rounded-full text-xs font-bold">
                                        ⭐ Featured
                                    </span>
                                @endif
                            </div>
                            <div class="p-6">
                                <div class="flex items-center gap-2 mb-3 flex-wrap">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold"
                                          style="background-color: {{ $event->category->color }}20; color: {{ $event->category->color }}">
                                        {{ $event->category->name }}
                                    </span>
                                    <x-event-type-badge :event="$event" size="xs" />
                                    @if($event->ticketTypes()->available()->count() === 0)
                                        <span class="text-red-600 text-xs font-semibold">Ausverkauft</span>
                                    @endif
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-2">{{ $event->title }}</h3>
                                <p class="text-gray-600 mb-4 line-clamp-2">{{ $event->description }}</p>
                                <div class="space-y-2 mb-4">
                                    <div class="flex items-center text-sm text-gray-700">
                                        <span class="mr-2">📅</span>
                                        @if($event->start_date->isSameDay($event->end_date))
                                            {{ $event->start_date->format('d.m.Y') }}
                                            @if($event->start_time)
                                                um {{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }} Uhr
                                            @endif
                                        @else
                                            {{ $event->start_date->format('d.m.Y') }} - {{ $event->end_date->format('d.m.Y') }}
                                        @endif
                                    </div>
                                    <div class="flex items-center text-sm text-gray-700">
                                        <span class="mr-2">📍</span>
                                        {{ $event->location ?? $event->venue_city }}
                                    </div>
                                    @php
                                        $minPrice = $event->getMinimumPrice();
                                    @endphp
                                    @if($minPrice)
                                        <div class="flex items-center text-sm text-gray-700">
                                            <span class="mr-2">💰</span>
                                            ab {{ number_format($minPrice, 2, ',', '.') }} €
                                        </div>
                                    @endif
                                </div>
                                <a href="{{ route('events.show', $event->slug) }}"
                                   class="block w-full text-center px-4 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg hover:shadow-lg transition font-semibold">
                                    Details ansehen
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 bg-white rounded-xl shadow">
                    <span class="text-6xl mb-4 block">📚</span>
                    <p class="text-gray-600 text-lg">Aktuell keine empfohlenen Fortbildungen verfügbar.</p>
                    <a href="{{ route('events.index') }}" class="inline-block mt-4 text-blue-600 hover:text-blue-800 font-semibold">
                        Alle Veranstaltungen durchsuchen →
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Upcoming Events -->
    <div class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-2">📅 Kommende Veranstaltungen</h2>
                <p class="text-lg text-gray-600">Aktuelle Fortbildungen und Workshops</p>
            </div>

            @php
                $upcomingEvents = \App\Models\Event::published()
                    ->where('start_date', '>=', now())
                    ->orderBy('start_date')
                    ->with('category')
                    ->limit(6)
                    ->get();
            @endphp

            @if($upcomingEvents->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($upcomingEvents as $event)
                        <a href="{{ route('events.show', $event->slug) }}"
                           class="block bg-white border-2 border-gray-200 rounded-lg p-6 hover:border-blue-500 hover:shadow-lg transition">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0">
                                    <div class="w-16 h-16 rounded-lg flex flex-col items-center justify-center text-white font-bold"
                                         style="background: linear-gradient(135deg, {{ $event->category->color }}, {{ $event->category->color }}dd);">
                                        <div class="text-xs">{{ $event->start_date->format('M') }}</div>
                                        <div class="text-2xl">{{ $event->start_date->format('d') }}</div>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-bold text-gray-900 mb-1 line-clamp-1">{{ $event->title }}</h3>
                                    <p class="text-sm text-gray-600 mb-2 line-clamp-1">{{ $event->location ?? $event->venue_city }}</p>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="inline-block px-2 py-1 rounded text-xs font-medium"
                                              style="background-color: {{ $event->category->color }}20; color: {{ $event->category->color }}">
                                            {{ $event->category->name }}
                                        </span>
                                        <x-event-type-badge :event="$event" size="xs" />
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
                <div class="text-center mt-10">
                    <a href="{{ route('events.calendar') }}"
                       class="inline-block px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold shadow hover:shadow-lg transition">
                        Zum Kalender →
                    </a>
                </div>
            @else
                <div class="text-center py-12 bg-gray-50 rounded-xl">
                    <span class="text-6xl mb-4 block">📆</span>
                    <p class="text-gray-600 text-lg">Aktuell keine kommenden Veranstaltungen geplant.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Features Section -->
    <div class="py-20 bg-gradient-to-br from-blue-50 to-purple-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Warum unser Bildungsportal?</h2>
                <p class="text-lg text-gray-600">Ihre Vorteile für professionelle Weiterbildung</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="bg-white p-8 rounded-xl shadow-md text-center">
                    <div class="text-5xl mb-4">🎓</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Qualifizierte Angebote</h3>
                    <p class="text-gray-600">Zertifizierte Fortbildungen von erfahrenen Referenten und Bildungsexperten</p>
                </div>

                <div class="bg-white p-8 rounded-xl shadow-md text-center">
                    <div class="text-5xl mb-4">📚</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Hauptfach Mensch</h3>
                    <p class="text-gray-600">Spezielle Angebote im Rahmen der Aktion für evangelische Schulen</p>
                </div>

                <div class="bg-white p-8 rounded-xl shadow-md text-center">
                    <div class="text-5xl mb-4">🤝</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Vernetzung</h3>
                    <p class="text-gray-600">Austausch mit Kolleg:innen und Aufbau eines pädagogischen Netzwerks</p>
                </div>

                <div class="bg-white p-8 rounded-xl shadow-md text-center">
                    <div class="text-5xl mb-4">📱</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Einfache Verwaltung</h3>
                    <p class="text-gray-600">Anmeldung, Teilnahmebestätigung und Zertifikate digital verwalten</p>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section for Organizers -->
    @if(config('app.allow_organizer_registration', true) || (auth()->check() && auth()->user()->hasRole('organizer')))
    <div class="py-20 bg-gradient-to-r from-blue-600 to-purple-600 text-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-bold mb-4">Sie möchten Fortbildungen anbieten?</h2>
            <p class="text-xl mb-8 text-white/90">
                Publizieren Sie Ihre Veranstaltungen, verwalten Sie Anmeldungen und erreichen Sie Lehrkräfte an evangelischen Schulen!
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @guest
                    <a href="{{ route('register') }}"
                       class="px-8 py-4 bg-white text-blue-600 rounded-lg hover:bg-gray-100 font-semibold shadow-lg hover:shadow-xl transition">
                        Als Veranstalter registrieren
                    </a>
                @else
                    @if(auth()->user()->hasRole('organizer'))
                        <a href="{{ route('organizer.events.create') }}"
                           class="px-8 py-4 bg-white text-blue-600 rounded-lg hover:bg-gray-100 font-semibold shadow-lg hover:shadow-xl transition">
                            Veranstaltung erstellen
                        </a>
                    @endif
                @endguest
                <a href="{{ route('events.index') }}"
                   class="px-8 py-4 bg-transparent border-2 border-white text-white rounded-lg hover:bg-white hover:text-blue-600 font-semibold transition">
                    Fortbildungen entdecken
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- Footer -->
    <x-footer />

    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.8s ease-out;
        }

        .line-clamp-1 {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</body>
</html>

