<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Serverfehler</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-lg w-full text-center">
            <div class="mb-8">
                <h1 class="text-9xl font-bold text-red-600">500</h1>
                <div class="text-6xl mb-4">⚠️</div>
            </div>

            <h2 class="text-3xl font-bold text-gray-900 mb-4">
                Interner Serverfehler
            </h2>

            <p class="text-lg text-gray-600 mb-8">
                Entschuldigung! Etwas ist schief gelaufen. Unser Team wurde benachrichtigt und arbeitet an einer Lösung.
            </p>

            <div class="space-y-3">
                <a href="{{ route('home') }}" class="inline-block w-full sm:w-auto px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                    Zurück zur Startseite
                </a>

                <button onclick="window.location.reload()" class="inline-block w-full sm:w-auto px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition ml-0 sm:ml-3">
                    Seite neu laden
                </button>
            </div>

            @if(config('app.debug'))
                <div class="mt-8 p-4 bg-red-50 border border-red-200 rounded-lg text-left">
                    <h3 class="text-sm font-semibold text-red-800 mb-2">Debug Information:</h3>
                    <pre class="text-xs text-red-700 overflow-auto">{{ $exception ?? 'Keine Details verfügbar' }}</pre>
                </div>
            @endif
        </div>
    </div>
</body>
</html>

