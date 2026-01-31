<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-8">
                <div class="text-center mb-6">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 dark:bg-yellow-900 mb-4">
                        <svg class="h-10 w-10 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                        Registrierung rückgängig machen?
                    </h2>
                </div>

                <div class="mb-6">
                    <p class="text-gray-700 dark:text-gray-300 mb-4">
                        Sie sind dabei, die Registrierung für das folgende Konto zu stornieren:
                    </p>
                    <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-4">
                        <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $email }}</p>
                    </div>
                    <p class="text-gray-700 dark:text-gray-300 mt-4">
                        <strong>Achtung:</strong> Dieser Vorgang kann nicht rückgängig gemacht werden. Ihr Konto und alle damit verbundenen Daten werden unwiderruflich gelöscht.
                    </p>
                </div>

                <form method="POST" action="{{ route('user.cancel-registration.process', $token) }}" class="space-y-4">
                    @csrf
                    <button type="submit"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                        Ja, Registrierung stornieren
                    </button>
                    <a href="{{ route('home') }}"
                       class="w-full flex justify-center py-3 px-4 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        Abbrechen
                    </a>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
