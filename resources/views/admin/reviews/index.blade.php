<x-layouts.app title="Bewertungen" :header="false">
    <x-ui.page-header title="Bewertungen verwalten" />

    <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-ui.stat label="Gesamt" :value="$stats['total']" />
        <x-ui.stat label="Warten auf Freigabe" :value="$stats['pending']" />
        <x-ui.stat label="Freigegeben" :value="$stats['approved']" />
        <x-ui.stat label="Durchschnittliche Bewertung" :value="number_format((float) ($stats['average_rating'] ?? 0), 1, ',', '.') . ' von 5'" />
    </div>

    <form method="GET" action="{{ route('admin.reviews.index') }}"
        class="mb-6 grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] items-end gap-x-4 gap-y-5">
        <x-ui.select label="Status" name="status" :old="false" :options="['' => 'Alle', 'pending' => 'Warten auf Freigabe', 'approved' => 'Freigegeben']" :selected="request('status')" />
        <x-ui.select label="Bewertung" name="rating" :old="false">
            <option value="">Alle</option>
            @for ($i = 5; $i >= 1; $i--)
                <option value="{{ $i }}" @selected(request('rating') == $i)>{{ $i }}
                    {{ $i === 1 ? 'Stern' : 'Sterne' }}</option>
            @endfor
        </x-ui.select>
        <x-ui.field label="Suche" name="search" :old="false" :value="request('search')"
            placeholder="Veranstaltung, Benutzer" />
        <div>
            <x-ui.button type="submit" variant="secondary" size="lg" full>Filtern</x-ui.button>
        </div>
    </form>

    {{-- Sammelaktionen --}}
    <div class="mb-6 rounded-box border border-line bg-zone p-4" id="bulk-actions" style="display: none;">
        <div class="flex flex-wrap items-center justify-between gap-x-6 gap-y-3">
            <p class="m-0 text-small text-ink">
                <span id="selected-count">0</span> <span id="selected-label">Bewertungen</span> ausgewählt
            </p>
            <div class="flex flex-wrap items-center gap-3">
                <form method="POST" action="{{ route('admin.reviews.bulk-approve') }}" id="bulk-approve-form">
                    @csrf
                    <input type="hidden" name="review_ids" id="bulk-approve-ids">
                    <x-ui.button type="submit">Freigeben</x-ui.button>
                </form>
                <form method="POST" action="{{ route('admin.reviews.bulk-reject') }}" id="bulk-reject-form">
                    @csrf
                    <input type="hidden" name="review_ids" id="bulk-reject-ids">
                    <x-ui.button type="submit" variant="secondary">Ablehnen</x-ui.button>
                </form>
            </div>
        </div>
    </div>

    <x-ui.table label="Bewertungen">
        <thead>
            <tr>
                <th scope="col" class="w-12 max-sm:hidden">
                    <input type="checkbox" id="select-all" class="ui-checkbox" aria-label="Alle Bewertungen auswählen">
                </th>
                <th scope="col">Veranstaltung</th>
                <th scope="col" class="max-sm:hidden">Benutzer</th>
                <th scope="col" class="max-sm:hidden">Bewertung</th>
                <th scope="col" class="max-sm:hidden">Kommentar</th>
                <th scope="col">Status</th>
                <th scope="col" class="actions"><span class="sr-only">Aktionen</span></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reviews as $review)
                <tr>
                    <td class="max-sm:hidden">
                        <input type="checkbox" class="ui-checkbox review-checkbox" value="{{ $review->id }}"
                            aria-label="Bewertung von {{ $review->reviewerName() }} auswählen">
                    </td>
                    <td class="min-w-48 max-sm:min-w-0">
                        <a href="{{ route('events.show', $review->event->slug) }}" target="_blank" rel="noopener"
                            class="text-ink underline underline-offset-[3px]">{{ Str::limit($review->event->title, 40) }}</a>
                        <span
                            class="block text-small text-muted tabular-nums">{{ $review->created_at->format('d.m.Y') }}</span>
                        <span class="block text-small text-muted tabular-nums sm:hidden">{{ $review->reviewerName() }}
                            · {{ $review->rating }} von 5</span>
                    </td>
                    <td class="max-sm:hidden">
                        <span class="block">{{ $review->reviewerName() }}</span>
                        <span class="block text-small text-muted">{{ $review->reviewerEmail() }}</span>
                    </td>
                    <td class="whitespace-nowrap tabular-nums max-sm:hidden">{{ $review->rating }} von 5</td>
                    <td class="min-w-48 max-w-xs max-sm:hidden">
                        @if ($review->comment)
                            {{ Str::limit($review->comment, 100) }}
                        @else
                            <span class="text-muted">Kein Kommentar</span>
                        @endif
                    </td>
                    <td>
                        @if ($review->is_approved)
                            <x-ui.status tone="success">Freigegeben</x-ui.status>
                        @else
                            <x-ui.status tone="warning">Wartet</x-ui.status>
                        @endif
                    </td>
                    <td class="actions">
                        <x-ui.dropdown :label="'Aktionen für Bewertung von ' . $review->reviewerName()" icon-only width="w-48">
                            @if (!$review->is_approved)
                                <x-ui.dropdown-item type="submit"
                                    :form="'approve-' . ($review->id)">Freigeben</x-ui.dropdown-item>
                            @else
                                <x-ui.dropdown-item type="submit"
                                    :form="'reject-' . ($review->id)">Ablehnen</x-ui.dropdown-item>
                            @endif
                            <x-ui.dropdown-item danger type="submit"
                                :form="'destroy-' . ($review->id)">Löschen</x-ui.dropdown-item>
                        </x-ui.dropdown>
                        @if (!$review->is_approved)
                            <form id="approve-{{ $review->id }}" method="POST"
                                action="{{ route('admin.reviews.approve', $review) }}" class="hidden">@csrf
                                @method('PATCH')</form>
                        @else
                            <form id="reject-{{ $review->id }}" method="POST"
                                action="{{ route('admin.reviews.reject', $review) }}" class="hidden">@csrf
                                @method('PATCH')</form>
                        @endif
                        <form id="destroy-{{ $review->id }}" method="POST"
                            action="{{ route('admin.reviews.destroy', $review) }}" class="hidden"
                            onsubmit="return confirm('Bewertung wirklich löschen?')">@csrf @method('DELETE')</form>
                    </td>
                </tr>
            @empty
                <tr class="hover:bg-transparent">
                    <td colspan="7" class="p-0">
                        <x-ui.empty :bordered="false">Keine Bewertungen vorhanden.</x-ui.empty>
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

    <script>
        // Sammelauswahl
        const selectAllCheckbox = document.getElementById('select-all');
        const reviewCheckboxes = document.querySelectorAll('.review-checkbox');
        const bulkActions = document.getElementById('bulk-actions');
        const selectedCountEl = document.getElementById('selected-count');

        selectAllCheckbox?.addEventListener('change', function() {
            reviewCheckboxes.forEach(cb => cb.checked = this.checked);
            updateBulkActions();
        });

        reviewCheckboxes.forEach(cb => {
            cb.addEventListener('change', updateBulkActions);
        });

        function updateBulkActions() {
            const selected = Array.from(reviewCheckboxes).filter(cb => cb.checked);
            const selectedIds = selected.map(cb => cb.value);

            if (selected.length > 0) {
                bulkActions.style.display = 'block';
                selectedCountEl.textContent = selected.length;
                document.getElementById('selected-label').textContent = selected.length === 1 ? 'Bewertung' : 'Bewertungen';
                document.getElementById('bulk-approve-ids').value = JSON.stringify(selectedIds);
                document.getElementById('bulk-reject-ids').value = JSON.stringify(selectedIds);
            } else {
                bulkActions.style.display = 'none';
            }
        }
    </script>
</x-layouts.app>
