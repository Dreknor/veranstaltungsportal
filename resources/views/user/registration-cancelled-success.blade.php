<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-8">
                <div class="text-center mb-6">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 dark:bg-green-900 mb-4">
                        <svg class="h-10 w-10 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                        Registrierung erfolgreich storniert
                    </h2>
                </div>

                <div class="mb-6">
                    <p class="text-gray-700 dark:text-gray-300 text-center mb-4">
                        Ihr Konto <strong>{{ $email }}</strong> wurde erfolgreich gelöscht.
                    </p>
                    <p class="text-gray-700 dark:text-gray-300 text-center">
                        Sie erhalten eine Bestätigungs-E-Mail an Ihre E-Mail-Adresse.
                    </p>
                </div>

                <a href="{{ route('home') }}"
                   class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    Zur Startseite
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>
