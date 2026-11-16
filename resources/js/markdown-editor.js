/**
 * Markdown-Eingabefeld (<x-ui.markdown-editor>): Werkzeugleiste für das <textarea> und eine Vorschau,
 * die der Server mit demselben Renderer erzeugt wie die spätere Anzeige (App\Support\Ui::markdown).
 *
 * Alpine-Komponente: x-data="markdownEditor({ previewUrl })", das Feld trägt x-ref="input".
 * Die Vorschau steht unter dem Feld (das Feld bleibt sichtbar, damit Pflichtfeld-Prüfung und Fokus funktionieren).
 */
export function markdownEditor({ previewUrl }) {
    return {
        preview: false,
        loading: false,
        html: '',
        failed: false,
        timer: null,

        field() {
            return this.$refs.input;
        },

        /** Auswahl mit Zeichen umschließen (Fett, Kursiv); ohne Auswahl einen Platzhalter einsetzen. */
        wrap(before, after, placeholder) {
            const el = this.field();
            const { selectionStart: start, selectionEnd: end, value } = el;
            const selected = value.slice(start, end) || placeholder;
            this.replace(start, end, before + selected + after);
            el.setSelectionRange(
                start + before.length,
                start + before.length + selected.length,
            );
        },

        /** Jede ausgewählte Zeile mit einem Präfix versehen (Überschrift, Aufzählung, Nummerierung). */
        prefixLines(prefix) {
            const el = this.field();
            const { value } = el;
            const lineStart =
                value.lastIndexOf('\n', el.selectionStart - 1) + 1;
            let lineEnd = value.indexOf('\n', el.selectionEnd);
            if (lineEnd === -1) lineEnd = value.length;

            const lines = value.slice(lineStart, lineEnd).split('\n');
            let n = 0;
            const text = lines
                .map((line) => {
                    // Leere Zeilen bleiben leer (sie trennen Absätze und Listen).
                    if (line.trim() === '') return line;
                    const clean = line.replace(
                        /^(#{1,6}\s+|[-*+]\s+|\d+\.\s+)/,
                        '',
                    );
                    n += 1;
                    return (
                        (typeof prefix === 'function'
                            ? prefix(n - 1)
                            : prefix) + clean
                    );
                })
                .join('\n');

            this.replace(lineStart, lineEnd, text);
            el.setSelectionRange(
                lineStart + text.length,
                lineStart + text.length,
            );
        },

        bold() {
            this.wrap('**', '**', 'fetter Text');
        },
        italic() {
            this.wrap('*', '*', 'kursiver Text');
        },
        heading() {
            this.prefixLines('## ');
        },
        bullets() {
            this.prefixLines('- ');
        },
        numbers() {
            this.prefixLines((i) => i + 1 + '. ');
        },

        /** Link: Auswahl wird Linktext, die Adresse ist danach markiert und kann direkt überschrieben werden. */
        link() {
            const el = this.field();
            const { selectionStart: start, selectionEnd: end, value } = el;
            const label = value.slice(start, end) || 'Linktext';
            const url = 'https://';
            this.replace(start, end, '[' + label + '](' + url + ')');
            const urlStart = start + label.length + 3;
            el.setSelectionRange(urlStart, urlStart + url.length);
        },

        /** Text ersetzen, ohne die Rückgängig-Funktion des Browsers zu verlieren (execCommand, sonst direkt). */
        replace(start, end, text) {
            const el = this.field();
            el.focus();
            el.setSelectionRange(start, end);
            const done =
                document.queryCommandSupported?.('insertText') &&
                document.execCommand('insertText', false, text);
            if (!done) {
                el.setRangeText(text, start, end, 'end');
                el.dispatchEvent(new Event('input', { bubbles: true }));
            }
        },

        /** Tastenkürzel im Feld: Strg/Cmd+B fett, Strg/Cmd+I kursiv, Strg/Cmd+K Link. */
        shortcut(event) {
            if (!(event.ctrlKey || event.metaKey) || event.altKey) return;
            const key = event.key.toLowerCase();
            const actions = { b: 'bold', i: 'italic', k: 'link' };
            if (actions[key]) {
                event.preventDefault();
                this[actions[key]]();
            }
        },

        /** Pfeiltasten in der Werkzeugleiste: zum nächsten bzw. vorigen Button (am Ende wieder vorn). */
        moveFocus(event, step) {
            const buttons = Array.from(
                event.currentTarget.querySelectorAll('button'),
            );
            const index = buttons.indexOf(document.activeElement);
            if (index === -1) return;
            buttons[(index + step + buttons.length) % buttons.length].focus();
        },

        /** Vorschau ein- und ausblenden; solange sie offen ist, folgt sie den Eingaben (leicht verzögert). */
        togglePreview() {
            this.preview = !this.preview;
            if (this.preview) this.render();
        },

        changed() {
            if (!this.preview) return;
            clearTimeout(this.timer);
            this.timer = setTimeout(() => this.render(), 400);
        },

        async render() {
            this.loading = true;
            this.failed = false;
            try {
                const response = await fetch(previewUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN':
                            document.querySelector('meta[name="csrf-token"]')
                                ?.content ?? '',
                    },
                    body: JSON.stringify({ text: this.field().value }),
                });
                if (!response.ok) throw new Error(response.statusText);
                this.html = (await response.json()).html;
            } catch (e) {
                this.failed = true;
            } finally {
                this.loading = false;
            }
        },
    };
}
