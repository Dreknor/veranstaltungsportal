<!-- Notification Bell Component -->
<div x-data="{ open: false, unreadCount: {{ auth()->user()->unreadNotifications()->count() }}, notifications: [] }"
     @click.away="open = false"
     class="relative">

    <!-- Bell Icon with Badge -->
    <button @click="open = !open; if(open) loadNotifications()"
            class="relative p-2 text-gray-600 hover:text-gray-800 dark:text-gray-300 dark:hover:text-gray-100 focus:outline-none">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>

        <!-- Badge -->
        <span x-show="unreadCount > 0"
              x-text="unreadCount > 9 ? '9+' : unreadCount"
              class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full min-w-[1.25rem]">
        </span>
    </button>

    <!-- Dropdown -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         class="absolute right-0 mt-2 w-96 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50"
         style="display: none;">

        <!-- Header -->
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Benachrichtigungen</h3>
            <div class="flex space-x-2">
                <a href="{{ route('notifications.index') }}" class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400">
                    Alle anzeigen
                </a>
                @if(auth()->user()->unreadNotifications()->count() > 0)
                    <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400">
                            Alle gelesen
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Notifications List -->
        <div class="max-h-96 overflow-y-auto">
            <template x-if="notifications.length === 0">
                <div class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <p class="text-sm">Keine neuen Benachrichtigungen</p>
                </div>
            </template>

            <template x-for="notification in notifications" :key="notification.id">
                <div class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mr-3">
                            <!-- Critical Log Icon -->
                            <template x-if="notification.type === 'App\\Notifications\\CriticalLogNotification'">
                                <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                            </template>
                        </div>

                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="notification.data.count > 1 ? notification.data.count + ' kritische Fehler' : 'Kritischer Fehler: ' + (notification.data.level || 'ERROR')">
                            </p>
                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 line-clamp-2" x-text="notification.data.message">
                            </p>
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-xs text-gray-500 dark:text-gray-400" x-text="formatTime(notification.created_at)">
                                </span>
                                <template x-if="notification.data.url">
                                    <a :href="`/notifications/${notification.id}/read`" class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 font-medium">
                                        Details →
                                    </a>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
function loadNotifications() {
    fetch('/notifications/unread')
        .then(response => response.json())
        .then(data => {
            this.notifications = data.notifications;
            this.unreadCount = data.unread_count;
        });
}

function formatTime(datetime) {
    const date = new Date(datetime);
    const now = new Date();
    const diff = Math.floor((now - date) / 1000); // seconds

    if (diff < 60) return 'Gerade eben';
    if (diff < 3600) return Math.floor(diff / 60) + ' Min.';
    if (diff < 86400) return Math.floor(diff / 3600) + ' Std.';
    if (diff < 604800) return Math.floor(diff / 86400) + ' Tage';

    return date.toLocaleDateString('de-DE');
}
</script>
