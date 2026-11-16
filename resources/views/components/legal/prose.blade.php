{{--
    Lesetext für Rechtstexte aus der Datenbank (LegalPage, HTML aus dem Editor) und Antworttexte:
    Klasse .richtext aus app.css (B6) mit Lesebreite 760 px.
--}}
<div {{ $attributes->class('richtext max-w-prose') }}>
    {{ $slot }}
</div>
