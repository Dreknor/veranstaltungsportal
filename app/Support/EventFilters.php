<?php

namespace App\Support;

use Carbon\CarbonImmutable;

/**
 * Filter der Suchzeile (Format, Zeitraum): erlaubte Werte, Beschriftungen und Datumsbereiche.
 * Unbekannte Werte werden ignoriert (kein Fehler, kein Filter).
 */
class EventFilters
{
    /** Wert in der URL => event_type in der Datenbank */
    public const FORMATS = [
        'praesenz' => 'physical',
        'online' => 'online',
        'hybrid' => 'hybrid',
    ];

    /** Alternative Schreibweisen in der URL */
    private const FORMAT_ALIASES = [
        'präsenz' => 'praesenz',
    ];

    public const FORMAT_LABELS = [
        'praesenz' => 'Präsenz',
        'online' => 'Online',
        'hybrid' => 'Hybrid',
    ];

    public const PERIOD_LABELS = [
        'heute' => 'Heute',
        'diese-woche' => 'Diese Woche',
        'dieser-monat' => 'Diesen Monat',
        'naechste-3-monate' => 'Nächste 3 Monate',
        'schuljahr' => 'Dieses Schuljahr',
    ];

    /**
     * Optionen für die Zelle „Format" (leer = alle)
     *
     * @return array<string, string>
     */
    public static function formatOptions(): array
    {
        return ['' => 'Alle'] + self::FORMAT_LABELS;
    }

    /**
     * Optionen für die Zelle „Zeitraum" (leer = beliebig)
     *
     * @return array<string, string>
     */
    public static function periodOptions(): array
    {
        return ['' => 'Beliebig'] + self::PERIOD_LABELS;
    }

    /** Gültiger Format-Schlüssel (praesenz|online|hybrid) oder null */
    public static function format(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = mb_strtolower(trim($value));
        $value = self::FORMAT_ALIASES[$value] ?? $value;

        return array_key_exists($value, self::FORMATS) ? $value : null;
    }

    /** Gültiger Zeitraum-Schlüssel oder null */
    public static function period(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = mb_strtolower(trim($value));

        return array_key_exists($value, self::PERIOD_LABELS) ? $value : null;
    }

    /**
     * Beginn und Ende eines Zeitraums (Startdatum der Veranstaltung liegt darin).
     *
     * @return array{0: CarbonImmutable, 1: CarbonImmutable}
     */
    public static function periodRange(string $period, ?CarbonImmutable $now = null): array
    {
        $now ??= CarbonImmutable::now();
        $start = $now->startOfDay();

        $end = match ($period) {
            'heute' => $now->endOfDay(),
            'diese-woche' => $now->endOfWeek(CarbonImmutable::SUNDAY),
            'dieser-monat' => $now->endOfMonth(),
            'naechste-3-monate' => $now->addMonthsNoOverflow(3)->endOfDay(),
            // Schuljahr in Sachsen: 1. August bis 31. Juli
            'schuljahr' => $now->setDate($now->month >= 8 ? $now->year + 1 : $now->year, 7, 31)->endOfDay(),
            default => throw new \InvalidArgumentException("Unbekannter Zeitraum: {$period}"),
        };

        return [$start, $end];
    }
}
