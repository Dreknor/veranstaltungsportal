# Bausteine des Design-Systems

Nachschlagewerk für alle, die Views auf das neue Design umstellen. Verbindliche Grundlage bleibt
[`DESIGN-RICHTLINIEN.md`](DESIGN-RICHTLINIEN.md); die abgenommene Optik zeigen
[`referenz/`](referenz/) und die Seite **`/styleguide`** (nur Admins), auf der jeder Baustein in
jedem Zustand steht.

- Bausteine: `resources/views/components/ui/*.blade.php` → `<x-ui.name>`
- Wortmarke: `resources/views/components/wordmark.blade.php` → `<x-wordmark>`
- Icons: `resources/views/components/icon/*.blade.php` → `<x-icon.name class="h-4 w-4" />`
- Darstellungshelfer (Datum, Preis, Format): `App\Support\Ui`
- Tokens: `@theme` in `resources/css/app.css`

## Grundregeln

1. In umgestellten Views stehen **nur Token-Klassen** (Tabelle unten). Keine `gray-*`, `blue-*`,
   `red-*` …, kein `shadow-*`, kein `dark:`, keine Verläufe, keine `fa-*`-Klassen, keine Emojis,
   kein `style="color:#…"`.
2. Gestaltung lebt in Bausteinen. Wer dieselbe Klassenkette zum dritten Mal schreibt, baut einen Baustein
   (oder fragt nach einem).
3. **Klassen und Attribute durchreichen:** Jeder Baustein nimmt zusätzliche Attribute an (`x-model`,
   `@click`, `:disabled`, `data-*`, `wire:*`, `autocomplete` …). Bei Feldern gilt: `class` landet am
   äußeren Element (Rasterplatz, Breite), **alle übrigen Attribute am Eingabeelement**. Klassen fürs
   Eingabeelement selbst: `input-class` / `select-class` / `textarea-class`.
4. Logik bleibt: Feldnamen, `name`-Attribute, Routen, Alpine-Zustände und View-Namen nicht ändern.
5. Texte nach A7 („Veranstaltung", „kostenfrei", Buttons benennen die Handlung). Wer sichtbaren Text
   ändert, sucht ihn vorher in `tests/`.

### Fallstricke (bitte lesen)

- **Benannter Slot direkt vor Text:** `</x-slot:icon>Bearbeiten` (ohne Leerzeichen) zerstört die Seite –
  Blade macht daraus `@endslotBearbeiten`, der Rest der Ausgabe verschwindet. Immer ein Leerzeichen
  oder einen Zeilenumbruch nach `</x-slot…>` setzen.
- **Alte Klassen an Tabellenzellen entfernen.** `<x-ui.table>` gestaltet `th`/`td` über die Klasse
  `ui-table`; Utility-Klassen an Zellen (`px-6 py-3 text-xs uppercase text-gray-500`) gewinnen
  gegen diese Regeln und müssen weg.
- **Bausteine mit Alpine** (`stepper`, `modal`, `dropdown`, `alert dismissible`, `search-bar` mit
  Filtern) brauchen das gebündelte `resources/js/app.js` (Alpine). Alle Layouts laden es.
- **`focus:outline-none` / `focus:ring-*`** müssen nicht mehr gesetzt werden – die globale Fokus-Regel
  (2 px `ink`, 2 px Abstand) gilt überall und setzt sich gegen diese Klassen durch. In Zellen ohne Platz
  nach außen: Klasse `focus-inset`.
- **Waagrecht scrollende Bereiche** (`overflow-x-auto`) brauchen `relative`, sonst schieben
  `sr-only`-Texte darin die ganze Seite in die Breite. `<x-ui.table>` und `<x-ui.tabs>` erledigen das.

## Token-Klassen

| Bereich | Klassen | Verwendung |
|---|---|---|
| Text | `text-ink` · `text-ink-soft` · `text-muted` | Haupttext · Sekundärtext mit Gewicht · Sekundärtext, Labels |
| Text auf primary | `text-on-primary` | Schrift auf `bg-primary` |
| Flächen | `bg-surface` · `bg-zone` · `bg-zone-hover` · `bg-primary` | Weiß · ruhige Zone · Hover Listenzeile · Primärfläche |
| Linien | `border-line` · `border-line-strong` · `border-ink` | Container, Trenner · Bedienelemente · Hover/aktiv |
| Punkt | `bg-leaf` | nur 8-px-Statuspunkt „frei" und Blatt der Wortmarke |
| Deaktiviert | `text-disabled` · `border-disabled` | deaktivierte Bedienelemente |
| Erfolg | `text-success` · `bg-success-zone` · `border-success-line` | Meldung, Status |
| Warnung | `text-warning` · `bg-warning-zone` · `border-warning-line` | Meldung, Status, Warteliste |
| Fehler | `text-danger` · `bg-danger-zone` · `border-danger-line` · `border-danger-field` · `bg-danger` · `bg-danger-hover` | Meldung · Feldkontur · Löschen-Button |
| Schriftgröße | `text-display` (H1 Startseite) · `text-title` (H1) · `text-h2` (24) · `text-h2-form` (22) · `text-h3` (18) · `text-base` (16) · `text-ui` (15) · `text-small` (14) · `text-caption` (13) | `text-h3` … `text-display` setzen Gewicht 600 und Laufweite mit |
| Gewicht | `font-normal` · `font-medium` · `font-semibold` | nie `font-bold` (über 600) |
| Ziffern | `tabular-nums` | Datum, Zeit, Preis, Mengen, Tabellenzahlen |
| Radius | `rounded-control` (8) · `rounded-box` (12) · `rounded-full` | Bedienelemente · Container · Punkte |
| Breite | `max-w-page` (1200) · `max-w-prose` (760) · `max-w-form` (640) · `max-w-summary` (368) | Seiteninhalt · Lesetext · Formularspalte · Zusammenfassung |
| Seitenrand | `px-page` | `clamp(16px, 4vw, 32px)` |
| Schatten | `shadow-overlay` | **nur** Dialoge und Aufklappmenüs |

Abstände nur aus der Skala 4, 8, 12, 16, 24, 32, 48, 64, 96 px (`gap-1/2/3/4/6/8/12/16/24`, ebenso
`p-`, `m-`). Ausnahmen aus den Richtlinien: Felder untereinander 20 px (`gap-y-5`), Label zum Feld 6 px.

Seitengerüst einer Inhaltsseite: `<div class="mx-auto max-w-page px-page">`.
Formularraster: `grid grid-cols-[repeat(auto-fit,minmax(248px,1fr))] gap-x-4 gap-y-5`.
Breakpoints: `lg` (1024) für Listenzeilen und Zweispalter, `md` (768) für Suchzeile und Navigation.

## Typische Umstellungen

### Button

```blade
{{-- vorher --}}
<a href="{{ route('bookings.create', $event) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Jetzt buchen</a>
<button type="submit" class="btn-secondary">Abbrechen</button>

{{-- nachher --}}
<x-ui.button :href="route('bookings.create', $event)">Buchen</x-ui.button>
<x-ui.button type="submit" variant="secondary">Abbrechen</x-ui.button>
```

### Formularfeld mit Fehler

```blade
{{-- vorher --}}
<label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">E-Mail *</label>
<input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
       class="mt-1 block w-full rounded-md border-gray-300 @error('email') border-red-500 @enderror">
@error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror

{{-- nachher: Label, old(), Fehler, aria-invalid, aria-describedby automatisch --}}
<x-ui.field label="E-Mail" name="email" type="email" :value="$user->email" required autocomplete="email" />
```

### Tabelle

```blade
{{-- vorher --}}
<div class="bg-white shadow rounded-lg overflow-hidden">
  <table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50"><tr>
      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
      <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Betrag</th>
      <th class="px-6 py-3"></th>
    </tr></thead>
    <tbody class="bg-white divide-y divide-gray-200">
      @foreach ($bookings as $booking)
        <tr class="hover:bg-gray-50">
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $booking->customer_name }}</td>
          <td class="px-6 py-4 text-right text-sm">{{ number_format($booking->total, 2, ',', '.') }} €</td>
          <td class="px-6 py-4 text-right"><a href="…" class="text-blue-600 hover:underline">Ansehen</a></td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>

{{-- nachher: Zellen ohne Klassen, nur num / actions --}}
<x-ui.table label="Buchungen">
    <thead>
        <tr>
            <th scope="col">Name</th>
            <th scope="col" class="num">Betrag</th>
            <th scope="col" class="actions"><span class="sr-only">Aktionen</span></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($bookings as $booking)
            <tr>
                <td>{{ $booking->customer_name }}</td>
                <td class="num">{{ \App\Support\Ui::money($booking->total) }}</td>
                <td class="actions"><x-ui.button size="sm" variant="secondary" href="…">Ansehen</x-ui.button></td>
            </tr>
        @endforeach
    </tbody>
</x-ui.table>
```

### Statusanzeige

```blade
{{-- vorher --}}
<span class="px-2 inline-flex text-xs font-semibold rounded-full bg-green-100 text-green-800">{{ $booking->statusLabel() }}</span>

{{-- nachher --}}
<x-ui.status :tone="\App\Support\Ui::bookingTone($booking->status)">{{ $booking->statusLabel() }}</x-ui.status>
```

### Meldung

```blade
{{-- vorher --}}
@if (session('success'))
  <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded"><p class="text-green-700">{{ session('success') }}</p></div>
@endif
<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-yellow-800">Diese Veranstaltung ist ausgebucht.</div>

{{-- nachher: Session-Meldungen macht das Layout mit <x-ui.flash />; in der View entfällt der Block --}}
<x-ui.alert tone="warning">Diese Veranstaltung ist ausgebucht.</x-ui.alert>
```

### Karte → Kasten

```blade
{{-- vorher --}}
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
  <h2 class="text-xl font-bold text-gray-900 mb-4">Rechnungsadresse</h2> …
</div>

{{-- nachher: erst prüfen, ob Abschnitt + Linie reicht --}}
<x-ui.section title="Rechnungsadresse"> … </x-ui.section>
{{-- nur wenn ein Kasten begründet ist (zusammengehörig, Hinweis, Zusammenfassung): --}}
<x-ui.box> <h2 class="m-0 mb-4 text-h3">Rechnungsadresse</h2> … </x-ui.box>
```

### Seitenkopf

```blade
{{-- vorher --}}
<div class="mb-6 flex justify-between items-center">
  <div>
    <a href="{{ route('organizer.events.index') }}" class="text-blue-600">← Zurück</a>
    <h1 class="text-3xl font-bold text-gray-900">Veranstaltung bearbeiten</h1>
  </div>
  <a href="…" class="btn-primary"><i class="fas fa-plus"></i> Neu</a>
</div>

{{-- nachher --}}
<x-ui.page-header title="Veranstaltung bearbeiten" :back-href="route('organizer.events.index')" back-label="Zu den Veranstaltungen">
    <x-slot:actions>
        <x-ui.button href="…">
            <x-slot:icon><x-icon.plus /></x-slot:icon>
            Veranstaltung anlegen
        </x-ui.button>
    </x-slot:actions>
</x-ui.page-header>
```

---

## Aktionen

### `<x-ui.button>`

Button oder Link in Button-Optik. Mit `href` ein `<a>`, sonst `<button type="button">`.

| Prop | Default | Werte / Bedeutung |
|---|---|---|
| `variant` | `primary` | `primary` · `secondary` · `danger` (nur Löschen/Stornieren) · `link` (Textlink) |
| `size` | `md` | `sm` 36 px (nur Tabellenzeilen) · `md` 44 px · `lg` 48 px · `xl` 52 px (Abschluss der Buchung) |
| `href` | `null` | rendert `<a>` |
| `type` | `button` | `submit` zum Absenden |
| `full` | `false` | volle Breite |
| `disabled` | `false` | auch bei `href` (dann `<a aria-disabled>` ohne Ziel) |
| `arrow` | `false` | Pfeil-Icon hinter dem Text (für `link`-Variante „Alle Veranstaltungen →") |
| `wrap` | `false` | mehrzeilige Beschriftung: Höhe wächst mit (Mindesthöhe wie `size`), Text bricht um, höchstens volle Breite; linksbündig, mit `full` zentriert |

Slots: Standard (Text), `icon` (16 px, vor dem Text).

```blade
<x-ui.button type="submit">Änderungen speichern</x-ui.button>
<x-ui.button variant="secondary" :href="route('events.calendar.export', $event->slug)">
    <x-slot:icon><x-icon.calendar /></x-slot:icon>
    In den Kalender
</x-ui.button>
<x-ui.button variant="link" :href="route('events.index')" arrow>Alle Veranstaltungen</x-ui.button>
<x-ui.button variant="secondary" size="sm" x-on:click="open = true" :disabled="! $canEdit">Bearbeiten</x-ui.button>
<x-ui.button type="submit" wrap>Zahlung eingegangen – Rechnung und Tickets senden</x-ui.button>
```

Lange Beschriftungen nicht mit `!h-auto !whitespace-normal` erzwingen, sondern `wrap` setzen.

Nicht verwenden: mehr als ein `primary` pro Abschnitt; `danger` für etwas anderes als endgültiges
Löschen/Stornieren (sonst `secondary`). Reine Fließtext-Links bleiben `<a class="font-medium underline underline-offset-[3px]">`.

### `<x-ui.dropdown>` und `<x-ui.dropdown-item>`

Aufklappmenü, z. B. „Mehr" in Tabellenzeilen ab drei Aktionen. Escape und Klick daneben schließen,
Fokus kehrt zum Button zurück.

| Prop (`dropdown`) | Default | |
|---|---|---|
| `label` | `Mehr` | Text des Buttons bzw. `aria-label` bei `icon-only` |
| `align` | `right` | `right` · `left` |
| `size` | `sm` | `sm` (Tabellen) · `md` |
| `icon-only` | `false` | nur Punkte-Icon |
| `width` | `w-56` | Breite des Menüs |

Slot `trigger`: eigener Button (`x-ref="button" x-on:click="toggle()"`).

| Prop (`dropdown-item`) | Default | |
|---|---|---|
| `href` | `null` | Link, sonst `<button>` |
| `type` | `button` | `submit` (mit `form="id"` für ein Formular außerhalb) |
| `danger` | `false` | Text in Fehlerfarbe |

Slot `icon`.

```blade
<x-ui.dropdown label="Weitere Aktionen" icon-only>
    <x-ui.dropdown-item :href="route('organizer.events.edit', $event)">Bearbeiten</x-ui.dropdown-item>
    <x-ui.dropdown-item type="submit" form="duplicate-{{ $event->id }}">Duplizieren</x-ui.dropdown-item>
    <x-ui.dropdown-item danger x-on:click="$dispatch('open-modal', 'delete-{{ $event->id }}')">Löschen</x-ui.dropdown-item>
</x-ui.dropdown>
<form id="duplicate-{{ $event->id }}" method="POST" action="{{ route('organizer.events.duplicate', $event) }}" class="hidden">@csrf</form>
```

Formulare nicht ins Menü schachteln, sondern über `form="…"` ansprechen (das Menü liegt in Tabellen oft
in einer Zelle mit anderen Formularen).

## Formulare

Alle Feld-Bausteine lesen automatisch: `old()` (nach abgelehntem Formular), den ersten Fehler aus
`$errors` zum `name` (auch `tickets[0][quantity]` → `tickets.0.quantity`) und setzen dann
`aria-invalid="true"` und `aria-describedby` auf Hinweis und Fehler. Die `id` ist standardmäßig der
Feldname ohne Klammern (`tickets_0_quantity`). Gemeinsame Props:

| Prop | Default | |
|---|---|---|
| `label` | `null` | sichtbares Label (ohne Label muss der Aufrufer selbst beschriften) |
| `name`, `id` | `null` | `id` aus `name` abgeleitet |
| `required` | `false` | „*" am Label (`aria-hidden`) + `required` |
| `optional` | `false` | „(optional)" am Label |
| `hint` | `null` | Hinweis unter dem Feld (Text); mit HTML (Link, Umbruch) als Slot `<x-slot:hint>` |
| `error` | auto | eigener Fehlertext; `:error="false"` unterdrückt die Automatik |
| `old` | `true` | `false`: `old()` ignorieren (z. B. Passwort-Bestätigung) |
| `bag` | `default` | benannter Error-Bag |

Über dem Formular steht einmal „* Pflichtfeld" (`<p class="text-small text-muted">* Pflichtfeld</p>`
oder `aside` von `<x-ui.step-heading>`).

### `<x-ui.field>`

Label + `<input>` + Hinweis + Fehler. Zusätzlich: `type` (`text`), `value`, `size` (`md` 48 px · `sm` 44 px),
`input-class`. Mit Inhalt im Standard-Slot ersetzt dieser das `<input>` (eigenes Bedienelement unter
demselben Label).

```blade
<div class="grid grid-cols-[repeat(auto-fit,minmax(248px,1fr))] gap-x-4 gap-y-5">
    <x-ui.field label="Vorname" name="first_name" :value="$user->first_name" required autocomplete="given-name" />
    <x-ui.field label="Telefon" name="phone" type="tel" optional hint="Nur für Rückfragen zur Buchung." />
</div>
<div class="flex flex-wrap gap-x-4 gap-y-5">
    <x-ui.field label="PLZ" name="postal_code" required inputmode="numeric" class="max-w-40 flex-[1_1_112px]" />
    <x-ui.field label="Ort" name="city" required class="min-w-0 flex-[3_1_200px]" />
</div>
<x-ui.field label="Beginn" name="start_date" type="datetime-local" x-model="start" required />
```

Hinweis mit Link oder Bedingung – Slot statt Einzelteilen (`label` + `input` + `hint`):

```blade
<x-ui.field label="Client ID" name="paypal_client_id" :required="! $organization->paypal_client_id">
    <x-slot:hint>
        Erhältlich im <a href="https://developer.paypal.com/dashboard/" class="font-medium text-ink underline underline-offset-[3px]">PayPal Developer Dashboard</a>.
    </x-slot:hint>
</x-ui.field>
```

Der Slot `hint` funktioniert genauso bei `x-ui.select` und `x-ui.textarea`; `aria-describedby` wird gesetzt.
Passwort- und Dateifelder bekommen nie einen Wert vorbelegt.

### `<x-ui.select>`

Wie `field`, mit Chevron. Optionen als `:options="['wert' => 'Text']"` mit `selected`, oder als
`<option>` im Slot (dann `@selected(...)` selbst setzen). `placeholder` erzeugt eine leere erste Option.
Ohne `label` nur das Feld (z. B. in Filterzeilen, dann `aria-label`). `select-class` für das `<select>`.

```blade
<x-ui.select label="Kategorie" name="event_category_id" :options="$categories->pluck('name', 'id')"
             :selected="$event->event_category_id" placeholder="Bitte wählen" required />
```

### `<x-ui.textarea>`

Wie `field`, Mindesthöhe 120 px. Inhalt über `value` oder den Slot; `rows` (4), `textarea-class`.

```blade
<x-ui.textarea label="Beschreibung" name="description" :value="$event->description" required rows="8" />
```

### `<x-ui.checkbox>` und `<x-ui.radio>`

20 px, Zeile mindestens 44 px, Label klickbar. Beschriftung über `label` oder den Slot (Slot erlaubt
Links). Props: `name`, `id`, `value` (Checkbox `1`), `checked`, `label`, `hint`, `required`; Checkbox
zusätzlich `error` (automatisch) und `size` (`ui` 15 px · `small` 14 px). `id` wird aus Name und Wert
gebildet. `class` landet am äußeren `<div>`, alles andere (`data-*`, `x-model`, `@change`, `form`,
`disabled` …) am `<input>`. Klassen für das `<input>` selbst (z. B. JS-Selektoren): `input-class`.
Für Checkbox-Gruppen `name="tags[]"`; `checked` wird nach Fehlern aus `old('tags')` abgeleitet.

```blade
<x-ui.checkbox name="newsletter" :checked="$user->newsletter" label="Newsletter abonnieren" />
<x-ui.checkbox name="permissions[]" :value="$permission->name" :label="$permission->name"
               data-perm="{{ Str::slug($group) }}" input-class="permission-checkbox" />
<x-ui.checkbox name="privacy" required size="small">
    Ich habe die <a href="…" class="font-medium underline underline-offset-[3px]">Datenschutzerklärung</a> gelesen.
</x-ui.checkbox>

<fieldset class="m-0 min-w-0 border-0 p-0">
    <x-ui.label as="legend" required>Teilnahme</x-ui.label>
    <x-ui.radio name="attendance" value="onsite" label="Vor Ort" :checked="$mode === 'onsite'" />
    <x-ui.radio name="attendance" value="online" label="Online" :checked="$mode === 'online'" />
    <x-ui.field-error name="attendance" />
</fieldset>
```

Hinweis: Checkboxen ohne `name`-Bindung an Laravel (reine Alpine-Schalter) einfach mit `x-model` und ohne `name` nutzen.

### `<x-ui.choice>`

Auswahlkarte (Zahlungsmethode, Ticketart mit Einzelauswahl). Props: `name`, `id`, `value`, `type`
(`radio` · `checkbox`), `checked`, `title`, `description`, `required`; Slot `aside` (rechts, z. B. Preis),
Standard-Slot für weitere Zeilen. Gewählt: Fläche `zone`, doppelte Kontur `ink`. Deaktiviert über
`disabled`. `compact`: ohne Mindesthöhe 88 px, Inhalt senkrecht mittig – für einzeilige Optionen in
Listen (z. B. Empfängergruppen mit Anzahl im `aside`); statt lokaler `min-h-0`. `input-class`: Klassen am `<input>`.

```blade
<fieldset class="m-0 min-w-0 border-0 p-0">
    <x-ui.label as="legend" required class="mb-2">Zahlungsmethode</x-ui.label>
    <div class="grid grid-cols-[repeat(auto-fit,minmax(248px,1fr))] gap-x-4 gap-y-3">
        <x-ui.choice name="payment_method" value="invoice" title="Rechnung" description="Sie erhalten die Rechnung per E-Mail" checked x-model="payment" />
        <x-ui.choice name="payment_method" value="paypal" title="PayPal" description="Sie werden nach dem Absenden zu PayPal weitergeleitet" x-model="payment" />
    </div>
    <x-ui.field-error name="payment_method" />
</fieldset>

<x-ui.choice name="segment" value="all" title="Alle Teilnehmenden" compact x-model="segment">
    <x-slot:aside>42</x-slot:aside>
</x-ui.choice>
```

### `<x-ui.stepper>`

Mengenwahl: Minus, Zahlenfeld (40 px), Plus (je 44 × 44 px) in einem 46-px-Rahmen.

| Prop | Default | |
|---|---|---|
| `name`, `id` | – | landen am `<input>` |
| `value` | `0` | Startwert (nach Fehlern `old()`) |
| `min` / `max` / `step` | `0` / `null` / `1` | als Attribute am Feld; werden bei jedem Klick frisch gelesen |
| `label` | `Anzahl` | Name der Sache („Teilnahme") → `aria-label` „Teilnahme: ein Ticket weniger" |
| `unit` | `Ticket` | Einheit im `aria-label` |
| `disabled` | `false` | ganzes Bedienelement aus |
| `input-class` | `''` | zusätzliche Klassen am Feld |

**Zusammenspiel mit bestehendem JavaScript:** Alle Attribute außer `class` landen am `<input>`
(`data-price`, `x-model`, `@change`, `class` über `input-class`). Die Buttons setzen den Wert und lösen
am Feld `input` **und** `change` (bubbling) aus – vorhandene Listener (`addEventListener('change')`,
`x-on:change`, `x-model`) reagieren wie bei Tastatureingabe. Ändert fremdes Skript den Wert oder
`max`, danach am Feld `input` auslösen, damit die Buttons ihren Zustand nachziehen. Eigene
`x-on:input`-Attribute bitte als `@input` schreiben (das `x-on:input` ist intern belegt).

```blade
<div class="flex flex-wrap items-center justify-between gap-x-6 gap-y-3 border-b border-line py-4">
    <div class="flex flex-[1_1_200px] flex-col gap-0.5">
        <span class="text-base font-semibold">{{ $ticketType->name }}</span>
        <span class="text-ui text-muted tabular-nums">{{ \App\Support\Ui::money($ticketType->price) }} pro Person</span>
    </div>
    <x-ui.stepper name="tickets[{{ $ticketType->id }}]" :label="$ticketType->name"
                  :max="min(10, $ticketType->availableQuantity())" data-price="{{ $ticketType->price }}"
                  x-on:change="recalculate()" />
</div>
```

### `<x-ui.input>`, `<x-ui.label>`, `<x-ui.field-error>`, `<x-ui.hint>`

Einzelteile für Sonderfälle, in denen ein Aufrufer das Feld selbst zusammenbaut (Feld mit Button,
Tabellenfilter).

- `x-ui.input`: nur `<input>`; Props `type`, `name`, `id`, `value`, `size` (`md` · `sm`), `invalid`
  (Standard: automatisch aus `$errors`), `old`, `bag`. **Braucht ein Label** (`x-ui.label` oder `aria-label`).
- `x-ui.label`: `for`, `required`, `optional`, `as` (`label` · `legend` · `span`).
- `x-ui.field-error`: `name` (liest `$errors`) oder `message`; `id` (Standard `{id}-error`). Rendert nichts ohne Fehler.
- `x-ui.hint`: `id`; Text im Slot.

```blade
<x-ui.label for="discount_code" optional>Rabattcode</x-ui.label>
<div class="flex max-w-[400px] flex-wrap gap-2">
    <x-ui.input name="discount_code" class="min-w-0 flex-[1_1_180px]" aria-describedby="discount_code-error" />
    <x-ui.button variant="secondary" size="lg" x-on:click="applyCode()">Einlösen</x-ui.button>
</div>
<x-ui.field-error name="discount_code" />
```

### `<x-ui.step-heading>`

Abschnittsmarke im Formular: Kreis 28 px mit Ziffer, daneben `h2-form`; die Nummer steht für
Screenreader im Titel. Props: `number`, `as` (`h2`), `id`; Slot `aside` (rechts, z. B. „* Pflichtfeld").
Abschnitte trennt eine Linie, kein Kasten.

```blade
<section aria-labelledby="step-2" class="border-t border-line py-8">
    <x-ui.step-heading number="2" id="step-2" aside="* Pflichtfeld" class="mb-6">Ihre Daten</x-ui.step-heading>
    …
</section>
```

### `<x-ui.search-bar>` und `<x-ui.search-cell>`

Suchzeile: ein Rahmen, Suchfeld, Zellen, Button „Suchen". Unter 768 px gestapelt.

| Prop (`search-bar`) | Default | |
|---|---|---|
| `action` | aktuelle URL | Ziel des Formulars |
| `method` | `get` | |
| `name` / `value` | `search` / `request(name)` | Suchfeld |
| `label` / `placeholder` | `Suche` / `Thema, Titel oder Ort` | |
| `button` | `Suchen` | |
| `aria-label` | `Veranstaltungen suchen` | Name der Suchregion |
| `filter-count` | `0` | Zähler am Button „Filter" |

Slots: Standard (Zellen), `hidden` (versteckte Felder), `filters` (weitere Filter hinter dem
Sekundärbutton „Filter", als Aufklappfeld), `active` (eine Textzeile der aktiven Filter).

`search-cell`: `label`, `name`, `id`, `options`, `selected` (Standard `request(name)`), `wide` (200 statt
176 px). Ohne `options` steht im Slot ein eigenes Bedienelement mit Klasse `ui-search-control`.

```blade
<x-ui.search-bar :action="route('events.index')" :filter-count="$activeFilters">
    <x-ui.search-cell label="Format" name="event_type" :options="['' => 'Alle', 'physical' => 'Präsenz', 'online' => 'Online', 'hybrid' => 'Hybrid']" />
    <x-ui.search-cell label="Zeitraum" name="period" wide :options="$periods" />
    <x-slot:filters>
        <div class="grid grid-cols-[repeat(auto-fit,minmax(248px,1fr))] gap-x-4 gap-y-5">
            <x-ui.select label="Kategorie" name="category" :options="$categories" placeholder="Alle" />
            <x-ui.field label="Ort" name="city" />
        </div>
    </x-slot:filters>
    @if ($activeFilters)
        <x-slot:active>Kategorie: {{ $categoryName }} · <a href="{{ route('events.index') }}" class="font-medium underline underline-offset-[3px]">Zurücksetzen</a></x-slot:active>
    @endif
</x-ui.search-bar>
```

Nicht verwenden: Chip-Leisten oder eine zweite Filterzeile über der Liste.

## Listen

### `<x-ui.row-list>` und `<x-ui.event-row>`

Die Listenzeile ist der prägende Baustein. `row-list` ist der Container (`<ul>`, Kontur `line`,
Radius 12, Trennlinien); `event-row` ermittelt alles aus dem Event-Modell:

- Datumsblock (`start_date`/`end_date`, mehrtägig „Fr–Sa / 22–23")
- Kategorie (`category->name`, Text in `muted`), Titel als Link auf `events.show`
- „Format · Ort" (`Präsenz`/`Online`/`Hybrid`/`Extern`; Ort = `venue_city`, nicht bei Online);
  darunter Zeit („14:00–17:30 Uhr"), mehrtägig „zweitägig" … ab acht Tagen „bis 23.12.2026",
  bei `has_multiple_dates` „mehrere Termine"
- Preis über `getMinimumPrice()` („45 €", „kostenfrei", „ab 30 €" bei mehreren Ticketpreisen)
- Platzstatus und Aktion:

| Zustand | Bedingung (Modell) | Status | Aktion |
|---|---|---|---|
| buchbar | `hasAvailableTickets()` | „noch 6 frei" (`availableTickets()` bzw. Summe der Ticketkontingente) oder „Plätze frei" | **Buchen** → `bookings.create` |
| Warteliste | ausgebucht (`max_attendees` erreicht oder alle Ticketarten bei 0) | „Warteliste" | **Auf die Warteliste** → `events.show#warteliste` |
| abgesagt | `is_cancelled` | „Abgesagt" | Details |
| beendet | Ende in der Vergangenheit | „Beendet" | Details |
| extern | `isExternal()` | „Anmeldung extern" | `external_booking_button_text` / „Zur Anmeldung" (neues Fenster) |
| sonst | z. B. Verkauf noch nicht gestartet | „derzeit nicht buchbar" | Details |

| Prop (`event-row`) | Default | |
|---|---|---|
| `event` | – | `App\Models\Event` |
| `href` | Detailseite | Ziel des Titels |
| `heading` | `h3` | Element des Titels (`h2` · `h3` · `p`) |
| `show-action` | `true` | `false` blendet die Aktionsspalte aus |

Slots zum Überschreiben: `meta`, `aside` (Preis/Status), `action`; Standard-Slot = zusätzliche Zeile
unter dem Titel. Gesetzte Slots sparen die zugehörigen Abfragen.

**Abfragen:** Für Listen vorladen: `Event::with(['category', 'ticketTypes'])`. Status und Preis rufen
Modellmethoden auf (`hasAvailableTickets()`, `availableTickets()`, `getMinimumPrice()`), die je Zeile
einige Abfragen auslösen – für 10–20 Zeilen unkritisch, für lange Listen paginieren.

**Warteliste:** Der Button springt zu `#warteliste` auf der Detailseite – die Detailseite muss dem
Wartelisten-Block `id="warteliste"` geben (Phase 1).

```blade
<x-ui.section title="Kommende Veranstaltungen" :link-href="route('events.index')" link-label="Alle Veranstaltungen">
    <x-ui.row-list>
        @foreach ($events as $event)
            <x-ui.event-row :event="$event" />
        @endforeach
    </x-ui.row-list>
</x-ui.section>

{{-- Meine Buchungen --}}
<x-ui.row-list>
    @foreach ($bookings as $booking)
        <x-ui.event-row :event="$booking->event" :href="route('bookings.show', $booking->booking_number)">
            <span class="text-small text-muted tabular-nums">Buchung {{ $booking->booking_number }}</span>
            <x-slot:aside>
                <span class="text-base font-semibold whitespace-nowrap">{{ \App\Support\Ui::money($booking->total) }}</span>
                <x-ui.status :tone="\App\Support\Ui::bookingTone($booking->status)">{{ $booking->statusLabel() }}</x-ui.status>
            </x-slot:aside>
            <x-slot:action>
                <x-ui.button variant="secondary" :href="route('bookings.show', $booking->booking_number)" full>Buchung ansehen</x-ui.button>
            </x-slot:action>
        </x-ui.event-row>
    @endforeach
</x-ui.row-list>
```

Nicht verwenden: Kartenraster für Veranstaltungen (gibt es nicht mehr); Kategoriefarben.

### `<x-ui.row>`

Generische Listenzeile im selben Raster, ohne Event-Bezug (Organisationen, Reihen, Abzeichen …).
Props: `as` (`li`), `hover` (`true`). Slots: `lead` (80 px, z. B. Datumsblock), Standard (Hauptspalte),
`meta`, `aside` (128 px, rechtsbündig), `action` (184 px). Fehlende Slots geben ihren Platz frei.
Unter 1024 px: Lead + Hauptspalte, darunter Meta links / Aside rechts, darunter Aktion in voller Breite.

```blade
<x-ui.row-list>
    <x-ui.row>
        <span class="text-small text-muted">Veranstaltungsreihe</span>
        <a href="{{ $seriesUrl }}" class="text-h3 text-ink hover:underline">{{ $series->title }}</a>
        <x-slot:meta><span class="text-ui font-medium">{{ $series->events->count() }} Termine</span></x-slot:meta>
        <x-slot:action><x-ui.button variant="secondary" :href="$seriesUrl" full>Reihe ansehen</x-ui.button></x-slot:action>
    </x-ui.row>
</x-ui.row-list>
```

### `<x-ui.avatar>`

Personenbild: Foto (rund, Kontur `line`) oder Initialen im `zone`-Kreis (`ink-soft`).

| Prop | Default | |
|---|---|---|
| `user` | `null` | `App\Models\User` – Foto nur bei eigenem Upload (`profile_photo`), sonst Initialen; keine Gravatar-Anfrage |
| `src` / `name` / `initials` | `null` | einzeln statt `user`; Initialen werden aus `name` gebildet |
| `size` | `md` | `sm` 40 px (Tabellen) · `md` 56 px (Listenzeilen) · `lg` 96 px (Profilkopf) |
| `label` | `null` | Name für Screenreader; ohne `label` ist das Bild Schmuck (`alt=""` / `aria-hidden`), weil der Name daneben steht |

```blade
<x-ui.avatar :user="$user" size="lg" />
<x-ui.avatar name="Maria Muster" size="sm" />
```

### `<x-ui.person-row>`

Listenzeile für eine Person (Verbindungen, Follower, Suche) auf Basis von `<x-ui.row>`, in
`<x-ui.row-list>`: Avatar (56 px) | Typ (`small` `muted`), Name (`h3`, Link), Bio (`small` `ink-soft`,
zwei Zeilen) | `meta` | `aside` | `action`.

| Prop | Default | |
|---|---|---|
| `user` | `null` | liefert Avatar, Name (`fullName()`), Typ (`userTypeLabel()`), Bio, Link auf `users.show` |
| `name`, `type`, `bio`, `avatar`, `initials` | `null` | einzeln statt bzw. zusätzlich zu `user` |
| `href` | Profil | Ziel des Namens; `:href="false"` ohne Link (z. B. blockierte Nutzer) |
| `bio-lines` | `2` | `2` · `3` Zeilen, danach abgeschnitten |
| `show-bio` | `true` | `false` blendet die Bio aus |

Slots: Standard (weitere Zeilen unter der Bio), `meta`, `aside`, `action` (Buttons mit `full`, Formulare).

```blade
<x-ui.row-list>
    @foreach ($followers as $follower)
        <x-ui.person-row :user="$follower">
            <x-slot:meta>
                <span class="text-ui font-medium tabular-nums">{{ $follower->events_count }} Veranstaltungen</span>
            </x-slot:meta>
            <x-slot:action>
                <x-ui.button variant="secondary" :href="route('users.show', $follower)" full>Profil ansehen</x-ui.button>
            </x-slot:action>
        </x-ui.person-row>
    @endforeach
</x-ui.row-list>
```

### `<x-ui.date-block>`

80 × 80 px als `<time datetime>`. Props: `date` (Carbon oder String), `end` (optional), `tone`
(`zone` · `white` auf Zonen-Fläche). Mehrtägig im selben Monat und
höchstens 7 Tage: „Fr–Sa / 22–23 / Jan 2027"; längere Spannen oder über den Monat hinaus:
„ab Mi / 25 / Nov 2026" (das Ende nennt die Zeile daneben). Für Screenreader wird das Datum ausgeschrieben.

```blade
<x-ui.date-block :date="$event->start_date" :end="$event->end_date" />
```

### `<x-ui.status>`

Punkt (8 px) plus Text; nie Farbe allein, nie gefüllte Pille. `tone`: `free` (Blattgrün, „noch 6 frei"),
`waitlist` (hohler Ring), `neutral` (Standard), `success`, `warning`, `danger`, `info`.
Für Buchungen/Zahlungen: `Ui::bookingTone($status)`, `Ui::paymentTone($status)`.

```blade
<x-ui.status tone="success">Bezahlt</x-ui.status>
<x-ui.status :tone="\App\Support\Ui::paymentTone($booking->payment_status)">{{ $booking->paymentStatusLabel() }}</x-ui.status>
```

### `<x-ui.table>`

**Entscheidung:** ein Wrapper-Baustein plus Klassen-Konvention statt `ui.th`/`ui.td`/`ui.tr`-Komponenten.
Begründung: Die 112 Tabellen enthalten Schleifen, Bedingungen, `colspan`, Formulare und Alpine in den
Zellen. Mit einem Wrapper bleibt das Markup unverändert – es werden nur der äußere Container
ausgetauscht und die Klassen an Zellen entfernt. Das ist mechanisch, gut prüfbar (`grep`) und ohne
Leistungseinbuße (eine Komponente je Zelle wäre bei langen Listen spürbar langsamer).

Props: `label` (Name für Screenreader; macht den Scrollbereich per Tastatur erreichbar – bei breiten
Tabellen setzen), `caption`, `dense` (44 statt 56 px Zeilen), `table-class`.
Zellenklassen: `num` (rechtsbündig, tabellarische Ziffern, kein Umbruch), `actions` (rechts, kein Umbruch,
8 px Abstand). Kopf: `th scope="col"`; Zeilenkopf: `th scope="row"` (Gewicht 500). `tfoot` wird fett.
Aktionen: Textlinks oder `<x-ui.button size="sm">`; ab drei Aktionen `<x-ui.dropdown icon-only>`.

```blade
<x-ui.table label="Teilnehmende">
    <thead><tr><th scope="col">Name</th><th scope="col">Status</th><th scope="col" class="num">Tickets</th></tr></thead>
    <tbody>
        @forelse ($attendees as $attendee)
            <tr>
                <th scope="row">{{ $attendee->name }}</th>
                <td><x-ui.status tone="success">Eingecheckt</x-ui.status></td>
                <td class="num">{{ $attendee->quantity }}</td>
            </tr>
        @empty
            <tr class="hover:bg-transparent"><td colspan="3" class="p-0">
                <x-ui.empty :bordered="false">Noch keine Teilnehmenden.</x-ui.empty>
            </td></tr>
        @endforelse
    </tbody>
</x-ui.table>
```

### `<x-ui.pagination>`

`<x-ui.pagination :paginator="$events" />` – rendert `links()` mit den angepassten Vorlagen
(`resources/views/vendor/pagination/tailwind.blade.php`, `simple-tailwind.blade.php`): „Zurück"/„Weiter"
als Sekundärbuttons, Seitenzahlen als 44-px-Flächen, aktive Seite `primary`; unter 768 px „Seite x von y".
Props: `paginator`, `on-each-side` (1). Bestehende `{{ $items->links() }}` bekommen die neue Optik
automatisch.

## Flächen und Struktur

### `<x-ui.page-header>`

| Prop | Default | |
|---|---|---|
| `title` | `null` | H1 (ersatzweise Slot) |
| `subtitle` | `null` | Zeile darunter (`muted`) |
| `back-href` / `back-label` | `null` / `Zurück` | Zurück-Link mit Pfeil darüber |
| `size` | `title` | `display` nur Startseite |
| `level` | `h1` | `h2` nur für Vorschauen (Styleguide), sonst immer `h1` |

Slots: `actions` (höchstens ein Primär- und ein Sekundärbutton), `meta` (Zeile unter dem Titel).
Abstand nach unten 32 px (`mb-8`, überschreibbar mit `class="mb-0"`). Genau eine H1 pro Seite.

```blade
<x-ui.page-header title="Meine Buchungen" subtitle="Alle Buchungen der letzten zwölf Monate." />
```

### `<x-ui.section>`

Abschnitt mit Überschrift (`h2` 24 px) und optional Link rechts. Props: `title`, `id` (aus dem Titel),
`link-href`, `link-label`, `level` (`h2` · `h3`), `description`; Slot `actions` statt Link.

```blade
<x-ui.section title="Ticketarten" :link-href="route('organizer.events.ticket-types.index', $event)" link-label="Ticketarten verwalten">…</x-ui.section>
```

### `<x-ui.box>`

Kasten mit Kontur `line`, Radius 12. Props: `as` (`div`), `tone` (`white` · `zone`), `padding`
(`none` · `sm` 16 · `md` 24 · `lg` 24–48). Nur mit Grund; Kästen nicht schachteln.
Ersetzt `bg-white rounded-lg shadow p-6` – aber zuerst prüfen, ob `x-ui.section` und eine Linie reichen.

### `<x-ui.zone>`

Vollbreite Fläche `zone` mit Linie (Einstieg, Fuß). Props: `as` (`section`), `contained` (`true`:
Inhalt auf `max-w-page px-page`), `border` (`bottom` · `top` · `both` · `none`).

```blade
<x-ui.zone>
    <div class="flex flex-col gap-8 py-[clamp(40px,6vw,72px)]">
        <h1 class="m-0 max-w-prose text-display">Fortbildungen für Menschen, die Schule gestalten.</h1>
        <x-ui.search-bar :action="route('events.index')" class="max-w-[960px]">…</x-ui.search-bar>
    </div>
</x-ui.zone>
```

### `<x-ui.summary>` mit `summary-lines`, `summary-line`, `summary-total`

Zusammenfassung (Buchung, Bestellung): Fläche `zone`, Polster 24, ab 1024 px mitlaufend.
Props: `title` (`Zusammenfassung`), `id`, `sticky` (`true`), `as` (`aside`); Slot `event` (erste Zeile
fett: Titel, dann Datum, Ort). `summary-line`: `label`, `amount` (oder Slot), `muted`.
`summary-total`: `label` (`Gesamt`), `amount`.

Zweispalter der Buchungsseite:

```blade
<div class="grid items-start gap-10 lg:grid-cols-[minmax(0,640px)_minmax(0,368px)] lg:justify-between lg:gap-x-12">
    <form …>…</form>
    <x-ui.summary>
        <x-slot:event>
            <span>{{ $event->title }}</span>
            <span>{{ \App\Support\Ui::dateTime($event->start_date, $event->end_date) }}</span>
            <span>{{ \App\Support\Ui::formatAndPlace($event) }}</span>
        </x-slot:event>
        <x-ui.summary-lines>
            <x-ui.summary-line label="2 × Teilnahme" amount="90,00 €" />
        </x-ui.summary-lines>
        <x-ui.summary-total amount="90,00 €" />
        <x-ui.checkbox name="privacy" required size="small" form="booking-form">…</x-ui.checkbox>
        <x-ui.button type="submit" form="booking-form" size="xl" full>Kostenpflichtig buchen</x-ui.button>
    </x-ui.summary>
</div>
```

Kasten „Gebuchte Veranstaltung" über dem Formular (Referenz `buchung.html`):
`<x-ui.box tone="zone" padding="sm" class="flex items-center gap-x-5 gap-y-4">` mit
`<x-ui.date-block … tone="white" />` und Kategorie / Titel / Metazeile daneben.

### `<x-ui.empty>`

Leerer Zustand: ein Satz (Slot), optional `title`, genau eine Aktion (`action-href` + `action-label`,
`action-variant` = `secondary`, oder Slot `action`). `bordered` (`true`) – `false` innerhalb von Tabellen.

```blade
<x-ui.empty title="Noch keine Buchungen" :action-href="route('events.index')" action-label="Veranstaltungen entdecken" action-variant="primary">
    Sobald Sie eine Veranstaltung buchen, erscheint sie hier.
</x-ui.empty>
```

### `<x-ui.auth-frame>`

Seitenrahmen für schmale Einzelformulare in `layouts.public` (Anmeldung, Registrierung, Passwort,
E-Mail bestätigen, Bestätigungs- und Ergebnisseiten): Seitenbreite mit `px-page`, Abstand
`clamp(32px, 6vw, 64px)` oben und unten, darin eine zentrierte Spalte von 440 px. `class` landet an der Spalte.

```blade
<x-layouts.public :title="__('Anmelden')">
    <x-ui.auth-frame>
        <x-ui.page-header :title="__('Bei Ihrem Konto anmelden')" />
        <form …>…</form>
    </x-ui.auth-frame>
</x-layouts.public>
```

### Lesetext `.richtext`

CSS-Klasse in `app.css` für HTML aus Editor oder Markdown (Rechtstexte, Beschreibungen, Antworten):
Absätze 16 px / 1,6, `h1`/`h2` wie `h2` (24 px), `h3` wie `h3` (18 px), `h4` 16 px/600, Listen mit
Aufzählungszeichen bzw. Ziffern, Links `ink` unterstrichen (3 px Abstand), Zitate mit Linie
`line-strong`, Tabellen mit Linien `line` und Kopf auf `zone` (scrollen bei Bedarf waagrecht),
Trennlinien `line`. Erstes Element ohne Abstand oben. Die Breite setzt der Aufrufer.

```blade
<div class="richtext max-w-prose">{!! $page->content !!}</div>
<x-legal.prose>{!! $page->content !!}</x-legal.prose>   {{-- = richtext + max-w-prose --}}
```

Im TipTap-Editor sitzt `.richtext` am Container des Editors (`admin/legal-pages/edit`), damit
Bearbeitung und Anzeige gleich aussehen. Keine `[&_p]:…`-Ketten mehr für Lesetext.

## Meldungen

### `<x-ui.alert>`

Props: `tone` (`info` · `success` · `warning` · `danger`), `title`, `dismissible` (nur flüchtige
Meldungen), `size` (`small` · `base`). Rolle `alert` bei danger/warning, sonst `status`. Slot darf
Absätze, Listen und Links enthalten (Links werden unterstrichen).

```blade
<x-ui.alert tone="warning" title="Warteliste">Diese Veranstaltung ist ausgebucht.</x-ui.alert>
<x-ui.alert tone="danger" title="Bitte prüfen Sie Ihre Eingaben."><ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></x-ui.alert>
```

### `<x-ui.flash>`

Rendert die Session-Meldungen `success`, `status` (→ success; bekannte Schlüssel wie
`verification-link-sent` werden übersetzt), `error` (→ danger), `warning`, `info`. Prop `show-errors`
(`false`) zeigt zusätzlich die Fehlerliste (nur für Seiten, deren Felder Fehler nicht selbst zeigen);
`dismissible` (`true`). **Einmal im Layout** über dem Inhalt; die vielen `@if (session('success'))`-Blöcke
in Views entfallen beim Umbau.

```blade
<x-ui.flash class="mb-6" />
```

## Dialoge, Reiter, Kennzahlen, Details

### `<x-ui.modal>`

Basiert auf `<dialog>` + `showModal()`: Fokus bleibt im Dialog, Escape schließt, der Fokus kehrt zum
Auslöser zurück. Props: `name`, `title`, `show` (`false`; `true` öffnet beim Laden, z. B. nach
Fehlern), `max-width` (`560px`). Slots: Standard, `footer` (Aktionen: sekundär links, primär rechts),
`trigger`. Öffnen/Schließen von außen: `$dispatch('open-modal', 'name')` / `$dispatch('close-modal', 'name')`;
innen auch `close()`.

```blade
<x-ui.modal name="cancel-booking" title="Buchung stornieren?" :show="$errors->has('reason')">
    <x-slot:trigger><x-ui.button variant="secondary">Stornieren</x-ui.button></x-slot:trigger>
    <form id="cancel-form" method="POST" action="{{ route('bookings.cancel', $booking->booking_number) }}">
        @csrf
        <x-ui.textarea label="Grund" name="reason" optional />
    </form>
    <x-slot:footer>
        <x-ui.button variant="secondary" x-on:click="close()">Abbrechen</x-ui.button>
        <x-ui.button variant="danger" type="submit" form="cancel-form">Buchung stornieren</x-ui.button>
    </x-slot:footer>
</x-ui.modal>
```

Ersetzt `confirm()`-Abfragen und eigene Overlays (`fixed inset-0 bg-black/50 …`).

**Auslöser außerhalb eines Alpine-Bereichs:** `$dispatch` gibt es nur innerhalb von `x-data`. Ein Button,
der nicht in einem Element mit `x-data` steht (z. B. direkt in einer Tabellenzelle), braucht deshalb
selbst ein leeres `x-data`, sonst passiert beim Klick nichts:

```blade
<x-ui.button variant="danger" size="sm" x-data x-on:click="$dispatch('open-modal', 'delete-{{ $event->id }}')">Löschen</x-ui.button>
```

Innerhalb von `<x-ui.dropdown>` oder eines anderen `x-data`-Bereichs ist das nicht nötig.

### `<x-ui.tabs>` und `<x-ui.tab>`

Reiter als Links (Seitenwechsel, kein JavaScript) oder als Umschalter innerhalb einer Seite.
`tabs`: `label` (Name der Navigation bzw. Gruppe), `variant` (`links` → `<nav>` · `toggle` →
`<div role="group">`). `tab`: `href`, `active`, `count`. Aktiv: Gewicht 600, 2-px-Linie `ink`.

- Mit `href`: Link, aktiv mit `aria-current="page"`.
- Ohne `href`: `<button type="button" aria-pressed>`. Die Optik folgt `aria-pressed="true"`; `active` setzt
  den Startwert, Alpine (`:aria-pressed`) oder ein bestehendes Skript (`setAttribute('aria-pressed', …)`)
  hält ihn aktuell. Klassen für den Zustand muss niemand mehr umschalten.

```blade
<x-ui.tabs label="Bereiche der Veranstaltung">
    <x-ui.tab :href="route('organizer.events.edit', $event)" :active="request()->routeIs('organizer.events.edit')">Übersicht</x-ui.tab>
    <x-ui.tab :href="route('organizer.bookings.index', ['event' => $event->id])" :count="$bookingCount">Buchungen</x-ui.tab>
</x-ui.tabs>
```

Umschalter innerhalb einer Seite (Inhalt per `x-show` bzw. `hidden`):

```blade
<div x-data="{ tab: 'participants' }">
    <x-ui.tabs label="Bereiche" variant="toggle" class="mb-6">
        <x-ui.tab x-on:click="tab = 'participants'" x-bind:aria-pressed="(tab === 'participants').toString()" active>Für Teilnehmer</x-ui.tab>
        <x-ui.tab x-on:click="tab = 'organizers'" x-bind:aria-pressed="(tab === 'organizers').toString()">Für Veranstalter</x-ui.tab>
    </x-ui.tabs>
    <div x-show="tab === 'participants'">…</div>
    <div x-show="tab === 'organizers'" x-cloak>…</div>
</div>
```

Umschalter und Links dürfen in einer Leiste gemischt stehen (`connections/index`); dann `variant="links"`.

### `<x-ui.stat>`

Kennzahl als schlichter Kasten: `label`, `value` (oder Slot), `sub`, `href`. Raster:
`<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">`. Keine Icons in Kreisen, keine farbigen Kacheln.

```blade
<x-ui.stat label="Buchungen (30 Tage)" :value="$bookingCount" sub="davon {{ $pending }} offen" />
```

### `<x-ui.progress>`

Fortschrittsbalken: Balken `primary` auf Fläche `line`, rund, 8 px (`size="lg"`: 12 px). Als
`role="progressbar"` mit `aria-valuenow/-min/-max`; Wert wird auf 0 … `max` begrenzt.

| Prop | Default | |
|---|---|---|
| `value` / `max` | `0` / `100` | Wert und Höchstwert (`max` z. B. größter Monatswert eines Diagramms) |
| `label` | `null` | Name für Screenreader (`aria-label`); alternativ `aria-labelledby="…"` durchreichen |
| `value-text` | `null` | `aria-valuetext`, z. B. „12 von 40 eingecheckt" |
| `size` | `md` | `md` 8 px · `lg` 12 px |
| `decorative` | `false` | nur Schmuck (`aria-hidden`, keine Rolle) – wenn die Zahl daneben als Text steht, z. B. Balken in Tabellen |

```blade
<span id="checkin-label">Check-in-Fortschritt</span> <span>{{ $progress }} %</span>
<x-ui.progress :value="$progress" aria-labelledby="checkin-label" />

<td><x-ui.progress :value="$month->count" :max="$maxMonthly" decorative /></td>
```

Keine eigenen `<div class="h-2 bg-line"><div class="bg-primary" style="width: …">` mehr.

### `<x-ui.dl>` und `<x-ui.dl-item>`

Beschriftung/Wert-Paare für Detailseiten. `dl`: `layout` (`rows`: ab 768 px Label 200 px links, Linien
dazwischen · `stack`: Label über Wert im Raster bis drei Spalten). `dl-item`: `label`, `num`; leerer
Slot zeigt „–".

```blade
<x-ui.dl>
    <x-ui.dl-item label="Buchungsnummer" num>{{ $booking->booking_number }}</x-ui.dl-item>
    <x-ui.dl-item label="Status"><x-ui.status :tone="\App\Support\Ui::bookingTone($booking->status)">{{ $booking->statusLabel() }}</x-ui.status></x-ui.dl-item>
</x-ui.dl>
```

## Navigation

Die Seitengerüste (`layouts.public`, `layouts.app`) bringen Kopf, Seitenleiste, Fuß und
`<x-ui.flash>` mit – Views setzen nur ihren Inhalt. Für Navigationen außerhalb der Layouts:

### `<x-ui.nav-link>`

Navigationslink in Kopfzeilen und Linkleisten: 44 px hoch, Text `ui`, Hover unterstrichen.
Props: `href`, `active` (Gewicht 600 + `aria-current="page"`, sonst 500), `block` (volle Breite,
z. B. Einträge in einem aufklappbaren Handy-Menü).

```blade
<nav aria-label="Veranstalter-Bereich" class="flex items-center gap-1">
    <x-ui.nav-link :href="route('organizer.dashboard')" :active="request()->routeIs('organizer.dashboard')">Dashboard</x-ui.nav-link>
</nav>
```

### `<x-ui.sidebar-item>` und `<x-ui.sidebar-group>`

Eintrag der Seitenleiste im angemeldeten Bereich: 44 px, Text `ui`, Icon 20 px in `muted`; aktiv
Fläche `zone`, Gewicht 600, `aria-current="page"` (kein farbiger Balken). Props: `href` (ohne:
`<button>`), `active`, `icon`, `count` (Zahl rechts), `count-label` (Ergänzung für Screenreader, Standard
„neu"). `icon` nimmt einen Icon-Namen (`calendar`) **oder** einen alten Font-Awesome-Namen
(`fas-calendar-days`, `fa-solid fa-ticket`) – `App\Support\Ui::icon()` bildet ihn auf ein Linien-Icon
ab, unbekannte Namen fallen auf `list` zurück.

`sidebar-group`: Gruppe mit Label (`caption`, `muted`) und `<ul>` für die Einträge. Prop `label`.
In der Seitenleiste werden Einträge über `<x-layouts.sidebar-link>` (= `<li>` + `sidebar-item`) gesetzt.

```blade
<x-ui.sidebar-group label="Veranstalter">
    <x-layouts.sidebar-link :href="route('organizer.events.index')" icon="calendar" :active="request()->routeIs('organizer.events.*')">
        Meine Veranstaltungen
    </x-layouts.sidebar-link>
    <x-layouts.sidebar-link :href="route('notifications.index')" icon="bell" :count="3" count-label="ungelesen">
        Benachrichtigungen
    </x-layouts.sidebar-link>
</x-ui.sidebar-group>
```

## Wortmarke und Icons

### `<x-wordmark>`

Platzhalter-Logo (A6): Petrol-Kreis mit Blatt, Name aus `config('app.name')`, darunter „Hauptfach Mensch".
Für helle Flächen (weiß, `zone`). Props: `size` (`md` Kopf 36 px · `sm` Fuß 32 px), `href` (`/`;
`false` ohne Link). `public/images/logo.png` nicht mehr verwenden.

### Icons

`<x-icon.name class="h-4 w-4" />` – Linien-Icons, viewBox 24, Strich 1,6, `aria-hidden`. Standardgröße
`w-5 h-5`; in Buttons und Text 16 px (`h-4 w-4`), in Navigation 20 px. Vorhanden (Auswahl):
`arrow-left`, `arrow-right`, `chevron-down/-up/-left/-right`, `search`, `plus`, `minus`, `check`,
`check-circle`, `x`, `info`, `warning`, `error`, `calendar`, `clock`, `location`, `monitor`, `ticket`,
`download`, `upload`, `external-link`, `filter`, `more`, `mail`, `lock`, `eye`, `copy`, `trash`,
`settings`, `home`, `menu`, `logout`, `medal`, `receipt`, `document`, `bank`, `tag`, `calendar-check`,
`credit-card`, `qr-code` – vollständige Liste auf `/styleguide`. Alle Icons haben Strich 1,6 und
`aria-hidden`. Icon-Buttons ohne Text brauchen `aria-label`. Font Awesome (`<i class="fas …">`) und
Emojis werden ersetzt oder entfallen.

**Icon-Namen aus Datenbank oder Konfiguration** (Kategorie `icon`, Abzeichen `icon`, Seitenleiste):
nie selbst zuordnen, sondern `<x-ui.icon :name="…" fallback="…">` bzw. `App\Support\Ui::icon($name, $fallback)`
(liefert den Komponentennamen `icon.…`). Akzeptiert eigene Namen (`academic-cap`) und alte
Font-Awesome-Namen in jeder Schreibweise (`fas fa-trophy`, `fa-solid fa-ticket`, `fas-calendar-days`, `fa-heart`);
unbekannte Namen ergeben das Ersatz-Icon. Neue Zuordnungen in `Ui::FA_ICONS` eintragen.

```blade
<x-ui.icon :name="$category->icon" fallback="calendar" class="h-5 w-5 text-muted" />
<x-dynamic-component :component="\App\Support\Ui::icon($badge->icon, 'medal')" class="h-6 w-6" />
```

`<x-badge-icon>` zeigt damit wieder das eigene Symbol des Abzeichens (Ersatz: Medaille).

## `App\Support\Ui` – Formate

| Methode | Ergebnis |
|---|---|
| `Ui::date($c)` | „Do, 26.11.2026" |
| `Ui::time($start, $end)` | „14:00–17:30 Uhr" / „14:00 Uhr" |
| `Ui::dateTime($start, $end)` | „Do, 26.11.2026, 14:00–17:30 Uhr" bzw. „Fr, 22.01.2027 – Sa, 23.01.2027" |
| `Ui::days($start, $end)` | „zweitägig", „5-tägig", ab acht Tagen „bis 23.12.2026" |
| `Ui::price($n)` | Listen: „45 €", „12,50 €", „kostenfrei" |
| `Ui::money($n)` | Buchung/Rechnung: „45,00 €" |
| `Ui::format($event)` / `Ui::formatAndPlace($event)` | „Präsenz" / „Präsenz · Radebeul" |
| `Ui::bookingTone($status)` / `Ui::paymentTone($status)` | Ton für `<x-ui.status>` |
| `Ui::weekday($c)` / `Ui::month($c)` | „Do" / „Nov" |
| `Ui::icon($name, $fallback = 'list')` | Komponentenname eines Linien-Icons zu einem Icon-/Font-Awesome-Namen: `icon.academic-cap` |
| `Ui::logLevelTone($level)` | Ton für die Stufe eines System-Logs (`ERROR` → `danger`, `WARNING` → `warning`, `INFO`/`NOTICE` → `info`) |
| `Ui::auditActionTone($action)` | Ton für eine Audit-Aktion (`…created` → `success`, `…updated` → `info`, `…deleted` → `danger`) |
