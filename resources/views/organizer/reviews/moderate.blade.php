<x-layouts.app title="Bewertung prüfen">
    <x-ui.page-header title="Bewertung prüfen" :subtitle="$review->event->title"
        :back-href="route('organizer.reviews.index')" back-label="Zu den Bewertungen" />

    <div class="flex max-w-prose flex-col gap-12">
        <x-ui.dl>
            <x-ui.dl-item label="Status">
                <x-review.status :review="$review" long />
            </x-ui.dl-item>
            <x-ui.dl-item label="Bewertung">
                <span class="inline-flex items-center gap-2">
                    <x-ui.stars :value="$review->rating" size="sm" decorative />
                    <span class="tabular-nums">{{ $review->rating }} von 5</span>
                </span>
            </x-ui.dl-item>
            <x-ui.dl-item label="Von">{{ $review->reviewerName() }}</x-ui.dl-item>
            <x-ui.dl-item label="Öffentlich angezeigt als">{{ $review->publicName() }}</x-ui.dl-item>
            <x-ui.dl-item label="Abgegeben am" num>{{ $review->created_at->format('d.m.Y') }}</x-ui.dl-item>
        </x-ui.dl>

        <x-ui.section title="Kommentar" id="kommentar">
            @if ($review->comment)
                <p class="m-0 whitespace-pre-line">{{ $review->comment }}</p>
            @else
                <p class="m-0 text-muted">Kein Kommentar.</p>
            @endif
        </x-ui.section>

        <x-ui.section title="Freigabe" id="freigabe"
            description="Lehnen Sie eine Bewertung nur ab, wenn sie gegen die Richtlinien verstößt – etwa bei Beleidigungen, persönlichen Daten Dritter, Werbung oder ohne Bezug zur Veranstaltung. Kritik allein ist kein Grund. Die Plattform sieht, wie viele Bewertungen Sie ablehnen.">
            <div class="flex flex-wrap gap-3">
                @if (!$review->is_approved)
                    <form method="POST" action="{{ route('organizer.reviews.approve', $review) }}">
                        @csrf
                        @method('PATCH')
                        <x-ui.button type="submit">Freigeben</x-ui.button>
                    </form>
                @endif
                @if (!$review->isRejected())
                    <form method="POST" action="{{ route('organizer.reviews.reject', $review) }}">
                        @csrf
                        @method('PATCH')
                        <x-ui.button type="submit" variant="secondary">
                            {{ $review->is_approved ? 'Freigabe zurückziehen' : 'Ablehnen (Regelverstoß)' }}
                        </x-ui.button>
                    </form>
                @endif
                <form method="POST" action="{{ route('organizer.reviews.destroy', $review) }}"
                    onsubmit="return confirm('Bewertung wirklich löschen?')">
                    @csrf
                    @method('DELETE')
                    <x-ui.button type="submit" variant="danger">Löschen</x-ui.button>
                </form>
            </div>
        </x-ui.section>

        <x-ui.section title="Öffentliche Antwort" id="antwort"
            description="Ihre Antwort erscheint unter der Bewertung auf der Veranstaltungsseite und in Ihrem Veranstalter-Profil. Sachlich und freundlich antworten wirkt glaubwürdiger als jede Ablehnung.">
            <form method="POST" action="{{ route('organizer.reviews.reply', $review) }}" class="flex max-w-form flex-col gap-5">
                @csrf
                @method('PATCH')
                <x-ui.textarea label="Antwort" name="organizer_reply" optional rows="4" maxlength="1000"
                    :value="$review->organizer_reply"
                    hint="Leer lassen und speichern, um eine Antwort zu entfernen." />
                @if ($review->organizer_replied_at)
                    <p class="m-0 text-small text-muted tabular-nums">Zuletzt geantwortet am
                        {{ $review->organizer_replied_at->format('d.m.Y') }}</p>
                @endif
                @unless ($review->is_approved)
                    <x-ui.alert tone="info">Die Antwort wird erst sichtbar, wenn die Bewertung freigegeben ist.</x-ui.alert>
                @endunless
                <div>
                    <x-ui.button type="submit" variant="secondary">Antwort speichern</x-ui.button>
                </div>
            </form>
        </x-ui.section>
    </div>
</x-layouts.app>
