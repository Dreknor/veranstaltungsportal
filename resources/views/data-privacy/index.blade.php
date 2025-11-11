<x-layouts.app>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Datenschutz & DSGVO') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Data Export -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 text-4xl mr-4">📥</div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold mb-2">Datenexport (DSGVO Art. 15)</h3>
                            <p class="text-gray-600 mb-4">
                                Laden Sie eine vollständige Kopie Ihrer persönlichen Daten herunter. Dies umfasst alle Ihre Buchungen,
                                erstellten Events, Bewertungen, Favoriten, Badges, Verbindungen und Benachrichtigungen.
                            </p>
                            <a href="{{ route('data-privacy.export') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                Daten als JSON exportieren
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Download Files -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 text-4xl mr-4">📁</div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold mb-2">Dateien herunterladen</h3>
                            <p class="text-gray-600 mb-4">
                                Laden Sie alle Ihre hochgeladenen Dateien herunter, einschließlich Profilbild,
                                Teilnahmezertifikate und andere Dokumente als ZIP-Archiv.
                            </p>
                            <a href="{{ route('data-privacy.download-files') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                Alle Dateien herunterladen (ZIP)
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Privacy Settings -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 text-4xl mr-4">⚙️</div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold mb-2">Datenschutzeinstellungen</h3>
                            <p class="text-gray-600 mb-4">
                                Passen Sie Ihre Datenschutzeinstellungen an und steuern Sie, wie Ihre Daten verwendet werden.
                                Verwalten Sie Netzwerk-Einstellungen, Profil-Sichtbarkeit und Datenanalyse-Präferenzen.
                            </p>
                            <a href="{{ route('settings.privacy.edit') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Einstellungen verwalten
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Deletion -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-2 border-red-200">
                <div class="p-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 text-4xl mr-4">🗑️</div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold mb-2 text-red-600">Konto löschen (DSGVO Art. 17)</h3>
                            <p class="text-gray-600 mb-4">
                                Beantragen Sie die vollständige Löschung Ihres Kontos und aller damit verbundenen Daten.
                                Diese Aktion kann nicht rückgängig gemacht werden.
                            </p>

                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                                <h4 class="font-semibold text-yellow-800 mb-2">⚠️ Wichtige Hinweise:</h4>
                                <ul class="list-disc list-inside text-sm text-yellow-700 space-y-1">
                                    <li>Sie können Ihr Konto nicht löschen, wenn Sie bevorstehende Events als Veranstalter haben</li>
                                    <li>Sie können Ihr Konto nicht löschen, wenn Sie bevorstehende Buchungen haben</li>
                                    <li>Alle Ihre Daten werden innerhalb von 30 Tagen vollständig gelöscht</li>
                                    <li>Diese Aktion kann nicht rückgängig gemacht werden</li>
                                </ul>
                            </div>

                            <button onclick="document.getElementById('delete-modal').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Konto löschen beantragen
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Account Modal -->
    <div id="delete-modal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full">
            <h3 class="text-lg font-semibold mb-4 text-red-600">Konto löschen</h3>
            <p class="text-gray-600 mb-4">
                Sind Sie sicher, dass Sie Ihr Konto löschen möchten? Bitte geben Sie Ihr Passwort ein, um fortzufahren.
            </p>

            <form action="{{ route('data-privacy.request-deletion') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Passwort
                    </label>
                    <input type="password" name="password" required class="w-full rounded-md border-gray-300">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Grund (optional)
                    </label>
                    <textarea name="reason" rows="3" class="w-full rounded-md border-gray-300" placeholder="Warum möchten Sie Ihr Konto löschen?"></textarea>
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="document.getElementById('delete-modal').classList.add('hidden')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Abbrechen
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        Konto löschen
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>

