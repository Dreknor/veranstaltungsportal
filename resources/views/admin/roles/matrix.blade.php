<x-layouts.app title="Berechtigungs-Matrix">
    <div class="py-12">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <a href="{{ route('admin.roles.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                        ← Zurück zu Rollen
                    </a>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-1">Berechtigungs-Matrix</h1>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.roles.create') }}"
                       class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm">
                        + Neue Rolle
                    </a>
                    <a href="{{ route('admin.permissions.create') }}"
                       class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                        + Neue Permission
                    </a>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-56 sticky left-0 bg-gray-50 dark:bg-gray-700 z-10">
                                    Permission
                                </th>
                                @foreach($roles as $role)
                                    <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider min-w-[100px]">
                                        <div class="flex flex-col items-center gap-1">
                                            <span class="w-3 h-3 rounded-full"
                                                  style="background-color: {{ $role->color ?? '#6b7280' }};"></span>
                                            <span style="color: {{ $role->color ?? '#6b7280' }}">
                                                {{ ucfirst($role->name) }}
                                            </span>
                                            @if($role->is_system)
                                                <span class="text-yellow-500 text-xs">🔒</span>
                                            @endif
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($permissions as $group => $groupPermissions)
                                {{-- Gruppen-Trennzeile --}}
                                <tr class="bg-gray-100 dark:bg-gray-700">
                                    <td colspan="{{ $roles->count() + 1 }}"
                                        class="px-4 py-2 text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-widest">
                                        📁 {{ $group }}
                                        <span class="font-normal text-gray-500 ml-1">({{ $groupPermissions->count() }})</span>
                                    </td>
                                </tr>

                                @foreach($groupPermissions as $permission)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                                        <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 sticky left-0 bg-white dark:bg-gray-800 font-mono">
                                            <div>
                                                <span class="font-medium">{{ $permission->name }}</span>
                                                @if($permission->description)
                                                    <p class="text-xs text-gray-400 font-sans">{{ $permission->description }}</p>
                                                @endif
                                                @if($permission->is_system)
                                                    <span class="text-xs text-yellow-600 font-sans">🔒</span>
                                                @endif
                                            </div>
                                        </td>
                                        @foreach($roles as $role)
                                            <td class="px-4 py-2 text-center">
                                                @if($role->hasPermissionTo($permission->name))
                                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-100 dark:bg-green-900">
                                                        <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                        </svg>
                                                    </span>
                                                @else
                                                    <span class="text-gray-300 dark:text-gray-600 text-lg">—</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Legende -->
            <div class="mt-4 flex items-center gap-6 text-sm text-gray-600 dark:text-gray-400">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-green-100">
                        <svg class="w-3 h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </span>
                    <span>Berechtigung vorhanden</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-gray-300 text-lg">—</span>
                    <span>Keine Berechtigung</span>
                </div>
                <div class="flex items-center gap-2">
                    <span>🔒</span>
                    <span>System-geschützt</span>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

