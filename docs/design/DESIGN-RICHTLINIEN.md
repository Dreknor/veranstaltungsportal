# Design-Richtlinien und Umbauanweisungen für das Bildungsportal

Stand: 15.11.2026 · Grundlage: Entwurf „E – Klar" (Startseite und Buchung)

Dieses Dokument ist verbindlich für jede Änderung an der Oberfläche des Portals. Es beschreibt,
wie das Portal aussehen soll (Teil A), wie die Bausteine gebaut werden (Teil B) und in welcher
Reihenfolge die bestehende Anwendung umgestellt wird (Teil C).

**Referenz:** Die beiden abgenommenen Seiten liegen als statisches HTML und als Bildschirmfotos in
`docs/design/referenz/` (`startseite.html`, `buchung.html`, jeweils mit `-1440.png` und `-390.png`).
Wo dieses Dokument schweigt, entscheidet die Referenz. Wo die Referenz schweigt (Tabellen,
Seitenleiste, Dialoge, E-Mails), gelten die Regeln hier.

**Herkunft der Gestaltung:** Die Farbwelt lehnt sich an die Schulstiftung der Ev.-Luth.
Landeskirche Sachsens an (Petrol, helles Graugrün, Blattgrün). Das Portal soll als Teil dieser
Familie erkennbar sein, aber ruhiger und präziser wirken.

---

## Teil A – Grundsätze

### A1 Fünf Leitsätze

1. **Ruhig vor laut.** Weiß ist die Fläche, Petrol ist die Tinte, Blattgrün ist ein Punkt.
   Es gibt keine gesättigte Vollfläche, keinen Verlauf, keinen Schlagschatten.
2. **Inhalt vor Rahmen.** Gliedert wird mit Abstand und feinen Linien. Ein Kasten ist die
   Ausnahme und braucht einen Grund (zusammengehörige Liste, Zusammenfassung, Hinweis).
3. **Eine Aktion pro Bereich.** Pro Abschnitt gibt es höchstens einen gefüllten Petrol-Button.
   Alles Weitere ist Sekundärbutton oder Textlink.
4. **Zahlen stehen ruhig.** Datum, Uhrzeit, Preis und Mengen laufen in tabellarischen Ziffern
   und stehen an festen Plätzen, damit man Zeilen vergleichen kann.
5. **Kompakt, nie eng.** Suche und Filter belegen eine Zeile. Klickflächen sind mindestens
   44 px hoch, Formularfelder 48 px.

### A2 Farben

Es gibt genau diese Farben. Neue Töne werden nicht „nach Gefühl" ergänzt, sondern hier eingetragen.

| Token | Hex | Verwendung | Kontrast |
|---|---|---|---|
| `ink` | `#1E3135` | Text, Überschriften, Fokus-Ring, Button-Hover | 13,6:1 auf Weiß |
| `primary` | `#2B4347` | Primärbutton, Abschnittsmarken, Zeichen der Wortmarke | Weiß darauf 10,5:1 |
| `ink-soft` | `#3B5053` | Sekundärtext mit Gewicht (z. B. Platzstatus) | 8,5:1 |
| `muted` | `#55676A` | Sekundärtext, Labels in Zellen, Kategoriezeile | 5,9:1 auf Weiß, 5,5:1 auf `zone` |
| `line` | `#DCE3E2` | Trennlinien, Kontur von Containern | nur Dekor |
| `line-strong` | `#7F9091` | Kontur von Feldern, Suchrahmen, Sekundärbuttons | 3,3:1 (Bedienelement) |
| `disabled` | `#9AA8A9` | deaktivierte Bedienelemente | bewusst unter 3:1 |
| `surface` | `#FFFFFF` | Seitengrund, Felder, Listencontainer | – |
| `zone` | `#F3F6F5` | ruhige Zonen: Einstieg, Schwerpunkt, Zusammenfassung, Footer, Datumsblock | – |
| `zone-hover` | `#F6F8F8` | Hover einer Listenzeile | – |
| `leaf` | `#8DC11D` | 8-px-Statuspunkt „frei", Blatt im Zeichen | nie als Textfarbe, nie mit weißer Schrift |

Meldungsfarben (nicht im Entwurf enthalten, hier festgelegt; immer Fläche + Kontur + Text + Icon):

| Zweck | Text | Fläche | Kontur | Kontrast Text/Fläche |
|---|---|---|---|---|
| Erfolg | `#2C5E1A` | `#EEF6E0` | `#B9D98A` | 6,9:1 |
| Warnung, Warteliste | `#6B4A0A` | `#FBF3DF` | `#E2C88A` | 7,3:1 |
| Fehler | `#9B1C1C` | `#FCEEEE` | `#E3A9A9` | 7,2:1 |
| Hinweis | `ink` | `zone` | `line` | 12,5:1 |

Fehlerhafte Felder bekommen die Kontur `#B42318` (6,6:1 auf Weiß) und einen Fehlertext in `#9B1C1C`.
Der Löschen-Button ist `#9B1C1C` mit weißer Schrift (8,2:1).

Regeln:

- Farbe trägt nie allein die Bedeutung. Ein Status hat immer auch Text („noch 6 frei", „Warteliste").
- **Kategoriefarben aus der Datenbank werden in der Oberfläche nicht mehr angezeigt.** Die Kategorie
  steht als Text in `muted` über dem Titel. Das Feld `color` bleibt in der Datenbank bestehen.
- Farben von Abzeichen (Badges) sind Inhalt und bleiben, aber nur innerhalb des Abzeichens selbst.
- Markenfarben fremder Dienste (Teilen-Buttons) entfallen; die Buttons werden Sekundärbuttons mit Icon.

### A3 Schrift

- **Eine Schrift: Geist** (Variable Font, Gewichte 400, 500, 600). Sie wird lokal eingebunden,
  nicht von Google geladen: Paket `@fontsource-variable/geist` (OFL-1.1), Import in `app.css`.
  Die bisherige „Instrument Sans" und ihre `@font-face`-Blöcke entfallen.
- Keine Serifen, keine zweite Schrift, kein Gewicht über 600, keine Versalien-Zeilen, keine Kursive als Schmuck.
- Zahlen in Datum, Zeit, Preis, Mengen und Tabellen: `tabular-nums`.

| Token | Größe / Zeilenhöhe | Gewicht | Laufweite | Verwendung |
|---|---|---|---|---|
| `display` | clamp(30px, 4.2vw, 52px) / 1,08 | 600 | −0,025em | H1 der Startseite |
| `title` | clamp(30px, 3.6vw, 44px) / 1,1 | 600 | −0,025em | H1 aller übrigen Seiten |
| `h2` | 24px / 1,25 | 600 | −0,015em | Abschnittsüberschrift |
| `h2-form` | 22px / 1,25 | 600 | −0,015em | Formularabschnitt, Aufrufzeile |
| `h3` | 18px / 1,3 | 600 | −0,01em | Zeilentitel, Kartenüberschrift |
| `body` | 16px / 1,5 | 400 | 0 | Fließtext, Feldinhalt |
| `ui` | 15px / 1,4 | 500 | 0 | Buttons, Navigation, Metazeile |
| `small` | 14px / 1,4 | 400 oder 500 | 0 | Labels, Sekundärzeilen |
| `caption` | 13px / 1,3 | 400 | 0 | Zellenlabels, Monat im Datumsblock |

Kleiner als 13 px wird nichts gesetzt. Überschriften stehen linksbündig, nie zentriert.

### A4 Raster, Abstände, Formen

- **Seitenbreite:** Inhalt maximal 1200 px, zentriert, seitlicher Rand `clamp(16px, 4vw, 32px)`.
  Lesetext maximal 760 px, Formularspalte maximal 640 px, Zusammenfassung 368 px.
- **Abstandsskala (8-px-Raster):** 4, 8, 12, 16, 24, 32, 48, 64, 96 px. Dazwischen gibt es nichts.
  Abschnitte auf Marketing-Seiten: oben `clamp(48px, 6vw, 80px)`. Formularabschnitte: 32 px oben und unten.
  Felder untereinander: 20 px. Label zum Feld: 6 px.
- **Radien:** 8 px für alles Bedienbare (Buttons, Felder, Datumsblock, Auswahlkarten),
  12 px für Container (Suchrahmen, Listencontainer, Zonen-Kästen), 50 % nur für Punkte und Abschnittsmarken.
- **Linien:** 1 px. Container `line`, Bedienelemente `line-strong`.
- **Schatten:** keine. Ebenen entstehen durch Kontur. Einzige Ausnahme: Dialoge und Aufklappmenüs
  dürfen `0 8px 24px rgba(30,49,53,0.12)` tragen, damit sie über dem Inhalt lesbar bleiben.
- **Umbruchpunkte:** unter 960 px stapeln Listenzeilen und die Buchungsspalten; unter 720 px
  stapelt die Suchzeile und die Navigation bricht in eine zweite Zeile. In Tailwind: `lg` (1024)
  und `md` (768) als nächstliegende Stufen verwenden.

### A5 Icons und Bilder

- **Ein Icon-System:** eigene Blade-Komponenten unter `components/icon/` als Linien-Icons
  (16 oder 20 px, `stroke="currentColor"`, Strichstärke 1,6, runde Enden, `aria-hidden="true"`).
- Font Awesome wird entfernt (Paket, CSS-Import, `fa-`-Klassen). Icon-Namen aus der Datenbank
  werden wie bisher auf Blade-Icons abgebildet.
- **Keine Emojis**, weder in Views noch in E-Mail-Betreffs oder Benachrichtigungen. Pfeile im
  Fließtext („→", „←") werden durch das Pfeil-Icon ersetzt.
- Icons begleiten Text, sie ersetzen ihn nicht. Ein Icon-Button ohne Text braucht `aria-label`.
- Das Portal kommt ohne Fotos aus. Veranstaltungsbilder werden nur auf der Detailseite gezeigt,
  falls vorhanden (Seitenverhältnis 16:9, Radius 12 px); es gibt keinen Platzhalter, wenn keines da ist.

### A6 Wortmarke

Das Logo ist offen. Bis ein endgültiges vorliegt, gilt der Platzhalter aus der Referenz:
Petrol-Kreis (36 px) mit Blattgrün-Blatt, daneben „Bildungsportal" (17 px, 600) und darunter
„Hauptfach Mensch" (13 px, `muted`). Die Wortmarke wird als eine Komponente gebaut
(`<x-wordmark>`), damit der Tausch später an einer Stelle geschieht. Der Name kommt aus
`config('app.name')`. `public/images/logo.png` (Koralle/Aubergine) wird nicht mehr verwendet.

### A7 Sprache

- Deutsch, Anrede „Sie". Kurz, sachlich, freundlich.
- Buttons benennen die Handlung: „Buchen", „Auf die Warteliste", „Kostenpflichtig buchen",
  „Veranstaltung anlegen". Nie „OK", „Absenden", „Hier klicken".
- Pflichtfelder tragen „*", freiwillige Felder „(optional)". Über dem Formular steht einmal „* Pflichtfeld".
- Datum: „Do, 26.11.2026"; Zeit: „14:00–17:30 Uhr" (Halbgeviertstrich); Preis in Listen „45 €",
  in Buchung und Rechnung „45,00 €"; kostenlos heißt „kostenfrei".
- Einheitliche Begriffe: „Veranstaltung" (nicht „Event"), „Veranstalter", „Buchung", „Warteliste",
  „Teilnahmezertifikat", „Abzeichen".
- Keine erfundenen Kennzahlen, keine Ausrufezeichen-Werbung.
- Achtung: Feature-Tests prüfen sichtbare Texte (`assertSee`, 194 Aufrufe). Wer einen Text ändert,
  sucht ihn vorher in `tests/` und passt den Test im selben Schritt an.

### A8 Barrierefreiheit (Mindeststandard WCAG 2.1 AA)

- Text mindestens 4,5:1, große Schrift und Bedienelement-Konturen mindestens 3:1. Die Werte in A2 sind geprüft.
- **Fokus:** überall sichtbar als 2 px `ink` mit 2 px Abstand (`outline`, nicht `box-shadow`).
  In Zellen ohne Platz nach außen: Abstand −2 px.
- Jedes Feld hat ein sichtbares `<label for>`. Platzhaltertext ersetzt kein Label.
- Echte Elemente: `<a href>` für Navigation, `<button>` für Aktionen, `<table>` für Tabellen,
  eine `<h1>` pro Seite, Überschriften ohne Sprünge.
- Fehlermeldungen stehen direkt unter dem Feld, sind per `aria-describedby` verknüpft, und das
  Feld trägt `aria-invalid="true"`.
- Zoom bis 200 % und 390 px Breite ohne horizontales Scrollen. Breite Tabellen scrollen in einem
  eigenen Kasten (`overflow-x: auto`).
- Bewegung: höchstens 150 ms Farb- und Konturwechsel. Keine Skalierung beim Hover, keine Einblend-Animation.

### A9 Was es nicht mehr gibt

- Verläufe (`bg-gradient-*`, `from-*`, `to-*`), Blau, Violett, Indigo als Akzent.
- Schatten an Karten (`shadow-*`), Glas-Effekte, Leuchten, Skalieren beim Hover.
- Karten mit farbigem Rand links, Icon im farbigen Kreis über jeder Überschrift.
- Emojis, Font-Awesome-Klassen, zentrierte Überschriften-Blöcke.
- Chip-Leisten für Filter; mehr als eine Filterzeile über einer Liste.
- Hart codierte Hex-Farben in Views (`style="color:#…"`), außer in E-Mails und PDFs (siehe B9).
- Eigene `<html>`-Gerüste in einzelnen Seiten.

---

## Teil B – Technik und Bausteine

### B1 Tokens in Tailwind v4

Die Tokens stehen einmal in `resources/css/app.css` und erzeugen Utility-Klassen
(`bg-zone`, `text-muted`, `border-line`, `rounded-control` …). Views verwenden ausschließlich diese.

```css
@import "tailwindcss";
@import "@fontsource-variable/geist";

@theme {
    --font-sans: "Geist Variable", ui-sans-serif, system-ui, sans-serif;

    --color-ink: #1e3135;
    --color-primary: #2b4347;
    --color-ink-soft: #3b5053;
    --color-muted: #55676a;
    --color-line: #dce3e2;
    --color-line-strong: #7f9091;
    --color-disabled: #9aa8a9;
    --color-surface: #ffffff;
    --color-zone: #f3f6f5;
    --color-zone-hover: #f6f8f8;
    --color-leaf: #8dc11d;
    --color-on-primary: #ffffff;

    --color-success: #2c5e1a;
    --color-success-zone: #eef6e0;
    --color-success-line: #b9d98a;
    --color-warning: #6b4a0a;
    --color-warning-zone: #fbf3df;
    --color-warning-line: #e2c88a;
    --color-danger: #9b1c1c;
    --color-danger-zone: #fceeee;
    --color-danger-line: #e3a9a9;
    --color-danger-field: #b42318;

    --radius-control: 8px;
    --radius-box: 12px;

    --text-caption: 13px;
    --text-caption--line-height: 1.3;
    --text-small: 14px;
    --text-small--line-height: 1.4;
    --text-ui: 15px;
    --text-ui--line-height: 1.4;
    --text-base: 16px;
    --text-base--line-height: 1.5;
    --text-h3: 18px;
    --text-h3--line-height: 1.3;
    --text-h2-form: 22px;
    --text-h2-form--line-height: 1.25;
    --text-h2: 24px;
    --text-h2--line-height: 1.25;
    --text-title: clamp(30px, 3.6vw, 44px);
    --text-title--line-height: 1.1;
    --text-display: clamp(30px, 4.2vw, 52px);
    --text-display--line-height: 1.08;

    --container-page: 1200px;
    --container-prose: 760px;
    --container-form: 640px;
}
```

Regeln für den Code:

- In Views stehen keine Tailwind-Standardfarben mehr (`gray-*`, `blue-*`, `red-*`, `green-*`,
  `yellow-*`, `purple-*` …). Jede Farbklasse ist ein Token aus der Liste oben.
- Die alten Hilfsklassen in `app.css` entfallen ersatzlos: `.btn-primary`, `.btn-secondary`,
  `.btn-success`, `.btn-danger`, `.card`, `.card-hover`, `.input-modern`, `.badge-*`, `.glass`,
  `.shadow-soft`, `.shadow-glow-*`, `.gradient-text`, `.transition-smooth`, `.animate-fadeIn`,
  ebenso die `--sidebar-*`-Variablen und der Import von Font Awesome.
- Gestaltung wiederholt sich nicht als Klassenkette in Views, sondern lebt in Blade-Komponenten (B3).
  Faustregel: Wer dieselbe Klassenkette zum dritten Mal schreibt, baut eine Komponente.
- Die Basisregeln für Formularfelder in `app.css` werden durch die Feld-Komponenten ersetzt;
  übrig bleibt nur `[x-cloak]` und die globale Fokus-Regel.

### B2 Dunkelmodus

Der Entwurf E ist ein helles Design; eine dunkle Fassung ist **nicht abgenommen**. Der Bestand ist
uneinheitlich (`dark:` in 145 von 306 Dateien; Startseite, öffentliche Veranstaltungsseiten,
Kalender und Fehlerseiten haben keine dunkle Fassung).

Festlegung für den Umbau:

- **Alle `dark:`-Klassen werden beim Umbau einer View entfernt.** Views kennen nur noch Tokens.
- Der Umschalter unter Einstellungen › Darstellung wird bis zur Abnahme einer dunklen Palette
  ausgeblendet; die vier duplizierten Inline-Skripte in den Layouts werden zu einem zusammengeführt.
- Wird der Dunkelmodus später gewünscht, entsteht er an genau einer Stelle, indem die Tokens unter
  `.dark` überschrieben werden. Vorschlag (Kontraste gerechnet, gestalterisch noch nicht geprüft):
  `surface #16272A`, `zone #1E3135`, `ink #E8EEED` (13,2:1), `muted #A9B8B8` (7,5:1),
  `line #3D5458`, `line-strong #6F8486` (3,9:1), `primary #CFE0DD` mit `on-primary #16272A` (11,3:1).

### B3 Bausteine

Jeder Baustein ist eine Blade-Komponente unter `resources/views/components/ui/`. Bestehende
Komponenten (`button`, `forms/input`, `forms/checkbox`) werden dorthin überführt und ersetzt.

**Button** `<x-ui.button variant="primary|secondary|danger|link" size="md|sm" :href>`
- Höhe 44 px (`sm`: 36 px, nur in Tabellenzeilen), Polster 16–18 px, Radius 8 px, Text `ui` (15 px, 500).
- `primary`: Fläche `primary`, Schrift weiß; Hover `ink`.
- `secondary`: Fläche weiß, Kontur `line-strong`, Schrift `ink`; Hover Fläche `zone`, Kontur `ink`.
- `danger`: Fläche `danger`, Schrift weiß. Nur für endgültiges Löschen oder Stornieren.
- `link`: Text `ink`, 500, beim Hover unterstrichen (Abstand 3 px); optional Pfeil-Icon dahinter.
- Deaktiviert: Fläche `zone`, Schrift `disabled`, `cursor: not-allowed`.
- Mit `href` rendert die Komponente ein `<a>`, sonst ein `<button type="button">`.
- Icon vor dem Text: 16 px, Abstand 8 px.

**Feld** `<x-ui.field label name type required hint error>` (kapselt Label, Eingabe, Hinweis, Fehler)
- Label darüber: `small`, 500, Abstand 6 px. Pflicht: „*" mit `aria-hidden`; freiwillig: „(optional)" in `muted`, 400.
- Eingabe: Höhe 48 px, Polster 0 14 px, Radius 8 px, Kontur `line-strong`, Schrift 16 px.
  Hover: Kontur `ink`. Fokus: Kontur `ink` plus Fokus-Ring. Fehler: Kontur `danger-field`.
- `<x-ui.select>`, `<x-ui.textarea>` (Mindesthöhe 120 px, Polster 12 14 px) sehen gleich aus.
  Das Select trägt rechts das Chevron-Icon (16 px).
- Hinweis unter dem Feld: `small`, `muted`. Fehler: `small`, `danger`, mit Icon.
- Felder stehen in einem Raster `repeat(auto-fit, minmax(248px, 1fr))`, Abstand 20 px / 16 px.
  PLZ schmal, Ort breit; zusammengehörige Felder nebeneinander, sonst untereinander.

**Checkbox und Radio** `<x-ui.checkbox>`, `<x-ui.radio>`
- 20 px, Kontur `line-strong`, gewählt `primary`. Das Label ist klickbar; die Zeile ist mindestens 44 px hoch.

**Auswahlkarte** `<x-ui.choice>` (Zahlungsmethode, Ticketart mit Einzelauswahl)
- Kasten mit Radius 8 px, Kontur `line-strong`, Polster 16 px; Titel 16 px/600, Unterzeile `small` `muted`.
- Gewählt: Fläche `zone`, Kontur `ink` doppelt (innen 1 px zusätzlich). Hover: Kontur `ink`.

**Stepper** `<x-ui.stepper>` (Mengenwahl)
- Rahmen 46 px hoch, Kontur `line-strong`, Radius 8 px; Minus- und Plus-Button je 44 × 44 px,
  dazwischen ein 40 px breites Zahlenfeld (16 px, 600, zentriert). Minus ist bei 0 deaktiviert.
- Beide Buttons tragen ein sprechendes `aria-label` („Teilnahme: ein Ticket weniger").

**Datumsblock** `<x-ui.date-block :date :end>`
- 80 × 80 px, Fläche `zone`, Kontur `line`, Radius 8 px, als `<time datetime>`.
- Drei Zeilen: Wochentag (`caption`, `muted`), Tag (24 px, 600), „Nov 2026" (`caption`, 500).
  Mehrtägig: „Fr–Sa" / „22–23" / „Jan 2027".

**Listenzeile** `<x-ui.event-row :event>` – der prägende Baustein des Portals
- Raster ab 960 px: Datumsblock 80 px | Kategorie + Titel (1,7 fr) | Format · Ort + Zeit (1 fr) |
  Preis + Status 128 px, rechtsbündig | Aktion 184 px. Spaltenabstand 24 px, Polster 20 × 24 px.
- Darunter: Datumsblock und Titel in der ersten Reihe, Meta links und Preis rechts in der zweiten,
  Aktion über die volle Breite in der dritten. Polster 16 px.
- Zeilen liegen in einem Container mit Kontur `line` und Radius 12 px, getrennt durch 1 px `line`. Hover: `zone-hover`.
- Titel `h3` als Link; Kategorie darüber `small` `muted`; Meta `ui` 500, Zeit `small` `muted`.
- Aktion: „Buchen" (primary) oder „Auf die Warteliste" (secondary).
- Dieselbe Zeile wird überall verwendet, wo Veranstaltungen gelistet werden: Startseite, Übersicht,
  Themenseiten, „Meine Buchungen", Veranstalter-Übersicht. Kartenraster für Veranstaltungen gibt es nicht mehr.

**Status** `<x-ui.status tone="free|waitlist|neutral|success|warning|danger">`
- `free`: 8-px-Punkt `leaf` + Text `small` `ink-soft` („noch 6 frei").
- `waitlist`: hohler 8-px-Ring `muted` + Text („Warteliste").
- Buchungs- und Zahlungsstatus im Backend (bestätigt, offen, storniert): Text mit Punkt in der
  Meldungsfarbe; keine gefüllten bunten Pillen.

**Abschnittsmarke** `<x-ui.step-heading number="1">`
- Kreis 28 px, Fläche `primary`, Ziffer weiß 14 px/600; daneben `h2-form`. Für Screenreader steht die
  Nummer versteckt im Titel. Abschnitte trennt eine Linie `line`, kein Kasten.

**Suchzeile** `<x-ui.search-bar>`
- Ein Rahmen (Kontur `line-strong`, Radius 12 px, Fläche weiß) mit vier Zellen: Suchfeld (flexibel),
  „Format" (176 px), „Zeitraum" (200 px), Button „Suchen". Zellen trennt 1 px `line`;
  das Label sitzt klein (`caption`, `muted`) in der Zelle über dem Wert.
- Unter 720 px: Suche volle Breite, Format und Zeitraum je zur Hälfte, Button volle Breite.
- Weitere Filter (Kategorie, Ort, Preis) liegen hinter einem Sekundärbutton „Filter" mit Zähler der
  aktiven Filter und öffnen ein Aufklappfeld. Aktive Filter erscheinen als eine Textzeile unter der
  Suche („Kategorie: Schulentwicklung · Zurücksetzen"), nicht als Chip-Reihe.

**Zone** `<x-ui.zone>` und **Kasten** `<x-ui.box>`
- Zone: vollbreite Fläche `zone` mit Linie `line` unten (Einstieg, Footer).
- Kasten: Fläche `zone` oder weiß, Kontur `line`, Radius 12 px, Polster 24–40 px
  (Schwerpunkt, Zusammenfassung, Aufrufzeile). Kästen werden nicht ineinander geschachtelt.

**Zusammenfassung** (Buchung, Bestellung)
- Kasten auf `zone`, 368 px breit, ab 960 px mitlaufend (`position: sticky; top: 24px`), darunter am Ende.
- Aufbau: Titel `h3`, Veranstaltung, Posten mit rechtsbündigem Betrag, Linie, „Gesamt" mit Betrag
  (24 px, 600), Zustimmung, Primärbutton über die volle Breite, kurzer Hinweis.

**Hinweis** `<x-ui.alert tone="info|success|warning|danger">`
- Fläche, Kontur und Text aus A2, Radius 8 px, Polster 12 × 16 px, Icon 20 px links, Text `small`/`body`.
- Ersetzt alle heutigen farbigen Meldungskästen und Session-Meldungen. Schließbar nur, wenn die Meldung flüchtig ist.

**Tabelle** `<x-ui.table>` (Backend; heute 112 Tabellen ohne gemeinsamen Baustein)
- Container mit Kontur `line`, Radius 12 px, `overflow-x: auto`.
- Kopfzeile: Fläche `zone`, Text `small` 500 `muted`, linksbündig, keine Versalien.
- Zeilen: Mindesthöhe 56 px, Linie `line`, Hover `zone-hover`; Zahlen rechtsbündig und tabellarisch.
- Aktionen stehen rechts als Textlinks oder `sm`-Buttons; ab drei Aktionen ein Menü „Mehr".
- Leerer Zustand: `<x-ui.empty>` mit einem Satz und genau einer Aktion.

**Seitenkopf** `<x-ui.page-header title :back :actions>`
- Optionaler Zurück-Link mit Pfeil (`small`, 500), darunter `title`, rechts höchstens ein Primär- und ein Sekundärbutton.

**Dialog** `<x-ui.modal>`
- Weiß, Radius 12 px, Breite bis 560 px, Polster 24 px, Schatten laut A4; Hintergrund `rgba(30,49,53,0.5)`.
- Titel `h3`, Schließen-Button oben rechts mit `aria-label`, Aktionen unten rechts (sekundär links, primär rechts).
- Fokus wird im Dialog gehalten und beim Schließen zurückgegeben; Escape schließt.

**Seitennavigation** – Laravel-Pagination wird veröffentlicht und angepasst: Sekundärbuttons „Zurück"
und „Weiter", dazwischen Seitenzahlen als 44-px-Flächen; aktive Seite Fläche `primary`, Schrift weiß.

### B4 Seitengerüste

Es gibt genau drei Layouts; jede View nutzt eines davon.

- **`layouts.public`** – öffentliche Seiten, Anmeldung, Buchung, Rechtliches, Fehlerseiten.
  Kopf: 72 px hoch, weiß, Linie `line` unten; links Wortmarke, rechts „Veranstaltungen", „Kalender",
  „Anmelden" (je 44 px hoch, `ui`), dann Button „Registrieren". Aktive Seite: Gewicht 600 und
  `aria-current="page"`. Unter 720 px rutschen die Links in eine zweite Zeile (kein Hamburger-Menü,
  solange es bei drei Links bleibt). Fuß: Zone mit Wortmarke, Links, darunter „© Jahr Bildungsportal ·
  Ein Angebot der ESdi GmbH" und „Mehr zur Aktion Hauptfach Mensch".
- **`layouts.app`** – angemeldeter Bereich für Teilnehmende, Veranstalter und Administration.
  Seitenleiste 264 px, weiß, Linie `line` rechts. Einträge 44 px hoch, `ui`, Icon 20 px; aktiver
  Eintrag: Fläche `zone`, Gewicht 600, kein farbiger Balken. Gruppen trennt ein Label in `caption`
  `muted`. Unter 1024 px wird die Leiste zum einblendbaren Menü. Kopf 64 px mit Seitentitel,
  Organisationswechsler, Benachrichtigungen, Konto. Inhalt auf weißem Grund, maximal 1200 px.
- **`layouts.mail`** und **`layouts.pdf`** – siehe B9.

`layouts.auth` (ungenutzt) und `layouts.guest` entfallen; ihre drei Views wechseln auf `layouts.public`.
`home.blade.php` wird gelöscht, sofern die Prüfung bestätigt, dass keine Route sie verwendet.
`welcome.blade.php` und die vier Fehlerseiten verlieren ihr eigenes `<html>`-Gerüst.

### B5 Seitentypen

- **Startseite:** Einstieg (Zone: H1, Suchzeile) → „Kommende Veranstaltungen" (Listenzeilen) →
  Kategorien (Raster aus Textlinks mit Pfeil, vier Spalten, je 60 px hoch, Linie darüber) →
  Schwerpunkt „Hauptfach Mensch" (Kasten) → drei Nutzenpunkte (Linie oben, Icon 20 px, `h3`, Satz) →
  Aufruf an Veranstalter (Kasten mit Sekundärbutton) → Fuß.
- **Übersicht und Suche:** Seitenkopf, Suchzeile, Trefferzahl als Satz, Listenzeilen, Seitennavigation.
- **Detailseite:** zwei Spalten wie die Buchung. Links Kategorie, Titel, Beschreibung als Lesetext
  (maximal 760 px), Ablauf, Ort, Veranstalter; rechts mitlaufender Kasten mit Datum, Format, Ort,
  Preis, Platzstatus, Button „Buchen" und „In den Kalender".
- **Buchung:** siehe Referenz. Eine Seite, drei nummerierte Abschnitte, Zusammenfassung rechts.
- **Formularseiten im Backend:** dieselbe Abschnittslogik; lange Formulare bekommen links die
  Formularspalte und rechts eine kurze Hilfe oder Vorschau, nie drei Spalten.
- **Listen im Backend:** Seitenkopf, eine Filterzeile, Tabelle, Seitennavigation.
- **Übersichten (Dashboards):** Kennzahlen als Zeile aus schlichten Kästen (Label `small` `muted`,
  Wert 24 px/600), keine farbigen Kacheln, keine Icons in Kreisen. Diagramme in `primary` und `muted`.
- **Kalender:** Monatsraster mit Linien `line`, heutiger Tag mit `primary`-Kreis um die Zahl,
  Termine als Textzeilen mit Uhrzeit. Die beiden fast gleichen Kalender-Views werden eine.
- **Hilfe und Rechtstexte:** Lesetext maximal 760 px, Überschriften `h2`/`h3`, Listen mit normalem
  Aufzählungszeichen; kein Emoji vor Überschriften.

### B6 Fremdbausteine

- **Cookie-Hinweis** (`vanilla-cookieconsent`): Farben, Radius und Schrift über die CSS-Variablen der
  Bibliothek an die Tokens angleichen; Buttons wie B3.
- **Editor** (TipTap, Rechtstexte): Werkzeugleiste aus `sm`-Sekundärbuttons, Inhalt als Lesetext;
  Linkfarbe `ink` unterstrichen statt Blau. Die `prose`-Klassen wirken heute nicht (das
  Typografie-Plugin fehlt); stattdessen eine eigene Klasse `.richtext` mit den Schriftregeln aus A3.
- **reCAPTCHA, PayPal:** Fremdoptik bleibt; der Rahmen darum folgt den Regeln.

### B7 E-Mails und PDFs

E-Mails und PDFs können keine Tailwind-Tokens nutzen und dürfen deshalb Hex-Werte enthalten –
aber nur die aus A2, und nur aus einer gemeinsamen Quelle.

- **E-Mails:** ein gemeinsames Layout `emails/layout.blade.php` für alle 16 handgebauten Mails;
  die sechs Markdown-Mails bekommen ein veröffentlichtes, angepasstes Laravel-Mail-Theme.
  Weißer Grund, Inhalt 600 px, Kopf mit Wortmarke auf Weiß und Linie `line` (kein Verlauf, kein Lila
  `#667eea`/`#764ba2`), Text `ink`, Button `primary` mit weißer Schrift und 8 px Radius, Fuß in `muted`.
  Schrift: Systemschriften (`-apple-system, "Segoe UI", Roboto, Arial, sans-serif`). Keine Emojis im Betreff.
- **PDFs** (Ticket, Rechnung, Zertifikat): ein gemeinsames Stylesheet-Partial mit den Farben aus A2;
  Blau `#2563eb` wird `primary`. DejaVu Sans bleibt (DomPDF). Linien statt gefüllter Tabellenköpfe.
  Die vier fast gleichen Rechnungsansichten werden auf eine Vorlage zurückgeführt.

---

## Teil C – Umbauanweisungen

### C1 Ausgangslage (gezählt am 15.11.2026)

- 306 Blade-Dateien. 153 nutzen `layouts.app`, 21 `layouts.public`, 3 `guest`; `welcome`, `home` und
  vier Fehlerseiten haben ein eigenes Gerüst.
- Kein Design-System: `bg-blue-600` 226-mal, Verläufe 69-mal, 333 rohe `<button>` gegenüber
  9 Verwendungen von `<x-button>`, 112 Tabellen ohne gemeinsamen Baustein.
- Drei Icon-Systeme nebeneinander (Blade-Icons 118, Inline-SVG 554, Font Awesome 146) und rund 460
  Zeilen mit Emojis oder Symbolzeichen, auch in `app/` (Benachrichtigungen, Konsolenbefehle).
- E-Mails und PDFs haben eine eigene Optik in Lila und Blau.

### C2 Reihenfolge

Jede Phase ist ein eigener Pull Request (große Phasen mehrere), bleibt lauffähig und wird für sich abgenommen.

**Phase 0 – Fundament** (keine sichtbare Änderung außer der Schrift)
1. `@fontsource-variable/geist` installieren, Tokens aus B1 in `app.css` eintragen, alte Hilfsklassen
   und Instrument-Sans-Blöcke noch stehen lassen.
2. Bausteine aus B3 unter `components/ui/` bauen, dazu `<x-wordmark>` und die fehlenden Icons.
3. Eine interne Seite `/styleguide` (nur für Admins) zeigt jeden Baustein in jedem Zustand.
   Sie ist die Prüfvorlage für alle weiteren Phasen.

**Phase 1 – Öffentlicher Auftritt**
`layouts.public`, `public-nav`, `footer`, Startseite (`welcome`), Veranstaltungsübersicht,
Detailseite, Kalender, Fehlerseiten, Rechtliches, Kontakt, FAQ. Startseite exakt nach Referenz.

**Phase 2 – Buchung und Konto**
Buchungsseite exakt nach Referenz, dann Konto anlegen, Bestätigen, Personalisieren, Buchungsdetail,
Warteliste, Anmeldung, Registrierung, Passwort.

**Phase 3 – Angemeldeter Bereich für Teilnehmende**
`layouts.app` mit Seitenleiste und Kopf, Übersicht, Meine Buchungen, Abzeichen, Benachrichtigungen,
Verbindungen, Einstellungen, Datenschutz-Selbstauskunft.

**Phase 4 – Veranstalter-Bereich** (rund 48 Views)
Veranstaltungen anlegen und bearbeiten, Ticketarten, Buchungen, Check-in, Rabattcodes, Reihen,
Rechnungen, Statistik, Organisationen.

**Phase 5 – Administration** (52 Views) und **Hilfe** (54 Views).

**Phase 6 – E-Mails und PDFs** nach B7.

**Phase 7 – Aufräumen**
Font Awesome und die zugehörigen PHP-Pakete entfernen, alte Hilfsklassen und `--sidebar-*`-Variablen
löschen, `layouts.auth`, `layouts.guest`, `home.blade.php`, `public/images/logo.png` entfernen,
Emojis in `app/` (Benachrichtigungen, Betreffzeilen) ersetzen.

### C3 Arbeitsanweisung je View

1. Referenz und dieses Dokument lesen; den passenden Seitentyp aus B5 bestimmen.
2. Eigenes `<html>`-Gerüst durch das richtige Layout ersetzen.
3. Markup auf Bausteine umstellen. Übersetzungshilfe:

   | Bisher | Künftig |
   |---|---|
   | `bg-blue-600 … rounded-lg` an Button oder Link | `<x-ui.button variant="primary">` |
   | grauer oder weißer Button mit Rand | `<x-ui.button variant="secondary">` |
   | `bg-red-600` | `<x-ui.button variant="danger">` (nur Löschen/Stornieren), sonst `secondary` |
   | `text-blue-600 hover:underline` | `<x-ui.button variant="link">` oder schlichter Link in `text-ink` |
   | `<input class="… border-gray-300 …">` + eigenes Label | `<x-ui.field>` |
   | `bg-white rounded-lg shadow p-6` | zuerst prüfen, ob Abstand und Linie reichen; sonst `<x-ui.box>` |
   | Kartenraster mit Veranstaltungen | `<x-ui.event-row>` im Listencontainer |
   | `bg-green-100 text-green-800 rounded-full` u. Ä. | `<x-ui.status>` |
   | farbiger Meldungskasten, Session-Meldung | `<x-ui.alert>` |
   | `<table class="min-w-full divide-y …">` | `<x-ui.table>` |
   | `text-gray-900` / `-700` / `-500`, `-600` | `text-ink` / `text-ink-soft` / `text-muted` |
   | `bg-gray-50`, `bg-gray-100` | `bg-zone` (oder weglassen) |
   | `border-gray-200`, `-300` | `border-line` (Container), `border-line-strong` (Bedienelement) |
   | `bg-gradient-to-*`, `from-*`, `to-*`, `shadow-*` | ersatzlos entfernen |
   | `dark:*` | entfernen (B2) |
   | `<i class="fas fa-…">`, Emoji | `<x-icon.…>` oder ersatzlos |
   | `style="color: {{ $category->color }}"` | entfernen; Kategorie als Text in `text-muted` |

4. Texte nach A7 prüfen („Event" → „Veranstaltung", Button-Beschriftungen). Geänderte Texte in `tests/` suchen.
5. Keine Logik ändern: Routen, Formularfeldnamen, `name`-Attribute, Alpine-Zustände und
   View-Namen bleiben. Tests prüfen View-Namen (`assertViewIs`) und Texte.
6. Bei 1440 px und 390 px ansehen; Tastaturbedienung einmal durchgehen.

### C4 Abnahme je Pull Request

- In den geänderten Views findet diese Suche nichts mehr:
  `grep -nE "(blue|purple|indigo|gray|slate|red|green|yellow|amber|orange)-[0-9]{2,3}|bg-gradient|shadow-|dark:|fa[srb]? fa-|style=\"[^\"]*#[0-9a-fA-F]{3,6}"`
- Keine Emojis in den geänderten Dateien.
- `npm run build`, dann `composer test` laufen durch; `npx prettier --check .` und `vendor/bin/pint --test` sind sauber.
- Kein horizontales Scrollen bei 390 px; jede Klickfläche mindestens 44 px hoch; Fokus überall sichtbar.
- Vorher-/Nachher-Bildschirmfotos der betroffenen Seiten (1440 px und 390 px) hängen am Pull Request.
- Jeder neue Baustein steht auf der Styleguide-Seite.

### C5 Offene Entscheidungen

| Thema | Vorschlag in diesem Dokument | Entscheidung nötig von |
|---|---|---|
| Dunkelmodus | vorerst abschalten, später über Tokens (B2) | Auftraggeber |
| Endgültiges Logo und Name („Bildungsportal") | Platzhalter-Wortmarke (A6) | Auftraggeber |
| Kategoriefarben | in der Oberfläche nicht mehr anzeigen (A2) | Auftraggeber |
| Veranstaltungsbilder | nur auf der Detailseite, ohne Platzhalter (A5) | Auftraggeber |
| Mobile Navigation ab mehr als drei Links | zweite Zeile; Menü erst bei Bedarf (B4) | bei Phase 1 |
| Löschen von `home.blade.php` | löschen, wenn keine Route sie nutzt (B4) | bei Phase 1 prüfen |
