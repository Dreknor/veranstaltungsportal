<x-layouts.app>
    <div class="max-w-4xl mx-auto py-8">
        <h1 class="text-2xl font-bold mb-4">Meine Featured Events</h1>
        <p class="text-gray-600 mb-6">Übersicht der hervorgehobenen Events und Zahlungen.</p>

        <div class="bg-white rounded shadow">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="text-left px-4 py-2">Event</th>
                        <th class="text-left px-4 py-2">Zeitraum</th>
                        <th class="text-left px-4 py-2">Status</th>
                        <th class="text-left px-4 py-2">Betrag</th>
                        <th class="text-left px-4 py-2">Aktion</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($featuredHistory as $fee)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $fee->event->title }}</td>
                            <td class="px-4 py-2">{{ optional($fee->featured_start_date)->format('d.m.Y') }} — {{ optional($fee->featured_end_date)->format('d.m.Y') }}</td>
                            <td class="px-4 py-2">{{ ucfirst($fee->payment_status) }}</td>
                            <td class="px-4 py-2">{{ number_format($fee->amount, 2, ',', '.') }} €</td>
                            <td class="px-4 py-2">
                                @if($fee->payment_status !== 'paid')
                                    <a href="{{ route('featured-events.payment', $fee) }}" class="text-blue-600 hover:text-blue-800">Zahlung</a>
                                @else
                                    <a href="{{ route('featured-events.extend', $fee->event) }}" class="text-blue-600 hover:text-blue-800">Verlängern</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-500">Keine hervorgehobenen Events gefunden.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
