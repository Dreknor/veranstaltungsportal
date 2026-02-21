<x-layouts.app>
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Benutzerverwaltung</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Verwalten Sie alle Benutzer im System</p>
        </div>

        <!-- Search and Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 p-6">
            <form action="{{ route('admin.users.index') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Suche</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                               placeholder="Name oder E-Mail..."
                               class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rolle</label>
                        <select name="role" id="role"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Alle</option>
                            <option value="organizer" {{ request('role') === 'organizer' ? 'selected' : '' }}>Veranstalter</option>
                            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administratoren</option>
                        </select>
                    </div>
                    <div>
                        <label for="sso_provider" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">SSO-Provider</label>
                        <select name="sso_provider" id="sso_provider"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Alle</option>
                            <option value="none" {{ request('sso_provider') === 'none' ? 'selected' : '' }}>Nur normale Benutzer</option>
                            @foreach($ssoProviders ?? [] as $provider)
                                <option value="{{ $provider }}" {{ request('sso_provider') === $provider ? 'selected' : '' }}>
                                    {{ ucfirst($provider) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Filtern
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="ml-2 px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-400 dark:hover:bg-gray-500">
                            Zurücksetzen
                        </a>
                    </div>
                </div>
            </form>
        </div>

        @if (session('success'))
            <div class="mb-6 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-200 px-4 py-3 rounded relative">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-200 px-4 py-3 rounded relative">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Users Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Benutzer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Rolle</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Events</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Buchungen</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Registriert</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aktionen</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($users as $user)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold">
                                            {{ $user->initials() }}
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="flex items-center gap-2">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $user->name }}</div>
                                            @if($user->isSsoUser())
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200"
                                                      title="SSO-Benutzer via {{ $user->ssoProviderName() }}">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                                    </svg>
                                                    {{ $user->ssoProviderName() }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                                        <div class="text-xs mt-0.5">
                                            @if($user->hasVerifiedEmail())
                                                <span class="text-green-600 dark:text-green-400">✓ E-Mail verifiziert</span>
                                            @else
                                                <span class="text-amber-600 dark:text-amber-400">⚠ Nicht verifiziert</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="space-y-1">
                                    @if($user->roles->count() > 0)
                                        @foreach($user->roles as $role)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                {{ $role->name === 'admin' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}
                                                {{ $role->name === 'organizer' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                                {{ $role->name === 'moderator' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                                {{ $role->name === 'user' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                                {{ $role->name === 'viewer' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' : '' }}">
                                                {{ ucfirst($role->name) }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                            Keine Rolle
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $user->events_count }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $user->bookings_count }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $user->created_at->format('d.m.Y') }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                <div x-data="{
                                    open: false,
                                    top: 0, left: 0,
                                    btnRect: null,
                                    toggle(btn) {
                                        if (this.open) { this.open = false; return; }
                                        this.btnRect = btn.getBoundingClientRect();
                                        this.left = this.btnRect.right - 192;
                                        this.top = this.btnRect.bottom + 4;
                                        this.open = true;
                                        this.$nextTick(() => {
                                            const menu = document.querySelector('[data-menu-uid=\'{{ $user->id }}\']');
                                            if (!menu) return;
                                            const menuH = menu.offsetHeight;
                                            const spaceBelow = window.innerHeight - this.btnRect.bottom;
                                            if (spaceBelow < menuH + 8) {
                                                this.top = this.btnRect.top - menuH - 4;
                                            }
                                        });
                                    }
                                }" @click.outside="open = false" @keydown.escape.window="open = false">

                                    <button @click="toggle($el)"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-md bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 text-sm font-medium">
                                        Aktionen
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>

                                    <template x-teleport="body">
                                        <div x-show="open"
                                             x-transition:enter="transition ease-out duration-100"
                                             x-transition:enter-start="opacity-0 scale-95"
                                             x-transition:enter-end="opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-75"
                                             x-transition:leave-start="opacity-100 scale-100"
                                             x-transition:leave-end="opacity-0 scale-95"
                                             :style="`position:fixed; top:${top}px; left:${left}px; width:192px; z-index:9999;`"
                                             data-menu-uid="{{ $user->id }}"
                                             class="rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 dark:divide-gray-700"
                                             style="display:none;">

                                            {{-- Bearbeiten --}}
                                            <div class="py-1">
                                                <a href="{{ route('admin.users.edit', $user) }}"
                                                   class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                    ✏️ Bearbeiten
                                                </a>
                                            </div>

                                            @if(auth()->id() !== $user->id)
                                                <div class="py-1">
                                                    {{-- Verkörpern --}}
                                                    @if(!$user->hasRole('admin'))
                                                        <form action="{{ route('admin.users.impersonate', $user) }}" method="POST">
                                                            @csrf
                                                            <button type="submit"
                                                                    class="w-full flex items-center gap-2 px-4 py-2 text-sm text-indigo-700 dark:text-indigo-300 hover:bg-gray-100 dark:hover:bg-gray-700 text-left">
                                                                👤 Verkörpern
                                                            </button>
                                                        </form>
                                                    @endif

                                                    {{-- Organisator befördern / degradieren --}}
                                                    @if(!$user->hasRole('organizer') && !$user->hasRole('admin'))
                                                        <form action="{{ route('admin.users.promote-organizer', $user) }}" method="POST">
                                                            @csrf
                                                            <button type="submit"
                                                                    class="w-full flex items-center gap-2 px-4 py-2 text-sm text-green-700 dark:text-green-300 hover:bg-gray-100 dark:hover:bg-gray-700 text-left">
                                                                ↑ Zum Organisator
                                                            </button>
                                                        </form>
                                                    @elseif($user->hasRole('organizer') && !$user->hasRole('admin'))
                                                        <form action="{{ route('admin.users.demote-participant', $user) }}" method="POST"
                                                              onsubmit="return confirm('Organisator-Rechte wirklich entfernen?');">
                                                            @csrf
                                                            <button type="submit"
                                                                    class="w-full flex items-center gap-2 px-4 py-2 text-sm text-orange-700 dark:text-orange-300 hover:bg-gray-100 dark:hover:bg-gray-700 text-left">
                                                                ↓ Zum Teilnehmer
                                                            </button>
                                                        </form>
                                                    @endif

                                                    {{-- Admin-Rolle --}}
                                                    <form action="{{ route('admin.users.toggle-admin', $user) }}" method="POST">
                                                        @csrf
                                                        <button type="submit"
                                                                class="w-full flex items-center gap-2 px-4 py-2 text-sm text-purple-700 dark:text-purple-300 hover:bg-gray-100 dark:hover:bg-gray-700 text-left">
                                                            {{ $user->hasRole('admin') ? '🔓 Admin entfernen' : '🔒 Zu Admin' }}
                                                        </button>
                                                    </form>
                                                </div>

                                                {{-- Löschen --}}
                                                <div class="py-1">
                                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                                          onsubmit="return confirm('Benutzer wirklich löschen?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-700 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 text-left">
                                                            🗑️ Löschen
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                Keine Benutzer gefunden.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="mt-6">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
