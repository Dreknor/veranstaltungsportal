<x-layouts.app title="Meine Favoriten">
    <x-ui.page-header title="Meine Favoriten" subtitle="Ihre gespeicherten Fortbildungen und Veranstaltungen." />

    @if ($favoriteEvents->count() > 0)
        <x-ui.row-list>
            @foreach ($favoriteEvents as $event)
                <x-ui.event-row :event="$event">
                    <x-ui.button variant="link" size="sm" class="self-start" :onclick="'toggleFavorite(' . ($event->id) . ')'">
                        <x-slot:icon><x-icon.heart class="h-4 w-4" /></x-slot:icon>
                        Aus Favoriten entfernen
                    </x-ui.button>
                </x-ui.event-row>
            @endforeach
        </x-ui.row-list>

        <div class="mt-8">
            {{ $favoriteEvents->links() }}
        </div>
    @else
        <x-ui.empty title="Noch keine Favoriten" :action-href="route('events.index')" action-label="Fortbildungen entdecken" action-variant="primary">
            Entdecken Sie Fortbildungen und speichern Sie Ihre Favoriten.
        </x-ui.empty>
    @endif

    @push('scripts')
    <script>
        function toggleFavorite(eventId) {
            fetch(`/events/${eventId}/favorite`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.favorited === false) {
                    // Reload page to update the list
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.');
            });
        }
    </script>
    @endpush
</x-layouts.app>
