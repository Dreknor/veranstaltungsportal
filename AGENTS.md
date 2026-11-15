# Hinweise für Agenten und Entwickler

## Gestaltung

Jede Änderung an der Oberfläche (Blade-Views, CSS, E-Mails, PDFs) folgt
`docs/design/DESIGN-RICHTLINIEN.md`. Die abgenommenen Referenzseiten liegen in
`docs/design/referenz/`.

Kurzfassung: nur die Farb- und Schrift-Tokens aus den Richtlinien verwenden, Bausteine unter
`resources/views/components/ui/` statt eigener Klassenketten, keine Verläufe, Schatten,
Emojis, Font-Awesome-Klassen oder `dark:`-Klassen.

## Prüfen

- `npm run build` (vor den Tests nötig, die Views laden das Vite-Manifest)
- `composer test`
- `npx prettier --check .` und `vendor/bin/pint --test`
