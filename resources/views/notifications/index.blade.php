<x-layouts.app>
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Benachrichtigungen</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Alle Ihre Benachrichtigungen</p>
                </div>
                <div class="flex space-x-2">
                    @if(auth()->user()->unreadNotifications()->count() > 0)
                        <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Alle als gelesen markieren
                            </button>
                        </form>
                    @endif
                    <form action="{{ route('notifications.delete-read') }}" method="POST" onsubmit="return confirm('Alle gelesenen Benachrichtigungen löschen?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Gelesene löschen
                        </button>
                    </form>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative dark:bg-green-900 dark:border-green-700 dark:text-green-200" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            @if($notifications->isEmpty())
                <div class="p-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400 text-lg">Keine Benachrichtigungen</p>
                    <p class="text-gray-400 dark:text-gray-500 text-sm mt-2">Sie haben derzeit keine Benachrichtigungen.</p>
                </div>
            @else
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($notifications as $notification)
                        @php
                            $data = $notification->data;
                            $title = $data['title'] ?? 'Benachrichtigung';
                            $message = $data['message'] ?? 'Keine Details verfügbar';
                            $url = $data['url'] ?? null;

                            // Bestimme Badge-Farbe basierend auf Notification-Typ
                            $isCritical = $notification->type === 'App\Notifications\CriticalLogNotification';
                            $badgeClass = $isCritical ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
                        @endphp

                        <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition {{ $notification->read_at ? 'opacity-75' : 'bg-blue-50 dark:bg-blue-900/20' }}">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center mb-2">
                                        @if(!$notification->read_at)
                                            <span class="w-2 h-2 bg-blue-600 rounded-full mr-2"></span>
                                        @endif

                                        @if($isCritical && isset($data['level']))
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $badgeClass }} mr-2">
                                                {{ $data['level'] }}
                                            </span>
                                        @endif

                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </span>
                                    </div>

                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">
                                        {{ $title }}
                                    </p>

                                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">
                                        {{ $message }}
                                    </p>

                                    @if($isCritical && isset($data['channel']))
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            Channel: {{ $data['channel'] }}
                                        </p>
                                    @endif

                                    @if(isset($data['booking_number']))
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            Buchungsnummer: {{ $data['booking_number'] }}
                                        </p>
                                    @endif
                                </div>

                                <div class="flex items-center space-x-2 ml-4">
                                    @if($url)
                                        <a href="{{ route('notifications.mark-read', $notification->id) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 text-sm font-medium">
                                            Details
                                        </a>
                                    @endif

                                    @if(!$notification->read_at)
                                        <form action="{{ route('notifications.mark-read', $notification->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-gray-600 hover:text-gray-800 dark:text-gray-400" title="Als gelesen markieren">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" class="inline" onsubmit="return confirm('Benachrichtigung löschen?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400" title="Löschen">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>

