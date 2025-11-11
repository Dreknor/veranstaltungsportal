<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Seite nicht gefunden</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-lg w-full text-center">
            <div class="mb-8">
                <h1 class="text-9xl font-bold text-blue-600">404</h1>
                <div class="text-6xl mb-4">🔍</div>
            </div>

            <h2 class="text-3xl font-bold text-gray-900 mb-4">
                Seite nicht gefunden
            </h2>

            <p class="text-lg text-gray-600 mb-8">
                Die von Ihnen gesuchte Seite existiert leider nicht. Möglicherweise wurde sie verschoben oder gelöscht.
            </p>

            <div class="space-y-3">
                <a href="{{ route('home') }}" class="inline-block w-full sm:w-auto px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                    Zurück zur Startseite
                </a>

                <button onclick="window.history.back()" class="inline-block w-full sm:w-auto px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition ml-0 sm:ml-3">
                    Zurück
                </button>
            </div>

            <div class="mt-12 pt-8 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    Hilfreiche Links
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <a href="{{ route('events.index') }}" class="text-blue-600 hover:underline">
                        Veranstaltungen
                    </a>
                    <a href="{{ route('events.calendar') }}" class="text-blue-600 hover:underline">
                        Kalender
                    </a>
                    <a href="{{ route('dashboard') }}" class="text-blue-600 hover:underline">
                        Dashboard
                    </a>
                    <a href="{{ route('contact') }}" class="text-blue-600 hover:underline">
                        Kontakt
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

