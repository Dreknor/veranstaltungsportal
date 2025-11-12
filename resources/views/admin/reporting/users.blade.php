<x-layouts.app>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Benutzer-Report') }}
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

            <!-- User Activity -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Benutzeraktivität</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Aktive Benutzer</span>
                                <span class="font-semibold">{{ number_format($data['user_activity']['active_users']) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Inaktive Benutzer</span>
                                <span class="font-semibold">{{ number_format($data['user_activity']['inactive_users']) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Benutzerrollen</h3>
                        <div class="space-y-3">
                            @foreach($data['user_roles'] as $role)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">{{ $role->is_organizer ? 'Veranstalter' : 'Teilnehmer' }}</span>
                                    <span class="font-semibold">{{ number_format($role->count) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Users -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Top 10 Benutzer (nach Buchungen)</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Benutzer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rolle</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Buchungen</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Registriert</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($data['top_users'] as $user)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $user->is_organizer ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                                {{ $user->is_organizer ? 'Veranstalter' : 'Teilnehmer' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap font-semibold">{{ $user->bookings_count }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->created_at->format('d.m.Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Keine Daten verfügbar</td>
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
