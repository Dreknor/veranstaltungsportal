<x-layouts.app title="Bewertungen">
    <x-ui.page-header title="Bewertungen" />

    {{-- Kennzahlen --}}
    <div class="mb-4 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <x-ui.stat label="Warten auf Freigabe" :value="$stats['pending']" />
        <x-ui.stat label="Freigegeben" :value="$stats['approved']" />
        <x-ui.stat label="Abgelehnt" :value="$stats['rejected']" />
        <x-ui.stat label="Öffentliche Bewertung"
            :value="$summary->isMeaningful() ? $summary->formattedAverage() . ' von 5' : 'noch keine'"
            :sub="$summary->countLabel() . ' aus ' . \App\Support\ReviewSummary::ORGANIZATION_MONTHS . ' Monaten'" />
    </div>
    <p class="mt-0 mb-8 max-w-prose text-small text-muted">
        Freigegebene Bewertungen erscheinen auf der Veranstaltungsseite, bei späteren Terminen desselben Angebots
        und in
        @if ($organization->hasPublicProfile())
            <a href="{{ route('organizations.show', $organization) }}" class="text-ink underline underline-offset-[3px]">Ihrem
                Veranstalter-Profil</a>.
        @else
            Ihrem Veranstalter-Profil.
        @endif
        Ein Durchschnitt wird ab {{ \App\Support\ReviewSummary::MIN_COUNT }} Bewertungen angezeigt. Lehnen Sie
        Bewertungen nur bei Regelverstoß ab.
    </p>

    {{-- Filter --}}
    <form method="GET" action="{{ route('organizer.reviews.index') }}" class="mb-8">
        <div class="grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] items-end gap-x-4 gap-y-5">
            <x-ui.select label="Status" name="status" :old="false">
                <option value="">Alle</option>
                <option value="pending" @selected(request('status') === 'pending')>Warten auf Freigabe</option>
                <option value="approved" @selected(request('status') === 'approved')>Freigegeben</option>
                <option value="rejected" @selected(request('status') === 'rejected')>Abgelehnt</option>
            </x-ui.select>
            <x-ui.select label="Veranstaltung" name="event_id" :old="false">
                <option value="">Alle Veranstaltungen</option>
                @foreach ($events as $event)
                    <option value="{{ $event->id }}" @selected(request('event_id') == $event->id)>{{ $event->title }}</option>
                @endforeach
            </x-ui.select>
            <x-ui.select label="Bewertung" name="rating" :old="false">
                <option value="">Alle</option>
                @for ($i = 5; $i >= 1; $i--)
                    <option value="{{ $i }}" @selected(request('rating') == $i)>{{ $i }}
                        {{ $i === 1 ? 'Stern' : 'Sterne' }}</option>
                @endfor
            </x-ui.select>
            <div>
                <x-ui.button type="submit" size="lg">Filtern</x-ui.button>
            </div>
        </div>
    </form>

    {{-- Bewertungen --}}
    <x-ui.table label="Bewertungen">
        <thead>
            <tr>
                <th scope="col">Veranstaltung</th>
                <th scope="col" class="max-2xl:hidden">Benutzer</th>
                <th scope="col" class="max-sm:hidden">Bewertung</th>
                <th scope="col" class="max-sm:hidden">Kommentar</th>
                <th scope="col" class="max-sm:hidden">Status</th>
                <th scope="col" class="actions"><span class="sr-only">Aktionen</span></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reviews as $review)
                <tr>
                    <td class="min-w-48 sm:min-w-56">
                        <a href="{{ route('organizer.events.edit', $review->event) }}"
                            class="text-ink underline underline-offset-[3px]">{{ $review->event->title }}</a>
                        <span class="block text-small text-muted tabular-nums">
                            <span class="2xl:hidden">{{ $review->reviewerName() }} ·
                            </span>{{ $review->created_at->format('d.m.Y') }}
                        </span>
                        <span class="mt-1 block text-small text-muted tabular-nums sm:hidden">
                            {{ $review->rating }}/5
                            @if ($review->comment)
                                · {{ Str::limit($review->comment, 60) }}
                            @endif
                        </span>
                        <span class="mt-1 block sm:hidden">
                            <x-review.status :review="$review" />
                        </span>
                    </td>
                    <td class="max-2xl:hidden">
                        {{ $review->reviewerName() }}
                        <span class="block text-small text-muted">{{ $review->reviewerEmail() }}</span>
                    </td>
                    <td class="whitespace-nowrap max-sm:hidden">
                        <x-ui.stars :value="$review->rating" size="sm" decorative />
                        <span class="block text-small text-muted tabular-nums">{{ $review->rating }}/5</span>
                    </td>
                    <td class="min-w-64 max-sm:hidden">
                        @if ($review->comment)
                            {{ Str::limit($review->comment, 100) }}
                        @else
                            <span class="text-muted">Kein Kommentar</span>
                        @endif
                    </td>
                    <td class="max-sm:hidden">
                        <x-review.status :review="$review" />
                        @if ($review->hasOrganizerReply())
                            <span class="mt-1 block text-small text-muted">beantwortet</span>
                        @endif
                    </td>
                    <td class="actions">
                        <x-ui.button size="sm" variant="secondary" :href="route('organizer.reviews.moderate', $review)">
                            {{ $review->isPending() ? 'Prüfen' : ($review->hasOrganizerReply() ? 'Ansehen' : 'Antworten') }}
                        </x-ui.button>
                        <x-ui.dropdown label="Weitere Aktionen für diese Bewertung" icon-only>
                            @if (!$review->is_approved)
                                <x-ui.dropdown-item type="submit"
                                    :form="'review-approve-' . ($review->id)">Freigeben</x-ui.dropdown-item>
                            @endif
                            @if (!$review->isRejected())
                                <x-ui.dropdown-item type="submit"
                                    :form="'review-reject-' . ($review->id)">Ablehnen</x-ui.dropdown-item>
                            @endif
                            <x-ui.dropdown-item type="submit" :form="'review-delete-' . ($review->id)"
                                danger>Löschen</x-ui.dropdown-item>
                        </x-ui.dropdown>
                        @if (!$review->is_approved)
                            <form id="review-approve-{{ $review->id }}" method="POST"
                                action="{{ route('organizer.reviews.approve', $review) }}" class="hidden">
                                @csrf
                                @method('PATCH')
                            </form>
                        @endif
                        @if (!$review->isRejected())
                            <form id="review-reject-{{ $review->id }}" method="POST"
                                action="{{ route('organizer.reviews.reject', $review) }}" class="hidden">
                                @csrf
                                @method('PATCH')
                            </form>
                        @endif
                        <form id="review-delete-{{ $review->id }}" method="POST"
                            action="{{ route('organizer.reviews.destroy', $review) }}" class="hidden"
                            onsubmit="return confirm('Bewertung wirklich löschen?')">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
            @empty
                <tr class="hover:bg-transparent">
                    <td colspan="6" class="p-0">
                        <x-ui.empty :bordered="false" title="Keine Bewertungen vorhanden">Bewertungen von Teilnehmenden
                            erscheinen hier.</x-ui.empty>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-ui.table>

    @if ($reviews->hasPages())
        <div class="mt-6">
            {{ $reviews->links() }}
        </div>
    @endif
</x-layouts.app>
