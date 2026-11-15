{{--
    Wartungsmodus: bewusst eigenes, schlankes Gerüst. Diese Seite darf weder Datenbank noch
    Session oder Anmeldung voraussetzen, deshalb kein <x-layouts.public> (Navigation und Fuß
    fragen den angemeldeten Benutzer ab). Nur CSS (Vite), Wortmarke und statischer Text.
--}}
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>503 – Wartungsarbeiten – {{ config('app.name') }}</title>
    @vite(['resources/css/app.css'])
</head>

<body class="flex min-h-screen flex-col bg-surface font-sans text-base text-ink antialiased">
    <header class="border-b border-line">
        <div class="mx-auto flex min-h-[72px] max-w-page items-center px-page">
            <x-wordmark :href="false" />
        </div>
    </header>

    <main class="flex-1">
        <div class="mx-auto max-w-page px-page py-[clamp(48px,8vw,96px)]">
            <div class="max-w-prose">
                <p class="m-0 mb-3 text-caption text-muted">Fehler 503</p>
                <h1 class="m-0 text-title">Wartungsarbeiten</h1>
                <p class="mt-2 mb-8 text-base text-muted">
                    Wir führen gerade Wartungsarbeiten durch. Das Bildungsportal ist vorübergehend nicht verfügbar.
                    Bitte versuchen Sie es in Kürze erneut.
                </p>
                <button type="button" onclick="window.location.reload()"
                    class="inline-flex h-11 cursor-pointer items-center justify-center rounded-control border border-primary bg-primary px-4 text-ui font-medium text-on-primary transition-colors duration-150 hover:border-ink hover:bg-ink">
                    Erneut versuchen
                </button>
            </div>
        </div>
    </main>
</body>

</html>
