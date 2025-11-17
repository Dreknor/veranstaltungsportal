# Organization System Migration

Diese Dokumentation beschreibt die Umstellung von einem User-basierten auf ein Organization-basiertes Veranstaltersystem.

## Übersicht

Das System wurde umgestellt von:
- **Alt:** Events gehören direkt zu einem User
- **Neu:** Events gehören zu Organizations, Organizations haben mehrere User als Team-Mitglieder

## Kernänderungen

### 1. Datenbank-Schema

#### Neue Tabellen:
- `organizations` - Speichert alle Organisationsdaten
- `organization_user` - Pivot-Tabelle für User-Organization-Beziehung mit Rollen

#### Geänderte Tabellen:
- `events` - Neue Spalte `organization_id`
- `event_series` - Neue Spalte `organization_id`

#### Migrations:
1. `2025_11_17_000001_create_organizations_table.php`
2. `2025_11_17_000002_create_organization_user_table.php`
3. `2025_11_17_000003_add_organization_id_to_events_table.php`
4. `2025_11_17_000004_migrate_organizers_to_organizations.php`
5. `2025_11_17_000005_add_organization_id_to_event_series_table.php`

### 2. Models

#### Neu:
- `App\Models\Organization` - Hauptmodel für Organisationen

#### Aktualisiert:
- `App\Models\User`
  - Neue Methoden: `organizations()`, `currentOrganization()`, `setCurrentOrganization()`, `isMemberOf()`, etc.
  
- `App\Models\Event`
  - Neue Methode: `organization()`
  - Helper-Methoden: `getOrganizerName()`, `getOrganizerEmail()`, etc.

- `App\Models\EventSeries`
  - Neue Spalte und Relationship: `organization_id`

### 3. Rollen-System

In der `organization_user` Pivot-Tabelle gibt es 3 Rollen:

- **owner** - Volle Kontrolle, kann Organisation löschen, Rollen verwalten
- **admin** - Kann Events verwalten, Team-Mitglieder einladen
- **member** - Nur Lesezugriff, kann bei Events helfen (z.B. Check-In)

### 4. Middleware

#### Neu:
- `EnsureHasOrganizationContext` - Stellt sicher, dass User eine aktive Organization ausgewählt hat

#### Geändert:
- `EnsureUserIsOrganizer` - Bleibt bestehen für grundlegende Organizer-Rolle-Prüfung

### 5. Policies

#### Neu:
- `OrganizationPolicy` - Kontrolle für Organization-Zugriff und Verwaltung

#### Aktualisiert:
- `EventPolicy` - Jetzt Organization-basiert statt User-basiert
  - Prüft ob User Mitglied der Event-Organization ist
  - Berücksichtigt Rollen (owner/admin können bearbeiten)

### 6. Controller

#### Neu:
- `Organizer\OrganizationController` - Verwaltung von Organizations und Teams

#### Aktualisiert:
- `Organizer\DashboardController` - Verwendet `currentOrganization()` statt `user`
- `Organizer\EventManagementController` - Events werden mit `organization_id` erstellt
- Weitere Organizer-Controller müssen entsprechend aktualisiert werden

### 7. Routes

Neue Route-Struktur:
```php
/organizer/organizations/select - Organization auswählen
/organizer/organizations/create - Neue Organization erstellen
/organizer/organizations/switch/{org} - Organization wechseln
/organizer/organization - Aktuelle Organization bearbeiten
/organizer/team - Team-Mitglieder verwalten
```

Alle bestehenden Organizer-Routen sind jetzt innerhalb einer `organization_context` Middleware-Gruppe.

## Migration bestehender Daten

Die Migration `2025_11_17_000004_migrate_organizers_to_organizations.php` übernimmt:

1. Findet alle User mit "organizer" Rolle
2. Erstellt für jeden eine Default-Organization
3. Kopiert Veranstalter-Daten vom User zur Organization:
   - `organization_name`, `organization_website`, `organization_description`
   - `organizer_billing_data`, `bank_account`, `payout_settings`
   - `billing_company`, `billing_address`, etc.
   - `invoice_settings`, `custom_platform_fee`
4. Verknüpft User als "owner" mit der Organization
5. Migriert alle Events des Users zur neuen Organization

## Session-Management

Der aktuelle Organization-Context wird in der Session gespeichert:
- Key: `current_organization_id`
- Automatische Auswahl der ersten Organization bei Login
- Wechsel zwischen Organizations über Organization-Switcher

## Verwendung im Code

### Organization des aktuellen Users abrufen:
```php
$organization = auth()->user()->currentOrganization();
```

### Organization wechseln:
```php
auth()->user()->setCurrentOrganization($organization);
```

### Events für Organization abrufen:
```php
$events = $organization->events()->get();
```

### Prüfen ob User Organization verwalten kann:
```php
if (auth()->user()->canManageOrganization($organization)) {
    // User ist Owner oder Admin
}
```

### Prüfen ob User Mitglied ist:
```php
if (auth()->user()->isMemberOf($organization)) {
    // User ist Mitglied
}
```

## Views (TODO)

Folgende Views müssen noch erstellt/angepasst werden:

1. `resources/views/organizer/organizations/select.blade.php`
2. `resources/views/organizer/organizations/create.blade.php`
3. `resources/views/organizer/organizations/edit.blade.php`
4. `resources/views/organizer/organizations/team.blade.php`
5. Navigation/Header - Organization-Switcher hinzufügen
6. Alle Organizer-Views - Referenzen zu User-Daten durch Organization-Daten ersetzen

## Weitere Anpassungen erforderlich

### Controller:
- `Organizer\StatisticsController` - Organization-basierte Statistiken
- `Organizer\BookingManagementController` - Organization-Filter
- `Organizer\SeriesController` - Organization-ID setzen
- `Organizer\ProfileController` - Evtl. Organization-Profil statt User-Profil
- `Organizer\BankAccountController` - Organization statt User
- `Organizer\InvoiceSettingsController` - Organization statt User

### Tests:
- Alle Tests aktualisieren, die Events erstellen
- Tests für Organization-Management hinzufügen
- Tests für Team-Verwaltung hinzufügen
- Tests für Organization-Switching hinzufügen

### Seeders/Factories:
- `OrganizationFactory` erstellen
- `EventFactory` mit `organization_id` aktualisieren

## Backwards Compatibility

Während der Übergangsphase:
- `user_id` bleibt in der `events` Tabelle erhalten
- Event-Model unterstützt beide Beziehungen (`user()` und `organization()`)
- Helper-Methoden wie `getOrganizerName()` funktionieren mit beiden Systemen

Nach vollständiger Migration kann `user_id` aus `events` entfernt werden.

## Ausführen der Migration

```bash
# Migrationen ausführen
php artisan migrate

# Falls Rollback nötig:
php artisan migrate:rollback --step=5
```

## Wichtige Hinweise

1. **Backup erstellen** vor der Migration!
2. Alle bestehenden Organizer erhalten automatisch eine Organization
3. User können mehrere Organizations haben und zwischen ihnen wechseln
4. User können gleichzeitig Organisator UND Teilnehmer sein
5. Die Rolle "organizer" bleibt bestehen für die grundlegende Berechtigung

