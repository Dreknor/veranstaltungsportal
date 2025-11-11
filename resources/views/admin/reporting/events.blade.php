<x-layouts.app>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Event-Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Period Filter -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" class="flex items-center space-x-4">
                        <label class="text-sm font-medium text-gray-700">Zeitraum:</label>
                        <select name="period" onchange="this.form.submit()" class="rounded-md border-gray-300">
                            <option value="7days" {{ $period === '7days' ? 'selected' : '' }}>Letzte 7 Tage</option>
                            <option value="30days" {{ $period === '30days' ? 'selected' : '' }}>Letzte 30 Tage</option>
                            <option value="90days" {{ $period === '90days' ? 'selected' : '' }}>Letzte 90 Tage</option>
                            <option value="365days" {{ $period === '365days' ? 'selected' : '' }}>Letztes Jahr</option>
                            <option value="all" {{ $period === 'all' ? 'selected' : '' }}>Gesamt</option>
                        </select>
                        <a href="{{ route('admin.reporting.index', ['period' => $period]) }}" class="ml-auto text-blue-600 hover:text-blue-800">
                            ← Zurück zur Übersicht
                        </a>
                    </form>
                </div>
            </div>

            <!-- Event Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Events nach Typ</h3>
                        @foreach($data['events_by_type'] as $type)
                            <div class="flex justify-between mb-1">
                                <span class="text-sm">{{ ucfirst($type->event_type) }}</span>
                                <span class="text-sm font-semibold">{{ $type->count }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Status</h3>
                        @foreach($data['published_vs_draft'] as $status)
                            <div class="flex justify-between mb-1">
                                <span class="text-sm">{{ $status->published ? 'Veröffentlicht' : 'Entwurf' }}</span>
                                <span class="text-sm font-semibold">{{ $status->count }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Durchschnittliche Kapazität</h3>
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($data['average_capacity'], 0) }}</p>
                        <p class="text-xs text-gray-500 mt-1">Teilnehmer pro Event</p>
                    </div>
                </div>
            </div>

            <!-- Events by Category -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Events nach Kategorie</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategorie</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Anzahl Events</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Umsatz</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($data['events_by_category'] as $category)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $category->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $category->events_count }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap font-semibold">
                                            €{{ number_format($category->bookings_sum_total_amount ?? 0, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">Keine Daten verfügbar</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
<x-layouts.app>
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Event-Report</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Event-Performance und Trends-Analyse</p>
    </div>

    <div class="space-y-6">
            <!-- Period Filter -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" class="flex items-center space-x-4">
                        <label class="text-sm font-medium text-gray-700">Zeitraum:</label>
                        <select name="period" onchange="this.form.submit()" class="rounded-md border-gray-300">
                            <option value="7days" {{ $period === '7days' ? 'selected' : '' }}>Letzte 7 Tage</option>
                            <option value="30days" {{ $period === '30days' ? 'selected' : '' }}>Letzte 30 Tage</option>
                            <option value="90days" {{ $period === '90days' ? 'selected' : '' }}>Letzte 90 Tage</option>
                            <option value="365days" {{ $period === '365days' ? 'selected' : '' }}>Letztes Jahr</option>
                            <option value="all" {{ $period === 'all' ? 'selected' : '' }}>Gesamt</option>
                        </select>
                        <a href="{{ route('admin.reporting.index', ['period' => $period]) }}" class="ml-auto text-blue-600 hover:text-blue-800">
                            ← Zurück zur Übersicht
                        </a>
                    </form>
                </div>
            </div>

            <!-- Event Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Events nach Typ</h3>
                        @foreach($data['events_by_type'] as $type)
                            <div class="flex justify-between mb-1">
                                <span class="text-sm">{{ ucfirst($type->event_type) }}</span>
                                <span class="text-sm font-semibold">{{ $type->count }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Status</h3>
                        @foreach($data['published_vs_draft'] as $status)
                            <div class="flex justify-between mb-1">
                                <span class="text-sm">{{ $status->published ? 'Veröffentlicht' : 'Entwurf' }}</span>
                                <span class="text-sm font-semibold">{{ $status->count }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Durchschnittliche Kapazität</h3>
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($data['average_capacity'], 0) }}</p>
                        <p class="text-xs text-gray-500 mt-1">Teilnehmer pro Event</p>
                    </div>
                </div>
            </div>

            <!-- Events by Category -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Events nach Kategorie</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategorie</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Anzahl Events</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Umsatz</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($data['events_by_category'] as $category)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $category->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $category->events_count }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap font-semibold">
                                            €{{ number_format($category->bookings_sum_total_amount ?? 0, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">Keine Daten verfügbar</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>

