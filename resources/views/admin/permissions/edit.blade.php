<x-layouts.app title="Permission bearbeiten: {{ $permission->name }}">
    <div class="py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <a href="{{ route('admin.permissions.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                        ← Zurück zu Permissions
                    </a>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-1">
                        Permission bearbeiten
                        @if($permission->is_system)
                            <span class="ml-2 text-sm bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 px-2 py-0.5 rounded-full font-medium align-middle">🔒 System</span>
                        @endif
                    </h1>
                </div>
                @if(!$permission->is_system)
                    <form action="{{ route('admin.permissions.destroy', $permission) }}" method="POST"
                          onsubmit="return confirm('Permission \'{{ $permission->name }}\' wirklich löschen?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm">
                            🗑 Löschen
                        </button>
                    </form>
                @endif
            </div>

            @if (session('success'))
                <div class="mb-4 bg-green-100 dark:bg-green-900 border border-green-400 text-green-700 dark:text-green-200 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 bg-red-100 dark:bg-red-900 border border-red-400 text-red-700 dark:text-red-200 px-4 py-3 rounded">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <form method="POST" action="{{ route('admin.permissions.update', $permission) }}">
                    @csrf
                    @method('PUT')

                    <!-- Name -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Permission-Name <span class="text-red-500">*</span>
                        </label>
                        @if($permission->is_system)
                            <input type="text" value="{{ $permission->name }}" disabled
                                   class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-gray-400 shadow-sm bg-gray-100 cursor-not-allowed">
                            <input type="hidden" name="name" value="{{ $permission->name }}">
                            <p class="mt-1 text-xs text-gray-500">🔒 System-Permission – Name nicht änderbar</p>
                        @else
                            <input type="text" name="name" value="{{ old('name', $permission->name) }}"
                                   class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    <!-- Gruppe -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Gruppe <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="group" id="group-input" value="{{ old('group', $permission->group) }}"
                               list="group-suggestions"
                               class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               required>
                        <datalist id="group-suggestions">
                            @foreach($groups as $g)
                                <option value="{{ $g }}">
                            @endforeach
                        </datalist>
                        @error('group')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Vorhandene Gruppen als Quick-Auswahl -->
                    @if($groups->isNotEmpty())
                        <div class="mb-4">
                            <p class="text-xs text-gray-500 mb-2">Vorhandene Gruppen:</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($groups as $g)
                                    <button type="button"
                                            class="px-3 py-1 rounded-full text-xs {{ $g === $permission->group ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }} hover:bg-blue-100 hover:text-blue-700 group-quick-btn"
                                            data-group="{{ $g }}">
                                        {{ $g }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Beschreibung -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Beschreibung</label>
                        <textarea name="description" rows="2"
                                  class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $permission->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Aktuelle Rollenzuweisungen -->
                    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Zugewiesen an Rollen:</p>
                        <div class="flex flex-wrap gap-2">
                            @forelse($permission->roles as $role)
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium"
                                      style="background-color: {{ $role->color ?? '#6b7280' }}20; color: {{ $role->color ?? '#6b7280' }}; border: 1px solid {{ $role->color ?? '#6b7280' }}40;">
                                    {{ ucfirst($role->name) }}
                                </span>
                            @empty
                                <span class="text-sm text-gray-400 italic">Keiner Rolle zugewiesen</span>
                            @endforelse
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('admin.permissions.index') }}"
                           class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-500">
                            Abbrechen
                        </a>
                        <button type="submit"
                                class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Speichern
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.querySelectorAll('.group-quick-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('group-input').value = this.dataset.group;
        });
    });
    </script>
</x-layouts.app>

