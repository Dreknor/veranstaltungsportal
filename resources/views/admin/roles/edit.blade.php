<x-layouts.app title="Rolle bearbeiten: {{ ucfirst($role->name) }}">
    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <a href="{{ route('admin.roles.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                        ← Zurück zu Rollen
                    </a>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-1">
                        Rolle bearbeiten:
                        <span style="color: {{ $role->color ?? '#6b7280' }}">{{ ucfirst($role->name) }}</span>
                        @if($role->is_system)
                            <span class="ml-2 text-sm bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 px-2 py-0.5 rounded-full font-medium align-middle">🔒 System-Rolle</span>
                        @endif
                    </h1>
                </div>
                @if(!$role->is_system)
                    <form action="{{ route('admin.roles.destroy', $role) }}" method="POST"
                          onsubmit="return confirm('Rolle \'{{ $role->name }}\' wirklich löschen? {{ $role->users()->count() }} Benutzer werden auf \'user\' zurückgesetzt.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm">
                            🗑 Rolle löschen
                        </button>
                    </form>
                @endif
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

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <form method="POST" action="{{ route('admin.roles.update', $role) }}">
                    @csrf
                    @method('PUT')

                    <!-- Name -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Rollenname <span class="text-red-500">*</span>
                        </label>
                        @if($role->is_system)
                            <input type="text" value="{{ $role->name }}" disabled
                                   class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-gray-400 shadow-sm bg-gray-100 cursor-not-allowed">
                            <input type="hidden" name="name" value="{{ $role->name }}">
                            <p class="mt-1 text-xs text-gray-500">🔒 System-Rolle – Name nicht änderbar</p>
                        @else
                            <input type="text" name="name" value="{{ old('name', $role->name) }}"
                                   class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   required>
                            <p class="mt-1 text-xs text-gray-500">Nur Kleinbuchstaben, Zahlen und Bindestriche</p>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    <!-- Beschreibung -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Beschreibung</label>
                        <textarea name="description" rows="2"
                                  class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $role->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Farbe -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Badge-Farbe</label>
                        <div class="flex gap-3 flex-wrap">
                            @foreach(['#6b7280','#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#ec4899','#06b6d4','#f97316'] as $color)
                                <label class="cursor-pointer">
                                    <input type="radio" name="color" value="{{ $color }}"
                                           {{ old('color', $role->color ?? '#6b7280') === $color ? 'checked' : '' }} class="sr-only color-radio">
                                    <span class="block w-8 h-8 rounded-full border-4 border-transparent transition-all hover:scale-110"
                                          style="background-color: {{ $color }};"></span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    @if($role->name !== 'admin')
                    <!-- Permissions -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Berechtigungen</h3>
                        @foreach($permissions as $group => $groupPermissions)
                            <div class="mb-4 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-medium text-gray-900 dark:text-gray-100 capitalize">{{ $group }}</h4>
                                    <button type="button"
                                            class="text-xs text-blue-600 hover:text-blue-800 select-group-btn"
                                            data-group="{{ $group }}">Alle wählen</button>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                    @foreach($groupPermissions as $permission)
                                        <label class="flex items-start gap-2 p-2 rounded hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                                   {{ in_array($permission->name, $rolePermissions) ? 'checked' : '' }}
                                                   class="mt-0.5 rounded border-gray-300 text-blue-600 perm-{{ Str::slug($group) }}">
                                            <div>
                                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $permission->name }}</span>
                                                @if($permission->description)
                                                    <p class="text-xs text-gray-500">{{ $permission->description }}</p>
                                                @endif
                                                @if($permission->is_system)
                                                    <span class="text-xs text-yellow-600">🔒 System</span>
                                                @endif
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @else
                        <div class="mb-6 p-4 bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700 rounded-lg">
                            <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                🔒 Die <strong>Admin-Rolle</strong> erhält automatisch alle Berechtigungen und kann nicht eingeschränkt werden.
                            </p>
                        </div>
                    @endif

                    <div class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            <strong>{{ count($rolePermissions) }}</strong> von <strong>{{ $permissions->flatten()->count() }}</strong> Berechtigungen ausgewählt
                        </div>
                        <div class="flex gap-3">
                            <a href="{{ route('admin.roles.index') }}"
                               class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-500">
                                Abbrechen
                            </a>
                            <button type="submit"
                                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Speichern
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Statistiken -->
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
                            <dt class="text-gray-600 dark:text-gray-400">System-Rolle:</dt>
                            <dd class="font-semibold">{{ $role->is_system ? '✅ Ja' : '❌ Nein' }}</dd>
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

    <script>
    document.querySelectorAll('.select-group-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const group = this.dataset.group;
            const slug = group.replace(/[^a-z0-9]/gi, '-').toLowerCase();
            const checkboxes = document.querySelectorAll('.perm-' + slug);
            const allChecked = Array.from(checkboxes).every(c => c.checked);
            checkboxes.forEach(c => c.checked = !allChecked);
            this.textContent = allChecked ? 'Alle wählen' : 'Keine wählen';
        });
    });

    // Farb-Auswahl visuell hervorheben
    document.querySelectorAll('.color-radio').forEach(radio => {
        const span = radio.nextElementSibling;
        if (radio.checked) span.style.outline = '2px solid #000';
        radio.addEventListener('change', function() {
            document.querySelectorAll('.color-radio').forEach(r => {
                r.nextElementSibling.style.outline = '';
            });
            if (this.checked) this.nextElementSibling.style.outline = '2px solid #000';
        });
    });
    </script>
</x-layouts.app>

