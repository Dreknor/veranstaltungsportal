# Nächste Schritte - Organization System Implementation

## Bereits erledigt ✅

1. ✅ Datenbank-Migrationen erstellt
2. ✅ Organization Model implementiert
3. ✅ User Model erweitert mit Organization-Relationships
4. ✅ Event und EventSeries Models aktualisiert
5. ✅ OrganizationPolicy erstellt
6. ✅ EventPolicy aktualisiert für Organization-basierte Authorization
7. ✅ OrganizationController implementiert
8. ✅ DashboardController aktualisiert
9. ✅ EventManagementController teilweise aktualisiert
10. ✅ Middleware EnsureHasOrganizationContext erstellt
11. ✅ Routen aktualisiert und Organization-Routen hinzugefügt
12. ✅ Basis-Views für Organization-Auswahl und -Erstellung

## Noch zu erledigen 📋

### 1. Migrationen ausführen
```bash
php artisan migrate
```

### 2. Restliche Organizer-Controller aktualisieren

Die folgenden Controller müssen noch angepasst werden:

- ✅ `Organizer\DashboardController` - Erledigt
- ⏳ `Organizer\EventManagementController` - Teilweise (nur index, create, store aktualisiert)
  - `edit()`, `update()`, `destroy()`, `duplicate()`, `cancel()` noch nicht angepasst
- ❌ `Organizer\StatisticsController` - Organization-basierte Stats
- ❌ `Organizer\BookingManagementController` - Organization-Filter
- ❌ `Organizer\SeriesController` - organization_id setzen
- ❌ `Organizer\ProfileController` - Organization-Profil statt User-Profil
- ❌ `Organizer\BankAccountController` - Organization statt User
- ❌ `Organizer\InvoiceSettingsController` - Organization statt User
- ❌ `Organizer\InvoiceController` - Organization-Kontext
- ❌ `Organizer\TicketTypeController` - Sollte bereits funktionieren (Event-basiert)
- ❌ `Organizer\DiscountCodeController` - Sollte bereits funktionieren (Event-basiert)
- ❌ `Organizer\CheckInController` - Policy-Check anpassen
- ❌ `Organizer\CertificateController` - Sollte bereits funktionieren (Event-basiert)
- ❌ `Organizer\ReviewController` - Organization-Filter

### 3. EventManagementController vervollständigen

Noch zu implementieren:
```php
// edit() - Prüfung ob Event zur aktuellen Organization gehört
// update() - Organization-basierte Validierung
// destroy() - Authorization über EventPolicy
// duplicate() - organization_id kopieren
// cancel() - Authorization
// downloadAttendees() - Authorization
// contactAttendees() - Authorization
```

### 4. Views erstellen/aktualisieren

#### Neu zu erstellen:
- `resources/views/organizer/organizations/edit.blade.php` - Organization-Einstellungen
- `resources/views/organizer/organizations/team.blade.php` - Team-Verwaltung

#### Zu aktualisieren:
- `resources/views/layouts/navigation.blade.php` - Organization-Switcher in Header
- `resources/views/organizer/dashboard.blade.php` - Organization-Name anzeigen
- `resources/views/organizer/events/index.blade.php` - Organization-Kontext
- `resources/views/organizer/events/create.blade.php` - Ggf. anpassen
- `resources/views/organizer/events/edit.blade.php` - Ggf. anpassen
- `resources/views/organizer/profile/edit.blade.php` - Organization-Daten statt User-Daten
- `resources/views/organizer/bank-account/index.blade.php` - Organization-Daten
- Alle anderen Organizer-Views entsprechend

### 5. Tests aktualisieren

```bash
# Alle bestehenden Tests durchsehen und aktualisieren
tests/Feature/
  - EventManagementTest.php
  - BookingTest.php
  - CheckInTest.php
  - SeriesTest.php
  etc.
```

Neue Tests erstellen:
```bash
tests/Feature/
  - OrganizationManagementTest.php
  - OrganizationTeamTest.php
  - OrganizationSwitchingTest.php
```

### 6. Seeders/Factories

```bash
# Erstellen:
database/factories/OrganizationFactory.php

# Aktualisieren:
database/factories/EventFactory.php - organization_id hinzufügen
database/seeders/DatabaseSeeder.php - Organizations seeden
```

### 7. Helper-Commands erstellen (Optional)

```php
// Artisan Command zum Migrieren einzelner User zu Organizations
php artisan make:command MigrateUserToOrganization

// Artisan Command zum Konsolidieren von User-Events unter einer Organization
php artisan make:command ConsolidateEventsToOrganization
```

### 8. Frontend/UI-Komponenten

- Organization-Switcher Dropdown in der Navigation
- Breadcrumbs mit aktueller Organization
- Team-Mitglieder Liste mit Rollen-Badges
- Einladungs-Formular für Team-Mitglieder
- Organization-Logo-Upload Interface

### 9. E-Mail-Benachrichtigungen (Optional)

Neue Mailable-Klassen:
- `OrganizationInvitation` - Einladung zu Organization
- `OrganizationRoleChanged` - Rolle wurde geändert
- `OrganizationMemberRemoved` - Entfernung aus Team

### 10. Permissions verfeinern (Optional)

Erweiterte Permissions für Organization-Rollen:
```php
// In OrganizationPolicy oder als separate Permissions
- 'view organization events'
- 'create organization events'
- 'edit organization events'
- 'delete organization events'
- 'manage organization team'
- 'manage organization billing'
- 'view organization statistics'
```

### 11. API-Endpoints (falls vorhanden)

Falls API existiert:
- Organization-Endpoints hinzufügen
- Event-Endpoints für Organization-Kontext anpassen
- Bearer-Token mit Organization-Scope

### 12. Dokumentation

- API-Dokumentation aktualisieren (falls vorhanden)
- Benutzerhandbuch für Organization-Verwaltung
- Admin-Handbuch für Multi-Org Support

## Prioritäten

### Hohe Priorität (Sofort):
1. Migrationen ausführen
2. EventManagementController vervollständigen
3. Basis-Views testen und vervollständigen
4. Critical Organizer-Controller aktualisieren

### Mittlere Priorität (Bald):
1. Alle restlichen Controller aktualisieren
2. Tests aktualisieren
3. Factories/Seeders
4. Navigation/UI-Komponenten

### Niedrige Priorität (Später):
1. E-Mail-Benachrichtigungen
2. Erweiterte Permissions
3. Optional Features
4. Dokumentation

## Testing-Checklist

Nach jeder Phase testen:

```bash
# 1. Migrationen
php artisan migrate:fresh --seed

# 2. Organization erstellen
- Als Organizer einloggen
- Neue Organization erstellen
- Organization auswählen

# 3. Events erstellen
- Event mit Organization erstellen
- Prüfen ob organization_id gesetzt ist

# 4. Team-Verwaltung
- Weiteren User zu Organization einladen
- Rollen ändern
- Member entfernen

# 5. Organization wechseln
- User zu mehreren Organizations hinzufügen
- Zwischen Organizations wechseln
- Prüfen ob richtige Events angezeigt werden

# 6. Permissions
- Als Admin: Alle Organizations sehen
- Als Owner: Volle Kontrolle über eigene Organization
- Als Admin (Org): Events verwalten
- Als Member: Nur lesen
```

## Bekannte Issues / Wichtige Hinweise

- Middleware Alias `organization_context` muss noch in `app/Http/Kernel.php` registriert werden (Datei derzeit nicht im Workspace sichtbar).
- Route-Auflistung leer -> Vermutlich fehlt Kernel oder Autoload; nach Registrierung sollte `php artisan route:list` wieder Routen anzeigen.
- Views für Organization (select, create, edit, team) wurden erstellt.
- Organization-Switcher Component hinzugefügt (`resources/views/components/organization-switcher.blade.php`).

## Rollback-Plan

Falls Probleme auftreten:
```bash
# Rollback der letzten 5 Migrationen
php artisan migrate:rollback --step=5

# Cache leeren
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Support

Bei Fragen oder Problemen:
1. Siehe `docs/ORGANIZATION_MIGRATION.md` für Details
2. Prüfe die Policy-Klassen für Authorization-Logik
3. Teste mit verschiedenen Rollen (owner, admin, member)
