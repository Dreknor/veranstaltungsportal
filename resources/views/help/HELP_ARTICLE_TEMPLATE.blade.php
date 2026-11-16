{{--
    VORLAGE FÜR HILFEARTIKEL (help/user/*, help/organizer/*, help/admin/*)
    Kopieren, Titel/Texte ersetzen, Beispielabschnitte löschen. Nach dieser Datei sehen alle Artikel gleich aus.

    GERÜST
    Jeder Artikel besteht nur aus <x-help.article> (resources/views/components/help/article.blade.php).
    Die Komponente bringt <x-layouts.app> selbst mit – den Artikel NICHT zusätzlich darin einwickeln.
    Sie liefert: Seitenkopf (<x-ui.page-header>) mit Zurück-Link und genau einer H1, Lesetext höchstens
    760 px (max-w-prose) und optional eine Linkliste "Weiterführende Artikel".

    Props
      title          H1 und Seitentitel (Pflicht)
      subtitle       eine Zeile unter der H1 (optional)
      back-href      Ziel des Zurück-Links; Standard route('help.index'). Artikel mit eigener Kategorie-
                     Übersicht (Veranstalter) setzen route('help.category', 'organizer')
      back-label     Standard "Zur Hilfe-Übersicht"; bei back-href der Kategorie z. B. "Zur Veranstalter-Hilfe"
      related-title  Überschrift der Linkliste, Standard "Weiterführende Artikel"
    Slots
      Standard       Inhalt des Artikels (siehe unten)
      related        Linkliste verwandter Artikel, je Zeile ein <x-ui.button variant="link" arrow :href>

    INHALT (schlichtes HTML, die Komponente setzt Schrift, Abstände und Listenzeichen)
      - Gliederung: pro Thema ein <section> mit einer <h2> (24 px, Typo-Skala h2); Unterpunkte als <h3> (18 px).
        Abschnitte haben 48 px Abstand, Überschriften sind linksbündig. Keine Nummern-Kreise, keine Icons,
        KEINE Emojis vor Überschriften. Nummern dürfen als Text in der Überschrift stehen ("1. Basisdaten").
      - Fließtext in <p>, Hervorhebung mit <strong>. Links im Text sind schlichte <a href> (werden unterstrichen).
      - Aufzählungen: <ul><li> mit normalem Aufzählungszeichen. Schritt-Listen: <ol><li> (nummeriert).
        Keine eigenen Klassen an p, ul, ol, li, h2, h3.
      - Hinweise, Tipps, Warnungen: <x-ui.alert tone="info|warning|success|danger" title="Tipp">Text</x-ui.alert>.
        Tipp und Best Practice = info, Wichtig = warning. Keine farbigen Kästen, kein Rand links.
        Hinweise am Artikelende mit class="mt-12" absetzen; mehrere Hinweise untereinander ohne Zusatzklasse.
      - Tabellen: <x-ui.table label="…"> mit <thead>/<tbody>, in <div class="my-6"> (Zellen ohne Klassen).
      - Aufklappfragen (FAQ): <details><summary>Frage</summary><p>Antwort</p></details>; mehrere untereinander
        ergeben eine liniengetrennte Liste, keine Kästen.
      - Inhaltsverzeichnis (optional, nur bei langen Artikeln): <nav aria-label="Inhalt"><ul><li><a href="#id">…</a></li></ul></nav>
        vor dem ersten Abschnitt; die Abschnitte tragen dann id="…".
      - Verwandte Artikel: nicht im Fließtext, sondern im Slot related (Linkliste mit Pfeil).
      - Texte: "Veranstaltung" statt "Event" (außer "Featured Events"), Anrede "Sie", keine Emojis, keine
        Pfeilzeichen im Fließtext außer in Menüpfaden ("Organizer › Meine Veranstaltungen").

    Nicht verwenden: bg-/text-/border-Farbklassen der Tailwind-Standardpalette, Dunkelmodus-Klassen, Schatten, Verläufe,
    Icon-Kreise, Karten-Raster, eigene Klassenketten für Listen und Überschriften.
--}}
<x-help.article title="Artikel-Titel hier" subtitle="Kurze Beschreibung des Artikels" :back-href="route('help.index')"
    back-label="Zur Hilfe-Übersicht">
    <p>
        Einleitungstext, der die Leserin oder den Leser abholt und erklärt, was in diesem Artikel behandelt wird.
    </p>

    <section>
        <h2>1. Erster Abschnitt</h2>
        <p>Beschreibungstext mit Erklärungen und <strong>Hervorhebung</strong>.</p>

        <h3>Unterüberschrift</h3>
        <p>Schritt-für-Schritt-Anleitung:</p>
        <ol>
            <li>Erster Schritt mit Beschreibung</li>
            <li>
                Zweiter Schritt
                <ul>
                    <li>Unterpunkt A</li>
                    <li>Unterpunkt B</li>
                </ul>
            </li>
            <li>Dritter Schritt</li>
        </ol>

        <x-ui.alert tone="info" title="Tipp">Hilfreicher Hinweis für die Nutzerin oder den Nutzer.</x-ui.alert>
    </section>

    <section>
        <h2>2. Zweiter Abschnitt</h2>
        <p>Text für den zweiten Abschnitt.</p>
        <ul>
            <li><strong>Funktion 1:</strong> Beschreibung der Funktion</li>
            <li><strong>Funktion 2:</strong> Beschreibung der Funktion</li>
        </ul>

        <x-ui.alert tone="warning" title="Wichtig">Wichtiger Hinweis oder Warnung.</x-ui.alert>
    </section>

    <section>
        <h2>3. Häufige Fragen</h2>
        <details>
            <summary>Frage, die oft gestellt wird?</summary>
            <p>Antwort auf die Frage.</p>
        </details>
        <details>
            <summary>Eine zweite Frage?</summary>
            <p>Antwort auf die zweite Frage.</p>
        </details>
    </section>

    <x-ui.alert class="mt-12" tone="success" title="Empfehlung">Best Practice oder Erfolgs-Tipp.</x-ui.alert>

    <x-slot:related>
        <x-ui.button variant="link" arrow :href="route('help.article', ['category' => 'CATEGORY', 'article' => 'previous-article'])">Titel des
            verwandten Artikels</x-ui.button>
        <x-ui.button variant="link" arrow :href="route('help.article', ['category' => 'CATEGORY', 'article' => 'next-article'])">Titel eines
            weiteren Artikels</x-ui.button>
    </x-slot:related>
</x-help.article>
