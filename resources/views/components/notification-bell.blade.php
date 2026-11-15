{{--
    Benachrichtigungen im Kopf des angemeldeten Bereichs: Icon-Button (44 px) mit Zähler,
    Aufklappfeld mit den ungelesenen Meldungen (per fetch nachgeladen).
    Unter 640 px liegt das Aufklappfeld über die volle Breite unter dem Kopf.
--}}
<div x-data="{
    open: false,
    unreadCount: {{ auth()->user()->unreadNotifications()->count() }},
    notifications: [],
    loadNotifications() {
        fetch('/notifications/unread')
            .then(response => response.json())
            .then(data => {
                this.notifications = data.notifications;
                this.unreadCount = data.unread_count;
            })
            .catch(error => console.error('Error loading notifications:', error));
    },
    formatTime(datetime) {
        const date = new Date(datetime);
        const now = new Date();
        const diff = Math.floor((now - date) / 1000);

        if (diff < 60) return 'Gerade eben';
        if (diff < 3600) return Math.floor(diff / 60) + ' Min.';
        if (diff < 86400) return Math.floor(diff / 3600) + ' Std.';
        if (diff < 604800) return Math.floor(diff / 86400) + ' Tage';

        return date.toLocaleDateString('de-DE');
    }
}" @click.away="open = false"
    x-on:keydown.escape.stop="if (open) { open = false; $refs.button.focus(); }" class="relative flex-none max-sm:static">

    <button type="button" x-ref="button" @click="open = !open; if(open) loadNotifications()"
        x-bind:aria-expanded="open.toString()" aria-expanded="false" aria-controls="notification-panel"
        class="relative inline-flex h-11 w-11 cursor-pointer items-center justify-center rounded-control border-0 bg-transparent text-ink transition-colors duration-150 hover:bg-zone">
        <x-icon.bell class="h-5 w-5 [&_*]:[stroke-width:1.6]" aria-hidden="true" />
        <span class="sr-only">Benachrichtigungen</span>
        <span x-show="unreadCount > 0" x-cloak
            class="absolute top-1 right-0.5 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-primary px-1 text-caption leading-none font-medium text-on-primary tabular-nums">
            <span x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
            <span class="sr-only">ungelesen</span>
        </span>
    </button>

    <div id="notification-panel" x-show="open" x-cloak
        class="absolute inset-x-4 z-40 mt-1 rounded-box border border-line bg-surface text-ink shadow-overlay sm:inset-x-auto sm:right-0 sm:w-96">

        <div class="flex flex-wrap items-center justify-between gap-x-4 gap-y-1 border-b border-line py-1 pr-2 pl-4">
            <p class="m-0 text-ui font-semibold">Benachrichtigungen</p>
            <div class="flex items-center gap-1">
                <a href="{{ route('notifications.index') }}"
                    class="inline-flex min-h-11 items-center px-2 text-small font-medium text-ink underline-offset-[3px] hover:underline">
                    Alle anzeigen
                </a>
                @if (auth()->user()->unreadNotifications()->count() > 0)
                    <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit"
                            class="inline-flex min-h-11 cursor-pointer items-center border-0 bg-transparent px-2 text-small font-medium text-ink underline-offset-[3px] hover:underline">
                            Alle gelesen
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="max-h-96 overflow-y-auto">
            <template x-if="notifications.length === 0">
                <p class="m-0 px-4 py-8 text-center text-small text-muted">Keine neuen Benachrichtigungen</p>
            </template>

            <template x-for="notification in notifications" :key="notification.id">
                <div class="flex items-start gap-3 border-b border-line px-4 py-3 last:border-b-0">
                    <div class="mt-0.5 flex-none text-muted [&_svg_*]:[stroke-width:1.6]">
                        <template x-if="notification.type === 'App\\Notifications\\CriticalLogNotification'">
                            <x-icon.warning class="h-5 w-5 text-danger" />
                        </template>

                        <template x-if="notification.type.includes('Booking')">
                            <x-icon.ticket class="h-5 w-5" />
                        </template>

                        <template x-if="notification.type.includes('Event') && !notification.type.includes('Booking')">
                            <x-icon.calendar class="h-5 w-5" />
                        </template>

                        <template x-if="notification.type.includes('Badge')">
                            <x-icon.medal class="h-5 w-5" />
                        </template>

                        <template x-if="notification.type.includes('Connection')">
                            <x-icon.user-group class="h-5 w-5" aria-hidden="true" />
                        </template>

                        <template
                            x-if="!notification.type.includes('Critical') && !notification.type.includes('Booking') && !notification.type.includes('Event') && !notification.type.includes('Badge') && !notification.type.includes('Connection')">
                            <x-icon.bell class="h-5 w-5" aria-hidden="true" />
                        </template>
                    </div>

                    <div class="min-w-0 flex-1">
                        <p class="m-0 text-small font-semibold" x-text="notification.data.title || 'Benachrichtigung'">
                        </p>
                        <p class="m-0 mt-1 line-clamp-2 text-small text-muted"
                            x-text="notification.data.message || 'Keine Details verfügbar'"></p>
                        <div class="mt-1 flex items-center justify-between gap-4">
                            <span class="text-caption text-muted tabular-nums"
                                x-text="formatTime(notification.created_at)"></span>
                            <template x-if="notification.data.url">
                                <a :href="`/notifications/${notification.id}/read`"
                                    class="inline-flex min-h-11 items-center gap-1 text-small font-medium text-ink underline-offset-[3px] hover:underline">
                                    Details
                                    <x-icon.arrow-right class="h-4 w-4" />
                                </a>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
