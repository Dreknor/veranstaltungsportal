<x-layouts.app title="Rolle bearbeiten: {{ ucfirst($role->name) }}">
    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('admin.roles.index') }}" class="text-blue-600 hover:text-blue-800">
                    ← Zurück zu Rollen
                </a>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">
                    Rolle bearbeiten: <span class="text-blue-600">{{ ucfirst($role->name) }}</span>
                </h1>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <form method="POST" action="{{ route('admin.roles.update', $role) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            Berechtigungen für {{ ucfirst($role->name) }}
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Wählen Sie die Berechtigungen aus, die dieser Rolle zugewiesen werden sollen.
                        </p>
                    </div>

                    @foreach($permissions as $group => $groupPermissions)
                        <div class="mb-8 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-3 capitalize">
                                {{ $group }}
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($groupPermissions as $permission)
                                    <label class="flex items-center p-2 rounded hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                                        <input type="checkbox"
                                               name="permissions[]"
                                               value="{{ $permission->name }}"
                                               {{ in_array($permission->name, $rolePermissions) ? 'checked' : '' }}
                                               class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                                            {{ $permission->name }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    <div class="flex justify-between items-center pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            <strong>{{ count($rolePermissions) }}</strong> von <strong>{{ $permissions->flatten()->count() }}</strong> Berechtigungen ausgewählt
                        </div>
                        <div class="flex gap-3">
                            <a href="{{ route('admin.roles.index') }}"
                               class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-400 dark:hover:bg-gray-500">
                                Abbrechen
                            </a>
                            <button type="submit"
                                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                Speichern
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Role Statistics -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Statistiken</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-gray-600 dark:text-gray-400">Benutzer mit dieser Rolle:</dt>
                            <dd class="font-semibold text-gray-900 dark:text-gray-100">{{ $role->users()->count() }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600 dark:text-gray-400">Berechtigungen:</dt>
                            <dd class="font-semibold text-gray-900 dark:text-gray-100">{{ $role->permissions->count() }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600 dark:text-gray-400">Erstellt am:</dt>
                            <dd class="font-semibold text-gray-900 dark:text-gray-100">{{ $role->created_at->format('d.m.Y') }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Aktuelle Berechtigungen</h3>
                    <div class="flex flex-wrap gap-2">
                        @forelse($role->permissions as $permission)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                {{ $permission->name }}
                            </span>
                        @empty
                            <p class="text-gray-500 dark:text-gray-400 text-sm">Keine Berechtigungen zugewiesen</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

