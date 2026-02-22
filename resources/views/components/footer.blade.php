<footer class="bg-gray-900 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <img src="{{ asset('images/logo.png') }}" alt="Bildungsportal Logo" class="h-12 w-12 object-contain">
                    <h3 class="text-xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                        Bildungsportal
                    </h3>
                </div>
                <p class="text-gray-400">
                    Fort- und Weiterbildungen für evangelische Schulen und Bildungseinrichtungen.
                </p>
                <div class="mt-4">
                    <a href="https://www.ev-schulen-sachsen.de/hauptfach-mensch-1" target="_blank" rel="noopener" class="text-sm text-blue-400 hover:text-blue-300 transition">
                        → Mehr zur Aktion Hauptfach Mensch
                    </a>
                </div>
            </div>
            <div>
                <h4 class="font-semibold mb-4">Veranstaltungen</h4>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="{{ route('events.index') }}" class="hover:text-white transition">Alle Veranstaltungen</a></li>
                    <li><a href="{{ route('events.calendar') }}" class="hover:text-white transition">Kalender</a></li>
                    @foreach(\App\Models\EventCategory::where('is_active', true)->limit(3)->get() as $cat)
                        <li><a href="{{ route('events.index', ['category' => $cat->id]) }}" class="hover:text-white transition">{{ $cat->name }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-4">Für Veranstalter</h4>
                <ul class="space-y-2 text-gray-400">
                    @auth
                        @if(auth()->user()->hasRole('organizer'))
                            <li><a href="{{ route('organizer.dashboard') }}" class="hover:text-white transition">Dashboard</a></li>
                            <li><a href="{{ route('organizer.events.index') }}" class="hover:text-white transition">Meine Veranstaltungen</a></li>
                        @endif
                    @else
                        @if(config('app.allow_organizer_registration', true))
                            <li><a href="{{ route('register') }}" class="hover:text-white transition">Als Veranstalter registrieren</a></li>
                        @endif
                    @endauth
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-4">Hilfe & Support</h4>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="{{ route('faq') }}" class="hover:text-white transition">FAQ</a></li>
                    <li><a href="{{ route('help.index') }}" class="hover:text-white transition">Hilfe</a></li>
                    <li><a href="{{ route('contact.show') }}" class="hover:text-white transition">Kontakt</a></li>
                    <li><a href="#" class="hover:text-white transition">AGB</a></li>
                    <li><a href="{{ route('datenschutz') }}" class="hover:text-white transition">Datenschutz</a></li>
                    <li><a href="{{ route('impressum') }}" class="hover:text-white transition">Impressum</a></li>
                </ul>
            </div>
        </div>
        <div class="mt-12 pt-8 border-t border-gray-800 text-center text-gray-400">
            <p>&copy; {{ date('Y') }}  {{ config('app.name') }} - Ein Angebot der <a href="https://www.esdigmbh.de/" target="_blank" rel="noopener" class="text-blue-400 hover:text-blue-300 transition">ESDI GmbH</a></p>
            <p class="text-sm mt-2">
                Ein Angebot im Rahmen der Aktion <a href="https://www.ev-schulen-sachsen.de/hauptfach-mensch-1" target="_blank" rel="noopener" class="text-blue-400 hover:text-blue-300 transition">Hauptfach Mensch</a>.
            </p>
            <p class="text-xs mt-3 space-x-3">
                <a href="{{ route('datenschutz') }}" class="text-gray-500 hover:text-gray-300 transition">Datenschutz</a>
                <span class="text-gray-700">·</span>
                <a href="{{ route('impressum') }}" class="text-gray-500 hover:text-gray-300 transition">Impressum</a>
                <span class="text-gray-700">·</span>
                <button onclick="window.showCookiePreferences()" class="text-gray-500 hover:text-gray-300 transition cursor-pointer bg-transparent border-0 p-0 text-xs">
                    Cookie-Einstellungen
                </button>
            </p>
        </div>
    </div>
</footer>

