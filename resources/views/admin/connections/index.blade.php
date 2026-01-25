<x-layouts.app>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Benutzerverbindungen Verwaltung
            </h2>
            <a href="{{ route('admin.connections.statistics') }}" class="btn-secondary">
                <i class="fas fa-chart-bar mr-2"></i>
                Statistiken
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Gesamt</div>
                    <div class="mt-2 text-3xl font-semibold text-gray-900 dark:text-white">{{ $stats['total'] }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Akzeptiert</div>
                    <div class="mt-2 text-3xl font-semibold text-green-600 dark:text-green-400">{{ $stats['accepted'] }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Ausstehend</div>
                    <div class="mt-2 text-3xl font-semibold text-yellow-600 dark:text-yellow-400">{{ $stats['pending'] }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Blockiert</div>
                    <div class="mt-2 text-3xl font-semibold text-red-600 dark:text-red-400">{{ $stats['blocked'] }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Aktivster Nutzer</div>
                    @if($stats['most_connected_user'])
                        <div class="mt-2 text-sm font-semibold text-blue-600 dark:text-blue-400">
                            {{ Str::limit($stats['most_connected_user']->name, 20) }}
                        </div>
                    @else
                        <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">-</div>
                    @endif
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6 mb-6">
                <form method="GET" action="{{ route('admin.connections.index') }}" class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Benutzer suchen..." class="input">
                    </div>
                    <div class="min-w-[150px]">
                        <select name="status" class="input">
                            <option value="">Alle Status</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Ausstehend</option>
                            <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>Akzeptiert</option>
                            <option value="blocked" {{ request('status') === 'blocked' ? 'selected' : '' }}>Blockiert</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-search mr-2"></i>
                        Filtern
                    </button>
                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('admin.connections.index') }}" class="btn-secondary">
                            <i class="fas fa-times mr-2"></i>
                            Zurücksetzen
                        </a>
                    @endif
                </form>
            </div>

            <!-- Connections Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <form id="bulkActionForm" method="POST" action="{{ route('admin.connections.bulk') }}">
                        @csrf
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                Verbindungen ({{ $connections->total() }})
                            </h3>
                            <div class="flex gap-2">
                                <select name="action" id="bulkAction" class="input" style="width: auto;">
                                    <option value="">Bulk-Aktion wählen...</option>
                                    <option value="approve">Freigeben</option>
                                    <option value="block">Blockieren</option>
                                    <option value="delete">Löschen</option>
                                </select>
                                <button type="submit" class="btn-secondary" onclick="return confirm('Sind Sie sicher?')">
                                    Ausführen
                                </button>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900">
                                    <tr>
                                        <th class="px-6 py-3 text-left">
                                            <input type="checkbox" id="selectAll" class="rounded">
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Follower</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Folgt</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Erstellt</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aktionen</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($connections as $connection)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <input type="checkbox" name="connection_ids[]" value="{{ $connection->id }}" class="connection-checkbox rounded">
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <img src="{{ $connection->follower->profilePhotoUrl() }}" alt="{{ $connection->follower->name }}" class="w-10 h-10 rounded-full mr-3">
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $connection->follower->name }}</div>
                                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $connection->follower->email }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <img src="{{ $connection->following->profilePhotoUrl() }}" alt="{{ $connection->following->name }}" class="w-10 h-10 rounded-full mr-3">
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $connection->following->name }}</div>
                                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $connection->following->email }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                                    @if($connection->status === 'accepted') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                    @elseif($connection->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                                    @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                                    @endif">
                                                    {{ ucfirst($connection->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ $connection->created_at->format('d.m.Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('admin.connections.show', $connection) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3">
                                                    Details
                                                </a>
                                                <form method="POST" action="{{ route('admin.connections.destroy', $connection) }}" class="inline" onsubmit="return confirm('Sind Sie sicher?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                        Löschen
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                                Keine Verbindungen gefunden.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>

                @if($connections->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $connections->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('selectAll').addEventListener('change', function(e) {
            document.querySelectorAll('.connection-checkbox').forEach(checkbox => {
                checkbox.checked = e.target.checked;
            });
        });
    </script>
    @endpush
</x-layouts.app>

