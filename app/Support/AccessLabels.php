<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Deutsche Anzeigenamen und Töne für Rollen, Berechtigungen und Gruppen
 * (Zuordnung in config/permission-labels.php; die internen Schlüssel bleiben unverändert).
 */
class AccessLabels
{
    public const DEFAULT_TONE = 'neutral';

    public static function role(?string $name): string
    {
        $name = (string) $name;

        return config('permission-labels.roles.'.$name) ?? Str::ucfirst($name);
    }

    public static function permission(?string $name): string
    {
        $name = (string) $name;

        return config('permission-labels.permissions.'.$name) ?? Str::ucfirst($name);
    }

    public static function group(?string $name): string
    {
        $name = (string) $name;

        return config('permission-labels.groups.'.$name) ?? Str::ucfirst($name);
    }

    /** Auswahl für das Formular: Schlüssel => Bezeichnung. */
    public static function toneOptions(): array
    {
        return array_map(fn (array $t) => $t['label'], config('permission-labels.tones'));
    }

    /** Gültiger Farbschlüssel (alte Hex-Werte oder Unbekanntes ergeben den Standard). */
    public static function toneKey(?string $color): string
    {
        return array_key_exists((string) $color, config('permission-labels.tones')) ? $color : self::DEFAULT_TONE;
    }

    /** Ton für <x-ui.status> zu einer Rollenfarbe. */
    public static function roleTone(?string $color): string
    {
        return config('permission-labels.tones.'.self::toneKey($color).'.tone', 'neutral');
    }
}
