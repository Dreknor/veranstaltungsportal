<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Benachrichtigungseinstellungen') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('settings.notifications.update') }}">
                        @csrf
                        @method('PUT')

                        <!-- Email Notifications -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                <x-icon-mail class="inline w-5 h-5 mr-2" />
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

                        <!-- Push Notifications -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                <x-icon-bell class="inline w-5 h-5 mr-2" />
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

                        <div class="flex items-center justify-end">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Einstellungen speichern
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

