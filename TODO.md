# Bildungsportal - Entwicklungs-Todo-Liste

## 🎯 Projektfokus & Zielgruppe

**Projektname:** Bildungsportal für Fort- und Weiterbildungen

**Zielgruppe:**
- Lehrkräfte an evangelischen Schulen in Sachsen
- Pädagogisches Personal an Bildungseinrichtungen
- Schulleitungen und Bildungsverantwortliche
- Fortbildungsanbieter und Referenten

**Schwerpunkte:**
- Aktion "Hauptfach Mensch" (https://www.ev-schulen-sachsen.de/hauptfach-mensch-1)
- Pädagogische Fort- und Weiterbildungen
- Fachliche und überfachliche Kompetenzentwicklung
- Vernetzung im Bildungsbereich
- Qualifizierte, zertifizierte Fortbildungsangebote

**Besonderheiten:**
- Fokus auf evangelische Schulen und deren Werte
- Ganzheitlicher Bildungsansatz (Kopf, Herz, Hand)
- Schulspezifische Themen und Bedarfe
- Netzwerkbildung unter Pädagog:innen

## ✅ Bereits Implementiert
- [x] Datenmodelle (Event, Booking, TicketType, EventCategory, etc.)
- [x] Migrations
- [x] Routing-Struktur
- [x] Home-View mit Hero-Section und Features
- [x] Event-Views (index, show, calendar, access)
- [x] Organizer Dashboard und Event-Views
- [x] **EventController - Vollständige Implementierung**
- [x] **BookingController - Vollständige Implementierung**
- [x] **Organizer/DashboardController**
- [x] **Organizer/EventManagementController**
- [x] **Organizer/BookingManagementController**
- [x] **Settings Controllers (Profile, Password, Appearance)**
- [x] **EventReviewController**
- [x] **Booking Views (create, show, verify)**
- [x] **Organizer Event Management Views (index, edit)**
- [x] **Organizer Booking Management Views (index, show)**
- [x] **Factories für alle Models (7 Factories)**
- [x] **Seeders (EventCategorySeeder, EventSeeder mit umfangreichen Test-Daten)**
- [x] **Feature Tests (Event, Booking, Organizer)**
- [x] **Unit Tests (Event)**
- [x] **Discount Code Validation API**
- [x] **Check-In System**
- [x] **Export-Funktionalität (CSV)**
- [x] **Email Templates (Buchungsbestätigung, Stornierung)**
- [x] **Mail Classes (BookingConfirmation, BookingCancellation)**
- [x] **Helper-Funktionen für häufige Aufgaben**
- [x] **API Dokumentation**
- [x] **Detaillierte README mit Installation**
- [x] **Development Summary**
- [x] **Welcome-Seite (Moderne Landing Page mit allen Features)**
- [x] **Review-System UI in Event Show View integriert**
- [x] **Icon-Komponenten (17 SVG-Icons für UI)**
- [x] **Veranstaltungskategorien für Bildungsbereich angepasst (15 Kategorien)**
- [x] **QR-Code Service (vollständig implementiert)**
- [x] **Ticket PDF Service (vollständig implementiert)**
- [x] **PDF-Ticket-Views (ticket.blade.php, tickets-multiple.blade.php)**
- [x] **Email-Anhänge für Tickets und Rechnungen**
- [x] **User Dashboard (DashboardController)**
- [x] **Favorites System (FavoriteController, Migration, Views)**
- [x] **Booking History, Upcoming & Past Events Views**

## 🔄 In Arbeit / Fehlend

### 1. Core Features (Hohe Priorität)
- [x] **QR-Code & PDF Generator**
  - [x] QR-Code Generator für Tickets
  - [x] PDF-Tickets mit QR-Code
  - [x] PDF-Download nach Buchung
  - [x] Email-Anhang (PDF-Ticket)
  - [x] QrCodeService implementiert
  - [x] TicketPdfService implementiert
  - [x] PDF-Views erstellt (ticket.blade.php, tickets-multiple.blade.php)
  - [x] Integration in BookingController
  - [x] Email-Anhänge in BookingConfirmation Mail

### 2. Benutzer Features
- [x] User Registration & Login (bereits vorhanden)
- [x] Password Reset (bereits vorhanden)
- [x] User Profile Management (bereits vorhanden)
- [x] Event Booking Flow (bereits vorhanden)
- [x] Booking Management (View, Cancel) (bereits vorhanden)
- [x] Review System (Event Reviews) (bereits vorhanden)
- [x] **User Dashboard mit Statistiken**
- [x] **Booking History View**
- [x] **Upcoming Events View**
- [x] **Past Events View**
- [x] **Wishlist/Favorites System**
- [x] **Favorite Toggle Button auf Event Show**
- [x] **Favorites View**
- [x] **Notification System (Email & In-App)**
  - [x] NotificationController implementiert
  - [x] Notification Views (index.blade.php)
  - [x] Notification Routes konfiguriert
  - [x] Database Migration für Notifications
  - [x] Notification Classes (BookingConfirmedNotification, EventReminderNotification, EventUpdatedNotification)
  - [x] In-App Notification Anzeige im User Dashboard
  - [x] Notification Settings im User Profile
  - [x] Scheduled Notifications (Event Reminders Command)
  - [x] Notification Read/Unread Status
  - [x] Event Observer für automatische Update-Benachrichtigungen
  - [x] Cleanup Command für alte Benachrichtigungen
  - [x] Event Reminder Email Template (event-reminder.blade.php)
  - [x] EventReminderMail Klasse implementiert
  - [x] Scheduler-Integration für automatische Erinnerungen
  - [x] Queue-Support für asynchronen Versand
  - [ ] Push Notifications (Web Push) - geplant
  - [ ] SMS-Benachrichtigungen - geplant
- [x] **Erweiterte Benutzerprofile**
  - [x] User Vor- und Nachname erfassen
  - [x] User Vorname und Nachname anzeigen
  - [x] User Profilbild Upload (max. 2MB, JPG/PNG/GIF)
  - [x] Profilbild-Anzeige im Dashboard Header
  - [x] Telefonnummer-Feld
  - [x] Bio/Beschreibungs-Feld (max. 1000 Zeichen)
  - [x] Gravatar-Integration als Fallback
  - [x] Profilbild löschen Funktion
  - [x] Migration für neue Profilfelder (first_name, last_name, profile_photo, phone, bio)
  - [x] fullName() und profilePhotoUrl() Methoden im User Model
  - [x] Storage Symlink für Profilbilder
- [x] **User Statistics Dashboard**
  - [x] Gesamtübersicht (Buchungen, Events, Stunden)
  - [x] Finanzübersicht
  - [x] Events nach Kategorie
  - [x] Monatliche Aktivitäts-Charts
  - [x] Jahresstatistiken
- [x] Discount Codes Anwendung beim Booking (bereits vorhanden)
- [x] Multi-Ticket Booking (bereits vorhanden)
- [x] **Mobile Responsive Design**
  - [x] Responsive Profilseite
  - [x] Responsive Dashboard
  - [x] Responsive Event-Views
  - [x] Mobile-optimierte Navigation
  - [x] Touch-freundliche UI-Elemente
- [x] Download von Tickets & Rechnungen
- [x] Download von Teilnahmezertifikaten
- [x] Kalender-Integration (Google Calendar, iCal)
- [x] **Automatisierte Erinnerungs-Emails an Teilnehmer**
  - [x] SendEventReminders Command
  - [x] EventReminderMail Klasse
  - [x] Event-Reminder Email-Template (professionelles Design)
  - [x] Scheduler-Konfiguration (24h und 3h vor Event)
  - [x] Integration mit Benachrichtigungseinstellungen
  - [x] Queue-Support für asynchronen Versand
  - [x] Opt-out Möglichkeit in Einstellungen
- [x] **User Settings vollständig**
  - [x] Profile Settings (Name, Email, Foto, Telefon, Bio)
  - [x] Password Settings
  - [x] Notification Preferences (6 verschiedene Optionen)
  - [x] Appearance Settings (Dark Mode)
  - [x] Account Deletion mit Bestätigung
 

### 3. Organizer Features
- [x] Organizer Registration & Login (verwendet is_organizer Flag)
- [x] Organizer Dashboard
  - [x] Organizer DashboardController implementiert
  - [x] Statistiken (Events, Buchungen, Umsatz)
  - [x] Upcoming Events Übersicht
  - [x] Recent Bookings Übersicht
- [x] Event Management (Create, Edit, Delete)
  - [x] Organizer EventManagementController implementiert
  - [x] Event Create View (create.blade.php)
  - [x] Event Edit View (edit.blade.php)
  - [x] Event Index View (index.blade.php) - erstellt
- [x] Booking Management (View, Export)
  - [x] Organizer BookingManagementController implementiert
  - [x] Booking Show View (show.blade.php)
  - [x] Booking Index View (index.blade.php) - erstellt
- [x] Ticket Type Management
  - [x] Organizer TicketTypeController implementiert
  - [x] CRUD Operationen für Ticket-Typen
  - [x] Ticket Type Reorder Funktion
- [x] Discount Code Management
  - [x] Organizer DiscountCodeController implementiert
  - [x] CRUD Operationen für Rabattcodes
  - [x] Toggle aktiv/inaktiv
  - [x] Code Generator
- [x] Event Reviews Management (über EventReviewController)
- [x] Check-In System (QR-Code Scanning)
  - [x] Check-In Funktion in BookingManagementController
- [ ] Organizer Profile Management
-  [ ] Event Reminder Emails
- [ ] Rechnungsanschrift
- [ ] Email Notifications für Organizer
- [ ] Event Statistics & Analytics
- [ ] Teilnehmerlisten Export (CSV, Excel)
- [ ] Event Promotion Tools (Share Links, Social Media)
- [ ] Anwesenheitszertifikate generieren und verwalten
- [ ] Kalender-Integration (Google Calendar, iCal)
- [ ] Automatisierte Erinnerungs-Emails an Teilnehmer
- [ ] Recurring Events (Wiederkehrende Events)



### 4. Admin Features
- [x] Admin Dashboard (Super-Admin)
  - [x] Admin DashboardController implementiert
  - [x] Admin Dashboard View (dashboard.blade.php)
  - [x] Statistiken (Benutzer, Events, Buchungen, Umsatz)
  - [x] Recent Users und Events Übersicht
- [x] User-Management (Admin)
  - [x] Admin UserManagementController implementiert
  - [x] Users Index View (index.blade.php)
  - [x] Users Edit View (edit.blade.php)
  - [x] User Suche und Filter
  - [x] Toggle Organizer/Admin Status
  - [x] User löschen
- [x] Event-Moderation
  - [x] Admin EventManagementController implementiert
  - [x] Events Index View (index.blade.php)
  - [x] Event Suche und Filter
  - [x] Toggle Publish/Featured Status
  - [x] Events löschen
- [x] Admin Middleware (AdminMiddleware.php)
- [x] Admin Routes konfiguriert
- [x] **Rollen & Berechtigungs-System (Spatie Permission)**
  - [x] Package installiert und konfiguriert
  - [x] Rollen erstellt (admin, organizer, user, moderator, viewer)
  - [x] 32 Berechtigungen definiert
  - [x] User Model mit HasRoles Trait erweitert
  - [x] Migration von is_admin zu Rollen-System
  - [x] RoleManagementController implementiert
  - [x] Role Management UI (index, edit)
  - [x] Permission Management UI
  - [x] User-Rollen-Zuweisung in Admin Panel
- [ ] Site-Wide Settings Management
- [ ] Kategorien-Management
- [ ] Preiseinstellungen (verschiedene Organizer-Gebühren)
- [ ] Reporting/Analytics
- [ ] System Settings
- [ ] Audit Log
- [ ] Support Ticket System


### 5. Erweiterte Features
- [ ] Multi-Language Support (i18n)
- [ ] Event-Duplikation (Clone Event)
- [ ] Warteliste für ausverkaufte Events
- [ ] Social Media Integration (Share Events)
- [ ] Event-Favoriten für User
- [ ] Notification System (In-App)
- [ ] Newsletter-Integration
- [ ] Analytics Dashboard (Google Analytics)
- [ ] SEO-Optimierung

### 6. Testing & Qualität
- [ ] Weitere Unit Tests für Models
  - [ ] Booking Model Tests
  - [ ] TicketType Model Tests
  - [ ] DiscountCode Model Tests
- [ ] Integration Tests
- [ ] Browser Tests (Laravel Dusk)
- [ ] API Tests mit Postman/Insomnia Collection
- [ ] Performance-Tests
- [ ] Security Audit
- [ ] Code Coverage > 80%

### 7. UI/UX Verbesserungen
- [x] Review-System UI in Event Show View integrieren
- [ ] Image Upload UI für Events verbessern (Drag & Drop)
- [ ] Organizer Event Create View komplettieren (Ticket-Typ-Formular inline)
- [ ] Responsive Design für Mobile optimieren
- [ ] Loading States & Animations
- [ ] Dark Mode Support
- [ ] Dashboard Charts/Graphs (Event-Statistiken)


### 8. Dokumentation
- [ ] User Manual (Endbenutzer)
- [ ] Organizer Guide
- [ ] Admin Guide
- [ ] Deployment Guide (Production)
- [ ] API Client Examples
- [ ] Video Tutorials

## 🐛 Bekannte Issues
1. ~~PDF-Layout könnte verbessert werden~~ (✅ Behoben)
2. ~~Mobile Responsive könnte verbessert werden~~
3. ~~Organizer Event Create View unvollständig~~
4. ~~Fehlende Tests für einige Models~~
5. ~~Fehlende Dokumentation für einige Features~~
6. ~~Laravel 11+ Middleware Compatibility Issue~~ (✅ Behoben - Alle Controller aktualisiert)
7. ~~Fehlende Icon-Komponenten (heart, academic, clock)~~ (✅ Behoben - Alle Icons erstellt)


## 💡 Feature-Ideen (Backlog)

- Event-Serien/Festivals mit mehreren Tagen
- Push-Notifications
- Event-Empfehlungen basierend auf User-Interesse
- Gamification (Badges für User)
- Social Features (User können sich connecten)
- Event-Streaming Integration (Hinterlegung von Online-Events)


