<x-layouts.app>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Badge-Verwaltung
            </h2>
            <a href="{{ route('admin.badges.create') }}" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>
                Neuen Badge erstellen
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Gesamt Badges</div>
                    <div class="mt-2 text-3xl font-semibold text-gray-900 dark:text-white">{{ $stats['total'] }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Teilnahme</div>
                    <div class="mt-2 text-3xl font-semibold text-blue-600 dark:text-blue-400">{{ $stats['attendance'] }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Erfolge</div>
                    <div class="mt-2 text-3xl font-semibold text-green-600 dark:text-green-400">{{ $stats['achievement'] }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Spezial</div>
                    <div class="mt-2 text-3xl font-semibold text-purple-600 dark:text-purple-400">{{ $stats['special'] }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Vergeben</div>
                    <div class="mt-2 text-3xl font-semibold text-yellow-600 dark:text-yellow-400">{{ $stats['total_awarded'] }}</div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6 mb-6">
                <form method="GET" action="{{ route('admin.badges.index') }}" class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Badge suchen..." class="input">
                    </div>
                    <div class="min-w-[150px]">
                        <select name="type" class="input">
                            <option value="">Alle Typen</option>
                            <option value="attendance" {{ request('type') === 'attendance' ? 'selected' : '' }}>Teilnahme</option>
                            <option value="achievement" {{ request('type') === 'achievement' ? 'selected' : '' }}>Erfolge</option>
                            <option value="special" {{ request('type') === 'special' ? 'selected' : '' }}>Spezial</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-search mr-2"></i>
                        Filtern
                    </button>
                    @if(request()->hasAny(['search', 'type']))
                        <a href="{{ route('admin.badges.index') }}" class="btn-secondary">
                            <i class="fas fa-times mr-2"></i>
                            Zurücksetzen
                        </a>
                    @endif
                </form>
            </div>

            <!-- Badges Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse($badges as $badge)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-shrink-0 w-16 h-16 rounded-full flex items-center justify-center text-3xl"
                                 style="background-color: {{ $badge->color }}20; color: {{ $badge->color }}">
                                @if($badge->image_path)
                                    <img src="{{ asset($badge->image_path) }}"
                                         alt="{{ $badge->name }}"
                                         class="w-16 h-16 rounded-full object-cover border-2"
                                         style="border-color: {{ $badge->color }}">
                                @else
                                    <i class="{{ $badge->icon }}"></i>
                                @endif
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                @if($badge->type === 'attendance') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                @elseif($badge->type === 'achievement') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @else bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                                @endif">
                                {{ ucfirst($badge->type) }}
                            </span>
                        </div>

                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                            {{ $badge->name }}
                        </h3>

                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            {{ Str::limit($badge->description, 80) }}
                        </p>

                        <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400 mb-4">
                            <span>
                                <i class="fas fa-star mr-1"></i>
                                {{ $badge->points }} Punkte
                            </span>
                            <span>
                                <i class="fas fa-users mr-1"></i>
                                {{ $badge->users_count ?? 0 }} vergeben
                            </span>
                        </div>

                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                            <strong>Anforderung:</strong>
                            {{ $badge->requirement_value }}
                            {{ str_replace('_', ' ', $badge->requirement_type) }}
                        </div>

                        <div class="flex gap-2">
                            <a href="{{ route('admin.badges.show', $badge) }}" class="flex-1 btn-secondary text-center text-sm py-2">
                                Details
                            </a>
                            <a href="{{ route('admin.badges.edit', $badge) }}" class="flex-1 btn-primary text-center text-sm py-2">
                                Bearbeiten
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-12 text-center">
                        <i class="fas fa-medal text-6xl text-gray-400 dark:text-gray-600 mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">Keine Badges gefunden.</p>
                        <a href="{{ route('admin.badges.create') }}" class="btn-primary">
                            <i class="fas fa-plus mr-2"></i>
                            Ersten Badge erstellen
                        </a>
                    </div>
                @endforelse
            </div>

            @if($badges->hasPages())
                <div class="mt-6">
                    {{ $badges->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>

