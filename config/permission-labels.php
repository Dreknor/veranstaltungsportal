<?php

/*
 * Deutsche Anzeigenamen für Rollen, Berechtigungen und Berechtigungsgruppen.
 * Die internen Schlüssel (Rollen-/Rechtenamen in der Datenbank) bleiben unverändert;
 * Zugriff in Views über App\Support\AccessLabels.
 */
return [
    'roles' => [
        'admin' => 'Administrator',
        'organizer' => 'Veranstalter',
        'user' => 'Teilnehmer',
        'moderator' => 'Moderator',
        'viewer' => 'Leser',
    ],

    'groups' => [
        'general' => 'Allgemein',
    ],

    'permissions' => [
        'access admin panel' => 'Admin-Bereich aufrufen',
        'cancel bookings' => 'Buchungen stornieren',
        'create bookings' => 'Buchungen anlegen',
        'create events' => 'Veranstaltungen anlegen',
        'create reviews' => 'Bewertungen verfassen',
        'create users' => 'Benutzer anlegen',
        'delete events' => 'Veranstaltungen löschen',
        'delete reviews' => 'Bewertungen löschen',
        'delete users' => 'Benutzer löschen',
        'edit events' => 'Veranstaltungen bearbeiten',
        'edit reviews' => 'Bewertungen bearbeiten',
        'edit users' => 'Benutzer bearbeiten',
        'export bookings' => 'Buchungen exportieren',
        'export reports' => 'Auswertungen exportieren',
        'feature events' => 'Veranstaltungen hervorheben',
        'manage all bookings' => 'Alle Buchungen verwalten',
        'manage categories' => 'Kategorien verwalten',
        'manage discount codes' => 'Rabattcodes verwalten',
        'manage own bookings' => 'Eigene Buchungen verwalten',
        'manage own events' => 'Eigene Veranstaltungen verwalten',
        'manage settings' => 'Einstellungen verwalten',
        'manage ticket types' => 'Tickettypen verwalten',
        'manage user roles' => 'Benutzerrollen verwalten',
        'moderate reviews' => 'Bewertungen moderieren',
        'receive-critical-log-notifications' => 'Meldungen zu kritischen Logs erhalten',
        'view audit log' => 'Audit-Log einsehen',
        'view bookings' => 'Buchungen ansehen',
        'view events' => 'Veranstaltungen ansehen',
        'view reports' => 'Auswertungen ansehen',
        'view reviews' => 'Bewertungen ansehen',
        'view statistics' => 'Statistiken ansehen',
        'view users' => 'Benutzer ansehen',
    ],

    /*
     * Rollenfarben: nur Töne aus der Palette der Richtlinien (A2). In roles.color steht der Schlüssel;
     * "tone" ist der Ton für <x-ui.status>.
     */
    'tones' => [
        'neutral' => ['label' => 'Grau', 'tone' => 'neutral'],
        'primary' => ['label' => 'Petrol', 'tone' => 'info'],
        'success' => ['label' => 'Grün', 'tone' => 'success'],
        'warning' => ['label' => 'Gelb', 'tone' => 'warning'],
        'danger' => ['label' => 'Rot', 'tone' => 'danger'],
    ],
];
