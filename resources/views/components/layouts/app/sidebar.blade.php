            <aside :class="{ 'w-full md:w-64': sidebarOpen, 'w-0 md:w-16 hidden md:block': !sidebarOpen }"
                class="bg-sidebar text-sidebar-foreground border-r border-gray-200 dark:border-gray-700 sidebar-transition overflow-hidden">
                <!-- Sidebar Content -->
                <div class="h-full flex flex-col">
                    <!-- Sidebar Menu -->
                    <nav class="flex-1 overflow-y-auto custom-scrollbar py-4">
                        <ul class="space-y-1 px-2">
                            <!-- Dashboard -->
                            <x-layouts.sidebar-link href="{{ route('dashboard') }}" icon='fas-house'
                                :active="request()->routeIs('dashboard')">Dashboard</x-layouts.sidebar-link>

                            <!-- My Events -->
                            <x-layouts.sidebar-link href="{{ route('events.index') }}" icon='fas-calendar'
                                :active="request()->routeIs('events.index', 'events.show')">Alle Events</x-layouts.sidebar-link>

                            <!-- My Bookings -->
                            <x-layouts.sidebar-link href="{{ route('user.bookings') }}" icon='fas-ticket'
                                :active="request()->routeIs('user.bookings')">Meine Buchungen</x-layouts.sidebar-link>

                            <!-- Favorites -->
                            <x-layouts.sidebar-link href="{{ route('favorites.index') }}" icon='fas-heart'
                                :active="request()->routeIs('favorites.index')">Favoriten</x-layouts.sidebar-link>

                            <!-- Notifications -->
                            <x-layouts.sidebar-link href="{{ route('notifications.index') }}" icon='fas-bell'
                                :active="request()->routeIs('notifications.*')">Benachrichtigungen</x-layouts.sidebar-link>

                            @if(auth()->user()->is_organizer)
                                <!-- Organizer Section -->
                                <li class="pt-4 pb-2 px-2">
                                    <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Veranstalter
                                    </h3>
                                </li>

                                <x-layouts.sidebar-link href="{{ route('organizer.dashboard') }}" icon='fas-chart-line'
                                    :active="request()->routeIs('organizer.dashboard')">Veranstalter Dashboard</x-layouts.sidebar-link>

                                <x-layouts.sidebar-link href="{{ route('organizer.events.index') }}" icon='fas-calendar-days'
                                    :active="request()->routeIs('organizer.events.*')">Meine Events</x-layouts.sidebar-link>

                                <x-layouts.sidebar-link href="{{ route('organizer.bookings.index') }}" icon='fas-receipt'
                                    :active="request()->routeIs('organizer.bookings.*')">Event Buchungen</x-layouts.sidebar-link>
                            @endif

                            @if(auth()->user()->is_admin)
                                <!-- Admin Section -->
                                <li class="pt-4 pb-2 px-2">
                                    <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Administration
                                    </h3>
                                </li>

                                <x-layouts.sidebar-link href="{{ route('admin.dashboard') }}" icon='fas-gauge'
                                    :active="request()->routeIs('admin.dashboard')">Admin Dashboard</x-layouts.sidebar-link>

                                <x-layouts.sidebar-link href="{{ route('admin.users.index') }}" icon='fas-users'
                                    :active="request()->routeIs('admin.users.*')">Benutzerverwaltung</x-layouts.sidebar-link>

                                <x-layouts.sidebar-link href="{{ route('admin.events.index') }}" icon='fas-calendar-check'
                                    :active="request()->routeIs('admin.events.*')">Event-Verwaltung</x-layouts.sidebar-link>
                            @endif
                        </ul>
                    </nav>
                </div>
            </aside>
