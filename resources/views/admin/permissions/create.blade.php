<x-layouts.app title="Neue Permission erstellen">
    <div class="py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('admin.permissions.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                    ← Zurück zu Permissions
                </a>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-1">Neue Permission erstellen</h1>
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
                <form method="POST" action="{{ route('admin.permissions.store') }}">
                    @csrf

                    <!-- Name -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Permission-Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               placeholder="z.B. manage invoices"
                               class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               required>
                        <p class="mt-1 text-xs text-gray-500">
                            Nur Kleinbuchstaben, Zahlen, Leerzeichen und Bindestriche. Konvention: <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">verb noun</code> (z.B. "view events")
                        </p>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Gruppe -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Gruppe <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-2">
                            <input type="text" name="group" id="group-input" value="{{ old('group') }}"
                                   placeholder="z.B. invoices"
                                   list="group-suggestions"
                                   class="flex-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   required>
                            <datalist id="group-suggestions">
                                @foreach($groups as $g)
                                    <option value="{{ $g }}">
                                @endforeach
                            </datalist>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Bestehende Gruppe auswählen oder neue eingeben</p>
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
                                            class="px-3 py-1 rounded-full text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-blue-100 hover:text-blue-700 group-quick-btn"
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
                                  placeholder="Was erlaubt diese Berechtigung?"
                                  class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg mb-6">
                        <p class="text-sm text-blue-800 dark:text-blue-200">
                            ℹ️ Die neue Permission wird automatisch der <strong>Admin-Rolle</strong> zugewiesen.
                        </p>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('admin.permissions.index') }}"
                           class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-500">
                            Abbrechen
                        </a>
                        <button type="submit"
                                class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            Permission erstellen
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

