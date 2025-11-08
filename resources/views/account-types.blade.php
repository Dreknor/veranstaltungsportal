<x-layouts.app>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Account Types') }}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('Learn about the different account types') }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Participant Account -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border-2 border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-center mb-4">
                <div class="bg-blue-100 dark:bg-blue-900 p-4 rounded-full">
                    <svg class="w-12 h-12 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
            </div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 text-center mb-2">{{ __('Teilnehmer') }}</h2>
            <p class="text-gray-600 dark:text-gray-400 text-center mb-4">{{ __('Für Event-Besucher') }}</p>

            <div class="space-y-3">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Events durchsuchen und ansehen') }}</span>
                </div>
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Tickets buchen') }}</span>
                </div>
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Events bewerten') }}</span>
                </div>
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Events als Favoriten speichern') }}</span>
                </div>
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Buchungsverlauf einsehen') }}</span>
                </div>
            </div>

            @auth
                @if(auth()->user()->isParticipant())
                    <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                        <p class="text-sm text-blue-800 dark:text-blue-200 text-center font-medium">
                            {{ __('Ihr aktueller Account-Typ') }}
                        </p>
                    </div>
                @endif
            @endauth
        </div>

        <!-- Organizer Account -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border-2 border-blue-500 dark:border-blue-600">
            <div class="flex items-center justify-center mb-4">
                <div class="bg-blue-100 dark:bg-blue-900 p-4 rounded-full">
                    <svg class="w-12 h-12 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 text-center mb-2">{{ __('Organisator') }}</h2>
            <p class="text-gray-600 dark:text-gray-400 text-center mb-4">{{ __('Für Event-Veranstalter') }}</p>

            <div class="space-y-3">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Alle Teilnehmer-Funktionen') }}</span>
                </div>
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-500 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="text-sm text-gray-700 dark:text-gray-300 font-semibold">{{ __('Events erstellen und verwalten') }}</span>
                </div>
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-500 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="text-sm text-gray-700 dark:text-gray-300 font-semibold">{{ __('Ticket-Typen konfigurieren') }}</span>
                </div>
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-500 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="text-sm text-gray-700 dark:text-gray-300 font-semibold">{{ __('Rabattcodes erstellen') }}</span>
                </div>
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-500 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="text-sm text-gray-700 dark:text-gray-300 font-semibold">{{ __('Buchungen verwalten') }}</span>
                </div>
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-500 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="text-sm text-gray-700 dark:text-gray-300 font-semibold">{{ __('Statistiken und Berichte') }}</span>
                </div>
            </div>

            @auth
                @if(auth()->user()->isOrganizer())
                    <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                        <p class="text-sm text-blue-800 dark:text-blue-200 text-center font-medium">
                            {{ __('Ihr aktueller Account-Typ') }}
                        </p>
                    </div>
                @endif
            @endauth
        </div>
    </div>

    @guest
        <div class="mt-8 text-center">
            <p class="text-gray-600 dark:text-gray-400 mb-4">
                {{ __('Wählen Sie bei der Registrierung den passenden Account-Typ für Ihre Bedürfnisse.') }}
            </p>
            <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition">
                {{ __('Jetzt registrieren') }}
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
        </div>
    @endguest

    @auth
        <div class="mt-8 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
            <div class="flex">
                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="text-sm text-yellow-800 dark:text-yellow-200">
                        {{ __('Möchten Sie Ihren Account-Typ ändern? Kontaktieren Sie bitte unseren Support.') }}
                    </p>
                </div>
            </div>
        </div>
    @endauth
</x-layouts.app>

