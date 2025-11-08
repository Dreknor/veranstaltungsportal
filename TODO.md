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
- [x] **Professionelle E-Mail-Templates mit vollständigem Inhalt**
  - [x] Buchungsbestätigung (confirmation.blade.php) - Vollständig überarbeitet
  - [x] Payment Confirmed (payment-confirmed.blade.php) - Neu erstellt
  - [x] Zahlungshinweise bei ausstehender Zahlung
  - [x] Anlagen-Übersicht in E-Mails
  - [x] Nächste Schritte je nach Zahlungsstatus
  - [x] Responsive E-Mail-Design
  - [x] Online-Event-Support (Zugangsdaten statt Tickets)
- [x] **Mail Classes (BookingConfirmation, BookingCancellation, PaymentConfirmed)**
  - [x] BookingConfirmation - Tickets nur bei paid Status
  - [x] PaymentConfirmed - Separate Mail für Ticket-Versand
  - [x] Conditional Attachments basierend auf payment_status
  - [x] Keine Tickets bei reinen Online-Events
- [x] **Online-Veranstaltungen Support**
  - [x] event_type Feld (physical, online, hybrid)
  - [x] online_url und online_access_code Felder
  - [x] Venue-Felder optional für Online-Events
  - [x] Event Model Methoden (isOnline, isHybrid, requiresVenue, etc.)
  - [x] Validierung in EventManagementController
  - [x] UI in Create/Edit Views mit dynamischen Sections
  - [x] Email-Templates zeigen Zugangsdaten statt/zusätzlich zu Venue
  - [x] Zugangsdaten nur nach Bezahlung sichtbar
  - [x] Migration ausgeführt
- [x] **Alle Event-Felder beim Bearbeiten verfügbar**
  - [x] Titelbild (featured_image) mit Vorschau
  - [x] Video-URL und Livestream-URL
  - [x] Preis ab und Max. Teilnehmer
  - [x] Veranstaltungsort (vollständig bearbeitbar)
  - [x] Online-Zugang (URL und Code)
  - [x] Veranstalter-Informationen (Info, Email, Telefon, Website)
  - [x] Alle Einstellungen (published, featured, private)
- [x] **Ticket-Versand nur nach Bezahlung**
  - [x] Logik in BookingConfirmation angepasst
  - [x] Separater Ticket-Versand nach Zahlungsbestätigung
  - [x] PaymentConfirmed Mail erstellt
  - [x] updatePaymentStatus erweitert für automatischen Ticket-Versand
  - [x] E-Mail wird automatisch versendet wenn Status auf "paid" gesetzt wird
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
  - [x] Event Duplikation (Clone Event)
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
- [x] **Organizer Profile Management**
  - [x] Organizer ProfileController implementiert
  - [x] Organizer Profile Edit View (edit.blade.php)
  - [x] Profilfelder (Vorname, Nachname, Email, Telefon, Bio)
  - [x] Organisationsfelder (Name, Website, Beschreibung)
  - [x] Profilbild Upload und Verwaltung
  - [x] Migration für organization_website Feld
- [x] **Email Notifications für Organizer**
  - [x] NewBookingNotification implementiert
  - [x] BookingCancelledNotification implementiert
  - [x] Integration in BookingController
  - [x] Mail & Database Benachrichtigungen
- [x] **Event Statistics & Analytics**
  - [x] Organizer StatisticsController implementiert
  - [x] Gesamtübersicht (Events, Buchungen, Umsatz, Tickets)
  - [x] Konversionsrate & Zahlungsstatus
  - [x] Monatliche Trends
  - [x] Top Events nach Umsatz und Teilnehmern
  - [x] Kategorie-Verteilung
  - [x] Event-spezifische Statistiken
  - [x] Ticket-Typ-Distribution
  - [x] Tägliche Buchungs-Trends
  - [x] Rabattcode-Nutzung
  - [x] Check-in-Rate
  - [x] Statistics Index View (index.blade.php)
  - [x] Event Statistics View (event.blade.php)
  - [x] Date Range Filter
- [x] **Teilnehmerlisten Export (CSV, Excel)**
  - [x] CSV Export mit UTF-8 BOM
  - [x] Excel Export (HTML-basiert)
  - [x] Detaillierte Exportdaten (19 Spalten)
  - [x] Filter-Support (Event, Status)
  - [x] Export-Buttons in Bookings Index
  - [x] Vorname/Nachname Splitting
  - [x] Ticket-basierter Export
  - [x] Check-in Status im Export
- [x] **Rechnungsanschrift**
  - [x] Migration für Billing-Felder (8 Felder)
  - [x] User Model erweitert (fillable)
  - [x] Organizer ProfileController Validierung
  - [x] Rechnungsadress-Sektion in Profile Edit View
  - [x] Firma/Institution, Adresse, PLZ, Stadt, Bundesland, Land, Steuernummer
- [x] **Event Promotion Tools (Share Links, Social Media)**
  - [x] SocialShareService implementiert
  - [x] Social-Share Component erstellt
  - [x] 6 Plattformen (Facebook, Twitter, LinkedIn, WhatsApp, Email, Telegram)
  - [x] Copy Link Funktion
  - [x] Integration in Event Show Views
- [x] **Anwesenheitszertifikate generieren und verwalten**
  - [x] CertificateService vollständig (bereits vorhanden)
  - [x] Certificate PDF Template (certificate.blade.php)
  - [x] Organizer CertificateController implementiert
  - [x] Individual & Bulk Certificate Generation
  - [x] Certificate Download & Email Versand
  - [x] Certificate Storage Management
  - [x] Migration für certificate_generated_at & certificate_path
  - [x] Booking Model erweitert
  - [x] Certificate Routes konfiguriert (6 Routes)
- [x] **Kalender-Integration (Google Calendar, iCal)**
  - [x] CalendarService vollständig implementiert
  - [x] iCal (.ics) File Generation
  - [x] Google Calendar URL Generator
  - [x] Outlook Calendar URL Generator
  - [x] Event iCal Export
  - [x] Booking iCal Export
  - [x] Calendar-Export Component (calendar-export.blade.php)
  - [x] Integration in Event Show Views
  - [x] Download-Funktionen in EventController & BookingController
  - [x] VTIMEZONE Support (Europe/Berlin)
  - [x] VALARM (24h Reminder)
  - [x] iCal String Escaping & Formatting

- [x] **Recurring Events & Veranstaltungsreihen**
  - [x] EventSeries Model erstellt
  - [x] Database Migration (event_series Tabelle)
  - [x] Recurrence Pattern System (daily, weekly, monthly, yearly)
  - [x] Recurrence Interval Support (alle X Tage/Wochen/Monate)
  - [x] Wochentag-Auswahl für wöchentliche Events
  - [x] Anzahl Termine oder Enddatum
  - [x] Template Data System für alle Events
  - [x] Auto-Generierung aller Events in Serie
  - [x] Series Position Tracking
  - [x] Exception Handling (Modified Events)
  - [x] Events-Relationship zum Series Model
  - [x] Series-Relationship zum Event Model
  - [x] SeriesController (CRUD + Spezialfunktionen)
  - [x] EventSeriesPolicy implementiert
  - [x] Series Index View (series/index.blade.php)
  - [x] Series Create View (series/create.blade.php)
  - [x] Series Show View (series/show.blade.php)
  - [x] Series Edit View (series/edit.blade.php)
  - [x] 8 neue Routes konfiguriert
  - [x] Migration ausgeführt

### ✅ ALLE ORGANIZER FEATURES 100% VOLLSTÄNDIG!

Verbleibend (nur noch optionale "Nice-to-have" Features):



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
- [x] **Kategorien-Management**
  - [x] Admin CategoryManagementController implementiert
  - [x] Categories Index View (index.blade.php)
  - [x] Categories Create View (create.blade.php)
  - [x] Categories Edit View (edit.blade.php)
  - [x] CRUD Operationen für Kategorien
  - [x] Kategorie Aktivieren/Deaktivieren
  - [x] Kategorie Suche und Filter
  - [x] Kategorie Icon & Farbe
- [x] **Site-Wide Settings Management**
  - [x] Setting Model erstellt
  - [x] Settings Migration (key-value Storage)
  - [x] Admin SettingsController implementiert
  - [x] Settings Index View mit Gruppen-Navigation
  - [x] Type-System (string, boolean, integer, json)
  - [x] Gruppen-System (general, email, booking, platform, appearance)
  - [x] Cache-Integration für Performance
  - [x] Default Settings Initialisierung (12 Einstellungen)
  - [x] settings() Helper-Funktion
  - [x] Public/Private Settings
  - [x] Settings Routes konfiguriert (5 Routes)
- [ ] Preiseinstellungen (verschiedene Organizer-Gebühren)
- [ ] Reporting/Analytics
- [ ] System Settings
- [ ] Audit Log
- [ ] Support Ticket System


### 5. Erweiterte Features
- [ ] Multi-Language Support (i18n)
- [x] **Event-Duplikation (Clone Event)**
  - [x] Duplicate-Methode in EventManagementController
  - [x] Duplizierung von Event-Daten
  - [x] Duplizierung von Ticket-Typen
  - [x] Duplizierung von Rabattcodes
  - [x] Duplizieren-Button in Event-Liste
  - [x] Route konfiguriert
- [x] **Warteliste für ausverkaufte Events**
  - [x] EventWaitlist Model erstellt
  - [x] Database Migration (event_waitlist Tabelle)
  - [x] WaitlistController implementiert
  - [x] Join/Leave Waitlist Funktionen
  - [x] Status-Tracking (waiting, notified, converted, expired)
  - [x] Notification System (48h Buchungsfrist)
  - [x] Waitlist-Join Component (waitlist-join.blade.php)
  - [x] Integration in Event Show Views
  - [x] Auto-Hide Ticket-Button wenn ausverkauft
  - [x] Organizer Waitlist Management
  - [x] Notify Next Person Funktion
  - [x] Routes konfiguriert
- [x] **Social Media Integration (Share Events)**
  - [x] SocialShareService erstellt
  - [x] Facebook Share
  - [x] Twitter Share
  - [x] LinkedIn Share
  - [x] WhatsApp Share
  - [x] Email Share
  - [x] Telegram Share
  - [x] Copy Link Funktion
  - [x] Social-Share Component (social-share.blade.php)
  - [x] Integration in Event Show Views
  - [x] Responsive Design mit Icons
  - [x] Open Graph Meta Tags
  - [x] Twitter Cards
  - [x] Schema.org Structured Data (JSON-LD)
  - [x] Meta-Tags Component (meta-tags.blade.php)
- [ ] Event-Favoriten für User (bereits implementiert als Favorites)
- [ ] Notification System (In-App) (bereits implementiert)
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
8. ~~User Dropdown-Menü öffnet sich nicht~~ (✅ Behoben - 'hidden' Klasse entfernt, x-cloak hinzugefügt)
9. ~~ParseError in meta-tags.blade.php (JSON-LD Syntax)~~ (✅ Behoben - Optionale Felder korrekt positioniert)


## 💡 Feature-Ideen (Backlog)

- Push-Notifications
- Event-Empfehlungen basierend auf User-Interesse
- Gamification (Badges für User)
- Social Features (User können sich connecten)
- Event-Streaming Integration (Hinterlegung von Online-Events)


