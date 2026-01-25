<x-layouts.app>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Meine Abzeichen') }}
            </h2>
            <a href="{{ route('badges.leaderboard') }}" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-trophy mr-1"></i> Bestenliste
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-md bg-blue-500 p-3">
                                <i class="fas fa-medal text-white text-2xl"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Abzeichen
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900">
                                        {{ $stats['earned_badges'] }} / {{ $stats['total_badges'] }}
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-md bg-green-500 p-3">
                                <i class="fas fa-chart-line text-white text-2xl"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Fortschritt
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900">
                                        {{ $stats['completion_percentage'] }}%
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-md bg-yellow-500 p-3">
                                <i class="fas fa-star text-white text-2xl"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Punkte
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900">
                                        {{ $stats['total_points'] }}
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-md bg-purple-500 p-3">
                                <i class="fas fa-award text-white text-2xl"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Zuletzt
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-sm font-semibold text-gray-900 truncate">
                                        @if($stats['recent_badges']->isNotEmpty())
                                            {{ $stats['recent_badges']->first()->name }}
                                        @else
                                            Noch keine
                                        @endif
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Earned Badges -->
            @if($earnedBadges->isNotEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            <i class="fas fa-trophy text-yellow-500 mr-2"></i>
                            Verdiente Abzeichen ({{ $earnedBadges->count() }})
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                            @foreach($earnedBadges as $badge)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-lg transition-shadow cursor-pointer relative"
                                     onclick="window.location.href='{{ route('badges.show', $badge) }}'">
                                    <!-- Highlight Toggle -->
                                    <div class="absolute top-2 right-2">
                                        <button onclick="event.stopPropagation(); toggleHighlight({{ $badge->id }})"
                                                class="text-yellow-500 hover:text-yellow-600"
                                                title="{{ $badge->pivot->is_highlighted ? 'Von Favoriten entfernen' : 'Als Favorit markieren' }}">
                                            <i class="fas fa-star {{ $badge->pivot->is_highlighted ? 'fas' : 'far' }}"></i>
                                        </button>
                                    </div>

                                    <div class="flex flex-col items-center text-center">
                                        <div class="w-20 h-20 rounded-full flex items-center justify-center mb-3"
                                             style="background-color: {{ $badge->color }}20;">
                                            @if($badge->image_path)
                                                <img src="{{ asset($badge->image_path) }}"
                                                     alt="{{ $badge->name }}"
                                                     class="w-20 h-20 rounded-full object-cover border-2"
                                                     style="border-color: {{ $badge->color }}">
                                            @else
                                                <i class="fas fa-medal text-4xl" style="color: {{ $badge->color }};"></i>
                                            @endif
                                        </div>
                                        <h4 class="font-semibold text-gray-900 mb-1">{{ $badge->name }}</h4>
                                        <p class="text-sm text-gray-600 mb-2 line-clamp-2">{{ $badge->description }}</p>
                                        <div class="flex items-center space-x-3 text-xs text-gray-500">
                                            <span>
                                                <i class="fas fa-star text-yellow-500"></i> {{ $badge->points }} Punkte
                                            </span>
                                            <span>
                                                <i class="far fa-clock"></i>
                                                @if($badge->pivot->earned_at instanceof \Carbon\Carbon)
                                                    {{ $badge->pivot->earned_at->diffForHumans() }}
                                                @else
                                                    {{ \Carbon\Carbon::parse($badge->pivot->earned_at)->diffForHumans() }}
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Badges by Type -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h4 class="font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user-check text-blue-500 mr-2"></i>Teilnahme
                    </h4>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['badges_by_type']['attendance'] }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h4 class="font-semibold text-gray-700 mb-2">
                        <i class="fas fa-trophy text-yellow-500 mr-2"></i>Leistung
                    </h4>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['badges_by_type']['achievement'] }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h4 class="font-semibold text-gray-700 mb-2">
                        <i class="fas fa-star text-purple-500 mr-2"></i>Besonders
                    </h4>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['badges_by_type']['special'] }}</p>
                </div>
            </div>

            <!-- Unearned Badges -->
            @if($unearnedBadges->isNotEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            <i class="fas fa-lock text-gray-400 mr-2"></i>
                            Noch zu verdienen ({{ $unearnedBadges->count() }})
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                            @foreach($unearnedBadges as $badge)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-lg transition-shadow cursor-pointer opacity-60 hover:opacity-100"
                                     onclick="window.location.href='{{ route('badges.show', $badge) }}'">
                                    <div class="flex flex-col items-center text-center">
                                        <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mb-3 relative">
                                            @if($badge->image_path)
                                                <img src="{{ asset($badge->image_path) }}"
                                                     alt="{{ $badge->name }}"
                                                     class="w-20 h-20 rounded-full object-cover border-2 border-gray-300 grayscale">
                                            @else
                                                <i class="fas fa-medal text-4xl text-gray-400"></i>
                                            @endif
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                <i class="fas fa-lock text-gray-600 text-sm"></i>
                                            </div>
                                        </div>
                                        <h4 class="font-semibold text-gray-900 mb-1">{{ $badge->name }}</h4>
                                        <p class="text-sm text-gray-600 mb-2 line-clamp-2">{{ $badge->description }}</p>
                                        <div class="text-xs text-gray-500 mb-2">
                                            <i class="fas fa-star text-yellow-500"></i> {{ $badge->points }} Punkte
                                        </div>

                                        <!-- Progress Bar -->
                                        @if(isset($badgeProgress[$badge->id]) && !empty($badgeProgress[$badge->id]))
                                            @php
                                                $firstProgress = array_values($badgeProgress[$badge->id])[0];
                                            @endphp
                                            <div class="w-full mt-2">
                                                <div class="flex justify-between text-xs text-gray-600 mb-1">
                                                    <span>Fortschritt</span>
                                                    <span>{{ $firstProgress['percentage'] }}%</span>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div class="bg-blue-600 h-2 rounded-full"
                                                         style="width: {{ $firstProgress['percentage'] }}%"></div>
                                                </div>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    {{ $firstProgress['current'] }} / {{ $firstProgress['required'] }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        function toggleHighlight(badgeId) {
            fetch(`/badges/${badgeId}/toggle-highlight`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => console.error('Error:', error));
        }
    </script>
    @endpush
</x-layouts.app>

