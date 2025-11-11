<x-layouts.app>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center mb-4">
                    <a href="{{ route('admin.newsletter.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                            {{ $type === 'weekly' ? 'Wöchentlicher' : 'Monatlicher' }} Newsletter
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400 mt-2">Newsletter erstellen und versenden</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column - Send Options -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 sticky top-8">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-6">Newsletter versenden</h2>

                        <!-- Preview Button -->
                        <a href="{{ route('admin.newsletter.preview', ['type' => $type]) }}"
                           target="_blank"
                           class="w-full mb-4 inline-flex items-center justify-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Vorschau anzeigen
                        </a>

                        <!-- Test Send -->
                        <form action="{{ route('admin.newsletter.send') }}" method="POST" class="mb-4">
                            @csrf
                            <input type="hidden" name="type" value="{{ $type }}">
                            <input type="hidden" name="send_to" value="test">

                            <button type="submit"
                                    onclick="return confirm('Test-Newsletter an alle Admins senden?')"
                                    class="w-full inline-flex items-center justify-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Test an Admins senden
                            </button>
                        </form>

                        <!-- Live Send -->
                        <form action="{{ route('admin.newsletter.send') }}" method="POST" class="mb-6">
                            @csrf
                            <input type="hidden" name="type" value="{{ $type }}">
                            <input type="hidden" name="send_to" value="all">

                            <button type="submit"
                                    onclick="return confirm('Newsletter wirklich an ALLE Abonnenten senden? Diese Aktion kann nicht rückgängig gemacht werden!')"
                                    class="w-full inline-flex items-center justify-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                An alle Abonnenten senden
                            </button>
                        </form>

                        <!-- Info Box -->
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                            <h3 class="font-semibold text-blue-900 dark:text-blue-100 mb-2">ℹ️ Hinweis</h3>
                            <ul class="text-sm text-blue-800 dark:text-blue-200 space-y-1">
                                <li>• Newsletter wird asynchron versendet</li>
                                <li>• Versand kann einige Minuten dauern</li>
                                <li>• Testen Sie vor dem Live-Versand!</li>
                            </ul>
                        </div>

                        <!-- Statistics -->
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">Versand-Statistik</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Empfänger:</span>
                                    <span class="font-semibold text-gray-900 dark:text-gray-100">{{ number_format(\App\Models\User::where('newsletter_subscribed', true)->count()) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Events:</span>
                                    <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $upcomingEvents->count() }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Featured:</span>
                                    <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $featuredEvents->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Content Preview -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-6">Newsletter-Inhalt</h2>

                        <!-- Featured Events -->
                        @if($featuredEvents->count() > 0)
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">⭐ Highlights</h3>
                            <div class="space-y-4">
                                @foreach($featuredEvents as $event)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex items-start">
                                        @if($event->featured_image)
                                            <img src="{{ asset('storage/' . $event->featured_image) }}"
                                                 alt="{{ $event->title }}"
                                                 class="w-20 h-20 object-cover rounded-lg mr-4">
                                        @else
                                            <div class="w-20 h-20 bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg mr-4 flex items-center justify-center">
                                                <i class="fas fa-calendar-alt text-white text-2xl"></i>
                                            </div>
                                        @endif
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-1">{{ $event->title }}</h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">
                                                📅 {{ $event->start_date->format('d.m.Y H:i') }} Uhr
                                            </p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                📍 {{ $event->isOnline() ? 'Online-Veranstaltung' : $event->venue_name }}
                                            </p>
                                            @if($event->category)
                                            <span class="inline-block mt-2 px-2 py-1 text-xs rounded-full"
                                                  style="background-color: {{ $event->category->color }}20; color: {{ $event->category->color }}">
                                                {{ $event->category->name }}
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Recommendations Preview -->
                        @if($recommendations->count() > 0)
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">💡 Personalisierte Empfehlungen</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                Beispiel für: {{ $sampleUser->fullName() }}
                            </p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($recommendations->take(4) as $event)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3">
                                    <h4 class="font-semibold text-gray-900 dark:text-gray-100 text-sm mb-1">{{ Str::limit($event->title, 50) }}</h4>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">
                                        📅 {{ $event->start_date->format('d.m.Y') }}
                                    </p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Upcoming Events -->
                        @if($upcomingEvents->count() > 0)
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">📅 Kommende Veranstaltungen</h3>
                            <div class="space-y-3">
                                @foreach($upcomingEvents->take(5) as $event)
                                <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-3">
                                    <div>
                                        <h4 class="font-medium text-gray-900 dark:text-gray-100 text-sm">{{ $event->title }}</h4>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">
                                            {{ $event->start_date->format('d.m.Y H:i') }} Uhr
                                        </p>
                                    </div>
                                    @if($event->category)
                                    <span class="text-xs px-2 py-1 rounded-full"
                                          style="background-color: {{ $event->category->color }}20; color: {{ $event->category->color }}">
                                        {{ $event->category->name }}
                                    </span>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if($upcomingEvents->count() === 0 && $featuredEvents->count() === 0)
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400">Keine Events für Newsletter verfügbar.</p>
                            <p class="text-sm text-gray-400 dark:text-gray-500 mt-2">Erstellen Sie zuerst einige Events.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

