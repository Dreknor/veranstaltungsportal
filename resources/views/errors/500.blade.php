{{--
    Serverfehler: bewusst eigenes, schlankes Gerüst wie 503. Diese Seite darf weder Datenbank noch
    Session oder Anmeldung voraussetzen – der Fehler kann genau dort liegen. Deshalb kein
    <x-layouts.public> (Navigation und Fuß fragen den angemeldeten Benutzer ab). Optik wie 404.
--}}
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>500 – Serverfehler – {{ config('app.name') }}</title>
    @vite(['resources/css/app.css'])
</head>

<body class="flex min-h-screen flex-col bg-surface font-sans text-base text-ink antialiased">
    <header class="border-b border-line">
        <div class="mx-auto flex min-h-[72px] max-w-page items-center px-page">
            <x-wordmark />
        </div>
    </header>

    <main class="flex-1">
        <div class="mx-auto max-w-page px-page py-[clamp(48px,8vw,96px)]">
            <div class="max-w-prose">
                <p class="m-0 mb-3 text-caption text-muted">Fehler 500</p>
                <x-ui.page-header title="Interner Serverfehler"
                    subtitle="Entschuldigung! Etwas ist schief gelaufen. Unser Team wurde benachrichtigt und arbeitet an einer Lösung." />

                <div class="flex flex-wrap items-center gap-3">
                    <x-ui.button :href="url('/')">Zur Startseite</x-ui.button>
                    <x-ui.button variant="secondary" onclick="window.location.reload()">Seite neu laden</x-ui.button>
                </div>

                @if (config('app.debug'))
                    <x-ui.alert tone="danger" title="Debug-Information" class="mt-12">
                        <pre class="m-0 mt-2 overflow-auto text-caption">{{ $exception ?? 'Keine Details verfügbar' }}</pre>
                    </x-ui.alert>
                @endif
            </div>
        </div>
    </main>
</body>

</html>
