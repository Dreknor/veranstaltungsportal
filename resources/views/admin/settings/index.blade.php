<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                System-Einstellungen
            </h2>
            <form method="POST" action="{{ route('admin.settings.initialize') }}">
                @csrf
                <button type="submit" class="rounded-md bg-gray-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500">
                    Standard-Einstellungen initialisieren
                </button>
            </form>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 rounded-md bg-green-50 p-4 dark:bg-green-900/20">
                    <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
                </div>
            @endif

            <!-- Group Navigation -->
            <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="flex space-x-8 px-6" aria-label="Tabs">
                        @foreach($groups as $g)
                            <a href="{{ route('admin.settings.index', ['group' => $g]) }}"
                               class="border-b-2 px-1 py-4 text-sm font-medium {{ $group === $g ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' }}">
                                {{ ucfirst($g) }}
                            </a>
                        @endforeach
                    </nav>
                </div>
            </div>

            <!-- Settings Form -->
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                <div class="p-6">
                    @if($settings->isEmpty())
                        <div class="text-center py-12">
                            <p class="text-gray-500 dark:text-gray-400">Keine Einstellungen in dieser Gruppe vorhanden.</p>
                        </div>
                    @else
                        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
                            @csrf
                            @method('PUT')

                            @foreach($settings as $setting)
                                <div class="border-b border-gray-200 pb-6 last:border-b-0 dark:border-gray-700">
                                    <label for="setting_{{ $setting->key }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ $setting->label }}
                                    </label>

                                    @if($setting->description)
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $setting->description }}</p>
                                    @endif

                                    <div class="mt-2">
                                        @if($setting->type === 'boolean')
                                            <div class="flex items-center">
                                                <input type="checkbox"
                                                       name="settings[{{ $setting->key }}]"
                                                       id="setting_{{ $setting->key }}"
                                                       value="1"
                                                       {{ $setting->getTypedValue() ? 'checked' : '' }}
                                                       class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700">
                                                <label for="setting_{{ $setting->key }}" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                                    Aktiviert
                                                </label>
                                            </div>
                                        @elseif($setting->type === 'integer')
                                            <input type="number"
                                                   name="settings[{{ $setting->key }}]"
                                                   id="setting_{{ $setting->key }}"
                                                   value="{{ $setting->getTypedValue() }}"
                                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 sm:w-64">
                                        @elseif($setting->key === 'primary_color')
                                            <div class="flex gap-2">
                                                <input type="color"
                                                       name="settings[{{ $setting->key }}]"
                                                       id="setting_{{ $setting->key }}"
                                                       value="{{ $setting->value }}"
                                                       class="h-10 w-20 rounded border-gray-300 dark:border-gray-600">
                                                <input type="text"
                                                       value="{{ $setting->value }}"
                                                       readonly
                                                       class="block w-32 rounded-md border-gray-300 bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                            </div>
                                        @else
                                            <input type="text"
                                                   name="settings[{{ $setting->key }}]"
                                                   id="setting_{{ $setting->key }}"
                                                   value="{{ $setting->value }}"
                                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                        @endif
                                    </div>

                                    <div class="mt-2 flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                                        <span>Schlüssel: <code class="rounded bg-gray-100 px-1 dark:bg-gray-700">{{ $setting->key }}</code></span>
                                        <span>Typ: {{ $setting->type }}</span>
                                        @if($setting->is_public)
                                            <span class="text-green-600 dark:text-green-400">Öffentlich</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                            <div class="flex justify-end pt-4">
                                <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                                    Einstellungen speichern
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

