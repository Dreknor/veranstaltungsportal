<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Deutsche Anzeigetexte für technische Werte im Admin-Bereich (Audit-Log, Berichte, Zahlungen).
 * Unbekannte Werte erscheinen unverändert, damit nichts verloren geht.
 */
class AdminLabels
{
    private const AUDIT_ACTIONS = [
        'login' => 'Anmeldung',
        'created' => 'Erstellt',
        'updated' => 'Geändert',
        'deleted' => 'Gelöscht',
        'role_created' => 'Rolle erstellt',
        'role_updated' => 'Rolle geändert',
        'role_deleted' => 'Rolle gelöscht',
        'permission_created' => 'Berechtigung erstellt',
        'permission_updated' => 'Berechtigung geändert',
        'permission_deleted' => 'Berechtigung gelöscht',
        'account_deletion_requested' => 'Kontolöschung beantragt',
        'data_export' => 'Datenexport',
        'files_download' => 'Dateien heruntergeladen',
        'impersonate_start' => 'Verkörperung gestartet',
        'impersonate_end' => 'Verkörperung beendet',
        'privacy_settings_updated' => 'Datenschutz geändert',
    ];

    private const AUDIT_MODELS = [
        'User' => 'Benutzer',
        'Event' => 'Veranstaltung',
        'EventReview' => 'Bewertung',
        'LegalPage' => 'Rechtstext',
        'Organization' => 'Organisation',
        'Booking' => 'Buchung',
        'Badge' => 'Abzeichen',
        'EventCategory' => 'Kategorie',
        'Invoice' => 'Rechnung',
        'Setting' => 'Einstellung',
        'Role' => 'Rolle',
        'Permission' => 'Berechtigung',
    ];

    private const PAYMENT_STATUS = [
        'paid' => 'Bezahlt',
        'pending' => 'Offen',
        'refunded' => 'Erstattet',
        'failed' => 'Fehlgeschlagen',
        'extern' => 'Extern',
        'free' => 'Kostenfrei',
    ];

    private const PAYMENT_METHODS = [
        'invoice' => 'Rechnung',
        'paypal' => 'PayPal',
        'bank_transfer' => 'Überweisung',
        'free' => 'Kostenfrei',
        'extern' => 'Extern',
    ];

    private const EVENT_TYPES = [
        'physical' => 'Präsenz',
        'online' => 'Online',
        'hybrid' => 'Hybrid',
        'external' => 'Extern',
    ];

    private const SETTING_GROUPS = [
        'general' => 'Allgemein',
        'email' => 'E-Mail',
        'booking' => 'Buchung',
        'payment' => 'Zahlung',
        'appearance' => 'Darstellung',
        'invoice' => 'Rechnung',
    ];

    private const SETTING_TYPES = [
        'string' => 'Text',
        'integer' => 'Zahl',
        'boolean' => 'Ja/Nein',
        'json' => 'Liste',
    ];

    public static function settingGroup(?string $group): string
    {
        return self::SETTING_GROUPS[(string) $group] ?? Str::ucfirst((string) $group);
    }

    public static function settingType(?string $type): string
    {
        return self::SETTING_TYPES[(string) $type] ?? (string) $type;
    }

    /** Symbole für Abzeichen: Name der Icon-Komponente (components/icon) => deutsche Bezeichnung. */
    private const BADGE_ICONS = [
        'medal' => 'Medaille',
        'star' => 'Stern',
        'star-filled' => 'Stern (gefüllt)',
        'sparkles' => 'Funken',
        'academic-cap' => 'Abschluss',
        'book-open' => 'Buch',
        'calendar-check' => 'Teilnahme',
        'check-circle' => 'Erfolg',
        'heart' => 'Herz',
        'heart-pulse' => 'Gesundheit',
        'ticket' => 'Ticket',
        'user-group' => 'Gruppe',
        'users' => 'Gemeinschaft',
        'chat-bubble-left-right' => 'Austausch',
        'shield-check' => 'Schutzschild',
        'globe' => 'Globus',
        'scale' => 'Waage',
        'tag' => 'Etikett',
        'bell' => 'Glocke',
        'clock' => 'Uhr',
    ];

    /**
     * Auswahl der Abzeichen-Symbole. $current (z. B. ein älterer Font-Awesome-Name wie "fas fa-trophy")
     * wird auf das gerenderte eigene Symbol abgebildet und bleibt dadurch auswählbar.
     *
     * @return array<string, string>
     */
    public static function badgeIcons(?string $current = null): array
    {
        $options = self::BADGE_ICONS;

        if ($current !== null && $current !== '') {
            $key = Str::after(Ui::icon($current, 'medal'), 'icon.');

            if (! isset($options[$key])) {
                $options[$key] = Str::headline($key);
            }
        }

        return $options;
    }

    /** Auswahlschlüssel zum gespeicherten Icon-Wert eines Abzeichens. */
    public static function badgeIconKey(?string $stored): string
    {
        return Str::after(Ui::icon($stored, 'medal'), 'icon.');
    }

    public static function auditAction(?string $action): string
    {
        return self::AUDIT_ACTIONS[(string) $action] ?? (string) $action;
    }

    /** Modellname (Klassenname mit oder ohne Namensraum). */
    public static function auditModel(?string $model): string
    {
        $name = class_basename((string) $model);

        return self::AUDIT_MODELS[$name] ?? $name;
    }

    public static function paymentStatus(?string $status): string
    {
        return self::PAYMENT_STATUS[(string) $status] ?? Str::ucfirst((string) $status);
    }

    public static function paymentMethod(?string $method): string
    {
        $method = (string) $method;

        return $method === '' ? 'Nicht angegeben' : (self::PAYMENT_METHODS[$method] ?? Str::ucfirst($method));
    }

    public static function eventType(?string $type): string
    {
        return self::EVENT_TYPES[(string) $type] ?? Str::ucfirst((string) $type);
    }
}
