<x-layouts.app>
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Admin Dashboard</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Übersicht über das gesamte System</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Users -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Benutzer gesamt</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['total_users'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Total Organizers -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Veranstalter</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['total_organizers'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Total Events -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Events gesamt</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['total_events'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">{{ $stats['published_events'] }} veröffentlicht</p>
                    </div>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Umsatz gesamt</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($stats['total_revenue'], 2) }} €</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Ausstehende Events</h3>
                <p class="text-3xl font-bold text-orange-500">{{ $stats['pending_events'] }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Buchungen gesamt</h3>
                <p class="text-3xl font-bold text-blue-500">{{ $stats['total_bookings'] }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Neue Buchungen (7 Tage)</h3>
                <p class="text-3xl font-bold text-green-500">{{ $stats['recent_bookings_count'] }}</p>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Users -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Neueste Benutzer</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @forelse($recentUsers as $user)
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                                </div>
                                <div class="text-right">
                                    @if($user->hasRole('organizer'))
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            Veranstalter
                                        </span>
                                    @endif
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $user->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 dark:text-gray-400">Keine Benutzer vorhanden.</p>
                        @endforelse
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('admin.users.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                            Alle Benutzer anzeigen →
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Events -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Neueste Events</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @forelse($recentEvents as $event)
                            <div class="flex items-center justify-between">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $event->title }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">von {{ $event->user->name }}</p>
                                </div>
                                <div class="text-right ml-4">
                                    @if($event->is_published)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            Veröffentlicht
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                            Entwurf
                                        </span>
                                    @endif
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $event->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 dark:text-gray-400">Keine Events vorhanden.</p>
                        @endforelse
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('admin.events.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                            Alle Events anzeigen →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
