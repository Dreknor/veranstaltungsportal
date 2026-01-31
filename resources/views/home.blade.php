<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veranstaltungsportal - Events entdecken und buchen</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="text-2xl font-bold text-blue-600">EventPortal</a>
                </div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('events.index') }}" class="text-gray-700 hover:text-gray-900">Events</a>
                    <a href="{{ route('events.calendar') }}" class="text-gray-700 hover:text-gray-900">Kalender</a>
                    @auth
                        <a href="{{ route('organizer.dashboard') }}" class="text-gray-700 hover:text-gray-900">Dashboard</a>
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Mein Konto
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900">Anmelden</a>
                        <a href="{{ route('register') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Registrieren
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-5xl font-bold mb-6">Entdecke unvergessliche Events</h1>
            <p class="text-xl mb-8">Finde und buche die besten Veranstaltungen in deiner Region</p>

            <div class="max-w-2xl mx-auto">
                <form action="{{ route('events.index') }}" method="GET" class="flex gap-4">
                    <input type="text" name="search" placeholder="Nach Events suchen..."
                           class="flex-1 px-6 py-4 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button type="submit" class="px-8 py-4 bg-yellow-500 text-gray-900 rounded-lg hover:bg-yellow-400 font-semibold">
                        Suchen
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Kategorien -->
    <div class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">Event-Kategorien</h2>

            <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
                @php
                    $categories = \App\Models\EventCategory::where('is_active', true)->get();
                @endphp
                @foreach($categories as $category)
                    <a href="{{ route('events.index', ['category' => $category->id]) }}"
                       class="flex flex-col items-center p-6 rounded-lg border-2 border-gray-200 hover:border-blue-500 transition">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center mb-3"
                             style="background-color: {{ $category->color }}20;">
                            <span class="text-2xl">🎉</span>
                        </div>
                        <span class="font-medium text-gray-900 text-center">{{ $category->name }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Featured Events -->
    <div class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-3xl font-bold text-gray-900">Featured Events</h2>
                <a href="{{ route('events.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                    Alle Events ansehen →
                </a>
            </div>

            @php
                $featuredEvents = \App\Models\Event::published()->featured()->with('category')->limit(3)->get();
            @endphp

            @if($featuredEvents->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @foreach($featuredEvents as $event)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                            <div class="h-48 bg-gradient-to-br from-blue-400 to-purple-500"></div>
                            <div class="p-6">
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-medium mb-3"
                                      style="background-color: {{ $event->category->color }}20; color: {{ $event->category->color }}">
                                    {{ $event->category->name }}
                                </span>
                                <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $event->title }}</h3>
                                <p class="text-gray-600 text-sm mb-4">{{ Str::limit($event->description, 100) }}</p>
                                <div class="flex items-center text-sm text-gray-500 mb-4">
                                    <span>📅 @if($event->start_date->isSameDay($event->end_date))
                                        {{ $event->start_date->format('d.m.Y') }}
                                    @else
                                        {{ $event->start_date->format('d.m.Y') }} - {{ $event->end_date->format('d.m.Y') }}
                                    @endif</span>
                                    <span class="mx-2">•</span>
                                    <span>📍 {{ $event->venue_city }}</span>
                                </div>
                                <a href="{{ route('events.show', $event->slug) }}"
                                   class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                    Details ansehen
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 text-gray-600">
                    <p>Noch keine Events verfügbar. Schauen Sie später wieder vorbei!</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Features -->
    <div class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-12 text-center">Warum EventPortal?</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-3xl">🎫</span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Einfache Buchung</h3>
                    <p class="text-gray-600">Tickets in wenigen Klicks buchen und sofort erhalten</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-3xl">🔒</span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Sicher & Zuverlässig</h3>
                    <p class="text-gray-600">DSGVO-konform und sichere Zahlungsabwicklung</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-3xl">📱</span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Mobil & Flexibel</h3>
                    <p class="text-gray-600">Zugriff auf alle Geräte, überall und jederzeit</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Call to Action -->
    <div class="py-16 bg-gradient-to-r from-blue-600 to-purple-600 text-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold mb-4">Bist du Veranstalter?</h2>
            <p class="text-xl mb-8">Erstelle und verwalte deine eigenen Events ganz einfach</p>
            @auth
                <a href="{{ route('organizer.events.create') }}"
                   class="inline-block px-8 py-4 bg-white text-blue-600 rounded-lg hover:bg-gray-100 font-semibold">
                    Event erstellen
                </a>
            @else
                <a href="{{ route('register') }}"
                   class="inline-block px-8 py-4 bg-white text-blue-600 rounded-lg hover:bg-gray-100 font-semibold">
                    Jetzt kostenlos registrieren
                </a>
            @endauth
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p>&copy; {{ date('Y') }} EventPortal. Alle Rechte vorbehalten.</p>
        </div>
    </footer>
</body>
</html>

