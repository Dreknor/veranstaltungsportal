<x-layouts.app title="Bewertungen">
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Bewertungen verwalten</h1>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-600 mb-2">Gesamt</div>
            <div class="text-3xl font-bold text-gray-900">{{ $stats['total'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-600 mb-2">Warten auf Freigabe</div>
            <div class="text-3xl font-bold text-orange-600">{{ $stats['pending'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-600 mb-2">Freigegeben</div>
            <div class="text-3xl font-bold text-green-600">{{ $stats['approved'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-600 mb-2">Ø Bewertung</div>
            <div class="text-3xl font-bold text-blue-600">
                {{ $stats['average_rating'] ?? '0' }}
                <span class="text-lg text-gray-500">/5</span>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ route('admin.reviews.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium mb-2">Status</label>
                <select name="status" class="w-full border-gray-300 rounded">
                    <option value="">Alle</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Warten auf Freigabe</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Freigegeben</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Bewertung</label>
                <select name="rating" class="w-full border-gray-300 rounded">
                    <option value="">Alle</option>
                    @for($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                            {{ str_repeat('⭐', $i) }} ({{ $i }})
                        </option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Suche</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Event, Benutzer..."
                       class="w-full border-gray-300 rounded">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    Filtern
                </button>
            </div>
        </form>
    </div>

    {{-- Bulk Actions --}}
    <div class="bg-white rounded-lg shadow p-4 mb-6" id="bulk-actions" style="display: none;">
        <div class="flex items-center justify-between">
            <span class="text-sm text-gray-600">
                <span id="selected-count">0</span> Bewertung(en) ausgewählt
            </span>
            <div class="space-x-2">
                <form method="POST" action="{{ route('admin.reviews.bulk-approve') }}" class="inline" id="bulk-approve-form">
                    @csrf
                    <input type="hidden" name="review_ids" id="bulk-approve-ids">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm">
                        Freigeben
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.reviews.bulk-reject') }}" class="inline" id="bulk-reject-form">
                    @csrf
                    <input type="hidden" name="review_ids" id="bulk-reject-ids">
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm">
                        Ablehnen
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Reviews Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left">
                        <input type="checkbox" id="select-all" class="rounded">
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Benutzer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bewertung</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kommentar</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Datum</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aktionen</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($reviews as $review)
                <tr>
                    <td class="px-6 py-4">
                        <input type="checkbox" class="review-checkbox rounded" value="{{ $review->id }}">
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('events.show', $review->event->slug) }}" target="_blank" class="text-blue-600 hover:underline">
                            {{ Str::limit($review->event->title, 40) }}
                        </a>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <div class="font-medium">{{ $review->user->name }}</div>
                        <div class="text-gray-500 text-xs">{{ $review->user->email }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-yellow-500">{{ str_repeat('⭐', $review->rating) }}</div>
                        <div class="text-xs text-gray-500">{{ $review->rating }}/5</div>
                    </td>
                    <td class="px-6 py-4">
                        @if($review->comment)
                            <div class="text-sm text-gray-900">{{ Str::limit($review->comment, 100) }}</div>
                        @else
                            <span class="text-gray-400 text-sm italic">Kein Kommentar</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $review->created_at->format('d.m.Y') }}
                        <div class="text-xs">{{ $review->created_at->diffForHumans() }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($review->is_approved)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Freigegeben
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                                Wartet
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-2">
                        @if(!$review->is_approved)
                            <form method="POST" action="{{ route('admin.reviews.approve', $review) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-green-600 hover:text-green-900">Freigeben</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.reviews.reject', $review) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-orange-600 hover:text-orange-900">Ablehnen</button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" class="inline"
                              onsubmit="return confirm('Bewertung wirklich löschen?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Löschen</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                        <div class="text-4xl mb-4">⭐</div>
                        <div class="text-lg">Keine Bewertungen vorhanden</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($reviews->hasPages())
    <div class="mt-6">
        {{ $reviews->links() }}
    </div>
    @endif
</div>

<script>
// Bulk selection
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
        document.getElementById('bulk-approve-ids').value = JSON.stringify(selectedIds);
        document.getElementById('bulk-reject-ids').value = JSON.stringify(selectedIds);
    } else {
        bulkActions.style.display = 'none';
    }
}
</script>
</x-layouts.app>
