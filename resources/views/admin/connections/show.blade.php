<x-layouts.app>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Verbindungsdetails
            </h2>
            <a href="{{ route('admin.connections.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>
                Zurück zur Übersicht
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Follower Info -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Follower</h3>

                    <div class="flex items-center mb-4">
                        <img src="{{ $connection->follower->profilePhotoUrl() }}" alt="{{ $connection->follower->name }}" class="w-16 h-16 rounded-full mr-4">
                        <div>
                            <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ $connection->follower->name }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $connection->follower->email }}</div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Registriert seit</label>
                            <p class="text-gray-900 dark:text-white">{{ $connection->follower->created_at->format('d.m.Y') }}</p>
                        </div>
                        @if($connection->follower->is_organizer)
                            <div>
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    Veranstalter
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Following Info -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Folgt</h3>

                    <div class="flex items-center mb-4">
                        <img src="{{ $connection->following->profilePhotoUrl() }}" alt="{{ $connection->following->name }}" class="w-16 h-16 rounded-full mr-4">
                        <div>
                            <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ $connection->following->name }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $connection->following->email }}</div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Registriert seit</label>
                            <p class="text-gray-900 dark:text-white">{{ $connection->following->created_at->format('d.m.Y') }}</p>
                        </div>
                        @if($connection->following->is_organizer)
                            <div>
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    Veranstalter
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Connection Details -->
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Verbindungsdetails</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full
                                @if($connection->status === 'accepted') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @elseif($connection->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                @endif">
                                {{ ucfirst($connection->status) }}
                            </span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Erstellt am</label>
                            <p class="text-gray-900 dark:text-white">{{ $connection->created_at->format('d.m.Y H:i') }}</p>
                        </div>

                        @if($connection->accepted_at)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Akzeptiert am</label>
                                <p class="text-gray-900 dark:text-white">{{ $connection->accepted_at->format('d.m.Y H:i') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Aktionen</h3>

                    <div class="flex gap-4">
                        <form method="POST" action="{{ route('admin.connections.update-status', $connection) }}">
                            @csrf
                            <input type="hidden" name="status" value="{{ $connection->status === 'accepted' ? 'blocked' : 'accepted' }}">
                            <button type="submit" class="btn-secondary">
                                @if($connection->status === 'accepted')
                                    <i class="fas fa-ban mr-2"></i>
                                    Blockieren
                                @else
                                    <i class="fas fa-check mr-2"></i>
                                    Freigeben
                                @endif
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.connections.destroy', $connection) }}" onsubmit="return confirm('Sind Sie sicher, dass Sie diese Verbindung löschen möchten?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-danger">
                                <i class="fas fa-trash mr-2"></i>
                                Verbindung löschen
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

