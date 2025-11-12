<x-layouts.app>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <a href="{{ route('badges.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">
                    <i class="fas fa-arrow-left"></i> Zurück zu Abzeichen
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight inline-block">
                    <i class="fas fa-trophy text-yellow-500 mr-2"></i>Bestenliste
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- User's Rank (if applicable) -->
            @if($userRank)
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg shadow-lg p-6 mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold mb-1">Ihre Position</h3>
                            <p class="text-3xl font-bold">Platz {{ $userRank }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm mb-1">Ihre Punkte</p>
                            <p class="text-2xl font-bold">{{ auth()->user()->getTotalBadgePoints() }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Leaderboard -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">
                        Top {{ count($leaderboard) }} Teilnehmer
                    </h3>

                    <div class="space-y-4">
                        @foreach($leaderboard as $index => $entry)
                            @php
                                $rank = $index + 1;
                                $isCurrentUser = auth()->check() && $entry['user']->id === auth()->id();
                            @endphp

                            <div class="flex items-center p-4 rounded-lg {{ $isCurrentUser ? 'bg-blue-50 border-2 border-blue-500' : 'bg-gray-50' }} hover:shadow-md transition-shadow">
                                <!-- Rank -->
                                <div class="flex-shrink-0 w-16 text-center">
                                    @if($rank === 1)
                                        <div class="text-3xl">🥇</div>
                                    @elseif($rank === 2)
                                        <div class="text-3xl">🥈</div>
                                    @elseif($rank === 3)
                                        <div class="text-3xl">🥉</div>
                                    @else
                                        <span class="text-2xl font-bold text-gray-600">#{{ $rank }}</span>
                                    @endif
                                </div>

                                <!-- User Info -->
                                <div class="flex-1 ml-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            @if($entry['user']->show_profile_public || ($entry['user']->allow_networking && auth()->check() && auth()->user()->isFollowing($entry['user'])))
                                                <img class="h-12 w-12 rounded-full"
                                                     src="{{ $entry['user']->profilePhotoUrl() }}"
                                                     alt="{{ $entry['user']->displayName(auth()->user()) }}">
                                            @else
                                                <div class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center">
                                                    <i class="fas fa-user text-gray-600"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <h4 class="text-lg font-semibold text-gray-900">
                                                {{ $entry['user']->displayName(auth()->user()) }}
                                                @if($isCurrentUser)
                                                    <span class="ml-2 text-sm text-blue-600">(Sie)</span>
                                                @endif
                                                @if(!$entry['user']->show_profile_public)
                                                    <i class="fas fa-lock text-gray-400 text-sm ml-1" title="Privates Profil"></i>
                                                @endif
                                            </h4>
                                            <p class="text-sm text-gray-600">
                                                {{ $entry['badges_count'] }} {{ $entry['badges_count'] === 1 ? 'Abzeichen' : 'Abzeichen' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Points -->
                                <div class="flex-shrink-0 text-right">
                                    <div class="text-2xl font-bold text-yellow-600">
                                        <i class="fas fa-star"></i> {{ $entry['total_points'] }}
                                    </div>
                                    <div class="text-sm text-gray-600">Punkte</div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if(empty($leaderboard))
                        <div class="text-center py-12">
                            <i class="fas fa-trophy text-gray-300 text-6xl mb-4"></i>
                            <p class="text-gray-600">Noch keine Einträge in der Bestenliste.</p>
                            <a href="{{ route('events.index') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                Veranstaltungen entdecken
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Info Box -->
            <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-500 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-sm font-semibold text-blue-900 mb-2">
                            Wie funktioniert die Bestenliste?
                        </h4>
                        <p class="text-sm text-blue-800 mb-3">
                            Die Bestenliste basiert auf den Gesamtpunkten, die Sie durch das Verdienen von Abzeichen sammeln.
                            Jedes Abzeichen hat einen bestimmten Punktewert. Je mehr Abzeichen Sie verdienen, desto höher steigen Sie in der Rangliste!
                            Nehmen Sie an Fortbildungen teil, schreiben Sie Bewertungen und engagieren Sie sich, um mehr Abzeichen zu verdienen.
                        </p>
                        <div class="flex items-start mt-3 pt-3 border-t border-blue-200">
                            <i class="fas fa-shield-alt text-blue-600 mr-2 mt-1"></i>
                            <div class="text-sm text-blue-800">
                                <strong>Datenschutz:</strong> Nur Benutzer mit öffentlichen Profilen oder aktiviertem Networking erscheinen in der Bestenliste.
                                Bei privaten Profilen wird der Name anonymisiert angezeigt (z.B. "Max M.").
                                Sie können Ihre Sichtbarkeitseinstellungen in Ihren
                                <a href="{{ route('profile.edit') }}" class="text-blue-600 hover:text-blue-800 underline">Profileinstellungen</a> anpassen.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

