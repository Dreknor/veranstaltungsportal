<x-layouts.app title="Rollen & Berechtigungen">
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Rollen & Berechtigungen</h1>
                <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-800">
                    ← Zurück zum Dashboard
                </a>
            </div>

            @if (session('success'))
                <div class="mb-6 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-200 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Roles Overview -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden mb-8">
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Rollen</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                    @foreach($roles as $role)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6 hover:shadow-lg transition">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                        {{ ucfirst($role->name) }}
                                    </h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $role->users_count }} {{ $role->users_count == 1 ? 'Benutzer' : 'Benutzer' }}
                                    </p>
                                </div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                    {{ $role->name === 'admin' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}
                                    {{ $role->name === 'organizer' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                    {{ $role->name === 'moderator' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                    {{ $role->name === 'user' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                    {{ $role->name === 'viewer' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' : '' }}">
                                    {{ $role->permissions->count() }} Berechtigungen
                                </span>
                            </div>

                            <div class="mb-4">
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Berechtigungen:</p>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($role->permissions->take(5) as $permission)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                            {{ $permission->name }}
                                        </span>
                                    @endforeach
                                    @if($role->permissions->count() > 5)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                            +{{ $role->permissions->count() - 5 }} mehr
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <a href="{{ route('admin.roles.edit', $role) }}"
                               class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                                Berechtigungen verwalten
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Permissions Overview -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Berechtigungen</h2>
                        <a href="{{ route('admin.roles.permissions') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                            Alle anzeigen →
                        </a>
                    </div>
                </div>
                <div class="p-6">
                    @foreach($permissions as $group => $groupPermissions)
                        <div class="mb-6 last:mb-0">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3 capitalize">
                                {{ $group }}
                            </h3>
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                                @foreach($groupPermissions as $permission)
                                    <div class="flex items-center px-3 py-2 bg-gray-50 dark:bg-gray-700 rounded-md">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">
                                            {{ $permission->name }}
                                        </span>
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

