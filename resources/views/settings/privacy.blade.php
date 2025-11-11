<x-layouts.app>
    <!-- Breadcrumbs -->
    <div class="mb-6 flex items-center text-sm">
        <a href="{{ route('dashboard') }}" class="text-blue-600 dark:text-blue-400 hover:underline">Dashboard</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <a href="{{ route('settings.profile.edit') }}" class="text-blue-600 dark:text-blue-400 hover:underline">Einstellungen</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-gray-500 dark:text-gray-400">Datenschutz</span>
    </div>

    <!-- Page Title -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Datenschutz-Einstellungen</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Steuern Sie, wer was von Ihrem Profil sehen kann</p>
    </div>

    <div class="p-6">
        <div class="flex flex-col md:flex-row gap-6">
            <!-- Sidebar Navigation -->
            @include('settings.partials.navigation')

            <!-- Privacy Settings Content -->
            <div class="flex-1">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="p-6">
                        <form action="{{ route('settings.privacy.update') }}" method="POST" class="space-y-6">
                            @csrf
                            @method('PUT')

                            <!-- Network & Connections Section -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Netzwerk & Verbindungen
                                </h3>

                                <div class="space-y-4">
                                    <!-- Allow Networking -->
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="allow_networking"
                                                   name="allow_networking"
                                                   type="checkbox"
                                                   value="1"
                                                   {{ old('allow_networking', $user->allow_networking ?? true) ? 'checked' : '' }}
                                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        </div>
                                        <div class="ml-3">
                                            <label for="allow_networking" class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                Vernetzung erlauben
                                            </label>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                Andere Benutzer können mit Ihnen in Kontakt treten, Ihnen Verbindungsanfragen senden und Sie zu ihrem Netzwerk hinzufügen
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Show Profile Public -->
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="show_profile_public"
                                                   name="show_profile_public"
                                                   type="checkbox"
                                                   value="1"
                                                   {{ old('show_profile_public', $user->show_profile_public ?? false) ? 'checked' : '' }}
                                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        </div>
                                        <div class="ml-3">
                                            <label for="show_profile_public" class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                Öffentliches Profil
                                            </label>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                Ihr Profil ist für alle Benutzer sichtbar, auch wenn sie nicht mit Ihnen verbunden sind
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Information Section -->
                            <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    Kontaktinformationen
                                </h3>

                                <div class="space-y-4">
                                    <!-- Show Email to Connections -->
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="show_email_to_connections"
                                                   name="show_email_to_connections"
                                                   type="checkbox"
                                                   value="1"
                                                   {{ old('show_email_to_connections', $user->show_email_to_connections) ? 'checked' : '' }}
                                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        </div>
                                        <div class="ml-3">
                                            <label for="show_email_to_connections" class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                E-Mail an Verbindungen zeigen
                                            </label>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                Nur Nutzer, mit denen Sie verbunden sind, können Ihre E-Mail-Adresse sehen
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Show Phone to Connections -->
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="show_phone_to_connections"
                                                   name="show_phone_to_connections"
                                                   type="checkbox"
                                                   value="1"
                                                   {{ old('show_phone_to_connections', $user->show_phone_to_connections) ? 'checked' : '' }}
                                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        </div>
                                        <div class="ml-3">
                                            <label for="show_phone_to_connections" class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                Telefonnummer an Verbindungen zeigen
                                            </label>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                Nur Nutzer, mit denen Sie verbunden sind, können Ihre Telefonnummer sehen
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Data Processing & Analytics Section -->
                            <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    Datenverarbeitung & Analytics
                                </h3>

                                <div class="space-y-4">
                                    <!-- Allow Data Analytics -->
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="allow_data_analytics"
                                                   name="allow_data_analytics"
                                                   type="checkbox"
                                                   value="1"
                                                   {{ old('allow_data_analytics', $user->allow_data_analytics ?? true) ? 'checked' : '' }}
                                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        </div>
                                        <div class="ml-3">
                                            <label for="allow_data_analytics" class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                Datenanalyse erlauben
                                            </label>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                Ihre anonymisierten Nutzungsdaten helfen uns, die Plattform zu verbessern
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- DSGVO Rights Info -->
                            <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-green-800 dark:text-green-300">
                                                Ihre Rechte nach DSGVO
                                            </h3>
                                            <div class="mt-2 text-sm text-green-700 dark:text-green-400">
                                                <p class="mb-2">Sie haben folgende Rechte bezüglich Ihrer persönlichen Daten:</p>
                                                <ul class="list-disc list-inside space-y-1 ml-2">
                                                    <li><strong>Recht auf Auskunft (Art. 15):</strong> Datenexport unter <a href="{{ route('data-privacy.index') }}" class="underline font-semibold">Meine Daten</a></li>
                                                    <li><strong>Recht auf Berichtigung (Art. 16):</strong> Profil-Einstellungen bearbeiten</li>
                                                    <li><strong>Recht auf Löschung (Art. 17):</strong> Kontolöschung unter <a href="{{ route('data-privacy.index') }}" class="underline font-semibold">Meine Daten</a></li>
                                                    <li><strong>Recht auf Datenübertragbarkeit (Art. 20):</strong> Export als JSON/ZIP</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Info Box -->
                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-blue-800 dark:text-blue-300">
                                            Über Ihre Privatsphäre
                                        </h3>
                                        <div class="mt-2 text-sm text-blue-700 dark:text-blue-400">
                                            <p>
                                                Ihre Privatsphäre ist uns wichtig. Sie haben jederzeit die volle Kontrolle darüber,
                                                wer welche Informationen von Ihnen sehen kann. Blockierte Nutzer können Ihr Profil
                                                nicht einsehen.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex items-center justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                                <button type="submit"
                                        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    Einstellungen speichern
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

