{{--
    Icon zu einem Namen aus Datenbank oder Konfiguration (Kategorie, Abzeichen, Seitenleiste).
    name: eigener Icon-Name ("calendar") oder alter Font-Awesome-Name ("fa-trophy", "fas-calendar-days");
    fallback: Icon für leere oder unbekannte Namen. Zuordnung: App\Support\Ui::icon().
    Feste Icons direkt als <x-icon.name /> setzen.
--}}
@props([
    'name' => null,
    'fallback' => 'list',
])

<x-dynamic-component :component="\App\Support\Ui::icon($name, $fallback)" aria-hidden="true" {{ $attributes }} />
