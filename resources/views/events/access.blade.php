<x-layouts.public title="Privates Event – Zugriff erforderlich">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8">
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-3xl">🔒</span>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Privates Event</h2>
                    <p class="text-gray-600 dark:text-gray-400">{{ $event->title }}</p>
                </div>

                <p class="text-gray-700 dark:text-gray-300 mb-6 text-center">
                    Dieses Event ist privat. Bitte gib den Access Code ein, um Zugriff zu erhalten.
                </p>

                @if ($errors->any())
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg mb-6">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('events.verify-access', $event->slug) }}" method="POST" data-recaptcha data-recaptcha-action="access_code">
                    @csrf
                    <div class="mb-6">
                        <label for="access_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Access Code
                        </label>
                        <input type="text"
                               name="access_code"
                               id="access_code"
                               required
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Gib deinen Access Code ein">
                    </div>

                    <!-- reCAPTCHA -->
                    <x-recaptcha action="access_code" />

                    <button type="submit"
                            class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold transition">
                        Zugriff gewähren
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <a href="{{ route('events.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                        ← Zurück zur Übersicht
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.public>

