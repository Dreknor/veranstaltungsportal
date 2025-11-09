# Veranstaltungsportal - Laravel Event Management System

Ein umfassendes Veranstaltungs- und Buchungssystem, entwickelt mit Laravel.

## ✨ Features

- 🎭 **Event-Management** - Erstellen und verwalten Sie Veranstaltungen
- 🎫 **Ticket-System** - Flexible Ticket-Typen mit unterschiedlichen Preisen
- 📝 **Buchungs-System** - Vollständiger Buchungsprozess mit Bestätigung
- 💰 **Zahlungs-Integration** - Verschiedene Zahlungsmethoden
- 📧 **E-Mail-Benachrichtigungen** - Automatische Bestätigungen und Erinnerungen
- 📄 **PDF-Generierung** - Tickets und Rechnungen als PDF
- 🔔 **Reminder-System** - Automatische Event-Erinnerungen
- 💵 **Platform-Fee Management** - Automatische Abrechnungen
- 🌐 **Online-Events** - Support für virtuelle Veranstaltungen
- ⭐ **Review-System** - Bewertungen und Feedback
- 📊 **Organizer-Dashboard** - Umfassendes Management-Interface
- 🔐 **Rollen & Permissions** - Admin, Organizer, User

## 🚀 Quick Start

### Installation

```bash
# Repository klonen
git clone <repository-url>
cd veranstaltungen.local

# Dependencies installieren
composer install
npm install

# Umgebung konfigurieren
cp .env.example .env
php artisan key:generate

# Datenbank erstellen und migrieren
php artisan migrate --seed

# Frontend bauen
npm run build

# Server starten
php artisan serve
```

### Test-Daten erstellen

```bash
# Event-Kategorien und Demo-Events erstellen
php artisan db:seed --class=EventCategorySeeder
php artisan db:seed --class=EventSeeder
```

## 🧪 Event Lifecycle Simulation

**NEU!** Simulieren Sie den kompletten Ablauf einer Veranstaltung mit einem einzigen Befehl:

```bash
# Komplette Simulation (ohne E-Mails)
php artisan events:simulate-lifecycle --no-emails

# Mit E-Mail-Versand
php artisan events:simulate-lifecycle

# Mit spezifischem Veranstalter
php artisan events:simulate-lifecycle --user=1 --no-emails

# Mit spezifischem Teilnehmer
php artisan events:simulate-lifecycle --participant=5 --no-emails

# Alles kombiniert
php artisan events:simulate-lifecycle --user=1 --participant=5 --days=14
```

Der Befehl simuliert:
1. ✅ Event-Erstellung
2. ✅ Ticket-Typen
3. ✅ Buchung durch Teilnehmer
4. ✅ Zahlungsbestätigung
5. ✅ E-Mail-Versand (Bestätigung, Zahlung, Erinnerung)
6. ✅ **Benachrichtigungen an Veranstalter** (Neue Buchung, Zahlung)
7. ✅ Event-Durchführung
8. ✅ Abrechnung mit Platform-Fee

**📖 Detaillierte Dokumentation:**
- [Vollständige Anleitung](docs/SIMULATE_EVENT_LIFECYCLE.md)
- [Quick Reference](docs/SIMULATE_EVENT_QUICK_REF.md)
- [Beispiel-Ausgabe](docs/SIMULATE_EVENT_OUTPUT_EXAMPLE.md)

## 📋 Artisan Commands

### Event-Management

```bash
# Event-Erinnerungen versenden (24h vorher)
php artisan events:send-reminders --hours=24

# Lifecycle-Simulation
php artisan events:simulate-lifecycle [--no-emails] [--user=ID] [--days=7]
```

### Abrechnungen

```bash
# Platform-Fee Rechnungen generieren
php artisan invoices:generate-event-invoices
```

### Maintenance

```bash
# Alte Benachrichtigungen löschen
php artisan notifications:cleanup

# Benutzer zu Rollen migrieren
php artisan users:migrate-to-roles
```

## 📁 Projekt-Struktur

```
app/
├── Console/Commands/     # Artisan Commands
├── Http/Controllers/     # Controller
├── Mail/                 # Mail Classes
├── Models/               # Eloquent Models
├── Notifications/        # Notification Classes
├── Observers/            # Model Observers
├── Services/             # Business Logic Services
└── helpers.php           # Helper Functions

resources/
├── views/
│   ├── events/          # Event Views
│   ├── bookings/        # Booking Views
│   ├── organizer/       # Organizer Dashboard
│   ├── emails/          # Email Templates
│   └── components/      # Blade Components

database/
├── migrations/          # Database Migrations
├── factories/           # Model Factories
└── seeders/            # Database Seeders

docs/                   # Dokumentation
tests/                  # Tests (Feature & Unit)
```

## 🗃️ Datenbank-Modelle

- **Event** - Veranstaltungen
- **EventCategory** - Kategorien
- **EventSeries** - Veranstaltungsreihen
- **TicketType** - Ticket-Typen
- **Booking** - Buchungen
- **BookingItem** - Gebuchte Tickets
- **Invoice** - Rechnungen
- **PlatformFee** - Plattform-Gebühren
- **EventReview** - Bewertungen
- **EventWaitlist** - Wartelisten
- **DiscountCode** - Rabattcodes
- **User** - Benutzer

## 🔧 Konfiguration

### Mail-Konfiguration (.env)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@veranstaltungen.local
MAIL_FROM_NAME="Veranstaltungsportal"
```

### Platform-Fee Einstellungen (config/monetization.php)

```php
'platform_fee_percentage' => 10,        // 10% Platform Fee
'payment_deadline_days' => 14,          // Zahlungsfrist
'auto_invoice' => true,                 // Automatische Rechnungserstellung
```

## 🧪 Testing

```bash
# Alle Tests ausführen
php artisan test

# Feature Tests
php artisan test --testsuite=Feature

# Unit Tests
php artisan test --testsuite=Unit

# Mit Coverage
php artisan test --coverage
```

## 📧 E-Mail Templates

Folgende E-Mails werden automatisch versendet:

| Template | Trigger | Empfänger |
|----------|---------|-----------|
| **BookingConfirmation** | Buchung erstellt | Teilnehmer |
| **PaymentConfirmed** | Zahlung bestätigt | Teilnehmer |
| **BookingCancellation** | Buchung storniert | Teilnehmer |
| **EventReminder** | 24h vor Event | Teilnehmer |
| **Platform Fee Invoice** | Event beendet | Veranstalter |

Alle Templates unterstützen:
- ✅ Online & Offline Events
- ✅ PDF-Anhänge (Tickets, Rechnungen)
- ✅ Responsive Design
- ✅ Bedingte Inhalte basierend auf Status

## 🔐 Rollen & Permissions

- **Admin** - Vollzugriff auf alle Funktionen
- **Organizer** - Event-Management, Buchungsverwaltung
- **User** - Event-Teilnahme, Buchungen

## 🛠️ Services

### InvoiceService
- Rechnung für Teilnehmer generieren
- Platform-Fee Rechnungen erstellen
- PDF-Generierung

### TicketPdfService
- Ticket-PDFs generieren
- QR-Codes für Check-In
- Multiple-Tickets in einem PDF

### QrCodeService
- QR-Codes für Tickets
- QR-Codes für Check-In
- Verschiedene Formate

## 📚 Weitere Dokumentation

- [TODO Liste](TODO.md) - Projektstatus und Roadmap
- [Development Summary](docs/DEVELOPMENT_SUMMARY.md) - Entwicklungs-Übersicht
- [Event Lifecycle Simulation](docs/SIMULATE_EVENT_LIFECYCLE.md) - Detaillierte Anleitung
- [API Documentation](docs/API.md) - API Endpoints

## 🤝 Contributing

Beiträge sind willkommen! Bitte erstellen Sie einen Pull Request.

## 📝 Lizenz

[Lizenz hier einfügen]

## 📞 Support

Bei Fragen oder Problemen öffnen Sie bitte ein Issue im Repository.

---

**Entwickelt mit ❤️ und Laravel**

