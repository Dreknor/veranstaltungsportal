@use('App\Support\Ui')
<x-layouts.public :title="$event->title" :description="\App\Support\Ui::plainText($event->description, 160) ?: null">
    <!-- SEO & Social Media Meta Tags -->
    @push('meta')
        @php
            $breadcrumbs = [
                ['name' => 'Home', 'url' => route('home')],
                ['name' => 'Veranstaltungen', 'url' => route('events.index')],
                [
                    'name' => $event->category->name ?? 'Event',
                    'url' => route('events.index', ['category' => $event->event_category_id]),
                ],
                ['name' => $event->title, 'url' => route('events.show', $event->slug)],
            ];
        @endphp
        <x-meta-tags :event="$event" :breadcrumbs="$breadcrumbs" />
    @endpush

    @php
        // Zustand der Veranstaltung – dieselbe Logik wie in der Listenzeile (<x-ui.event-row>)
        $free = null;
        if ($event->is_cancelled) {
            $state = 'cancelled';
        } elseif ($event->end_date->isPast()) {
            $state = 'past';
        } elseif ($event->isExternal()) {
            $state = 'external';
        } elseif ($event->hasAvailableTickets()) {
            $state = 'bookable';
            if ($event->max_attendees) {
                $free = $event->availableTickets();
            } else {
                $onSale = $event->ticketTypes->filter(fn($t) => $t->isOnSale());
                $free =
                    $onSale->isNotEmpty() && $onSale->every(fn($t) => $t->quantity !== null)
                        ? $onSale->sum(fn($t) => $t->availableQuantity())
                        : null;
            }
        } else {
            $soldOut =
                ($event->max_attendees && $event->availableTickets() <= 0) ||
                ($event->ticketTypes->isNotEmpty() &&
                    $event->ticketTypes->every(fn($t) => $t->availableQuantity() === 0));
            $state = $soldOut ? 'waitlist' : 'closed';
        }

        // Preis: niedrigster buchbarer Preis; "ab", wenn es mehrere Preise gibt
        $priceText = null;
        if (!$event->isExternal() && !in_array($state, ['cancelled', 'past'], true)) {
            $minPrice = $event->getMinimumPrice() ?? $event->ticketTypes->min('price');
            if ($minPrice !== null) {
                $several = $event->ticketTypes->pluck('price')->map(fn($p) => (float) $p)->unique()->count() > 1;
                $priceText = ($several && (float) $minPrice > 0 ? 'ab ' : '') . Ui::price($minPrice);
            }
        }

        $multiDay = !$event->start_date->isSameDay($event->end_date);
        $dates = $event->hasMultipleDates() ? $event->dates()->active()->orderBy('start_date')->get() : collect();
        $hasVenue = filled($event->venue_name) || filled($event->venue_city);

        $visibleTickets = $event->ticketTypes->where('is_available', true);
        $showTickets =
            in_array($state, ['bookable', 'waitlist', 'closed'], true) &&
            ($visibleTickets->isNotEmpty() || $event->price_from !== null);

        $approvedReviews = $event->reviews->where('is_approved', true);
        $organization = $event->organization;
        $showOrganization =
            $organization &&
            ($organization->description || $organization->email || $organization->phone || $organization->website);
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
                @if ($event->category)
                    <p class="m-0 text-small text-muted">{{ $event->category->name }}</p>
                @endif
                <h1 class="m-0 text-title text-ink">{{ $event->title }}</h1>
                <p class="m-0 text-ui text-ink-soft">
                    <x-event-type-badge :event="$event" size="md" />
                    <span aria-hidden="true">·</span>
                    Veranstalter: {{ $event->getOrganizerName() }}
                </p>

                @if ($event->is_cancelled)
                    <x-ui.alert tone="danger" title="Diese Veranstaltung wurde abgesagt." size="base"
                        class="mt-3">
                        <p>Diese Veranstaltung findet nicht wie geplant statt.</p>
                        @if ($event->cancellation_reason)
                            <p><span class="font-medium">Grund:</span> {{ $event->cancellation_reason }}</p>
                        @endif
                        @if ($event->cancelled_at)
                            <p class="text-small">Abgesagt am {{ $event->cancelled_at->format('d.m.Y, H:i') }} Uhr</p>
                        @endif
                    </x-ui.alert>
                @elseif($event->end_date->isPast())
                    <x-ui.alert tone="info" title="Diese Veranstaltung hat bereits stattgefunden." size="base"
                        class="mt-3">
                        Sie endete am {{ Ui::date($event->end_date) }} um {{ $event->end_date->format('H:i') }} Uhr.
                        Eine Buchung ist nicht mehr möglich.
                    </x-ui.alert>
                @endif
            </header>

            {{-- Mitlaufender Kasten: unter 1024 px direkt nach dem Titelbereich --}}
            <x-ui.summary title="Auf einen Blick" id="kasten-titel" class="lg:col-start-2 lg:row-span-2 lg:row-start-1">
                <dl class="m-0 flex flex-col gap-3 border-t border-line pt-4">
                    <div class="flex flex-col gap-0.5">
                        <dt class="text-small text-muted">Datum</dt>
                        <dd class="m-0 text-ui font-medium tabular-nums">
                            @if ($dates->isNotEmpty())
                                {{ $dates->count() }} Termine, {{ Ui::date($dates->first()->start_date) }} bis
                                {{ Ui::date($dates->last()->start_date) }}
                                <a href="#termine"
                                    class="mt-0.5 block text-small font-medium text-ink underline underline-offset-[3px]">Alle
                                    Termine</a>
                            @elseif ($multiDay)
                                {{ Ui::date($event->start_date) }} – {{ Ui::date($event->end_date) }}
                            @else
                                {{ Ui::date($event->start_date) }}
                            @endif
                        </dd>
                    </div>
                    @if ($dates->isEmpty())
                        <div class="flex flex-col gap-0.5">
                            <dt class="text-small text-muted">Zeit</dt>
                            <dd class="m-0 text-ui font-medium tabular-nums">
                                @if ($multiDay)
                                    Beginn {{ $event->start_date->format('H:i') }} Uhr, Ende
                                    {{ $event->end_date->format('H:i') }} Uhr
                                @else
                                    {{ Ui::time($event->start_date, $event->end_date) }}
                                @endif
                            </dd>
                        </div>
                    @endif
                    <div class="flex flex-col gap-0.5">
                        <dt class="text-small text-muted">Format</dt>
                        <dd class="m-0 text-ui font-medium">{{ Ui::format($event) }}</dd>
                    </div>
                    @if ($hasVenue)
                        <div class="flex flex-col gap-0.5">
                            <dt class="text-small text-muted">Ort</dt>
                            <dd class="m-0 text-ui font-medium">
                                {{ collect([$event->venue_name, $event->venue_city])->filter()->implode(', ') }}
                            </dd>
                        </div>
                    @endif
                </dl>

                @if ($priceText)
                    <x-ui.summary-total label="Preis" :amount="$priceText" />
                @endif

                <div class="flex flex-col gap-3">
                    @switch($state)
                        @case('bookable')
                            <x-ui.status
                                tone="free">{{ $free === null ? 'Plätze frei' : 'noch ' . $free . ' frei' }}</x-ui.status>
                            <x-ui.button :href="route('bookings.create', $event)" size="lg" full>Buchen</x-ui.button>
                        @break

                        @case('waitlist')
                            <x-ui.status tone="waitlist">Ausgebucht – Warteliste</x-ui.status>
                            <x-ui.button href="#warteliste" size="lg" full>Auf die Warteliste</x-ui.button>
                        @break

                        @case('external')
                            <x-ui.status tone="neutral">Anmeldung extern</x-ui.status>
                            @if ($event->external_booking_url)
                                <x-ui.button :href="$event->external_booking_url" target="_blank" rel="noopener noreferrer" size="lg" full>
                                    {{ $event->external_booking_button_text ?? 'Zur Anmeldung' }}
                                    <x-icon.external-link class="h-4 w-4 shrink-0" />
                                    <span class="sr-only">(öffnet eine externe Seite)</span>
                                </x-ui.button>
                            @endif
                            <p class="m-0 text-small text-muted">Die Anmeldung erfolgt über die Webseite des
                                Veranstalters.</p>
                        @break

                        @case('cancelled')
                            <x-ui.status tone="danger">Abgesagt</x-ui.status>
                            <p class="m-0 text-small text-ink-soft">Diese Veranstaltung findet nicht statt.</p>
                        @break

                        @case('past')
                            <x-ui.status tone="neutral">Beendet</x-ui.status>
                            <p class="m-0 text-small text-ink-soft">Eine Buchung ist nicht mehr möglich.</p>
                        @break

                        @default
                            <x-ui.status tone="neutral">derzeit nicht buchbar</x-ui.status>
                            <p class="m-0 text-small text-ink-soft">Der Ticketverkauf ist aktuell nicht geöffnet.</p>
                    @endswitch

                    @if (!in_array($state, ['cancelled', 'past'], true))
                        <x-calendar-export :event="$event" />
                    @endif

                    @auth
                        <x-ui.button variant="secondary" full onclick="toggleFavorite({{ $event->id }})"
                            id="favorite-btn-{{ $event->id }}"
                            aria-pressed="{{ auth()->user()->hasFavorited($event) ? 'true' : 'false' }}">
                            <x-slot:icon>
                                <x-icon.heart
                                    fill="{{ auth()->user()->hasFavorited($event) ? 'currentColor' : 'none' }}" />
                            </x-slot:icon>
                            <span id="favorite-text-{{ $event->id }}">
                                {{ auth()->user()->hasFavorited($event) ? 'Aus Favoriten entfernen' : 'Zu Favoriten hinzufügen' }}
                            </span>
                        </x-ui.button>
                    @endauth
                </div>
            </x-ui.summary>

            {{-- Hauptspalte --}}
            <div
                class="flex min-w-0 flex-col gap-10 lg:col-start-1 lg:row-start-2 [&>*+*]:border-t [&>*+*]:border-line [&>*+*]:pt-10">
                <div class="flex flex-col gap-8">
                    @if ($event->featured_image)
                        <img src="{{ Storage::url($event->featured_image) }}" alt="{{ $event->title }}"
                            class="aspect-video w-full rounded-box object-cover">
                    @endif

                    <x-ui.section title="Über die Veranstaltung" id="beschreibung">
                        <div class="richtext max-w-prose">{!! Ui::markdown($event->description) !!}</div>
                    </x-ui.section>
                </div>

                @if ($state === 'waitlist')
                    <x-waitlist-join :event="$event" />
                @endif

                @if ($dates->isNotEmpty())
                    <x-ui.section title="Termine" id="termine"
                        description="Diese Veranstaltung besteht aus {{ $dates->count() }} Terminen. Die Buchung gilt für alle Termine."
                        class="scroll-mt-6">
                        <x-ui.row-list>
                            @foreach ($dates as $date)
                                <x-ui.row :hover="false">
                                    <x-slot:lead>
                                        <x-ui.date-block :date="$date->start_date" />
                                    </x-slot:lead>
                                    <span @class([
                                        'text-base font-semibold tabular-nums',
                                        'text-muted' => $date->start_date->isPast(),
                                    ])>{{ Ui::date($date->start_date) }}</span>
                                    <span
                                        class="text-small text-ink-soft tabular-nums">{{ Ui::time($date->start_date, $date->end_date) }}</span>
                                    @if ($date->venue_name && $date->venue_name !== $event->venue_name)
                                        <span class="inline-flex items-center gap-1.5 text-small text-ink-soft">
                                            <x-icon.location class="h-4 w-4 flex-none text-muted" />
                                            {{ $date->venue_name }}, {{ $date->venue_city }}
                                        </span>
                                    @endif
                                    @if ($date->notes)
                                        <span class="text-small text-muted">{{ $date->notes }}</span>
                                    @endif
                                    @if ($date->start_date->isPast())
                                        <x-ui.status tone="neutral" class="mt-1">Bereits vorbei</x-ui.status>
                                    @endif
                                </x-ui.row>
                            @endforeach
                        </x-ui.row-list>
                    </x-ui.section>
                @endif

                @if ($showTickets)
                    <x-ui.section title="Tickets" id="tickets">
                        <ul class="m-0 flex list-none flex-col divide-y divide-line border-t border-line p-0">
                            @foreach ($visibleTickets as $ticket)
                                <li class="flex flex-wrap items-start justify-between gap-x-6 gap-y-1 py-4">
                                    <div class="flex min-w-0 flex-[1_1_240px] flex-col gap-1">
                                        <span class="text-base font-semibold">{{ $ticket->name }}</span>
                                        @if ($ticket->description)
                                            <span class="text-small text-ink-soft">{{ $ticket->description }}</span>
                                        @endif
                                        @if (!$ticket->isOnSale())
                                            @if ($ticket->sale_start && now()->lt($ticket->sale_start))
                                                <x-ui.status tone="neutral">
                                                    Verkauf ab {{ $ticket->sale_start->format('d.m.Y') }}
                                                    @if ($ticket->sale_start->format('H:i') !== '00:00')
                                                        um {{ $ticket->sale_start->format('H:i') }} Uhr
                                                    @endif
                                                </x-ui.status>
                                            @elseif($ticket->sale_end && now()->gt($ticket->sale_end))
                                                <x-ui.status tone="neutral">Verkauf beendet</x-ui.status>
                                            @endif
                                        @elseif($ticket->availableQuantity() === 0)
                                            <x-ui.status tone="waitlist">Ausverkauft</x-ui.status>
                                        @elseif($ticket->quantity)
                                            <x-ui.status tone="free">noch {{ $ticket->availableQuantity() }}
                                                verfügbar</x-ui.status>
                                        @else
                                            <x-ui.status tone="free">verfügbar</x-ui.status>
                                        @endif
                                    </div>
                                    <span
                                        class="text-base font-semibold whitespace-nowrap tabular-nums">{{ $ticket->price > 0 ? Ui::money($ticket->price) : 'kostenfrei' }}</span>
                                </li>
                            @endforeach

                            @if ($event->price_from !== null && $visibleTickets->isEmpty())
                                <li class="flex flex-wrap items-start justify-between gap-x-6 gap-y-1 py-4">
                                    <div class="flex min-w-0 flex-[1_1_240px] flex-col gap-1">
                                        <span class="text-base font-semibold">
                                            {{ $event->price_from > 0 ? 'Standard-Ticket' : 'Kostenfreie Teilnahme' }}
                                        </span>
                                        <span class="text-small text-ink-soft">Regulärer Zugang zur Veranstaltung</span>
                                    </div>
                                    <span
                                        class="text-base font-semibold whitespace-nowrap tabular-nums">{{ $event->price_from > 0 ? Ui::money($event->price_from) : 'kostenfrei' }}</span>
                                </li>
                            @endif
                        </ul>
                    </x-ui.section>
                @endif

                @if ($hasVenue)
                    <x-ui.section title="Veranstaltungsort" id="ort">
                        <address class="flex flex-col gap-0.5 text-base not-italic">
                            @if ($event->venue_name)
                                <span class="font-semibold">{{ $event->venue_name }}</span>
                            @endif
                            @if ($event->venue_address)
                                <span>{{ $event->venue_address }}</span>
                            @endif
                            <span>{{ $event->venue_postal_code }} {{ $event->venue_city }}</span>
                            @if ($event->venue_country)
                                <span class="text-ink-soft">{{ Ui::country($event->venue_country) }}</span>
                            @endif
                        </address>

                        @if ($event->directions)
                            <p class="m-0 mt-4 max-w-prose text-base text-ink-soft">{{ $event->directions }}</p>
                        @endif

                        @if ($event->venue_latitude && $event->venue_longitude)
                            <x-ui.button variant="secondary" class="mt-6"
                                href="https://www.google.com/maps?q={{ $event->venue_latitude }},{{ $event->venue_longitude }}"
                                target="_blank" rel="noopener noreferrer">
                                <x-slot:icon>
                                    <x-icon.location />
                                </x-slot:icon>
                                Auf Karte anzeigen
                                <span class="sr-only">(öffnet eine externe Seite)</span>
                            </x-ui.button>
                        @endif
                    </x-ui.section>
                @endif

                @if ($showOrganization)
                    <x-ui.section title="Veranstalter" id="veranstalter">
                        <div class="flex flex-col gap-4">
                            <div class="flex items-center gap-4">
                                @if ($organization->logo)
                                    <img src="{{ Storage::url($organization->logo) }}"
                                        alt="{{ $organization->name }} Logo"
                                        class="h-16 w-16 flex-none rounded-control border border-line bg-surface object-contain p-2">
                                @endif
                                <p class="m-0 text-h3">{{ $organization->name }}</p>
                            </div>
                            @if ($organization->description)
                                <p class="m-0 max-w-prose text-base text-ink-soft">{{ $organization->description }}
                                </p>
                            @endif
                            @if ($organization->email || $organization->phone || $organization->website)
                                <ul class="m-0 flex list-none flex-col gap-2 p-0 text-base">
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
                        </div>
                    </x-ui.section>
                @endif

                <x-ui.section title="Bewertungen" id="bewertungen">
                    @if ($approvedReviews->count() > 0)
                        @php $average = $event->averageRating(); @endphp
                        <div class="mb-6 flex items-center gap-4">
                            <span class="text-h2 tabular-nums">{{ number_format($average, 1, ',', '.') }}</span>
                            <div class="flex flex-col gap-1">
                                <span class="flex text-ink" role="img"
                                    aria-label="{{ number_format($average, 1, ',', '.') }} von 5 Sternen">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= $average)
                                            <x-icon.star-filled class="h-5 w-5" />
                                        @else
                                            <x-icon.star class="h-5 w-5 text-line-strong" />
                                        @endif
                                    @endfor
                                </span>
                                <span
                                    class="text-small text-muted">{{ trans_choice(':count Bewertung|:count Bewertungen', $approvedReviews->count()) }}</span>
                            </div>
                        </div>

                        <ul class="m-0 flex list-none flex-col divide-y divide-line border-t border-line p-0">
                            @foreach ($approvedReviews->take(5) as $review)
                                <li class="flex flex-col gap-2 py-4">
                                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                                        <span class="text-ui font-medium">{{ $review->reviewerName() }}</span>
                                        <span class="flex text-ink" role="img"
                                            aria-label="{{ $review->rating }} von 5 Sternen">
                                            @for ($i = 1; $i <= 5; $i++)
                                                @if ($i <= $review->rating)
                                                    <x-icon.star-filled class="h-4 w-4" />
                                                @else
                                                    <x-icon.star class="h-4 w-4 text-line-strong" />
                                                @endif
                                            @endfor
                                        </span>
                                        <span
                                            class="text-small text-muted">{{ $review->created_at->diffForHumans() }}</span>
                                    </div>
                                    @if ($review->comment)
                                        <p class="m-0 max-w-prose text-base text-ink-soft">{{ $review->comment }}</p>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="m-0 text-base text-muted">Noch keine Bewertungen vorhanden.</p>
                    @endif

                    <!-- Review Form for Authenticated Users -->
                    @auth
                        @php
                            $userReview = $event
                                ->reviews()
                                ->where('user_id', auth()->id())
                                ->first();
                            $hasAttended = $event->canBeReviewedBy(auth()->user());
                        @endphp

                        @if ($hasAttended && !$userReview)
                            <div class="mt-8 max-w-form">
                                <h3 class="m-0 mb-4 text-h3">Ihre Bewertung</h3>
                                <form action="{{ route('events.reviews.store', $event) }}" method="POST"
                                    class="flex flex-col gap-y-5">
                                    @csrf

                                    <!-- Star Rating -->
                                    <fieldset class="m-0 min-w-0 border-0 p-0">
                                        <x-ui.label as="legend" required>Bewertung</x-ui.label>
                                        <div class="flex gap-1" x-data="{ rating: 0, hoverRating: 0 }">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <button type="button" @click="rating = {{ $i }}"
                                                    @mouseenter="hoverRating = {{ $i }}"
                                                    @mouseleave="hoverRating = 0"
                                                    aria-label="{{ $i }} von 5 Sternen"
                                                    :aria-pressed="(rating === {{ $i }}).toString()"
                                                    class="inline-flex h-11 w-11 cursor-pointer items-center justify-center rounded-control transition-colors duration-150 hover:bg-zone"
                                                    :class="(hoverRating >= {{ $i }} || (rating >=
                                                        {{ $i }} && hoverRating === 0)) ? 'text-ink' :
                                                    'text-line-strong'">
                                                    <x-icon.star-filled class="h-6 w-6" />
                                                </button>
                                            @endfor
                                            <input type="hidden" name="rating" x-model="rating" required>
                                        </div>
                                        <x-ui.field-error name="rating" />
                                    </fieldset>

                                    <!-- Comment -->
                                    <x-ui.textarea label="Kommentar" name="comment" optional rows="4"
                                        placeholder="Teilen Sie Ihre Erfahrungen mit anderen Teilnehmenden." />

                                    <div>
                                        <x-ui.button type="submit">Bewertung abgeben</x-ui.button>
                                    </div>
                                </form>
                            </div>
                        @elseif($userReview)
                            <x-ui.alert tone="info" title="Ihre Bewertung wurde eingereicht." class="mt-6">
                                @if ($userReview->is_approved)
                                    Ihre Bewertung ist veröffentlicht.
                                @else
                                    Ihre Bewertung wird gerade geprüft und in Kürze veröffentlicht.
                                @endif
                            </x-ui.alert>
                        @elseif(!$hasAttended && $event->hasEnded())
                            <p class="m-0 mt-6 text-small text-muted">Nur Teilnehmende können eine Bewertung abgeben.</p>
                        @endif
                    @else
                        <div class="mt-6 flex flex-wrap items-center gap-x-4 gap-y-3">
                            <p class="m-0 text-base text-ink-soft">Melden Sie sich an, um eine Bewertung abzugeben.</p>
                            <x-ui.button variant="secondary" :href="route('login')">Anmelden</x-ui.button>
                        </div>
                    @endauth
                </x-ui.section>

                <x-social-share :event="$event" :shareUrls="$shareUrls" :shareableLink="$shareableLink" />
            </div>
        </div>

        @if ($relatedEvents->count() > 0)
            <x-ui.section title="Ähnliche Veranstaltungen" id="aehnliche" class="mt-16 border-t border-line pt-12">
                <x-ui.row-list>
                    @foreach ($relatedEvents as $relatedEvent)
                        <x-ui.event-row :event="$relatedEvent" />
                    @endforeach
                </x-ui.row-list>
            </x-ui.section>
        @endif
    </div>

    @auth
        @push('scripts')
            <script>
                function toggleFavorite(eventId) {
                    const btn = document.getElementById(`favorite-btn-${eventId}`);
                    const text = document.getElementById(`favorite-text-${eventId}`);
                    const icon = btn.querySelector('svg');

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
                            if (data.favorited) {
                                btn.setAttribute('aria-pressed', 'true');
                                text.textContent = 'Aus Favoriten entfernen';
                                icon.setAttribute('fill', 'currentColor');
                            } else {
                                btn.setAttribute('aria-pressed', 'false');
                                text.textContent = 'Zu Favoriten hinzufügen';
                                icon.setAttribute('fill', 'none');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.');
                        });
                }
            </script>
        @endpush
    @endauth
</x-layouts.public>
