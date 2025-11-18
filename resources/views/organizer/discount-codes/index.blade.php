<x-layouts.app title="Rabattcodes für {{ $event->title }}">
<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Rabattcodes</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">{{ $event->title }}</p>
        </div>
        <a href="{{ route('organizer.events.discount-codes.create', $event) }}"
           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Neuer Rabattcode
        </a>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('organizer.events.edit', $event) }}" class="text-blue-600 hover:text-blue-800">
                    ← Zurück zum Event
                </a>
            </div>

            @if (session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if ($discountCodes->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Typ</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wert</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Verwendet</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gültig</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aktionen</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($discountCodes as $code)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-mono font-bold text-gray-900">{{ $code->code }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                @if ($code->type === 'percentage')
                                                    Prozent
                                                @else
                                                    Festbetrag
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                @if ($code->type === 'percentage')
                                                    {{ $code->value }}%
                                                @else
                                                    {{ number_format($code->value, 2) }} €
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $code->bookings_count }}
                                                @if ($code->max_uses)
                                                    / {{ $code->max_uses }}
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @if ($code->valid_from || $code->valid_until)
                                                    <div class="text-xs">
                                                        @if ($code->valid_from)
                                                            Ab: {{ $code->valid_from->format('d.m.Y') }}<br>
                                                        @endif
                                                        @if ($code->valid_until)
                                                            Bis: {{ $code->valid_until->format('d.m.Y') }}
                                                        @endif
                                                    </div>
                                                @else
                                                    Unbegrenzt
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if ($code->is_active && $code->isValid())
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Aktiv
                                                    </span>
                                                @elseif ($code->is_active && !$code->isValid())
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        Abgelaufen
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                        Inaktiv
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('organizer.events.discount-codes.edit', [$event, $code]) }}"
                                                   class="text-blue-600 hover:text-blue-900 mr-3">Bearbeiten</a>

                                                <form action="{{ route('organizer.events.discount-codes.toggle', [$event, $code]) }}"
                                                      method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="text-yellow-600 hover:text-yellow-900 mr-3">
                                                        {{ $code->is_active ? 'Deaktivieren' : 'Aktivieren' }}
                                                    </button>
                                                </form>

                                                @if ($code->bookings_count == 0)
                                                    <form action="{{ route('organizer.events.discount-codes.destroy', [$event, $code]) }}"
                                                          method="POST" class="inline"
                                                          onsubmit="return confirm('Sind Sie sicher, dass Sie diesen Rabattcode löschen möchten?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900">Löschen</button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Keine Rabattcodes</h3>
                            <p class="mt-1 text-sm text-gray-500">Erstellen Sie Ihren ersten Rabattcode für dieses Event.</p>
                            <div class="mt-6">
                                <a href="{{ route('organizer.events.discount-codes.create', $event) }}"
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Rabattcode erstellen
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</x-layouts.app>

