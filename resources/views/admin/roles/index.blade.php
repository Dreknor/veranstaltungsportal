<x-layouts.app title="Rollen & Berechtigungen">
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Rollen & Berechtigungen</h1>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.roles.matrix') }}"
                       class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 text-sm font-medium">
                        📊 Matrix-Ansicht
                    </a>
                    <a href="{{ route('admin.permissions.index') }}"
                       class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 text-sm font-medium">
                        🔑 Permissions verwalten
                    </a>
                    <a href="{{ route('admin.roles.create') }}"
                       class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium">
                        + Neue Rolle
                    </a>
                    <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                        ← Dashboard
                    </a>
                </div>
            </div>

            @if (session('success'))
                <div class="mb-6 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-200 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-200 px-4 py-3 rounded relative">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Roles Overview -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden mb-8">
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                        Rollen <span class="text-sm font-normal text-gray-500">({{ $roles->count() }} gesamt)</span>
                    </h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                    @foreach($roles as $role)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6 hover:shadow-lg transition relative">

                            @if($role->is_system)
                                <span class="absolute top-3 right-3 text-xs bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 px-2 py-0.5 rounded-full font-medium">
                                    🔒 System
                                </span>
                            @endif

                            <div class="flex justify-between items-start mb-3 pr-16">
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full inline-block flex-shrink-0"
                                          style="background-color: {{ $role->color ?? '#6b7280' }};"></span>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                        {{ ucfirst($role->name) }}
                                    </h3>
                                </div>
                            </div>

                            @if($role->description)
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">{{ $role->description }}</p>
                            @endif

                            <div class="flex gap-4 text-sm text-gray-600 dark:text-gray-400 mb-3">
                                <span>👥 {{ $role->users_count }} Benutzer</span>
                                <span>🔑 {{ $role->permissions->count() }} Permissions</span>
                            </div>

                            <div class="mb-4">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($role->permissions->take(4) as $permission)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                            {{ $permission->name }}
                                        </span>
                                    @endforeach
                                    @if($role->permissions->count() > 4)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                                            +{{ $role->permissions->count() - 4 }} mehr
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                                <a href="{{ route('admin.roles.edit', $role) }}"
                                   class="flex-1 text-center px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm transition">
                                    Bearbeiten
                                </a>
                                @if(!$role->is_system)
                                    <form action="{{ route('admin.roles.destroy', $role) }}" method="POST"
                                          onsubmit="return confirm('Rolle \'{{ $role->name }}\' wirklich löschen? {{ $role->users_count }} Benutzer werden auf \'user\' zurückgesetzt.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm transition">
                                            Löschen
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Quick Permissions Overview -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                            Berechtigungen
                            <span class="text-sm font-normal text-gray-500">({{ $permissions->flatten()->count() }} gesamt)</span>
                        </h2>
                        <a href="{{ route('admin.permissions.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                            Alle verwalten →
                        </a>
                    </div>
                </div>
                <div class="p-6">
                    @foreach($permissions as $group => $groupPermissions)
                        <div class="mb-6 last:mb-0">
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 uppercase tracking-wider">
                                {{ $group }}
                                <span class="ml-1 text-xs text-gray-400 font-normal normal-case">({{ $groupPermissions->count() }})</span>
                            </h3>
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                                @foreach($groupPermissions as $permission)
                                    <div class="flex items-center gap-2 px-3 py-2 bg-gray-50 dark:bg-gray-700 rounded-md">
                                        @if($permission->is_system)
                                            <span class="text-yellow-500 text-xs">🔒</span>
                                        @endif
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $permission->name }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

