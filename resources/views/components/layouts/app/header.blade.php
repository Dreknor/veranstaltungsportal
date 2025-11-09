<!-- Modern Header with Glassmorphism -->
<header class="sticky top-0 z-30 bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg shadow-sm border-b border-gray-200 dark:border-gray-700">
    <div class="flex items-center justify-between h-16 px-4 lg:px-6">
        <!-- Left side: Logo, Toggle, Breadcrumb -->
        <div class="flex items-center space-x-4">
            <!-- Sidebar Toggle Button -->
            <button @click="toggleSidebar"
                class="p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <!-- Logo -->
            <div class="flex items-center">
                <a href="/" class="flex items-center gap-2">
                        <span class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                            {{ config('app.name') }}
                        </span>
                </a>
            </div>
        </div>

        <!-- Center: Search Bar (Desktop) -->
        <div class="hidden lg:flex flex-1 max-w-xl mx-8">
            <form action="{{ route('events.index') }}" method="GET" class="w-full">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="search"
                           name="search"
                           placeholder="Events suchen..."
                           class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                           value="{{ request('search') }}">
                </div>
            </form>
        </div>

        <!-- Right side: Quick Actions, Notifications, Profile -->
        <div class="flex items-center space-x-2 md:space-x-3">
            @auth
            <!-- Search Icon (Mobile) -->
            <button @click="$dispatch('open-search')" class="lg:hidden p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700 transition-all duration-200">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </button>

            <!-- Notifications -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="relative p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    @if(auth()->user()->unreadNotifications->count() > 0)
                    <span class="absolute top-1 right-1 h-4 w-4 bg-red-500 rounded-full flex items-center justify-center text-white text-xs font-bold animate-pulse">
                        {{ auth()->user()->unreadNotifications->count() > 9 ? '9+' : auth()->user()->unreadNotifications->count() }}
                    </span>
                    @endif
                </button>

                <!-- Notifications Dropdown -->
                <div x-show="open"
                     x-cloak
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-xl shadow-xl py-2 z-50 border border-gray-200 dark:border-gray-700 max-h-96 overflow-y-auto">
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Benachrichtigungen</h3>
                            <a href="{{ route('notifications.index') }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">Alle anzeigen</a>
                        </div>
                    </div>
                    @forelse(auth()->user()->unreadNotifications->take(5) as $notification)
                    <a href="{{ $notification->data['url'] ?? route('notifications.index') }}"
                       class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                        <p class="text-sm text-gray-900 dark:text-gray-100 font-medium">{{ $notification->data['title'] ?? 'Benachrichtigung' }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $notification->data['message'] ?? '' }}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                    </a>
                    @empty
                    <div class="px-4 py-8 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Keine neuen Benachrichtigungen</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Profile Dropdown -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex items-center space-x-2 p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                    <div class="relative flex-shrink-0">
                        <img src="{{ Auth::user()->profilePhotoUrl() }}"
                             alt="{{ Auth::user()->fullName() }}"
                             class="h-8 w-8 rounded-full object-cover ring-2 ring-blue-500 dark:ring-blue-400">
                        <span class="absolute bottom-0 right-0 h-2.5 w-2.5 rounded-full bg-green-400 border-2 border-white dark:border-gray-800"></span>
                    </div>
                    <div class="hidden md:block text-left">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ Auth::user()->fullName() }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</p>
                    </div>
                    <svg class="hidden md:block h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Profile Dropdown Menu -->
                <div x-show="open"
                     x-cloak
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-xl shadow-xl py-2 z-50 border border-gray-200 dark:border-gray-700">
                    <!-- User Info (Mobile) -->
                    <div class="md:hidden px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ Auth::user()->fullName() }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ Auth::user()->email }}</p>
                    </div>

                    <a href="{{ route('dashboard') }}"
                        class="flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                        <svg class="h-5 w-5 mr-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>
                    <a href="{{ route('settings.profile.edit') }}"
                        class="flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                        <svg class="h-5 w-5 mr-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Profil
                    </a>
                    <a href="{{ route('settings.password.edit') }}"
                        class="flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                        <svg class="h-5 w-5 mr-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Einstellungen
                    </a>
                    <a href="{{ route('settings.notifications.edit') }}"
                        class="flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                        <svg class="h-5 w-5 mr-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        Benachrichtigungen
                    </a>

                    <div class="border-t border-gray-200 dark:border-gray-700 my-2"></div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="flex items-center w-full px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-150">
                            <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Abmelden
                        </button>
                    </form>
                </div>
            </div>
            @endauth
        </div>
    </div>
</header>
