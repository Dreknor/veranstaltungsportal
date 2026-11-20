@use('App\Support\ReviewSummary')
<x-layouts.public :title="$organization->name . ' – Veranstalter'" :description="\App\Support\Ui::plainText($organization->description, 160) ?:
    'Veranstaltungen und Bewertungen von ' . $organization->name">
    @php
        $profileUrl = fn(string $ansicht) => route(
            'organizations.show',
            $ansicht === 'aktuell' ? [$organization] : [$organization, 'ansicht' => $ansicht],
        );
        $hasContact = $organization->email || $organization->phone || $organization->website;
    @endphp

    <div class="mx-auto max-w-page px-page pt-6 pb-16">
        <div
            class="grid items-start gap-y-8 lg:grid-cols-[minmax(0,760px)_minmax(0,368px)] lg:justify-between lg:gap-x-12">

            {{-- Titelbereich --}}
            <header class="flex min-w-0 flex-col gap-3 lg:col-start-1 lg:row-start-1">
                <a href="{{ route('events.index') }}"
                    class="mb-1 inline-flex min-h-11 items-center gap-2 self-start text-ui font-medium text-ink-soft no-underline underline-offset-[3px] hover:underline">
                    <x-icon.arrow-left class="h-4 w-4" />
                    Alle Veranstaltungen
                </a>
                <p class="m-0 text-small text-muted">Veranstalter</p>
                <div class="flex items-center gap-4">
                    @if ($organization->logo)
                        <img src="{{ Storage::url($organization->logo) }}" alt=""
                            class="h-16 w-16 flex-none rounded-control border border-line bg-surface object-contain p-2">
                    @endif
                    <h1 class="m-0 min-w-0 text-title text-ink">{{ $organization->name }}</h1>
                </div>
                <x-ui.rating :summary="$summary" :href="$profileUrl('bewertungen')" />
                @if ($organization->description)
                    <p class="m-0 mt-2 max-w-prose text-base text-ink-soft">{{ $organization->description }}</p>
                @endif
            </header>

            {{-- Mitlaufender Kasten --}}
            <x-ui.summary title="Auf einen Blick" id="kasten-titel" :sticky="false"
                class="lg:col-start-2 lg:row-start-1">
                <dl class="m-0 flex flex-col gap-3 border-t border-line pt-4">
                    <div class="flex flex-col gap-0.5">
                        <dt class="text-small text-muted">Aktuelle Veranstaltungen</dt>
                        <dd class="m-0 text-ui font-medium tabular-nums">{{ $counts['aktuell'] }}</dd>
                    </div>
                    <div class="flex flex-col gap-0.5">
                        <dt class="text-small text-muted">Vergangene Veranstaltungen</dt>
                        <dd class="m-0 text-ui font-medium tabular-nums">{{ $counts['vergangen'] }}</dd>
                    </div>
                    <div class="flex flex-col gap-0.5">
                        <dt class="text-small text-muted">Bewertung</dt>
                        <dd class="m-0 text-ui font-medium">
                            @if ($summary->isMeaningful())
                                <span class="tabular-nums">{{ $summary->formattedAverage() }} von 5</span>
                                <span class="block text-small font-normal text-muted">
                                    {{ $summary->countLabel() }} aus den letzten {{ $months }} Monaten
                                </span>
                            @else
                                <span class="font-normal text-ink-soft">Noch zu wenige Bewertungen</span>
                            @endif
                        </dd>
                    </div>
                </dl>

                @if ($hasContact)
                    <ul class="m-0 flex list-none flex-col gap-2 border-t border-line p-0 pt-4 text-base">
                        @if ($organization->email)
                            <li class="flex items-center">
                                <x-protected-email :email="$organization->email" />
                            </li>
                        @endif
                        @if ($organization->phone)
                            <li class="flex items-center">
                                <x-protected-phone :phone="$organization->phone" />
                            </li>
                        @endif
                        @if ($organization->website)
                            <li class="flex items-center">
                                <x-protected-url :url="$organization->website" />
                            </li>
                        @endif
                    </ul>
                @endif
            </x-ui.summary>
        </div>

        {{-- Reiter und Listen über die volle Breite (Listenzeilen brauchen ihr Raster) --}}
        <div class="mt-12 flex min-w-0 flex-col gap-8">
            <x-ui.tabs label="Ansichten des Veranstalter-Profils" wrap>
                <x-ui.tab :href="$profileUrl('aktuell')" :active="$view === 'aktuell'" :count="$counts['aktuell']">Aktuelle
                    Veranstaltungen</x-ui.tab>
                <x-ui.tab :href="$profileUrl('vergangen')" :active="$view === 'vergangen'" :count="$counts['vergangen']">Vergangene
                    Veranstaltungen</x-ui.tab>
                <x-ui.tab :href="$profileUrl('bewertungen')" :active="$view === 'bewertungen'" :count="$counts['bewertungen']">Bewertungen</x-ui.tab>
            </x-ui.tabs>

            @if ($view === 'aktuell')
                <section aria-labelledby="aktuell-titel" class="flex flex-col gap-6">
                    <h2 id="aktuell-titel" class="sr-only">Aktuelle Veranstaltungen</h2>
                    @if ($upcoming->isEmpty())
                        <x-ui.empty :action-href="$profileUrl('vergangen')" action-label="Vergangene Veranstaltungen ansehen">
                            Zurzeit sind keine Veranstaltungen von {{ $organization->name }} geplant.
                        </x-ui.empty>
                    @else
                        <x-ui.row-list>
                            @foreach ($upcoming as $event)
                                <x-ui.event-row :event="$event" />
                            @endforeach
                        </x-ui.row-list>
                        @if ($upcoming->hasPages())
                            {{ $upcoming->links() }}
                        @endif
                    @endif
                </section>

                @if ($latestReviews->isNotEmpty())
                    <x-ui.section title="Neueste Bewertungen" id="neueste-bewertungen"
                        class="border-t border-line pt-10" :link-href="$profileUrl('bewertungen')" link-label="Alle Bewertungen">
                        <x-review.list :reviews="$latestReviews" show-event :organization-name="$organization->name" />
                    </x-ui.section>
                @endif
            @elseif ($view === 'vergangen')
                <section aria-labelledby="vergangen-titel" class="flex flex-col gap-6">
                    <h2 id="vergangen-titel" class="sr-only">Vergangene Veranstaltungen</h2>
                    @if ($past->isEmpty())
                        <x-ui.empty>Es gibt noch keine vergangenen Veranstaltungen.</x-ui.empty>
                    @else
                        <x-ui.row-list>
                            @foreach ($past as $event)
                                @php
                                    $eventSummary = new ReviewSummary(
                                        (int) $event->approved_reviews_count,
                                        round((float) $event->approved_reviews_avg, 1),
                                    );
                                @endphp
                                <x-ui.event-row :event="$event">
                                    <x-slot:aside>
                                        @if ($eventSummary->isMeaningful())
                                            <span class="text-base font-semibold whitespace-nowrap tabular-nums">
                                                {{ $eventSummary->formattedAverage() }} von 5</span>
                                            <span
                                                class="text-small text-muted tabular-nums">{{ $eventSummary->countLabel() }}</span>
                                        @elseif ($eventSummary->hasReviews())
                                            <span
                                                class="text-small text-muted tabular-nums">{{ $eventSummary->countLabel() }}</span>
                                        @else
                                            <span class="text-small text-muted">Keine Bewertungen</span>
                                        @endif
                                    </x-slot:aside>
                                    <x-slot:action>
                                        <x-ui.button variant="secondary" :href="route('events.show', $event->slug) . '#bewertungen'" full>Bewertungen
                                            ansehen</x-ui.button>
                                    </x-slot:action>
                                </x-ui.event-row>
                            @endforeach
                        </x-ui.row-list>
                        @if ($past->hasPages())
                            {{ $past->links() }}
                        @endif
                    @endif
                </section>
            @else
                <section aria-labelledby="bewertungen-titel" class="flex flex-col gap-8">
                    <div class="flex flex-col gap-1">
                        <h2 id="bewertungen-titel" class="m-0 text-h2 text-ink">Bewertungen</h2>
                        <p class="m-0 max-w-prose text-small text-muted">
                            Bewerten können nur Personen, die an der Veranstaltung teilgenommen haben. Der
                            Durchschnitt berücksichtigt die letzten {{ $months }} Monate.
                        </p>
                    </div>
                    <x-review.summary :summary="$summary" :note="'aus den letzten ' . $months . ' Monaten'" />
                    @if ($reviews->isNotEmpty())
                        <x-review.list :reviews="$reviews" show-event :organization-name="$organization->name" />
                        @if ($reviews->hasPages())
                            {{ $reviews->links() }}
                        @endif
                    @endif
                </section>
            @endif
        </div>
    </div>
</x-layouts.public>
