<?php

namespace App\Support;

use App\Models\Event;
use Carbon\CarbonInterface as Carbon;
use Illuminate\Support\Facades\View;
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

    /**
     * Alte Font-Awesome-Namen (Datenbank: Kategorien, Abzeichen; Konfiguration der Seitenleiste)
     * auf eigene Linien-Icons unter components/icon/ abbilden. Schlüssel ohne Präfix.
     *
     * @var array<string, string>
     */
    private const FA_ICONS = [
        // Navigation, Übersicht
        'house' => 'home',
        'home' => 'home',
        'gauge' => 'chart',
        'gauge-high' => 'chart',
        'tachometer-alt' => 'chart',
        'chart-line' => 'chart',
        'chart-bar' => 'chart',
        'chart-pie' => 'chart',
        'chart-simple' => 'chart',
        'list' => 'list',
        'list-ul' => 'list',
        'clipboard-list' => 'list',
        'bars' => 'menu',
        'gear' => 'settings',
        'gears' => 'settings',
        'cog' => 'settings',
        'cogs' => 'settings',
        'sliders' => 'settings',
        'circle-question' => 'help',
        'question-circle' => 'help',
        'question' => 'help',
        'circle-info' => 'info',
        'info-circle' => 'info',
        'info' => 'info',
        'bell' => 'bell',
        'envelope' => 'mail',
        'envelope-open' => 'mail',
        'inbox' => 'mail',
        'magnifying-glass' => 'search',
        'search' => 'search',
        'filter' => 'filter',
        'ellipsis' => 'ellipsis-horizontal',
        'ellipsis-h' => 'ellipsis-horizontal',
        'ellipsis-vertical' => 'more',
        'link' => 'link',
        'globe' => 'globe',
        'lock' => 'lock',
        'eye' => 'eye',
        'clock' => 'clock',
        'location-dot' => 'location',
        'map-marker-alt' => 'location',
        'map-marker' => 'location',
        'phone' => 'phone',
        'video' => 'video',
        'tv' => 'monitor',
        'desktop' => 'computer-desktop',
        'laptop' => 'computer-desktop',
        'laptop-code' => 'computer-desktop',
        // Veranstaltungen, Buchungen
        'calendar' => 'calendar',
        'calendar-days' => 'calendar',
        'calendar-alt' => 'calendar',
        'calendar-day' => 'calendar',
        'calendar-week' => 'calendar',
        'calendar-check' => 'calendar-check',
        'ticket' => 'ticket',
        'ticket-alt' => 'ticket',
        'ticket-simple' => 'ticket',
        'tag' => 'tag',
        'tags' => 'tag',
        'percent' => 'tag',
        'receipt' => 'receipt',
        'file-invoice' => 'document',
        'file-invoice-dollar' => 'document',
        'file-lines' => 'document',
        'file-alt' => 'document',
        'file' => 'document',
        'file-contract' => 'scale',
        'file-export' => 'download',
        'cloud-arrow-down' => 'download',
        'download' => 'download',
        'upload' => 'upload',
        'qrcode' => 'qr-code',
        'qr-code' => 'qr-code',
        'check' => 'check',
        'circle-check' => 'check-circle',
        'check-circle' => 'check-circle',
        'clipboard-check' => 'check-circle',
        // Organisation, Zahlung
        'building' => 'building-office',
        'school' => 'building-office',
        'building-columns' => 'bank',
        'university' => 'bank',
        'landmark' => 'bank',
        'paypal' => 'credit-card',
        'credit-card' => 'credit-card',
        'money-bill' => 'currency',
        'money-bill-wave' => 'currency',
        'coins' => 'currency',
        'euro-sign' => 'currency',
        'scale-balanced' => 'scale',
        'balance-scale' => 'scale',
        'gavel' => 'scale',
        'shield-halved' => 'shield-check',
        'shield-alt' => 'shield-check',
        'shield' => 'shield-check',
        'user-shield' => 'shield-check',
        // Personen
        'user' => 'user',
        'user-circle' => 'user-circle',
        'circle-user' => 'user-circle',
        'user-tie' => 'user',
        'user-graduate' => 'academic-cap',
        'chalkboard-teacher' => 'academic-cap',
        'chalkboard-user' => 'academic-cap',
        'users' => 'users',
        'users-gear' => 'users',
        'user-friends' => 'user-group',
        'user-group' => 'user-group',
        'people-group' => 'user-group',
        'handshake' => 'user-group',
        'comments' => 'chat-bubble-left-right',
        'comment' => 'chat-bubble-left-right',
        'comment-dots' => 'chat-bubble-left-right',
        // Themen (Kategorien)
        'graduation-cap' => 'academic-cap',
        'book' => 'book-open',
        'book-open' => 'book-open',
        'book-reader' => 'book-open',
        'heart' => 'heart',
        'heartbeat' => 'heart-pulse',
        'heart-pulse' => 'heart-pulse',
        'hand-holding-heart' => 'heart',
        'lightbulb' => 'sparkles',
        'brain' => 'sparkles',
        'wand-magic-sparkles' => 'sparkles',
        // Abzeichen
        'medal' => 'medal',
        'award' => 'medal',
        'trophy' => 'medal',
        'crown' => 'medal',
        'ribbon' => 'medal',
        'certificate' => 'sparkles',
        'star' => 'star',
        'star-half-stroke' => 'star',
        'fire' => 'sparkles',
        'bolt' => 'sparkles',
        'rocket' => 'sparkles',
        'gem' => 'sparkles',
        'thumbs-up' => 'check-circle',
    ];

    /** @var array<string, bool> */
    private static array $iconExists = [];

    /**
     * Komponentenname eines Linien-Icons ("icon.calendar") zu einem Icon-Namen aus der Datenbank oder
     * Konfiguration. Nimmt eigene Namen ("calendar", "academic-cap") und alte Font-Awesome-Namen in jeder
     * Schreibweise ("fas fa-trophy", "fa-solid fa-ticket", "fas-calendar-days", "fa-heart").
     * Unbekannte oder leere Namen ergeben das Ersatz-Icon. Verwendung:
     * <x-dynamic-component :component="Ui::icon($category->icon, 'calendar')" class="h-5 w-5" />
     */
    public static function icon(?string $name, string $fallback = 'list'): string
    {
        $key = Str::lower(trim((string) $name));
        // Präfixe entfernen: "fa-solid fa-x", "fas fa-x", "fas-x", "fa-x", "heroicon-o-x"
        $key = (string) preg_replace('/^(fa-(solid|regular|brands|light|duotone)|fa[srbld]?)[-\s]+/', '', $key);
        $key = (string) preg_replace('/^(fa-|heroicon-[os]-|icon\.)/', '', $key);
        $key = (string) preg_replace('/\s+fa-(fw|lg|xl|[0-9]x)$/', '', $key);

        $candidate = self::FA_ICONS[$key] ?? $key;

        foreach ([$candidate, $fallback, 'list'] as $icon) {
            if ($icon !== '' && preg_match('/^[a-z0-9-]+$/', $icon) && self::iconExists($icon)) {
                return 'icon.'.$icon;
            }
        }

        return 'icon.list';
    }

    private static function iconExists(string $icon): bool
    {
        return self::$iconExists[$icon] ??= View::exists('components.icon.'.$icon);
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
