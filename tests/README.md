# Test-Dokumentation

## Übersicht

Diese Anwendung verfügt über umfassende Tests für alle Hauptfunktionen. Die Tests sind in Unit-Tests und Feature-Tests unterteilt.

## Test-Struktur

### Unit Tests (`tests/Unit/`)

Unit-Tests testen einzelne Klassen und Methoden isoliert:

- **Models/EventTest.php** - Tests für das Event-Model
- **Models/BookingTest.php** - Tests für das Booking-Model
- **Models/UserTest.php** - Tests für das User-Model
- **Models/TicketTypeTest.php** - Tests für das TicketType-Model
- **Models/DiscountCodeTest.php** - Tests für das DiscountCode-Model

### Feature Tests (`tests/Feature/`)

Feature-Tests testen komplette Workflows und Funktionen:

- **AuthenticationTest.php** - Login, Registrierung, Logout
- **BookingProcessTest.php** - Vollständiger Buchungsprozess
- **DashboardAccessTest.php** - Dashboard-Zugriff für verschiedene Benutzer
- **DiscountCodeManagementTest.php** - Rabattcode-Verwaltung
- **EmailTest.php** - E-Mail-Versand
- **EventCancellationTest.php** - Event-Stornierung
- **EventManagementTest.php** - Event-CRUD-Operationen
- **EventReviewTest.php** - Bewertungssystem
- **EventSearchAndFilterTest.php** - Suche und Filter
- **EventSeriesTest.php** - Event-Serien
- **FavoriteTest.php** - Favoriten-Funktionalität
- **MediaUploadTest.php** - Bild-Upload
- **NotificationTest.php** - Benachrichtigungssystem
- **PaymentTest.php** - Zahlungsabwicklung
- **PermissionsTest.php** - Berechtigungen und Rollen
- **ProfileManagementTest.php** - Profilverwaltung
- **ReportingTest.php** - Berichte und Statistiken
- **TicketManagementTest.php** - Ticket-Verwaltung
- **TicketPdfGenerationTest.php** - PDF-Ticket-Generierung
- **WaitlistTest.php** - Warteliste

## Tests ausführen

### Alle Tests ausführen

```bash
php artisan test
```

oder mit Pest:

```bash
vendor/bin/pest
```

### Nur Unit-Tests ausführen

```bash
php artisan test --testsuite=Unit
```

### Nur Feature-Tests ausführen

```bash
php artisan test --testsuite=Feature
```

### Bestimmte Test-Datei ausführen

```bash
php artisan test tests/Feature/BookingProcessTest.php
```

### Tests mit Coverage ausführen

```bash
php artisan test --coverage
```

Für detaillierte Coverage:

```bash
php artisan test --coverage --min=80
```

### Tests parallel ausführen

```bash
php artisan test --parallel
```

## Test-Datenbank

Die Tests verwenden eine separate SQLite-Datenbank in-memory. Die Konfiguration befindet sich in `phpunit.xml`.

## Factories

Für die Tests stehen folgende Factories zur Verfügung:

- **UserFactory** - Erstellt Test-Benutzer
- **EventFactory** - Erstellt Test-Events
- **BookingFactory** - Erstellt Test-Buchungen
- **BookingItemFactory** - Erstellt Buchungspositionen
- **TicketTypeFactory** - Erstellt Ticket-Typen
- **DiscountCodeFactory** - Erstellt Rabattcodes
- **EventCategoryFactory** - Erstellt Event-Kategorien
- **EventSeriesFactory** - Erstellt Event-Serien
- **EventReviewFactory** - Erstellt Bewertungen
- **EventWaitlistFactory** - Erstellt Wartelisten-Einträge
- **PlatformFeeFactory** - Erstellt Plattformgebühren

## Helper-Funktionen

In `tests/Pest.php` sind hilfreiche Funktionen definiert:

```php
createUser()         // Erstellt einen Test-User
createEvent()        // Erstellt ein Test-Event
createBooking()      // Erstellt eine Test-Buchung
createOrganizer()    // Erstellt einen Organisator
createAdmin()        // Erstellt einen Admin
```

## Best Practices

1. **Verwende beschreibende Test-Namen**: Jeder Test sollte klar beschreiben, was er testet
2. **Ein Test, eine Assertion**: Versuche, jeden Test auf eine Hauptfunktion zu fokussieren
3. **Arrange-Act-Assert**: Strukturiere Tests in Setup, Ausführung und Überprüfung
4. **Verwende Factories**: Nutze Factories statt manuelle Datenerstellung
5. **Teste Edge Cases**: Denke an Grenzfälle und Fehlerfälle

## Kontinuierliche Integration

Die Tests sollten vor jedem Commit ausgeführt werden. Stelle sicher, dass alle Tests erfolgreich durchlaufen.

## Fehlerbehebung

### Fehler: "Database does not exist"

```bash
php artisan migrate:fresh --env=testing
```

### Fehler: "Class not found"

```bash
composer dump-autoload
```

### Fehler bei Permission-Tests

```bash
php artisan config:clear
php artisan cache:clear
```

## Test-Coverage-Ziele

- **Gesamt**: Mindestens 80%
- **Models**: Mindestens 90%
- **Controllers**: Mindestens 75%
- **Services**: Mindestens 85%

## Neue Tests hinzufügen

1. Erstelle eine neue Test-Datei im entsprechenden Verzeichnis
2. Erweitere `TestCase` oder verwende Pest-Syntax
3. Nutze `RefreshDatabase` Trait für Datenbank-Tests
4. Füge aussagekräftige Kommentare hinzu
5. Führe die Tests aus und stelle sicher, dass sie erfolgreich sind

## Beispiel-Test

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_view_event()
    {
        // Arrange
        $event = Event::factory()->create(['is_published' => true]);

        // Act
        $response = $this->get(route('events.show', $event));

        // Assert
        $response->assertStatus(200);
        $response->assertSee($event->title);
    }
}
```

