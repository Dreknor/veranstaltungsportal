<x-layouts.app>
    <div class="max-w-3xl mx-auto py-8">
        <h1 class="text-2xl font-bold mb-4">Featured Zeitraum verlängern</h1>
        <p class="text-gray-600 mb-6">Verlängern Sie die Hervorhebung Ihres Events.</p>

        @if($activeFee)
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded">
                <strong>Aktiv:</strong> Ihr Event ist aktuell bis {{ $activeFee->featured_end_date->format('d.m.Y') }} hervorgehoben.
            </div>
        @endif

        <form method="POST" action="{{ route('featured-events.process-extension', $event) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block font-medium mb-1">Dauer</label>
                <select name="duration_type" class="w-full border rounded p-2" required>
                    <option value="daily">1 Tag ({{ number_format($pricing['daily'], 2, ',', '.') }} €)</option>
                    <option value="weekly">7 Tage ({{ number_format($pricing['weekly'], 2, ',', '.') }} €)</option>
                    <option value="monthly">30 Tage ({{ number_format($pricing['monthly'], 2, ',', '.') }} €)</option>
                    <option value="custom">Individuell</option>
                </select>
            </div>
            <div>
                <label class="block font-medium mb-1">Eigene Tage (optional)</label>
                <input type="number" name="custom_days" class="w-full border rounded p-2" min="1" max="{{ config('monetization.featured_event_max_duration_days') }}">
            </div>

            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Verlängerung erstellen</button>
        </form>
    </div>
</x-layouts.app>
