<x-layouts.app>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Datenschutzeinstellungen') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('data-privacy.settings.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="space-y-6">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input
                                        id="allow_networking"
                                        name="allow_networking"
                                        type="checkbox"
                                        value="1"
                                        {{ old('allow_networking', auth()->user()->allow_networking ?? true) ? 'checked' : '' }}
                                        class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                                    >
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="allow_networking" class="font-medium text-gray-700">
                                        Vernetzung erlauben
                                    </label>
                                    <p class="text-gray-500">
                                        Andere Benutzer können mit Ihnen in Kontakt treten und Sie zu ihrem Netzwerk hinzufügen.
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input
                                        id="show_profile_public"
                                        name="show_profile_public"
                                        type="checkbox"
                                        value="1"
                                        {{ old('show_profile_public', auth()->user()->show_profile_public ?? false) ? 'checked' : '' }}
                                        class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                                    >
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="show_profile_public" class="font-medium text-gray-700">
                                        Öffentliches Profil
                                    </label>
                                    <p class="text-gray-500">
                                        Ihr Profil ist für alle Benutzer sichtbar, auch wenn sie nicht mit Ihnen verbunden sind.
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input
                                        id="allow_data_analytics"
                                        name="allow_data_analytics"
                                        type="checkbox"
                                        value="1"
                                        {{ old('allow_data_analytics', auth()->user()->allow_data_analytics ?? true) ? 'checked' : '' }}
                                        class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                                    >
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="allow_data_analytics" class="font-medium text-gray-700">
                                        Datenanalyse erlauben
                                    </label>
                                    <p class="text-gray-500">
                                        Ihre anonymisierten Nutzungsdaten helfen uns, die Plattform zu verbessern.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                Einstellungen speichern
                            </button>
                        </div>
                    </form>

                    <div class="mt-8 pt-8 border-t border-gray-200">
                        <h3 class="text-lg font-semibold mb-4">Ihre Rechte nach DSGVO</h3>
                        <ul class="list-disc list-inside space-y-2 text-gray-700">
                            <li><strong>Recht auf Auskunft (Art. 15):</strong> Sie können eine Kopie Ihrer Daten anfordern</li>
                            <li><strong>Recht auf Berichtigung (Art. 16):</strong> Sie können fehlerhafte Daten korrigieren lassen</li>
                            <li><strong>Recht auf Löschung (Art. 17):</strong> Sie können die Löschung Ihrer Daten beantragen</li>
                            <li><strong>Recht auf Datenübertragbarkeit (Art. 20):</strong> Sie können Ihre Daten in einem strukturierten Format erhalten</li>
                            <li><strong>Recht auf Widerspruch (Art. 21):</strong> Sie können der Verarbeitung Ihrer Daten widersprechen</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

