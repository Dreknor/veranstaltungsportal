<x-layouts.app title="Benachrichtigungseinstellungen">
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Benachrichtigungseinstellungen</h1>
                <p class="text-gray-600 mt-2">Verwalten Sie Ihre E-Mail- und Push-Benachrichtigungen</p>
            </div>

            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('settings.notifications.update') }}">
                @csrf
                @method('PUT')

                <!-- Email Notifications -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        E-Mail Benachrichtigungen
                    </h3>
                    <div class="space-y-4">
                                <div class="flex items-center justify-between py-3 border-b">
                                    <div>
                                        <label class="font-medium text-gray-700">Buchungsbestätigung</label>
                                        <p class="text-sm text-gray-500">Erhalten Sie eine E-Mail, wenn Ihre Buchung bestätigt wurde</p>
                                    </div>
                                    <div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="email_booking_confirmed" value="1"
                                                   class="sr-only peer"
                                                   {{ ($preferences['email_booking_confirmed'] ?? true) ? 'checked' : '' }}>
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between py-3 border-b">
                                    <div>
                                        <label class="font-medium text-gray-700">Stornierungsbestätigung</label>
                                        <p class="text-sm text-gray-500">Erhalten Sie eine E-Mail, wenn eine Buchung storniert wurde</p>
                                    </div>
                                    <div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="email_booking_cancelled" value="1"
                                                   class="sr-only peer"
                                                   {{ ($preferences['email_booking_cancelled'] ?? true) ? 'checked' : '' }}>
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between py-3 border-b">
                                    <div>
                                        <label class="font-medium text-gray-700">Veranstaltungserinnerung</label>
                                        <p class="text-sm text-gray-500">Erinnerung 24 Stunden vor Veranstaltungsbeginn</p>
                                    </div>
                                    <div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="email_event_reminder" value="1"
                                                   class="sr-only peer"
                                                   {{ ($preferences['email_event_reminder'] ?? true) ? 'checked' : '' }}>
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between py-3 border-b">
                                    <div>
                                        <label class="font-medium text-gray-700">Veranstaltungsänderungen</label>
                                        <p class="text-sm text-gray-500">Benachrichtigung bei Änderungen an gebuchten Veranstaltungen</p>
                                    </div>
                                    <div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="email_event_updated" value="1"
                                                   class="sr-only peer"
                                                   {{ ($preferences['email_event_updated'] ?? true) ? 'checked' : '' }}>
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between py-3 border-b">
                                    <div>
                                        <label class="font-medium text-gray-700">Neue Bewertungen</label>
                                        <p class="text-sm text-gray-500">Benachrichtigung bei neuen Bewertungen Ihrer Veranstaltungen (nur für Veranstalter)</p>
                                    </div>
                                    <div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="email_new_review" value="1"
                                                   class="sr-only peer"
                                                   {{ ($preferences['email_new_review'] ?? false) ? 'checked' : '' }}>
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between py-3">
                                    <div>
                                        <label class="font-medium text-gray-700">Marketing & Newsletter</label>
                                        <p class="text-sm text-gray-500">Informationen über neue Veranstaltungen und Angebote</p>
                                    </div>
                                    <div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="email_marketing" value="1"
                                                   class="sr-only peer"
                                                   {{ ($preferences['email_marketing'] ?? false) ? 'checked' : '' }}>
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                <!-- Push Notifications -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        Push-Benachrichtigungen
                    </h3>
                    <p class="text-sm text-gray-600 mb-4">Browser-Benachrichtigungen (in Entwicklung)</p>
                    <div class="space-y-4 opacity-50">
                                <div class="flex items-center justify-between py-3 border-b">
                                    <div>
                                        <label class="font-medium text-gray-700">Buchungsbestätigung</label>
                                        <p class="text-sm text-gray-500">Browser-Benachrichtigung bei Buchungsbestätigung</p>
                                    </div>
                                    <div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="push_booking_confirmed" value="1"
                                                   class="sr-only peer" disabled
                                                   {{ ($preferences['push_booking_confirmed'] ?? false) ? 'checked' : '' }}>
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between py-3 border-b">
                                    <div>
                                        <label class="font-medium text-gray-700">Veranstaltungserinnerung</label>
                                        <p class="text-sm text-gray-500">Browser-Benachrichtigung vor Veranstaltungsbeginn</p>
                                    </div>
                                    <div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="push_event_reminder" value="1"
                                                   class="sr-only peer" disabled
                                                   {{ ($preferences['push_event_reminder'] ?? false) ? 'checked' : '' }}>
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between py-3">
                                    <div>
                                        <label class="font-medium text-gray-700">Veranstaltungsänderungen</label>
                                        <p class="text-sm text-gray-500">Browser-Benachrichtigung bei Änderungen</p>
                                    </div>
                                    <div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="push_event_updated" value="1"
                                                   class="sr-only peer" disabled
                                                   {{ ($preferences['push_event_updated'] ?? false) ? 'checked' : '' }}>
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                        Einstellungen speichern
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>

