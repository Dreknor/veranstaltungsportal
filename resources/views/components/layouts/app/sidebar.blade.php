<!-- Modern Sidebar with Smooth Animations -->
<!-- Overlay for mobile (moved outside of the aside so it blurs the page but not the sidebar) -->
<div x-show="sidebarOpen"
     @click="toggleSidebar"
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm md:hidden z-20 pointer-events-auto"
     aria-hidden="true"></div>

<aside :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full md:translate-x-0': !sidebarOpen, 'md:w-64': sidebarOpen, 'md:w-20': !sidebarOpen }"
    class="fixed md:static inset-y-0 left-0 z-40 w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transition-all duration-300 ease-in-out shadow-xl md:shadow-none">

    <!-- Sidebar Content -->
    <div class="relative h-full flex flex-col bg-gradient-to-b from-white to-gray-50 dark:from-gray-800 dark:to-gray-900">


        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto py-4 px-3 custom-scrollbar">
            <ul class="space-y-1">
                <!-- Dashboard -->
                <x-layouts.sidebar-link href="{{ route('dashboard') }}" icon='fas-house'
                    :active="request()->routeIs('dashboard')">
                    Dashboard
                </x-layouts.sidebar-link>

                <!-- My Events -->
                <x-layouts.sidebar-link href="{{ route('events.index') }}" icon='fas-calendar'
                    :active="request()->routeIs('events.index', 'events.show')">
                    Alle Events
                </x-layouts.sidebar-link>

                <!-- My Bookings -->
                <x-layouts.sidebar-link href="{{ route('user.bookings') }}" icon='fas-ticket'
                    :active="request()->routeIs('user.bookings')">
                    Meine Buchungen
                </x-layouts.sidebar-link>

                <!-- Favorites -->
                <x-layouts.sidebar-link href="{{ route('favorites.index') }}" icon='fas-heart'
                    :active="request()->routeIs('favorites.index')">
                    Favoriten
                </x-layouts.sidebar-link>

                <!-- Badges -->
                <x-layouts.sidebar-link href="{{ route('badges.index') }}" icon='fas-medal'
                    :active="request()->routeIs('badges.*')">
                    Abzeichen
                </x-layouts.sidebar-link>

                <!-- Connections -->
                <li>
                    <a href="{{ route('connections.index') }}"
                       class="group relative flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 ease-in-out
                              {{ request()->routeIs('connections.*', 'users.*')
                                  ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md shadow-blue-500/30 dark:shadow-blue-400/20'
                                  : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400' }}"
                       :class="{ 'justify-center': !sidebarOpen, 'justify-start': sidebarOpen }">
                        <!-- Active Indicator -->
                        @if(request()->routeIs('connections.*', 'users.*'))
                        <span class="absolute left-0 w-1 h-8 bg-white rounded-r-full"></span>
                        @endif

                        <!-- Icon Container -->
                        <span @class([
                            'flex items-center justify-center transition-transform duration-200 relative',
                            'group-hover:scale-110' => !request()->routeIs('connections.*', 'users.*')
                        ])>
                            <svg class="w-5 h-5 {{ request()->routeIs('connections.*', 'users.*') ? 'text-white' : 'text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400' }}"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <!-- Pending Requests Badge -->
                            @auth
                                @if(auth()->user()->getPendingRequestsCount() > 0)
                                <span class="absolute -top-1 -right-1 h-4 w-4 bg-green-500 rounded-full flex items-center justify-center text-white text-xs font-bold animate-pulse">
                                    {{ auth()->user()->getPendingRequestsCount() > 9 ? '9+' : auth()->user()->getPendingRequestsCount() }}
                                </span>
                                @endif
                            @endauth
                        </span>

                        <!-- Text -->
                        <span x-show="sidebarOpen"
                              x-transition:enter="transition ease-out duration-200"
                              x-transition:enter-start="opacity-0 -translate-x-2"
                              x-transition:enter-end="opacity-100 translate-x-0"
                              x-transition:leave="transition ease-in duration-150"
                              x-transition:leave-start="opacity-100 translate-x-0"
                              x-transition:leave-end="opacity-0 -translate-x-2"
                              class="ml-3 font-medium text-sm whitespace-nowrap flex-1">
                            Netzwerk
                        </span>

                        <!-- Hover Effect -->
                        @if(!request()->routeIs('connections.*', 'users.*'))
                        <span class="absolute inset-0 rounded-lg bg-gradient-to-r from-blue-500/0 to-purple-500/0 group-hover:from-blue-500/5 group-hover:to-purple-500/5 dark:group-hover:from-blue-500/10 dark:group-hover:to-purple-500/10 transition-all duration-200"></span>
                        @endif
                    </a>
                </li>

                <!-- Notifications with Badge -->
                <li>
                    <a href="{{ route('notifications.index') }}"
                       class="group relative flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 ease-in-out
                              {{ request()->routeIs('notifications.*')
                                  ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md shadow-blue-500/30 dark:shadow-blue-400/20'
                                  : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400' }}"
                       :class="{ 'justify-center': !sidebarOpen, 'justify-start': sidebarOpen }">
                        <!-- Active Indicator -->
                        @if(request()->routeIs('notifications.*'))
                        <span class="absolute left-0 w-1 h-8 bg-white rounded-r-full"></span>
                        @endif

                        <!-- Icon Container -->
                        <span @class([
                            'flex items-center justify-center w-5 h-5 transition-transform duration-200 relative',
                            'group-hover:scale-110' => !request()->routeIs('notifications.*')
                        ])>
                            <svg class="h-5 w-5 {{ request()->routeIs('notifications.*') ? 'text-white' : 'text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400' }}"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <!-- Notification Badge (only visible for authenticated users) -->
                            @auth
                                @if(auth()->user()->unreadNotifications->count() > 0)
                                <span class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 rounded-full flex items-center justify-center text-white text-xs font-bold animate-pulse">
                                    {{ auth()->user()->unreadNotifications->count() > 9 ? '9+' : auth()->user()->unreadNotifications->count() }}
                                </span>
                                @endif
                            @endauth
                        </span>

                        <!-- Text -->
                        <span x-show="sidebarOpen"
                              x-transition:enter="transition ease-out duration-200"
                              x-transition:enter-start="opacity-0 -translate-x-2"
                              x-transition:enter-end="opacity-100 translate-x-0"
                              x-transition:leave="transition ease-in duration-150"
                              x-transition:leave-start="opacity-100 translate-x-0"
                              x-transition:leave-end="opacity-0 -translate-x-2"
                              class="ml-3 font-medium text-sm whitespace-nowrap flex-1">
                            Benachrichtigungen
                        </span>

                        <!-- Hover Effect -->
                        @if(!request()->routeIs('notifications.*'))
                        <span class="absolute inset-0 rounded-lg bg-gradient-to-r from-blue-500/0 to-purple-500/0 group-hover:from-blue-500/5 group-hover:to-purple-500/5 dark:group-hover:from-blue-500/10 dark:group-hover:to-purple-500/10 transition-all duration-200"></span>
                        @endif
                    </a>
                </li>

                @auth
                @if(auth()->user()->hasRole('organizer'))
                    <!-- Organizer Section -->
                    <li class="pt-6 pb-2">
                        <div :class="{ 'px-3': sidebarOpen, 'px-0 text-center': !sidebarOpen }">
                            <h3 x-show="sidebarOpen" class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Veranstalter
                            </h3>
                            <div x-show="!sidebarOpen" class="h-px bg-gray-300 dark:bg-gray-600"></div>
                        </div>
                    </li>

                    <x-layouts.sidebar-link href="{{ route('organizer.dashboard') }}" icon='fas-chart-line'
                        :active="request()->routeIs('organizer.dashboard')">
                        Veranstalter Dashboard
                    </x-layouts.sidebar-link>

                    <x-layouts.sidebar-link href="{{ route('organizer.events.index') }}" icon='fas-calendar-days'
                        :active="request()->routeIs('organizer.events.*')">
                        Meine Events
                    </x-layouts.sidebar-link>

                    <x-layouts.sidebar-link href="{{ route('organizer.bookings.index') }}" icon='fas-receipt'
                        :active="request()->routeIs('organizer.bookings.*')">
                        Event Buchungen
                    </x-layouts.sidebar-link>

                    <x-layouts.sidebar-link href="{{ route('organizer.reviews.index') }}" icon='fas-star'
                        :active="request()->routeIs('organizer.reviews.*')">
                        Bewertungen
                    </x-layouts.sidebar-link>

                    <x-layouts.sidebar-link href="{{ route('organizer.invoices.index') }}" icon='fas-file-invoice-dollar'
                        :active="request()->routeIs('organizer.invoices.*')">
                        Rechnungen
                    </x-layouts.sidebar-link>

                    <x-layouts.sidebar-link href="{{ route('organizer.bank-account.index') }}" icon='fas-building-columns'
                        :active="request()->routeIs('organizer.bank-account.*')">
                        Kontoverbindung
                    </x-layouts.sidebar-link>

                    <x-layouts.sidebar-link href="{{ route('organizer.organization.edit') }}" icon='fas-building'
                        :active="request()->routeIs('organizer.organization.*')">
                        Veranstalter-Profil
                    </x-layouts.sidebar-link>

                    <x-layouts.sidebar-link href="{{ route('organizer.team.index') }}" icon='fas-users-gear'
                        :active="request()->routeIs('organizer.team.*')">
                        Team-Verwaltung
                    </x-layouts.sidebar-link>
                @endif
                @endauth

                @auth
                @if(auth()->user()->hasRole('admin'))
                    <!-- Admin Section -->
                    <li class="pt-6 pb-2">
                        <div :class="{ 'px-3': sidebarOpen, 'px-0 text-center': !sidebarOpen }">
                            <h3 x-show="sidebarOpen" class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Administration
                            </h3>
                            <div x-show="!sidebarOpen" class="h-px bg-gray-300 dark:bg-gray-600"></div>
                        </div>
                    </li>

                    <x-layouts.sidebar-link href="{{ route('admin.dashboard') }}" icon='fas-gauge'
                        :active="request()->routeIs('admin.dashboard')">
                        Admin Dashboard
                    </x-layouts.sidebar-link>

                    <x-layouts.sidebar-link href="{{ route('admin.users.index') }}" icon='fas-users'
                        :active="request()->routeIs('admin.users.*')">
                        Benutzerverwaltung
                    </x-layouts.sidebar-link>

                    <x-layouts.sidebar-link href="{{ route('admin.events.index') }}" icon='fas-calendar-check'
                        :active="request()->routeIs('admin.events.*')">
                        Event-Verwaltung
                    </x-layouts.sidebar-link>

                    <x-layouts.sidebar-link href="{{ route('admin.reviews.index') }}" icon='fas-star'
                        :active="request()->routeIs('admin.reviews.*')">
                        Bewertungen
                    </x-layouts.sidebar-link>

                    <x-layouts.sidebar-link href="{{ route('admin.categories.index') }}" icon='fas-tags'
                        :active="request()->routeIs('admin.categories.*')">
                        Kategorien
                    </x-layouts.sidebar-link>

                    <x-layouts.sidebar-link href="{{ route('admin.reporting.index') }}" icon='fas-chart-bar'
                        :active="request()->routeIs('admin.reporting.*')">
                        Analytics & Reports
                    </x-layouts.sidebar-link>

                    <x-layouts.sidebar-link href="{{ route('admin.audit-logs.index') }}" icon='fas-clipboard-list'
                        :active="request()->routeIs('admin.audit-logs.*')">
                        Audit Logs
                    </x-layouts.sidebar-link>

                    <x-layouts.sidebar-link href="{{ route('admin.invoices.index') }}" icon='fas-file-invoice-dollar'
                        :active="request()->routeIs('admin.invoices.*')">
                        Rechnungen
                    </x-layouts.sidebar-link>

                    <x-layouts.sidebar-link href="{{ route('admin.newsletter.index') }}" icon='fas-envelope'
                        :active="request()->routeIs('admin.newsletter.*')">
                        Newsletter
                    </x-layouts.sidebar-link>

                    <x-layouts.sidebar-link href="{{ route('admin.monetization.index') }}" icon='fas-coins'
                        :active="request()->routeIs('admin.monetization.*')">
                        Monetarisierung
                    </x-layouts.sidebar-link>


                @endif
                @endauth

                <!-- Hilfe & Support Section -->
                <li class="pt-6 pb-2">
                    <div :class="{ 'px-3': sidebarOpen, 'px-0 text-center': !sidebarOpen }">
                        <h3 x-show="sidebarOpen" class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Hilfe & Support
                        </h3>
                        <div x-show="!sidebarOpen" class="h-px bg-gray-300 dark:bg-gray-600"></div>
                    </div>
                </li>

                <x-layouts.sidebar-link href="{{ route('help.index') }}" icon='fas-circle-question'
                    :active="request()->routeIs('help.*')">
                    Hilfe & Anleitungen
                </x-layouts.sidebar-link>

                <!-- Datenschutz Section (für alle Benutzer) -->
                @auth
                <li class="pt-6 pb-2">
                    <div :class="{ 'px-3': sidebarOpen, 'px-0 text-center': !sidebarOpen }">
                        <h3 x-show="sidebarOpen" class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Datenschutz
                        </h3>
                        <div x-show="!sidebarOpen" class="h-px bg-gray-300 dark:bg-gray-600"></div>
                    </div>
                </li>

                <x-layouts.sidebar-link href="{{ route('data-privacy.index') }}" icon='fas-shield-halved'
                    :active="request()->routeIs('data-privacy.*')">
                    Meine Daten & DSGVO
                </x-layouts.sidebar-link>
                @endauth
            </ul>
        </nav>

        <!-- Footer: Settings & Appearance -->
        <div class="p-3 border-t border-gray-200 dark:border-gray-700 space-y-1">
            <!-- Appearance Toggle -->
            <div x-data="{ appearance: localStorage.getItem('appearance') || 'system' }"
                 class="flex items-center justify-between"
                 :class="{ 'px-2': sidebarOpen }">
                <span x-show="sidebarOpen" class="text-xs font-medium text-gray-700 dark:text-gray-300">Design</span>
                <div class="flex items-center space-x-1 bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                    <button @click="setAppearance('light'); appearance = 'light'"
                            :class="{ 'bg-white dark:bg-gray-600 shadow-sm': appearance === 'light' }"
                            class="p-1.5 rounded transition-all duration-200"
                            title="Hell">
                        <svg class="h-4 w-4 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </button>
                    <button @click="setAppearance('dark'); appearance = 'dark'"
                            :class="{ 'bg-white dark:bg-gray-600 shadow-sm': appearance === 'dark' }"
                            class="p-1.5 rounded transition-all duration-200"
                            title="Dunkel">
                        <svg class="h-4 w-4 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>
                    <button @click="setAppearance('system'); appearance = 'system'"
                            :class="{ 'bg-white dark:bg-gray-600 shadow-sm': appearance === 'system' }"
                            class="p-1.5 rounded transition-all duration-200"
                            title="System">
                        <svg class="h-4 w-4 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</aside>
