<x-layouts.app title="Bewertungen">
    <x-ui.page-header title="Bewertungen" />

    {{-- Kennzahlen --}}
    <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-ui.stat label="Gesamt" :value="$stats['total']" />
        <x-ui.stat label="Warten auf Freigabe" :value="$stats['pending']" />
        <x-ui.stat label="Freigegeben" :value="$stats['approved']" />
        <x-ui.stat label="Ø Bewertung" :value="($stats['average_rating'] ?? '0') . ' / 5'" />
    </div>

    {{-- Filter --}}
    <form method="GET" action="{{ route('organizer.reviews.index') }}" class="mb-8">
        <div class="grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] items-end gap-x-4 gap-y-5">
            <x-ui.select label="Status" name="status" :old="false">
                <option value="">Alle</option>
                <option value="pending" @selected(request('status') === 'pending')>Warten auf Freigabe</option>
                <option value="approved" @selected(request('status') === 'approved')>Freigegeben</option>
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
                    <option value="{{ $i }}" @selected(request('rating') == $i)>{{ $i }} {{ $i === 1 ? 'Stern' : 'Sterne' }}</option>
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
                <th scope="col">Benutzer</th>
                <th scope="col">Bewertung</th>
                <th scope="col">Kommentar</th>
                <th scope="col">Datum</th>
                <th scope="col">Status</th>
                <th scope="col" class="actions"><span class="sr-only">Aktionen</span></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reviews as $review)
                <tr>
                    <td>
                        <a href="{{ route('organizer.events.edit', $review->event) }}" class="text-ink underline underline-offset-[3px]">{{ Str::limit($review->event->title, 40) }}</a>
                    </td>
                    <td>
                        {{ $review->reviewerName() }}
                        <span class="block text-small text-muted">{{ $review->reviewerEmail() }}</span>
                    </td>
                    <td class="whitespace-nowrap">
                        <span class="flex items-center gap-0.5 text-ink" aria-hidden="true">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= $review->rating)
                                    <x-icon.star-filled class="h-4 w-4" />
                                @else
                                    <x-icon.star class="h-4 w-4 text-line-strong" />
                                @endif
                            @endfor
                        </span>
                        <span class="block text-small text-muted tabular-nums">{{ $review->rating }}/5</span>
                    </td>
                    <td>
                        @if ($review->comment)
                            {{ Str::limit($review->comment, 100) }}
                        @else
                            <span class="text-muted">Kein Kommentar</span>
                        @endif
                    </td>
                    <td class="whitespace-nowrap tabular-nums">
                        {{ $review->created_at->format('d.m.Y') }}
                        <span class="block text-small text-muted">{{ $review->created_at->diffForHumans() }}</span>
                    </td>
                    <td>
                        @if ($review->is_approved)
                            <x-ui.status tone="success">Freigegeben</x-ui.status>
                        @else
                            <x-ui.status tone="warning">Wartet</x-ui.status>
                        @endif
                    </td>
                    <td class="actions">
                        @if (!$review->is_approved)
                            <form method="POST" action="{{ route('organizer.reviews.approve', $review) }}" class="inline-block">
                                @csrf
                                @method('PATCH')
                                <x-ui.button type="submit" size="sm" variant="secondary">Freigeben</x-ui.button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('organizer.reviews.reject', $review) }}" class="inline-block">
                                @csrf
                                @method('PATCH')
                                <x-ui.button type="submit" size="sm" variant="secondary">Ablehnen</x-ui.button>
                            </form>
                        @endif
                        <x-ui.dropdown label="Weitere Aktionen für diese Bewertung" icon-only>
                            <x-ui.dropdown-item type="submit" form="review-delete-{{ $review->id }}" danger>Löschen</x-ui.dropdown-item>
                        </x-ui.dropdown>
                        <form id="review-delete-{{ $review->id }}" method="POST" action="{{ route('organizer.reviews.destroy', $review) }}" class="hidden"
                              onsubmit="return confirm('Bewertung wirklich löschen?')">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
            @empty
                <tr class="hover:bg-transparent">
                    <td colspan="7" class="p-0">
                        <x-ui.empty :bordered="false" title="Keine Bewertungen vorhanden">Bewertungen von Teilnehmenden erscheinen hier.</x-ui.empty>
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
