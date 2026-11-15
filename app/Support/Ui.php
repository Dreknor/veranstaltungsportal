<?php

namespace App\Support;

use App\Models\Event;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\ViewErrorBag;

/**
 * Kleine Helfer für die Bausteine unter resources/views/components/ui/.
 * Hier liegt nur Darstellungslogik (Schlüssel, Formate, Farbtöne) – keine Fachlogik.
 */
class Ui
{
    /**
     * Feldname in Punkt-Schreibweise, wie ihn old() und der Fehler-Speicher verwenden:
     * "tickets[0][quantity]" wird "tickets.0.quantity", "tags[]" wird "tags".
     */
    public static function key(?string $name): ?string
    {
        if ($name === null || $name === '') {
            return null;
        }

        $key = preg_replace('/\[([^\]]*)\]/', '.$1', $name);

        return trim((string) preg_replace('/\.+/', '.', (string) $key), '.');
    }

    /**
     * Standard-ID eines Feldes: der Feldname ohne Klammern ("tickets_0_quantity").
     */
    public static function id(?string $name): ?string
    {
        $key = self::key($name);

        return $key === null ? null : str_replace('.', '_', $key);
    }

    /**
     * Standard-ID für Checkbox, Radio und Auswahlkarte: Feldname plus Wert ("payment_method_invoice"),
     * damit mehrere Optionen gleichen Namens eindeutige IDs bekommen.
     */
    public static function optionId(?string $name, mixed $value = null, bool $withValue = true): string
    {
        $base = self::id($name) ?? 'option-'.Str::lower(Str::random(6));
        $suffix = $withValue ? Str::slug((string) $value, '_') : '';

        return $suffix !== '' ? $base.'_'.$suffix : $base;
    }

    /**
     * Erste Fehlermeldung zu einem Feld aus dem geteilten $errors der View.
     */
    public static function error(mixed $errors, ?string $name, string $bag = 'default'): ?string
    {
        $key = self::key($name);

        if ($key === null || ! $errors instanceof ViewErrorBag) {
            return null;
        }

        $message = $errors->getBag($bag)->first($key);

        return $message !== '' ? $message : null;
    }

    /**
     * Zuletzt abgeschickter Wert eines Feldes, sonst der übergebene Wert.
     */
    public static function old(?string $name, mixed $value = null): mixed
    {
        $key = self::key($name);

        return $key === null ? $value : old($key, $value);
    }

    /**
     * Zustand einer Checkbox oder eines Radios nach einem abgelehnten Formular:
     * Gab es alte Eingaben, entscheiden sie; sonst gilt der Vorgabewert.
     */
    public static function checked(?string $name, mixed $value, bool $default): bool
    {
        $key = self::key($name);

        if ($key === null || ! session()->hasOldInput()) {
            return $default;
        }

        $old = old($key);

        if (is_array($old)) {
            return in_array((string) $value, array_map('strval', $old), true);
        }

        return $old !== null && (string) $old === (string) $value;
    }

    /** Wochentag kurz: "Do". */
    public static function weekday(Carbon $date): string
    {
        return match ($date->dayOfWeek) {
            0 => 'So',
            1 => 'Mo',
            2 => 'Di',
            3 => 'Mi',
            4 => 'Do',
            5 => 'Fr',
            default => 'Sa',
        };
    }

    /** Monat kurz: "Nov". */
    public static function month(Carbon $date): string
    {
        return match ($date->month) {
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mär',
            4 => 'Apr',
            5 => 'Mai',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Aug',
            9 => 'Sep',
            10 => 'Okt',
            11 => 'Nov',
            default => 'Dez',
        };
    }

    /** Datum nach A7: "Do, 26.11.2026". */
    public static function date(Carbon $date): string
    {
        return self::weekday($date).', '.$date->format('d.m.Y');
    }

    /** Zeit nach A7: "14:00–17:30 Uhr" bzw. "14:00 Uhr". */
    public static function time(Carbon $start, ?Carbon $end = null): string
    {
        if ($end && $end->isSameDay($start) && $end->format('H:i') !== $start->format('H:i')) {
            return $start->format('H:i').'–'.$end->format('H:i').' Uhr';
        }

        return $start->format('H:i').' Uhr';
    }

    /** Datum und Zeit in einer Zeile: "Do, 26.11.2026, 14:00–17:30 Uhr" bzw. "Fr, 22.01.2027 – Sa, 23.01.2027". */
    public static function dateTime(Carbon $start, ?Carbon $end = null): string
    {
        if ($end && ! $end->isSameDay($start)) {
            return self::date($start).' – '.self::date($end);
        }

        return self::date($start).', '.self::time($start, $end);
    }

    /** Dauer in Kalendertagen: "zweitägig", "5-tägig"; ab acht Tagen "bis 23.12.2026". */
    public static function days(Carbon $start, Carbon $end): string
    {
        $days = (int) $start->copy()->startOfDay()->diffInDays($end->copy()->startOfDay()) + 1;

        return match (true) {
            $days === 2 => 'zweitägig',
            $days === 3 => 'dreitägig',
            $days <= 7 => $days.'-tägig',
            default => 'bis '.$end->format('d.m.Y'),
        };
    }

    /** Preis in Listen: "45 €", "12,50 €", "kostenfrei". */
    public static function price(float|int|string|null $amount): string
    {
        $amount = (float) $amount;

        if ($amount <= 0) {
            return 'kostenfrei';
        }

        $decimals = fmod($amount, 1.0) === 0.0 ? 0 : 2;

        return number_format($amount, $decimals, ',', '.').' €';
    }

    /** Betrag in Buchung und Rechnung: "45,00 €". */
    public static function money(float|int|string|null $amount): string
    {
        return number_format((float) $amount, 2, ',', '.').' €';
    }

    /** Format einer Veranstaltung als Wort: "Präsenz", "Online", "Hybrid", "Extern". */
    public static function format(Event $event): string
    {
        // Bewusst als string gelesen: Die Spalte kann ältere Werte enthalten als das Schema vorsieht.
        $type = (string) $event->getAttribute('event_type');

        return match ($type) {
            'online' => 'Online',
            'hybrid' => 'Hybrid',
            'external' => 'Extern',
            default => 'Präsenz',
        };
    }

    /** "Format · Ort" (Ort nur bei Präsenz und Hybrid, falls vorhanden). */
    public static function formatAndPlace(Event $event): string
    {
        $parts = [self::format($event)];

        $city = (string) $event->getAttribute('venue_city');

        if (! $event->isOnline() && $city !== '') {
            $parts[] = $city;
        }

        return implode(' · ', $parts);
    }

    /** Ton für <x-ui.status> zum Buchungsstatus. */
    public static function bookingTone(?string $status): string
    {
        return match ($status) {
            'confirmed', 'completed' => 'success',
            'pending', 'pending_approval' => 'warning',
            'cancelled' => 'danger',
            default => 'neutral',
        };
    }

    /** Ton für <x-ui.status> zum Zahlungsstatus. */
    public static function paymentTone(?string $status): string
    {
        return match ($status) {
            'paid' => 'success',
            'pending' => 'warning',
            'failed' => 'danger',
            default => 'neutral',
        };
    }
}
