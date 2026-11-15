<x-layouts.app title="Benachrichtigungen">
    <x-ui.page-header title="Benachrichtigungen" subtitle="Alle Ihre Benachrichtigungen.">
        <x-slot:actions>
            <form action="{{ route('notifications.delete-read') }}" method="POST" class="m-0" onsubmit="return confirm('Alle gelesenen Benachrichtigungen löschen?')">
                @csrf
                @method('DELETE')
                <x-ui.button type="submit" variant="secondary">
                    <x-slot:icon><x-icon.trash /></x-slot:icon>
                    Gelesene löschen
                </x-ui.button>
            </form>
            @if (auth()->user()->unreadNotifications()->count() > 0)
                <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="m-0">
                    @csrf
                    <x-ui.button type="submit">
                        <x-slot:icon><x-icon.check /></x-slot:icon>
                        Alle als gelesen markieren
                    </x-ui.button>
                </form>
            @endif
        </x-slot:actions>
    </x-ui.page-header>

    @if ($notifications->isEmpty())
        <x-ui.empty title="Keine Benachrichtigungen">Sie haben derzeit keine Benachrichtigungen.</x-ui.empty>
    @else
        <x-ui.row-list>
            @foreach ($notifications as $notification)
                @php
                    $data = $notification->data;
                    $title = $data['title'] ?? 'Benachrichtigung';
                    $message = $data['message'] ?? 'Keine Details verfügbar';
                    $url = $data['url'] ?? null;
                    $isCritical = $notification->type === 'App\Notifications\CriticalLogNotification';
                    $unread = !$notification->read_at;
                @endphp
                <x-ui.row>
                    <p @class(['m-0 flex items-center gap-2 text-base', 'font-semibold' => $unread, 'font-normal' => !$unread])>
                        @if ($unread)
                            <span aria-hidden="true" class="h-2 w-2 flex-none rounded-full bg-leaf"></span>
                            <span class="sr-only">Ungelesen:</span>
                        @endif
                        {{ $title }}
                    </p>
                    <p class="m-0 text-small text-muted">{{ $message }}</p>
                    @if ($isCritical && isset($data['channel']))
                        <p class="m-0 text-small text-muted">Channel: {{ $data['channel'] }}</p>
                    @endif
                    @if (isset($data['booking_number']))
                        <p class="m-0 text-small text-muted tabular-nums">Buchungsnummer: {{ $data['booking_number'] }}</p>
                    @endif

                    <x-slot:aside>
                        @if ($isCritical && isset($data['level']))
                            <x-ui.status tone="danger">{{ $data['level'] }}</x-ui.status>
                        @endif
                        <span class="text-small text-muted">{{ $notification->created_at->diffForHumans() }}</span>
                    </x-slot:aside>

                    <x-slot:action>
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 lg:flex-col lg:items-start">
                            @if ($url)
                                <x-ui.button variant="link" size="sm" :href="route('notifications.mark-read', $notification->id)">Details</x-ui.button>
                            @endif
                            @if ($unread)
                                <form action="{{ route('notifications.mark-read', $notification->id) }}" method="POST" class="m-0">
                                    @csrf
                                    <x-ui.button type="submit" variant="link" size="sm">Als gelesen markieren</x-ui.button>
                                </form>
                            @endif
                            <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" class="m-0" onsubmit="return confirm('Benachrichtigung löschen?')">
                                @csrf
                                @method('DELETE')
                                <x-ui.button type="submit" variant="link" size="sm">Löschen</x-ui.button>
                            </form>
                        </div>
                    </x-slot:action>
                </x-ui.row>
            @endforeach
        </x-ui.row-list>

        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @endif
</x-layouts.app>
