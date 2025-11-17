<x-layouts.app title="Team-Mitglieder importieren">
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Team-Mitglieder importieren</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Importieren Sie mehrere Mitglieder auf einmal per CSV</p>
        </div>
        <a href="{{ route('organizer.team.index') }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium rounded-lg transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Zurück zum Team
        </a>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-4 rounded">
            <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
            @if(session('import_errors') && count(session('import_errors')) > 0)
                <details class="mt-3">
                    <summary class="cursor-pointer text-sm text-green-700 dark:text-green-300 hover:text-green-900 dark:hover:text-green-100">
                        Fehler anzeigen ({{ count(session('import_errors')) }})
                    </summary>
                    <ul class="mt-2 text-sm list-disc list-inside space-y-1 text-green-700 dark:text-green-300">
                        @foreach(session('import_errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </details>
            @endif
        </div>
    @endif

    <!-- Anleitung -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">📋 Anleitung</h2>
        <ol class="list-decimal list-inside space-y-2 text-gray-700 dark:text-gray-300">
            <li>Laden Sie die CSV-Vorlage herunter</li>
            <li>Tragen Sie die E-Mail-Adressen und Rollen der Mitglieder ein</li>
            <li>Speichern Sie die Datei im CSV-Format</li>
            <li>Laden Sie die Datei hier hoch</li>
        </ol>

        <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
            <p class="text-sm font-medium text-blue-900 dark:text-blue-100 mb-2">Wichtig:</p>
            <ul class="text-sm text-blue-800 dark:text-blue-200 list-disc list-inside space-y-1">
                <li>Die Benutzer müssen bereits registriert sein</li>
                <li>Rollen können sein: <code class="bg-blue-100 dark:bg-blue-900 px-1.5 py-0.5 rounded">admin</code> oder <code class="bg-blue-100 dark:bg-blue-900 px-1.5 py-0.5 rounded">member</code></li>
                <li>Bereits vorhandene Mitglieder werden übersprungen</li>
                <li>Alle importierten Mitglieder erhalten eine E-Mail-Benachrichtigung</li>
            </ul>
        </div>

        <a href="{{ route('organizer.team.import.template') }}"
           class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors mt-6">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            CSV-Vorlage herunterladen
        </a>
    </div>

    <!-- Upload Form -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">CSV-Datei hochladen</h2>

        <form method="POST" action="{{ route('organizer.team.import.process') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    CSV-Datei <span class="text-red-500">*</span>
                </label>
                <input type="file" name="csv_file" accept=".csv,.txt" required
                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-100">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Format: CSV mit Komma als Trennzeichen, max. 2MB</p>
                @error('csv_file')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Standard-Rolle (falls nicht in CSV angegeben)
                </label>
                <select name="default_role" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-100">
                    <option value="member" selected>Member (eingeschränkte Berechtigungen)</option>
                    <option value="admin">Admin (volle Berechtigungen)</option>
                </select>
                @error('default_role')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4">
                <a href="{{ route('organizer.team.index') }}"
                   class="w-full sm:w-auto px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-center">
                    Abbrechen
                </a>
                <button type="submit"
                        class="w-full sm:w-auto px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    Importieren
                </button>
            </div>
        </form>
    </div>

    <!-- Beispiel -->
    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg shadow-sm p-6">
        <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">💡 Beispiel CSV-Format:</h3>
        <pre class="bg-white dark:bg-gray-800 p-4 rounded border border-gray-200 dark:border-gray-700 text-sm overflow-x-auto"><code>email,role
max@example.com,member
sarah@example.com,admin
tim@example.com,member</code></pre>
    </div>
</div>
</x-layouts.app>

