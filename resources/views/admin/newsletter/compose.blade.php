<x-layouts.app :title="($type === 'weekly' ? 'Wöchentlicher' : 'Monatlicher') . ' Newsletter'">
    <x-ui.page-header :title="($type === 'weekly' ? 'Wöchentlicher' : 'Monatlicher') . ' Newsletter'"
        subtitle="Newsletter erstellen und versenden."
        :back-href="route('admin.newsletter.index')" back-label="Zurück zur Newsletter-Verwaltung" />

    <div class="grid items-start gap-10 lg:grid-cols-[minmax(0,1fr)_minmax(0,368px)] lg:gap-x-12">
        {{-- Newsletter-Inhalt --}}
        <div class="flex min-w-0 flex-col gap-12">
            @if ($featuredEvents->count() > 0)
                <x-ui.section title="Highlights">
                    <ul class="m-0 list-none border-t border-line p-0">
                        @foreach ($featuredEvents as $event)
                            <li class="flex items-start gap-4 border-b border-line py-4">
                                @if ($event->featured_image)
                                    <img src="{{ asset('storage/' . $event->featured_image) }}" alt=""
                                        class="h-20 w-20 flex-none rounded-control object-cover">
                                @endif
                                <div class="flex min-w-0 flex-col gap-1">
                                    @if ($event->category)
                                        <span class="text-small text-muted">{{ $event->category->name }}</span>
                                    @endif
                                    <h3 class="m-0 text-h3">{{ $event->title }}</h3>
                                    <p class="m-0 text-small text-ink-soft tabular-nums">
                                        {{ $event->start_date->format('d.m.Y H:i') }} Uhr
                                        · {{ $event->isOnline() ? 'Online-Veranstaltung' : $event->venue_name }}
                                    </p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </x-ui.section>
            @endif

            @if ($recommendations->count() > 0)
                <x-ui.section title="Personalisierte Empfehlungen" :description="'Beispiel für: ' . $sampleUser->fullName()">
                    <ul class="m-0 list-none border-t border-line p-0">
                        @foreach ($recommendations->take(4) as $event)
                            <li class="flex flex-col gap-1 border-b border-line py-3">
                                <span class="text-base font-medium">{{ Str::limit($event->title, 50) }}</span>
                                <span class="text-small text-muted tabular-nums">{{ $event->start_date->format('d.m.Y') }}</span>
                            </li>
                        @endforeach
                    </ul>
                </x-ui.section>
            @endif

            @if ($upcomingEvents->count() > 0)
                <x-ui.section title="Kommende Veranstaltungen">
                    <ul class="m-0 list-none border-t border-line p-0">
                        @foreach ($upcomingEvents->take(5) as $event)
                            <li class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-1 border-b border-line py-3">
                                <div class="flex min-w-0 flex-col gap-0.5">
                                    <span class="text-base font-medium">{{ $event->title }}</span>
                                    <span class="text-small text-muted tabular-nums">{{ $event->start_date->format('d.m.Y H:i') }} Uhr</span>
                                </div>
                                @if ($event->category)
                                    <span class="text-small text-muted">{{ $event->category->name }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </x-ui.section>
            @endif

            @if ($upcomingEvents->count() === 0 && $featuredEvents->count() === 0)
                <x-ui.empty title="Keine Veranstaltungen für den Newsletter verfügbar"
                    :action-href="route('admin.newsletter.index')" action-label="Zur Newsletter-Verwaltung">
                    Legen Sie zuerst einige Veranstaltungen an.
                </x-ui.empty>
            @endif
        </div>

        {{-- Versand --}}
        <x-ui.summary title="Newsletter versenden">
            <x-ui.button variant="secondary" :href="route('admin.newsletter.preview', ['type' => $type])" target="_blank" rel="noopener" full>
                <x-slot:icon><x-icon.eye /></x-slot:icon>
                Vorschau anzeigen
            </x-ui.button>

            <form action="{{ route('admin.newsletter.send') }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="{{ $type }}">
                <input type="hidden" name="send_to" value="test">
                <x-ui.button type="submit" variant="secondary" full
                    onclick="return confirm('Test-Newsletter an alle Admins senden?')">Test an Admins senden</x-ui.button>
            </form>

            <form action="{{ route('admin.newsletter.send') }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="{{ $type }}">
                <input type="hidden" name="send_to" value="all">
                <x-ui.button type="submit" full
                    onclick="return confirm('Newsletter wirklich an ALLE Abonnenten senden? Diese Aktion kann nicht rückgängig gemacht werden!')">An alle Abonnenten senden</x-ui.button>
            </form>

            <x-ui.alert tone="info" title="Hinweis" size="small">
                <ul class="m-0 list-disc pl-5">
                    <li>Der Newsletter wird asynchron versendet.</li>
                    <li>Der Versand kann einige Minuten dauern.</li>
                    <li>Testen Sie vor dem Live-Versand.</li>
                </ul>
            </x-ui.alert>

            <div class="border-t border-line pt-5">
                <h3 class="m-0 mb-3 text-ui font-semibold">Versand-Statistik</h3>
                <dl class="m-0 flex flex-col gap-2 text-small">
                    <div class="flex justify-between gap-4">
                        <dt class="text-muted">Empfänger</dt>
                        <dd class="m-0 font-semibold tabular-nums">{{ number_format(\App\Models\User::where('newsletter_subscribed', true)->count()) }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-muted">Veranstaltungen</dt>
                        <dd class="m-0 font-semibold tabular-nums">{{ $upcomingEvents->count() }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-muted">Hervorgehoben</dt>
                        <dd class="m-0 font-semibold tabular-nums">{{ $featuredEvents->count() }}</dd>
                    </div>
                </dl>
            </div>
        </x-ui.summary>
    </div>
</x-layouts.app>
