<x-layouts.app title="Berechtigungen verwalten">
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <a href="{{ route('admin.roles.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                        ← Zurück zu Rollen
                    </a>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-1">
                        Berechtigungen verwalten
                        <span class="text-lg font-normal text-gray-500">({{ $permissions->flatten()->count() }} gesamt)</span>
                    </h1>
                </div>
                <a href="{{ route('admin.permissions.create') }}"
                   class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium">
                    + Neue Permission
                </a>
            </div>

            @if (session('success'))
                <div class="mb-4 bg-green-100 dark:bg-green-900 border border-green-400 text-green-700 dark:text-green-200 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 dark:bg-red-900 border border-red-400 text-red-700 dark:text-red-200 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Gruppen-Filter -->
            <div class="mb-6 flex flex-wrap gap-2">
                <button type="button" class="group-filter-btn active px-3 py-1 rounded-full text-sm font-medium bg-blue-600 text-white" data-group="all">
                    Alle ({{ $permissions->flatten()->count() }})
                </button>
                @foreach($groups as $group)
                    <button type="button"
                            class="group-filter-btn px-3 py-1 rounded-full text-sm font-medium bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300"
                            data-group="{{ $group }}">
                        {{ ucfirst($group) }} ({{ $permissions->get($group)?->count() ?? 0 }})
                    </button>
                @endforeach
            </div>

            @foreach($permissions as $group => $groupPermissions)
                <div class="group-section mb-6" data-group="{{ $group }}">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                        <div class="px-6 py-3 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 flex items-center justify-between">
                            <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100 uppercase tracking-wider">
                                📁 {{ ucfirst($group) }}
                                <span class="ml-1 text-sm font-normal text-gray-500 normal-case">({{ $groupPermissions->count() }})</span>
                            </h2>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Beschreibung</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rollen</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">System</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aktionen</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($groupPermissions as $permission)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                                            <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-gray-100">
                                                {{ $permission->name }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                                {{ $permission->description ?? '—' }}
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach($permission->roles->take(3) as $role)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                                                              style="background-color: {{ $role->color ?? '#6b7280' }}20; color: {{ $role->color ?? '#6b7280' }};">
                                                            {{ $role->name }}
                                                        </span>
                                                    @endforeach
                                                    @if($permission->roles->count() > 3)
                                                        <span class="text-xs text-gray-400">+{{ $permission->roles->count() - 3 }} mehr</span>
                                                    @endif
                                                    @if($permission->roles->isEmpty())
                                                        <span class="text-xs text-gray-400 italic">keine</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-center text-sm">
                                                {{ $permission->is_system ? '🔒' : '—' }}
                                            </td>
                                            <td class="px-4 py-3 text-right">
                                                <div class="flex justify-end gap-2">
                                                    <a href="{{ route('admin.permissions.edit', $permission) }}"
                                                       class="px-3 py-1 bg-blue-600 text-white rounded text-xs hover:bg-blue-700">
                                                        Bearbeiten
                                                    </a>
                                                    @if(!$permission->is_system)
                                                        <form action="{{ route('admin.permissions.destroy', $permission) }}" method="POST"
                                                              onsubmit="return confirm('Permission \'{{ $permission->name }}\' wirklich löschen? Sie ist {{ $permission->roles->count() }} Rolle(n) zugewiesen.')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    class="px-3 py-1 bg-red-600 text-white rounded text-xs hover:bg-red-700">
                                                                Löschen
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="px-3 py-1 bg-gray-200 dark:bg-gray-700 text-gray-400 rounded text-xs cursor-not-allowed" title="System-Permission – nicht löschbar">
                                                            🔒
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
    document.querySelectorAll('.group-filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const group = this.dataset.group;

            // Aktiven Button hervorheben
            document.querySelectorAll('.group-filter-btn').forEach(b => {
                b.classList.remove('bg-blue-600', 'text-white');
                b.classList.add('bg-gray-200', 'dark:bg-gray-700', 'text-gray-700');
            });
            this.classList.add('bg-blue-600', 'text-white');
            this.classList.remove('bg-gray-200', 'dark:bg-gray-700', 'text-gray-700');

            // Gruppen-Sektionen filtern
            document.querySelectorAll('.group-section').forEach(section => {
                if (group === 'all' || section.dataset.group === group) {
                    section.style.display = '';
                } else {
                    section.style.display = 'none';
                }
            });
        });
    });
    </script>
</x-layouts.app>

