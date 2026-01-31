<x-layouts.app :title="$user->fullName()">
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Profile Header -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-8">
                <div class="h-32 bg-gradient-to-r from-blue-500 to-blue-600"></div>
                <div class="px-6 pb-6">
                    <div class="flex items-end justify-between -mt-16">
                        <div class="flex items-end">
                            <img src="{{ $user->profilePhotoUrl() }}" alt="{{ $user->fullName() }}" class="w-32 h-32 rounded-full border-4 border-white">
                            <div class="ml-6 mb-2">
                                <h1 class="text-3xl font-bold text-gray-900">{{ $user->fullName() }}</h1>
                                <p class="text-gray-600">{{ $user->userTypeLabel() }}</p>
                            </div>
                        </div>

                        @if(auth()->check() && auth()->id() !== $user->id)
                            <div class="mb-2">
                                @if($connectionStatus === 'following')
                                    <div class="flex space-x-2">
                                        <span class="px-4 py-2 border border-green-500 bg-green-50 rounded-md text-sm font-medium text-green-700 flex items-center">
                                            <x-icon.check class="w-4 h-4 mr-2" />
                                            Verbunden
                                        </span>
                                        <form action="{{ route('connections.remove', $user) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50" onclick="return confirm('Verbindung wirklich entfernen?')">
                                                Entfernen
                                            </button>
                                        </form>
                                    </div>
                                @elseif($connectionStatus === 'pending')
                                    <div class="flex space-x-2">
                                        @if($isPendingRequest)
                                            <!-- User sent the request - can cancel -->
                                            <span class="px-4 py-2 border border-yellow-500 bg-yellow-50 rounded-md text-sm font-medium text-yellow-700">
                                                Anfrage gesendet
                                            </span>
                                            <form action="{{ route('connections.cancel', $user) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                                    Zurückziehen
                                                </button>
                                            </form>
                                        @else
                                            <!-- User received the request - can accept or decline -->
                                            <span class="px-4 py-2 border border-blue-500 bg-blue-50 rounded-md text-sm font-medium text-blue-700">
                                                Möchte sich verbinden
                                            </span>
                                            <form action="{{ route('connections.accept', $user) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                                                    Zurückfolgen
                                                </button>
                                            </form>
                                            <form action="{{ route('connections.decline', $user) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                                    Ablehnen
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @elseif($canSendConnectionRequest)
                                    <form action="{{ route('connections.send', $user) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-6 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                            Verbinden
                                        </button>
                                    </form>
                                @else
                                    <span class="px-4 py-2 border border-gray-300 bg-gray-100 rounded-md text-sm font-medium text-gray-500">
                                        Verbindungsanfragen deaktiviert
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Bio -->
                    @if($user->bio)
                        <div class="mt-6">
                            <p class="text-gray-700">{{ $user->bio }}</p>
                        </div>
                    @endif

                    <!-- Stats -->
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mt-6 pt-6 border-t border-gray-200">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['events_attended'] }}</p>
                            <p class="text-sm text-gray-600">Events besucht</p>
                        </div>
                        @if($user->isOrganizer())
                            <div class="text-center">
                                <p class="text-2xl font-bold text-gray-900">{{ $stats['events_organized'] }}</p>
                                <p class="text-sm text-gray-600">Events organisiert</p>
                            </div>
                        @endif
                        <div class="text-center">
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_hours'] }}</p>
                            <p class="text-sm text-gray-600">Stunden Fortbildung</p>
                        </div>
                        <div class="text-center">
                            <a href="{{ route('users.followers', $user) }}" class="block hover:text-blue-600">
                                <p class="text-2xl font-bold text-gray-900">{{ $stats['followers_count'] }}</p>
                                <p class="text-sm text-gray-600">Follower</p>
                            </a>
                        </div>
                        <div class="text-center">
                            <a href="{{ route('users.following', $user) }}" class="block hover:text-blue-600">
                                <p class="text-2xl font-bold text-gray-900">{{ $stats['following_count'] }}</p>
                                <p class="text-sm text-gray-600">Folgt</p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Contact Info -->
                    @if(($user->email || $user->phone) && auth()->check())
                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <h2 class="text-lg font-bold text-gray-900 mb-4">Kontakt</h2>
                            @if($showEmail && $user->email)
                                <div class="flex items-center text-sm text-gray-600 mb-2">
                                    <x-icon.mail class="w-5 h-5 mr-2" />
                                    <a href="mailto:{{ $user->email }}" class="hover:text-blue-600">{{ $user->email }}</a>
                                </div>
                            @endif
                            @if($showPhone && $user->phone)
                                <div class="flex items-center text-sm text-gray-600">
                                    <x-icon.phone class="w-5 h-5 mr-2" />
                                    <a href="tel:{{ $user->phone }}" class="hover:text-blue-600">{{ $user->phone }}</a>
                                </div>
                            @endif
                            @if(!$showEmail && !$showPhone)
                                <p class="text-sm text-gray-500 italic">Verbinden Sie sich, um Kontaktdaten zu sehen</p>
                            @endif
                        </div>
                    @endif

                    <!-- Badges -->
                    @if($user->badges->isNotEmpty())
                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <h2 class="text-lg font-bold text-gray-900 mb-4">Auszeichnungen</h2>
                            <div class="grid grid-cols-2 gap-4">
                                @foreach($user->badges as $badge)
                                    <a href="{{ route('badges.show', $badge) }}" class="flex flex-col items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                                        <div class="text-4xl mb-2">{{ $badge->icon }}</div>
                                        <p class="text-xs font-medium text-gray-900 text-center">{{ $badge->name }}</p>
                                    </a>
                                @endforeach
                            </div>
                            @if($user->badges->count() > 6)
                                <div class="mt-4 text-center">
                                    <a href="{{ route('badges.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                                        Alle Badges ansehen →
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Right Column -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Recent Events -->
                    @if($recentEvents->isNotEmpty())
                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <h2 class="text-lg font-bold text-gray-900 mb-4">
                                @if($user->isOrganizer())
                                    Organisierte Veranstaltungen
                                @else
                                    Besuchte Veranstaltungen
                                @endif
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($recentEvents as $event)
                                    <a href="{{ route('events.show', $event->slug) }}" class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                                        @if($event->featured_image)
                                            <img src="{{ asset('storage/' . $event->featured_image) }}" alt="{{ $event->title }}" class="w-full h-32 object-cover rounded mb-3">
                                        @endif
                                        <h3 class="font-semibold text-gray-900 mb-1">{{ $event->title }}</h3>
                                        <p class="text-sm text-gray-600">
                                            <x-icon.calendar class="inline w-4 h-4 mr-1" />
                                            @if($event->start_date->isSameDay($event->end_date))
                                                {{ $event->start_date->format('d.m.Y') }}
                                            @else
                                                {{ $event->start_date->format('d.m.Y') }} - {{ $event->end_date->format('d.m.Y') }}
                                            @endif
                                        </p>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Activity Feed -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4">Aktivität</h2>
                        <div class="space-y-4">
                            @if($stats['events_attended'] > 0)
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <x-icon.ticket class="w-5 h-5 text-blue-600" />
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm text-gray-900">
                                            Hat <span class="font-semibold">{{ $stats['events_attended'] }}</span> Veranstaltung(en) besucht
                                        </p>
                                    </div>
                                </div>
                            @endif

                            @if($user->isOrganizer() && $stats['events_organized'] > 0)
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                            <x-icon.calendar class="w-5 h-5 text-green-600" />
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm text-gray-900">
                                            Hat <span class="font-semibold">{{ $stats['events_organized'] }}</span> Veranstaltung(en) organisiert
                                        </p>
                                    </div>
                                </div>
                            @endif

                            @if($user->badges->isNotEmpty())
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                                            <span class="text-xl">🏆</span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm text-gray-900">
                                            Hat <span class="font-semibold">{{ $user->badges->count() }}</span> Badge(s) verdient
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

