# Benötigte Bilder für SEO-Optimierung

Diese Bilder werden für eine vollständige SEO-Optimierung benötigt.

## 1. Open Graph Default-Bild

**Pfad:** `public/images/og-default.jpg`

**Spezifikationen:**
- Format: JPG oder PNG
- Größe: 1200 x 630 Pixel
- Maximale Dateigröße: 200 KB
- Farbformat: RGB

**Verwendung:**
- Wird als Vorschaubild in Social Media angezeigt, wenn ein Event kein eigenes Featured Image hat
- Erscheint bei Facebook, LinkedIn, Twitter Links
- Sollte das Logo und einen ansprechenden Hintergrund enthalten

**Design-Empfehlungen:**
- Platziere das Logo zentral oder links
- Verwende Farben des Corporate Designs
- Füge einen Slogan hinzu: z.B. "Bildungsportal für Fort- und Weiterbildungen"
- Halte wichtige Inhalte in der "Safe Zone" (innere 1200x600px)
- Vermeide kleine Texte (Mindestschriftgröße 40px)

**Beispiel-Tools zum Erstellen:**
- Canva (kostenlos): https://www.canva.com/
- Adobe Express (kostenlos): https://www.adobe.com/express/
- Figma (kostenlos): https://www.figma.com/

**Template-Vorlage:** Suche nach "Open Graph Template 1200x630"

---

## 2. Logo

**Pfad:** `public/images/logo.png`

**Spezifikationen:**
- Format: PNG mit transparentem Hintergrund
- Größe: 512 x 512 Pixel (quadratisch)
- Maximale Dateigröße: 100 KB
- Farbformat: RGBA (mit Alpha-Kanal für Transparenz)

**Verwendung:**
- Schema.org Organization-Markup
- Wird von Google in Suchergebnissen verwendet
- Kann in Knowledge Graph erscheinen

**Design-Empfehlungen:**
- Klares, einfaches Logo ohne Text (oder minimal)
- Gute Sichtbarkeit auch in kleinen Größen
- Transparenter Hintergrund
- Ausreichend Padding um das Logo (min. 20px)
- Vektorbasiert exportiert für Schärfe

---

## 3. Favicon

### 3a. Favicon ICO
**Pfad:** `public/favicon.ico`

**Spezifikationen:**
- Format: ICO (Multi-Size)
- Enthaltene Größen: 16x16, 32x32, 48x48 Pixel
- Maximale Dateigröße: 50 KB

### 3b. Favicon PNG (für moderne Browser)
**Pfad:** `public/images/favicon-192.png`

**Spezifikationen:**
- Format: PNG
- Größe: 192 x 192 Pixel
- Transparenter Hintergrund

### 3c. Favicon PNG (für hochauflösende Displays)
**Pfad:** `public/images/favicon-512.png`

**Spezifikationen:**
- Format: PNG
- Größe: 512 x 512 Pixel
- Transparenter Hintergrund

**Verwendung:**
- Browser-Tabs
- Lesezeichen
- Mobile Home-Screen Icons
- Progressive Web App Icons

**Design-Empfehlungen:**
- Vereinfachte Version des Logos
- Gut erkennbar in kleinen Größen
- Starker Kontrast
- Markante Farben

**Favicon-Generator-Tools:**
- https://realfavicongenerator.net/
- https://favicon.io/
- https://www.favicon-generator.org/

---

## 4. Apple Touch Icon (optional, aber empfohlen)

**Pfad:** `public/images/apple-touch-icon.png`

**Spezifikationen:**
- Format: PNG
- Größe: 180 x 180 Pixel
- Kein transparenter Hintergrund (Hintergrundfarbe verwenden)

**Verwendung:**
- iOS Home-Screen Icon
- Safari Bookmarks

**Einbindung in HTML:**
```html
<link rel="apple-touch-icon" href="/images/apple-touch-icon.png">
```

---

## Quick-Start Anleitung

### Option 1: Mit Canva (Empfohlen für Nicht-Designer)

1. Gehe zu https://www.canva.com/
2. Registriere dich kostenlos
3. Erstelle Designs mit folgenden Vorlagen:
   - "Facebook Post" (1200x630) für Open Graph
   - "Logo" (500x500) für Logo
   - "Favicon" für Favicons
4. Exportiere in den angegebenen Formaten
5. Lade die Dateien in die entsprechenden Ordner hoch

### Option 2: Mit Adobe Express

1. Gehe zu https://www.adobe.com/express/
2. Erstelle ein kostenloses Konto
3. Nutze Templates für Social Media Graphics
4. Exportiere und lade hoch

### Option 3: Professioneller Designer

Beauftrage einen Designer über:
- Fiverr: https://www.fiverr.com/
- 99designs: https://99designs.de/
- Upwork: https://www.upwork.com/

**Brief für Designer:**
"Ich benötige folgende Grafiken für mein Bildungsportal:
1. Open Graph Bild (1200x630px)
2. Logo (512x512px, transparent)
3. Favicon-Set (16x16, 32x32, 48x48, 192x192)

Farbschema: [Füge deine Farben ein]
Stil: Modern, professionell, für Bildungsbereich"

---

## Temporäre Placeholder (bis echte Bilder erstellt sind)

Du kannst temporär Placeholder-Bilder verwenden:

### Placeholder Open Graph Bild erstellen:

```html
<!-- Temporär können Sie eine einfache Grafik mit Text erstellen -->
<!-- Größe: 1200x630px, Hintergrundfarbe, Logo/Text zentriert -->
```

### Online Placeholder-Generatoren:
- https://placehold.co/ (z.B. https://placehold.co/1200x630/4F46E5/FFFFFF/png?text=Bildungsportal)
- https://via.placeholder.com/

---

## Checkliste nach dem Erstellen

- [ ] og-default.jpg (1200x630px) in `public/images/` hochgeladen
- [ ] logo.png (512x512px) in `public/images/` hochgeladen
- [ ] favicon.ico in `public/` hochgeladen
- [ ] favicon-192.png in `public/images/` hochgeladen
- [ ] favicon-512.png in `public/images/` hochgeladen
- [ ] apple-touch-icon.png (optional) in `public/images/` hochgeladen
- [ ] Alle Bilder komprimiert (z.B. mit TinyPNG.com)
- [ ] Browser-Cache geleert und Seite getestet
- [ ] Social Media Share-Test durchgeführt (z.B. Facebook Debugger)

---

## Weitere HTML-Einbindungen (optional)

Wenn Sie zusätzliche Icons erstellen, fügen Sie diese in Ihr Layout ein:

```html
<!-- In resources/views/layouts/public.blade.php im <head> -->
<link rel="apple-touch-icon" sizes="180x180" href="/images/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="192x192" href="/images/favicon-192.png">
<link rel="icon" type="image/png" sizes="512x512" href="/images/favicon-512.png">
<link rel="manifest" href="/site.webmanifest">
```

## Web Manifest (für PWA - optional)

Erstelle `public/site.webmanifest`:

```json
{
  "name": "Bildungsportal",
  "short_name": "Bildungsportal",
  "icons": [
    {
      "src": "/images/favicon-192.png",
      "sizes": "192x192",
      "type": "image/png"
    },
    {
      "src": "/images/favicon-512.png",
      "sizes": "512x512",
      "type": "image/png"
    }
  ],
  "theme_color": "#4F46E5",
  "background_color": "#ffffff",
  "display": "standalone"
}
```

---

Bei Fragen oder Problemen beim Erstellen der Bilder, konsultieren Sie die SEO-GUIDE.md oder kontaktieren Sie Ihren Webdesigner.
