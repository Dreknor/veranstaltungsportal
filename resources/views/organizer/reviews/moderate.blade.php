<x-layouts.app title="Bewertung prüfen">
    <x-ui.page-header title="Bewertung prüfen" :subtitle="$review->event->title"
        :back-href="route('organizer.reviews.index')" back-label="Zu den Bewertungen" />

    <div class="max-w-prose">
        <x-ui.dl>
            <x-ui.dl-item label="Status">
                @if ($review->is_approved)
                    <x-ui.status tone="success">Freigegeben</x-ui.status>
                @else
                    <x-ui.status tone="warning">Wartet auf Freigabe</x-ui.status>
                @endif
            </x-ui.dl-item>
            <x-ui.dl-item label="Bewertung">
                <span class="inline-flex items-center gap-1 text-ink" aria-hidden="true">
                    @for ($i = 1; $i <= 5; $i++)
                        @if ($i <= $review->rating)
                            <x-icon.star-filled class="h-4 w-4" />
                        @else
                            <x-icon.star class="h-4 w-4 text-line-strong" />
                        @endif
                    @endfor
                </span>
                <span class="ml-2 tabular-nums">{{ $review->rating }} von 5</span>
            </x-ui.dl-item>
            <x-ui.dl-item label="Von">{{ $review->user?->fullName() ?? 'Unbekannt' }}</x-ui.dl-item>
            <x-ui.dl-item label="Abgegeben am" num>{{ $review->created_at->format('d.m.Y') }}</x-ui.dl-item>
        </x-ui.dl>

        <x-ui.section title="Kommentar" class="mt-12">
            @if ($review->comment)
                <p class="m-0 whitespace-pre-line">{{ $review->comment }}</p>
            @else
                <p class="m-0 text-muted">Kein Kommentar.</p>
            @endif
        </x-ui.section>

        <div class="mt-12 flex flex-wrap gap-3 border-t border-line pt-8">
            @if (!$review->is_approved)
                <form method="POST" action="{{ route('organizer.reviews.approve', $review) }}">
                    @csrf
                    @method('PATCH')
                    <x-ui.button type="submit">Freigeben</x-ui.button>
                </form>
            @else
                <form method="POST" action="{{ route('organizer.reviews.reject', $review) }}">
                    @csrf
                    @method('PATCH')
                    <x-ui.button type="submit" variant="secondary">Freigabe zurückziehen</x-ui.button>
                </form>
            @endif
            <form method="POST" action="{{ route('organizer.reviews.destroy', $review) }}"
                onsubmit="return confirm('Bewertung wirklich löschen?')">
                @csrf
                @method('DELETE')
                <x-ui.button type="submit" variant="danger">Löschen</x-ui.button>
            </form>
        </div>
    </div>
</x-layouts.app>
