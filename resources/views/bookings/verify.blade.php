<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buchung verifizieren</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="text-2xl font-bold text-blue-600">EventPortal</a>
                </div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('events.index') }}" class="text-gray-700 hover:text-gray-900">Events</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-3xl">🎫</span>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Buchung verifizieren</h2>
                    <p class="text-gray-600">Buchungsnummer: {{ $booking->booking_number }}</p>
                </div>

                <p class="text-gray-700 mb-6 text-center">
                    Bitte gib deine E-Mail-Adresse ein, um auf die Buchungsdetails zuzugreifen.
                </p>

                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('bookings.verify-email', $booking->booking_number) }}" method="POST">
                    @csrf
                    <div class="mb-6">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            E-Mail-Adresse
                        </label>
                        <input type="email"
                               name="email"
                               id="email"
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="deine@email.de">
                        <p class="text-xs text-gray-500 mt-1">
                            Verwende die E-Mail-Adresse, die du bei der Buchung angegeben hast.
                        </p>
                    </div>

                    <button type="submit"
                            class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold transition">
                        Buchung ansehen
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <a href="{{ route('events.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                        ← Zurück zur Übersicht
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

