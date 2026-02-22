# Optimierungsplan – Bildungsportal Fort- und Weiterbildungen

> **Stand:** Februar 2026 | **Analysiert:** Laravel 12, PHP 8.2, Tailwind 4, Alpine.js
>
> **Legende Aufwand:** S = < 1h | M = 1–4h | L = 4–16h | XL = > 16h
> **Legende Priorität:** ⭐⭐⭐⭐⭐ = kritisch | ⭐⭐⭐⭐ = hoch | ⭐⭐⭐ = mittel | ⭐⭐ = niedrig | ⭐ = nice-to-have

---

## 🔴 1. Sicherheit – Kritische & wichtige Probleme

### 1.1 Mailtrap-Credentials in `.env.example` entfernen ⭐⭐⭐⭐⭐ | Aufwand: S
**Problem:** `.env.example` enthält echte Mailtrap-API-Credentials (`MAIL_USERNAME=fb7319ea484eaf`, `MAIL_PASSWORD=745f3b11f827a8`).  
**Risiko:** Diese werden beim `git clone` öffentlich mitgegeben; jeder kann E-Mails über das Mailtrap-Konto einsehen.  
**Fix:** Credentials mit Platzhaltern ersetzen, z. B. `MAIL_USERNAME=your-mailtrap-username`.  
**Datei:** `.env.example`, Zeile 50–51

### 1.2 CSP `unsafe-inline` / `unsafe-eval` durch Nonce-basiertes CSP ersetzen ⭐⭐⭐⭐ | Aufwand: L
**Problem:** `SecurityHeadersMiddleware.php` erlaubt `'unsafe-inline'` und `'unsafe-eval'` im `script-src`. Das macht die CSP bei XSS-Angriffen weitgehend wirkungslos.  
**Ursache:** Alpine.js benötigt kein `unsafe-eval`. Nur Inline-Event-Handler könnten ein Problem sein, aber diese können mit Nonces gelöst werden.  
**Fix:**
- Laravel Nonce-Middleware implementieren (Nonce pro Request generieren, an Blade-Template übergeben)
- `unsafe-eval` vollständig entfernen (Alpine.js funktioniert ohne es)
- `unsafe-inline` durch `'nonce-{zufälliger-nonce}'` ersetzen
- Alle `<script>`-Tags in Blade-Templates mit `@nonce` versehen  
**Datei:** `app/Http/Middleware/SecurityHeadersMiddleware.php`, Zeile 49–60

### 1.3 `Log::info()` mit sensitiven Buchungsdaten im ProductionCode entfernen ⭐⭐⭐⭐ | Aufwand: S
**Problem:** `BookingController::store()` loggt vollständige Ticket-Rohdaten aus dem Request mit `Log::info('Booking store - Incoming tickets data', ['tickets' => $request->tickets, ...])`.  
**Risiko:** Personenbezogene Daten (Namen, E-Mails, Zahlungsmethoden) landen in Logdateien. DSGVO-Problem.  
**Fix:** Debug-Logs entweder komplett entfernen oder hinter `if (config('app.debug'))` / `Log::debug()` stellen. In Production sollte das Log-Level mindestens `warning` sein.  
**Datei:** `app/Http/Controllers/BookingController.php`, ca. Zeile 130

### 1.4 Fehlende CSRF-Verifikation beim Booking-Storno ⭐⭐⭐ | Aufwand: S
**Problem:** `POST /bookings/{bookingNumber}/cancel` ist ohne Auth-Middleware erreichbar. Jeder kann mit einer `bookingNumber` eine Buchung stornieren, wenn er die Buchungsnummer kennt (Brute-Force).  
**Fix:** Rate-Limiting verschärfen (aktuell `throttle:10,1`), zusätzlich einen HMAC-signierten Token für Gastnutzer-Stornierungen implementieren oder Email-Verifikation vor Stornierung fordern.  
**Datei:** `routes/web.php`, Zeile 88

### 1.5 `APP_KEY` leer in `.env.example` ⭐⭐⭐⭐ | Aufwand: S
**Problem:** `.env.example` lässt `APP_KEY=` leer. In automatisierten Deployments ohne manuelles `php artisan key:generate` wird die App mit einem leeren Key betrieben.  
**Fix:** Deployment-Dokumentation ergänzen oder `APP_KEY=base64:GENERATE_WITH_PHP_ARTISAN_KEY_GENERATE` als Hinweis eintragen. CI-Workflow prüft dies bereits.

### 1.6 PayPal `client_secret` im Klartext in DB-Queries sichtbar ⭐⭐⭐ | Aufwand: M
**Problem:** `Organization::paypal_client_secret` ist zwar mit `'encrypted'` Cast definiert, aber der `paypal_client_id` ist unverschlüsselt. Wenn Datenbank-Backups kompromittiert werden, sind Live-PayPal-Credentials exponiert.  
**Empfehlung:** Beide PayPal-Credentials sollten mit dem `encrypted` Cast gespeichert werden. `paypal_client_id` ebenfalls auf `'encrypted'` setzen.  
**Datei:** `app/Models/Organization.php`, Zeile 56

### 1.7 Fehlende Autorisierung bei Download-Endpoints ⭐⭐⭐⭐ | Aufwand: M
**Problem:** Folgende Routes fehlen eine explizite Auth-Überprüfung oder nutzen nur `bookingNumber` als "Sicherheit":
- `GET /bookings/{bookingNumber}/ticket` → Ticket-PDF-Download
- `GET /bookings/{bookingNumber}/invoice` → Rechnungs-Download
- `GET /bookings/{bookingNumber}/certificate` → Zertifikat-Download

**Risiko:** Jeder mit einer Buchungsnummer (vorhersehbares Format `BK-` + `strtoupper(uniqid())`) kann Dokumente herunterladen.  
**Fix:** Policy hinzufügen: Nur der Buchungsinhaber (via `user_id`) oder eine verifizierte E-Mail-Session darf downloaden.  
**Datei:** `app/Http/Controllers/BookingController.php`

### 1.8 Fehlender Rate-Limit für Discount-Code-Validierung ⭐⭐⭐ | Aufwand: S
**Problem:** `POST /api/validate-discount-code` hat `throttle:30,1` (30 Anfragen/Minute). Ein Angreifer kann systematisch alle Codes durchprobieren.  
**Fix:** Rate-Limit auf `10,1` senken + per User/IP kombiniert begrenzen.  
**Datei:** `routes/web.php`, Zeile 115

---

## 🟡 2. Performance-Optimierungen

### 2.1 N+1-Query-Problem in EventController::index beheben ⭐⭐⭐⭐ | Aufwand: M
**Problem:** `EventController::index` lädt `->with(['category', 'organization.users', 'dates'])`. Das Laden aller `organization.users` für jeden Event in der Liste ist unnötig – auf der Listen-Seite wird nur der Organisationsname gebraucht.  
**Fix:** `->with(['category', 'organization', 'dates'])` statt `organization.users`. Nur im `show`-View die Users laden. Zusätzlich einen `withCount('bookings')` für die Kapazitätsanzeige verwenden.  
**Datei:** `app/Http/Controllers/EventController.php`, Zeile 14

### 2.2 HTTP-Response-Caching für öffentliche Event-Seiten ⭐⭐⭐ | Aufwand: M
**Problem:** Die öffentliche Events-Liste und Event-Detailseiten werden bei jedem Request vollständig neu gerendert.  
**Fix:**
- `Cache::remember()` für öffentliche Event-Abfragen (5 Minuten)
- HTTP-Cache-Headers (`Cache-Control`, `ETag`) für GET-Requests ohne Auth
- Optional: `spatie/laravel-responsecache` Package installieren  
**Vorteil:** Deutliche Last-Reduzierung bei Peaks (z.B. Kampagnenstart).

### 2.3 DomPDF-Generierung in Queue-Jobs auslagern ⭐⭐⭐ | Aufwand: M
**Problem:** Ticket-PDFs und Rechnungs-PDFs werden synchron im Request-Cycle generiert (`TicketPdfService`, `InvoiceService`). DomPDF ist langsam bei komplexen HTML-Templates.  
**Fix:**
- `GenerateTicketPdfJob` und `GenerateInvoicePdfJob` als dispatchable Jobs erstellen
- Nach Buchungsabschluss: Job in Queue einreihen, User per E-Mail benachrichtigen wenn fertig
- Zwischenzustand "Dokument wird erstellt..." in UI anzeigen  
**Dateien:** `app/Services/TicketPdfService.php`, `app/Services/InvoiceService.php`

### 2.4 Fehlende Datenbankindizes für häufige Filter-Queries ⭐⭐⭐⭐ | Aufwand: M
**Problem:** Folgende Spalten werden regelmäßig in WHERE-Klauseln genutzt, haben aber wahrscheinlich keine Indizes:
- `events.venue_city` (Stadt-Filter in EventController)
- `events.start_date` (Datum-Filter, überall verwendet)
- `events.is_published`, `events.is_featured` (häufige Status-Filter)
- `bookings.payment_status`, `bookings.status`
- `events.event_category_id` (Kategorie-Filter)

**Fix:** Migration erstellen mit zusammengesetzten Indizes:
```php
$table->index(['is_published', 'start_date', 'event_category_id']);
$table->index(['payment_status', 'status']);
```
**Aufwand:** Analyze existing queries with `EXPLAIN` first.

### 2.5 Bilder-Optimierung: WebP-Konvertierung und Lazy Loading ⭐⭐⭐ | Aufwand: L
**Problem:** Event-Bilder werden ohne Komprimierung/Größenanpassung gespeichert (Spatie MediaLibrary ist vorhanden aber unklar ob Konvertierungen konfiguriert sind). Keine Lazy-Loading-Attribute auf `<img>`-Tags erkennbar.  
**Fix:**
- Spatie MediaLibrary Conversions für Events konfigurieren: Thumbnail (400x300), Medium (800x600), WebP-Varianten
- `loading="lazy"` zu allen Event-Vorschaubildern hinzufügen
- Dateigrößenlimit für Uploads in Controller validieren (max 2MB für Events)

### 2.6 Eager Loading in Admin-Reporting-Controller ⭐⭐⭐ | Aufwand: M
**Problem:** Admin-Reporting und Admin-Dashboard laden wahrscheinlich `User`- und `Event`-Daten ohne optimiertes Eager Loading (Code nicht vollständig analysiert, aber 21 Admin-Controller sind ein Risiko).  
**Fix:** Alle Admin-Controller auf N+1-Probleme prüfen mit Laravel Debugbar (nur Dev-Umgebung: `barryvdh/laravel-debugbar`) und dann gezielt beheben.

---

## 🟢 3. SEO-Verbesserungen

### 3.1 Fehlende OG-Default-Bilder erstellen ⭐⭐⭐⭐⭐ | Aufwand: S
**Problem:** `meta-tags.blade.php` referenziert `asset('images/og-default.jpg')`, aber die Datei fehlt im `public/images/`-Verzeichnis. Alle Seiten ohne Event-Bild zeigen einen broken Image-Link in Social Media.  
**Fix:** 
- `og-default.jpg` (1200×630px) erstellen: Plattform-Logo + Beschreibungstext + Hintergrundfarbe
- Zusätzlich `logo.png` und `favicon.ico` in korrekten Größen anlegen
- Twitter Card `summary_large_image` benötigt Bild ≥ 300×157px  
**Datei:** `public/images/` (Verzeichnis befüllen)

### 3.2 Canonical URLs für paginierte Seiten ⭐⭐⭐ | Aufwand: S
**Problem:** Events-Liste hat Pagination, aber keine `<link rel="canonical">` Tags. Google indexiert `/events?page=2` ohne Canonical als separate URL.  
**Fix:** `<link rel="canonical" href="{{ url()->current() }}">` in alle paginierten Seiten einfügen. Bei Filtern zusätzlich `rel="noindex"` für gefilterte Views erwägen.

### 3.3 Strukturierte Daten für Kurse/Bildungsveranstaltungen ⭐⭐⭐ | Aufwand: M
**Problem:** Schema.org nutzt `Event`-Type, aber für Fortbildungskurse ist `Course` oder `EducationEvent` (Untertype von Event) präziser. Google kann Kurse als "Learning Resources" hervorheben.  
**Fix:** Schema.org `EducationEvent` implementieren mit:
- `educationLevel`: "Lehrkräfte", "Pädagogen"
- `teaches`: Fortbildungsthema
- `provider`: Organization
- `courseCode`: Wenn Fortbildungspunkte vergeben werden  
**Datei:** `resources/views/components/meta-tags.blade.php`

### 3.4 Hreflang-Tags für sprachliche Optimierung ⭐⭐ | Aufwand: S
**Problem:** Seite ist ausschließlich auf Deutsch, aber `hreflang="de"` und `hreflang="x-default"` fehlen.  
**Fix:** In `meta-tags.blade.php` hinzufügen:
```html
<link rel="alternate" hreflang="de" href="{{ url()->current() }}" />
<link rel="alternate" hreflang="x-default" href="{{ url()->current() }}" />
```

### 3.5 Google Search Console einrichten und Sitemaps einreichen ⭐⭐⭐⭐ | Aufwand: S
**Problem:** Sitemaps sind technisch generiert, aber nicht bei Google Search Console eingereicht.  
**Fix:**
1. Google Search Console verifizieren (Meta-Tag in Layout oder DNS-TXT-Eintrag)
2. Sitemap-Index unter `sitemap.xml` einreichen
3. Indexierungsstatus überwachen


---

## 🔵 4. UX & Accessibility

### 4.1 Cookie-Consent-Banner implementieren ⭐⭐⭐⭐⭐ | Aufwand: L
**Problem:** reCAPTCHA v3 lädt Google-Scripts ohne vorherige Einwilligung. PayPal-Scripts ebenfalls. Das ist ein klarer DSGVO-Verstoß.  
**Fix:**
- Cookie-Consent-Banner mit Kategorien: Notwendig, Funktional, Analytics
- reCAPTCHA und PayPal erst nach Zustimmung laden
- Einwilligung in LocalStorage/Cookie speichern
- Empfehlung: `orestbida/cookieconsent` (leichtgewichtig, ~6KB)  
**Hinweis:** Auch ohne proprietäre CDNs: Google Fonts werden in CSS geladen → DSGVO-Risiko (IP-Übermittlung).

### 4.2 Accessibility (WCAG 2.1 AA) verbessern ⭐⭐⭐⭐ | Aufwand: L
**Problem:** Keine erkennbaren Accessibility-Maßnahmen im Code. Spezifische Issues:
- Fehlende `aria-label` auf Icon-Buttons (z. B. Notification-Bell, Social-Share-Buttons)
- Fehlende `alt`-Attribute auf dynamischen Bildern
- Unklarer Fokus-Indicator in Tailwind CSS (standardmäßig oft versteckt)
- Formular-Fehlermeldungen nicht mit `role="alert"` oder `aria-describedby` verknüpft
- Kontrastprobleme in Dark Mode (ungeprüft)

**Fix:**
- Axe-Tool (Browser-Extension) durchlaufen lassen
- Alle `<button>`-Tags mit Icons: `aria-label` hinzufügen
- Alle `<img>`-Tags: Alt-Texte mit Event-Titel befüllen
- Skip-to-content-Link am Seitenanfang  
**Datei:** Alle Blade-Views in `resources/views/`


### 4.34 Buchungs-Flow: Fortschrittsanzeige ⭐⭐⭐ | Aufwand: M
**Problem:** Der Buchungs-Flow (Tickets wählen → Daten eingeben → Bezahlen) hat keine sichtbare Fortschrittsanzeige.  
**Fix:** Step-Indicator-Komponente (3 Schritte) oben im Buchungsformular einbauen. Alpine.js-basierter Multi-Step-Form-Wizard.  
**Datei:** `resources/views/bookings/create.blade.php`

### 4.4 Fehler-Feedback bei langen Formularen verbessern ⭐⭐⭐ | Aufwand: M
**Problem:** Bei Laravel-Validierungsfehlern springt die Seite ans Formular-Ende, Fehler an einzelnen Feldern sind in langen Formularen (Event-Erstellung: ~50 Felder) schwer zu finden.  
**Fix:**
- Fehler-Zusammenfassung am Formular-Anfang ("`Es sind Fehler aufgetreten: Bitte Felder X, Y, Z korrigieren`")
- Fokus automatisch auf erstes fehlerhaftes Feld setzen (Alpine.js)
- Live-Validierung für kritische Felder (Email, Datum) per AJAX

### 4.5 Mobile-Navigation verbessern ⭐⭐⭐ | Aufwand: M
**Problem:** Bei komplexem Organizer-Dashboard mit vielen Menüpunkten ist die mobile Seitenleiste wahrscheinlich schwer navigierbar (nicht im Detail analysiert).  
**Fix:** 
- Bottom-Navigation-Bar für Mobile (die 5 wichtigsten Aktionen)
- Touch-Targets mindestens 44×44px (WCAG 2.5.5)
- Swipe-Geste zum Schließen von Sidebars

### 4.6 Buchungsbestätigung – Direkte Ticket-Download-Option ⭐⭐⭐ | Aufwand: S
**Problem:** Nach Buchung wird der User zur Buchungsübersicht weitergeleitet. Der PDF-Ticket-Download ist nicht direkt sichtbar/hervorgehoben.  
**Fix:** Auf der Bestätigungsseite (`bookings/show.blade.php`) den Ticket-Download-Button prominent als primäre Aktion platzieren (nicht versteckt in sekundären Links).

---

## 🟣 5. Code-Qualität & Refactoring

### 5.1 Doppelte View-Dateien für Auth/Guest konsolidieren ⭐⭐⭐ | Aufwand: L
**Problem:** Für Events und Kalender gibt es jeweils zwei Views:
- `events.index` und `events.index-auth`
- `events.calendar` und `events.calendar-auth`
- `events.show` und `events.show-auth`

Das sind 6 Blade-Dateien, die ähnlichen Code duplizieren.  
**Fix:** Eine View mit `@auth` / `@guest` Blade-Direktiven. Den Controller-Code vereinfachen.  
**Dateien:** `resources/views/events/`

### 5.2 `Booking`-Model ohne `SoftDeletes` ⭐⭐⭐⭐ | Aufwand: M
**Problem:** `Booking`-Model hat kein `SoftDeletes`-Trait. Buchungen, die gelöscht werden, sind unwiederbringlich weg. Das ist ein DSGVO-Problem (Aufbewahrungspflicht für Rechnungen: 10 Jahre!) und ein Audit-Problem.  
**Fix:**
- `SoftDeletes` zu `Booking`-Model hinzufügen
- Migration: `deleted_at`-Spalte zur `bookings`-Tabelle
- Prüfen: Gibt es an irgendeiner Stelle `Booking::delete()`?  
**Datei:** `app/Models/Booking.php`


### 5.3 `helpers.php` dokumentieren und testen ⭐⭐ | Aufwand: M
**Problem:** `app/helpers.php` wird global via Composer autoload eingebunden, aber der Inhalt und die Testabdeckung dieser Hilfsfunktionen sind unbekannt.  
**Fix:** Alle Helper-Funktionen mit PHPDoc dokumentieren und Unit Tests in `tests/Unit/HelpersTest.php` anlegen.

### 5.4 Konsistente Fehlerbehandlung in Services ⭐⭐⭐ | Aufwand: L
**Problem:** In `InvoiceService.php` werden Fehler mit `Log::warning()` protokolliert und `null` zurückgegeben. An anderen Stellen werden Exceptions geworfen. Keine einheitliche Strategie.  
**Fix:** Custom Exception-Klassen erstellen:
- `App\Exceptions\BookingException`
- `App\Exceptions\InvoiceException`
- `App\Exceptions\PaymentException`  
Einheitliches Exception-Handling im `Handler.php`.

---

## ⚫ 6. Testing & CI/CD

### 6.1 Integration Tests für Buchungs-PayPal-Callback-Flow ⭐⭐⭐⭐ | Aufwand: L
**Problem:** Der komplexeste Flow der Anwendung (Buchung → PayPal → Webhook → Buchungsbestätigung → Ticket-E-Mail) hat wahrscheinlich keinen End-to-End-Test.  
**Fix:** Feature-Test mit gemocktem PayPal-Service:
- Buchung erstellen
- PayPal-Order mocken
- Webhook simulieren
- Buchungsstatus-Änderung prüfen
- E-Mail-Versand prüfen (Mail::fake())  
**Datei:** `tests/Feature/PayPalCheckoutTest.php` (ggf. erweitern)

### 6.2 Tests für DSGVO-Datenlöschung ⭐⭐⭐ | Aufwand: M
**Problem:** Ob die DSGVO-Datenlöschung wirklich alle personenbezogenen Daten entfernt, wird vermutlich nicht getestet.  
**Fix:** Test: Benutzer beantragt Löschung → Verifiziere, dass alle PII-Felder anonymisiert/gelöscht sind. Alle verknüpften Tabellen (Bookings, AuditLogs, etc.) prüfen.

### 6.6 Performance-Regression-Tests ⭐⭐ | Aufwand: L
**Problem:** Keine Performance-Benchmarks für kritische Datenbankabfragen.  
**Fix:** `tests/Performance/` Verzeichnis mit DB-Query-Count-Assertions:
```php
DB::enableQueryLog();
// Aktion ausführen
$this->assertCount(5, DB::getQueryLog()); // Maximal 5 Queries erwartet
```

---

## 🔶 7. DSGVO & Compliance

### 7.1 Cookie-Consent vor reCAPTCHA / Analytics-Skripten ⭐⭐⭐⭐⭐ | Aufwand: L
**Problem:** reCAPTCHA v3 wird auf allen kritischen Seiten geladen, ohne dass der Nutzer eine informierte Einwilligung für Google-Tracking gegeben hat.  
**Risiko:** Bußgeld bis 4% des Jahresumsatzes bzw. bis zu 20 Mio. EUR (DSGVO Art. 83).  
**Fix:** (Siehe auch 4.1 - Cookie Consent Banner)
- reCAPTCHA v3 nur nach Einwilligung laden
- Alternative: reCAPTCHA v3 durch Honeypot-Technik + IP-Throttling ersetzen (kein Cookie-Consent nötig)

### 7.2 Admin-Interface für Datenlöschungs-Anfragen ⭐⭐⭐⭐ | Aufwand: L
**Problem:** `DataPrivacyController` ermöglicht Nutzern, Löschanfragen zu stellen, aber es gibt kein Admin-Interface, um diese zu verwalten und zu bearbeiten.  
**Fix:**
- `Admin\DataPrivacyRequestController` erstellen
- Liste aller offenen Löschanfragen (gefiltert nach `deletion_requested_at IS NOT NULL`)
- One-Click Anonymisierung aller PII-Daten des Users (Name → "Gelöscht", E-Mail → Hash)
- Aufbewahrungspflichten prüfen (Buchungen mit Rechnungen: 10 Jahre nicht löschbar)  
**Datei:** Neu erstellen: `app/Http/Controllers/Admin/DataPrivacyRequestController.php`

### 7.3 Automatisierter Datenlöschungs-Prozess (Scheduler) ⭐⭐⭐⭐ | Aufwand: L
**Problem:** Wenn ein User die Löschung beantragt, muss dies laut DSGVO Art. 17 "ohne unangemessene Verzögerung" erfolgen. Ein manueller Prozess ist unzureichend.  
**Fix:**
- Scheduler-Job: Tägliche Prüfung auf Löschanfragen älter als 30 Tage
- Automatische Anonymisierung nach Bestätigungsmail und Fristablauf
- Aufbewahrungspflichten-Check vor Löschung (Rechnungen etc.)

### 7.4 Datenschutzerklärung und Impressum als Pflichtseiten ⭐⭐⭐⭐⭐ | Aufwand: M
**Problem:** Keine Hinweise auf Datenschutzerklärung oder Impressum im Code. Diese sind rechtlich zwingend erforderlich (§5 TMG, DSGVO Art. 13/14).  
**Fix:**
- Statische Seiten: `/datenschutz` und `/impressum`
- Links im Footer auf jeder Seite
- Buchungsformular: Checkbox "Ich habe die Datenschutzerklärung gelesen und akzeptiere sie"  
**Datei:** Neu erstellen: `resources/views/datenschutz.blade.php`, `resources/views/impressum.blade.php`

### 7.5 Aufbewahrungsfristen für Logs und Daten ⭐⭐⭐ | Aufwand: M
**Problem:** Audit-Logs werden zeitlich unbegrenzt gespeichert. Systemlogs ebenso. DSGVO fordert Datensparsamkeit.  
**Fix:**
- Automatische Löschung von Audit-Logs nach 12 Monaten (Scheduler)
- Systemlogs nach 90 Tagen löschen
- Notification-Einträge nach 30 Tagen löschen (bereits ein Command vorhanden?)  
**Datei:** `routes/console.php` (Scheduler erweitern)

### 7.6 Google Fonts: DSGVO-konforme Lösung ⭐⭐⭐⭐ | Aufwand: S
**Problem:** Werden Google Fonts über `https://fonts.googleapis.com` geladen (erkennbar am CSP-Eintrag `style-src ... https://fonts.googleapis.com`)? Das überträgt IP-Adressen an Google ohne Einwilligung.  
**Fix:** Google Fonts lokal hosten:
1. Schriften als TTF/WOFF2 herunterladen (`google-webfonts-helper.vercel.app`)
2. In `public/fonts/` ablegen
3. CSS in `resources/css/app.css` einbinden
4. Google Fonts aus CSP entfernen  
**Vorteil:** Auch Performance-Gewinn (kein externer DNS-Lookup).

---
---

## 🟤 9. Neue Features & Erweiterungen



### 9.5 Web Push Notifications ⭐⭐⭐ | Aufwand: L
**Problem:** Bereits im Backlog. In-App und E-Mail-Notifications sind vorhanden, aber Push Notifications für mobile Nutzer fehlen.  
**Fix:**
- Service Worker implementieren
- `laravel-notification-channels/webpush` Package
- User-Opt-In UI
- Push für: Buchungsbestätigung, Event-Erinnerung, Neue Buchung (Organizer)


### 9.8 Admin-Benachrichtigungen für ausstehende Featured-Event-Zahlungen ⭐⭐⭐ | Aufwand: M
**Problem:** Bereits im bestehenden TODO markiert.  
**Fix:** Scheduler-Command, das täglich prüft, ob Featured-Event-Zahlungen seit >7 Tagen ausstehen, und Admin-Notifications (E-Mail + In-App) sendet.

### 9.9 Event-Bewertungs-Widget für externe Einbettung ⭐⭐ | Aufwand: L
**Problem/Opportunity:** Schulen könnten Events auf ihrer eigenen Website bewerben wollen.  
**Fix:** Embeddable Widget (`/events/{slug}/widget`):
- Kleines iFrame mit Event-Info, Datum, Preis, Buchungs-Button
- `Content-Security-Policy: frame-ancestors` auf der Widget-Route anpassen

### 9.10 Schuljahres-Kalender-Integration ⭐⭐⭐ | Aufwand: M
**Problem/Opportunity:** Fortbildungen im Bildungsbereich folgen dem Schuljahresrhythmus (Schulhalbjahre, Ferien, Fortbildungstage).  
**Fix:**
- Schuljahr als Filter in der Events-Liste
- Schulferien aus iCal-Feed importieren (z.B. sachsen.de) und als "Events-freie Zeiträume" kennzeichnen
- "Empfohlen für dieses Schulhalbjahr" Label

---

## 💡 10. Strategische Ideen für die Zielgruppe (Umsetzung nur nach Rückfrage)

### 10.1 Digitales Fortbildungsportfolio für Lehrkräfte ⭐⭐⭐⭐ | Aufwand: XL
Jede Lehrkraft hat eine persönliche, druckbare "Fortbildungsmappe" mit:
- Alle absolvierten Fortbildungen mit Zertifikaten
- Gesammelte Fortbildungspunkte/Stunden pro Schuljahr
- Selbsteinschätzungs-Kompetenzraster (freiwillig)
- Exportierbar als PDF für Personalgespräche

### 10.2 Schulspezifische Landing Pages ⭐⭐⭐ | Aufwand: L
Jede evangelische Schule bekommt eine eigene Landing Page (`/schulen/gymnasium-xyz`) mit:
- Für diese Schule empfohlene Fortbildungen
- Bereits gebuchte Fortbildungen von Kollegen
- Schulspezifische Ankündigungen

### 10.3 Veranstalter-Kooperationen & Co-Hosting ⭐⭐⭐ | Aufwand: L
**Idee:** Mehrere Organisationen können gemeinsam eine Veranstaltung ausrichten (z.B. zwei evangelische Schulverbände).  
**Fix:** `event_organizers` Pivot-Tabelle für Co-Hosts, geteilte Buchungs-Einnahmen.

### 10.4 Peer-Learning-Gruppen ⭐⭐ | Aufwand: XL
**Idee:** Lehrkräfte, die dasselbe Event besucht haben, werden automatisch als "Lerngruppe" verbunden. Nachfolge-Diskussionen, Erfahrungsaustausch im Portal.  
**Fix:** Gruppen-Funktionalität nach dem Vorbild der bestehenden User-Connections.

---

## 📊 Zusammenfassung nach Priorität

| Priorität | Anzahl | Kategorie |
|-----------|--------|-----------|
| ⭐⭐⭐⭐⭐ | 4 | 1.1, 1.4, 4.1, 7.4 |
| ⭐⭐⭐⭐ | 16 | Sicherheit, Performance, DSGVO, Code-Qualität |
| ⭐⭐⭐ | 18 | SEO, UX, Testing, Neue Features |
| ⭐⭐ | 7 | Nice-to-have |
| ⭐ | 2 | Strategische Langzeitideen |

## 🚀 Quick Wins (S-Aufwand, hohe Priorität)

1. **Mailtrap-Credentials aus `.env.example` entfernen** → 5 Minuten
2. **Debug-Log aus `BookingController::store` entfernen** → 5 Minuten
3. **`og-default.jpg` erstellen** → 30 Minuten
4. **Hreflang-Tags ergänzen** → 15 Minuten
6. **Google Fonts lokal hosten** → 30 Minuten
7. **DSGVO-konforme Cookie-Consent-Implementierung** → 2 Stunden (je nach Komplexität)
8. **`paypal_client_id` mit `encrypted` Cast versehen** → 10 Minuten
9. **Rate-Limit für Discount-Code-API senken** → 5 Minuten
10. **Ticket-Download-Button auf Buchungsbestätigung hervorheben** → 15 Minuten
11. **`APP_KEY`-Hinweis in `.env.example` ergänzen** → 5 Minuten
12. **Fehlende Auth-Middleware bei Booking-Storno absichern** → 30 Minuten
13. **Admin-Benachrichtigung für ausstehende Featured-Event-Zahlungen** → 1 Stunde
14. **Canonical URLs für paginierte Seiten hinzufügen** → 15 Minuten

---

*Erstellt: Februar 2026 | Analysiert anhand von Quellcode, Konfigurationsdateien, CI-Workflows und bestehendem TODO.md*

