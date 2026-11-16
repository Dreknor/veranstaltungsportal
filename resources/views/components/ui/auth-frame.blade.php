{{--
    Seitenrahmen für schmale Einzelformulare im öffentlichen Layout (Anmeldung, Registrierung,
    Passwort, Bestätigungs- und Ergebnisseiten): Seitenbreite mit Seitenrand und Abstand oben/unten,
    darin eine zentrierte Spalte von 440 px. Inhalt: <x-ui.page-header> und das Formular.
    class landet an der inneren Spalte.
--}}
<div class="mx-auto max-w-page px-page py-[clamp(32px,6vw,64px)]">
    <div {{ $attributes->class('mx-auto max-w-[440px]') }}>
        {{ $slot }}
    </div>
</div>
