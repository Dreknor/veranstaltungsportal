<x-layouts.app title="Neue Rolle erstellen">
    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <a href="{{ route('admin.roles.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                        ← Zurück zu Rollen
                    </a>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-1">Neue Rolle erstellen</h1>
                </div>
            </div>

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
                <form method="POST" action="{{ route('admin.roles.store') }}">
                    @csrf

                    <!-- Name -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Rollenname <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               placeholder="z.B. content-editor"
                               class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               required>
                        <p class="mt-1 text-xs text-gray-500">Nur Kleinbuchstaben, Zahlen und Bindestriche erlaubt</p>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Beschreibung -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Beschreibung</label>
                        <textarea name="description" rows="2"
                                  placeholder="Kurze Beschreibung der Rolle..."
                                  class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
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
                                           {{ old('color', '#6b7280') === $color ? 'checked' : '' }} class="sr-only color-radio">
                                    <span class="block w-8 h-8 rounded-full border-4 border-transparent transition-all hover:scale-110"
                                          style="background-color: {{ $color }};"></span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Permissions -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Berechtigungen zuweisen</h3>
                            <button type="button" id="selectAllBtn" class="text-sm text-blue-600 hover:text-blue-800">Alle wählen</button>
                        </div>

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
                                                   {{ is_array(old('permissions')) && in_array($permission->name, old('permissions')) ? 'checked' : '' }}
                                                   class="mt-0.5 rounded border-gray-300 text-blue-600 perm-checkbox perm-{{ Str::slug($group) }}">
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

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('admin.roles.index') }}"
                           class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-500">
                            Abbrechen
                        </a>
                        <button type="submit"
                                class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            Rolle erstellen
                        </button>
                    </div>
                </form>
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

    document.getElementById('selectAllBtn').addEventListener('click', function() {
        const all = document.querySelectorAll('.perm-checkbox');
        const allChecked = Array.from(all).every(c => c.checked);
        all.forEach(c => c.checked = !allChecked);
        this.textContent = allChecked ? 'Alle wählen' : 'Keine wählen';
    });

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

