import { Editor } from "@tiptap/core";
import StarterKit from "@tiptap/starter-kit";
import TextAlign from "@tiptap/extension-text-align";
import { TextStyle } from "@tiptap/extension-text-style";
import Color from "@tiptap/extension-color";
import Highlight from "@tiptap/extension-highlight";

/**
 * Alpine.js-Komponente für den Tiptap Rich-Text-Editor (Tiptap v3).
 * Registrierung: Alpine.data('richEditor', richEditor)
 * Verwendung:    x-data="richEditor({ name: 'content', value: '...' })"
 */
export function richEditor({ name, value = "" }) {
    return {
        editor: null,
        content: value,
        fieldName: name,
        showLinkInput: false,
        linkUrl: "",
        // Reaktiver Zähler – wird bei jeder Selektion/Transaction inkrementiert,
        // damit Alpine :class-Bindings mit isActive() neu auswertet.
        _tick: 0,

        init() {
            const self = this;
            const container = this.$refs.editorContent;

            // Tiptap v3: Editor OHNE element-Option erstellen,
            // dann view.dom manuell in den Container hängen.
            // Das vermeidet "mismatched transaction" Fehler.
            this.editor = new Editor({
                extensions: [
                    StarterKit.configure({
                        heading: { levels: [1, 2, 3, 4] },
                        link: {
                            openOnClick: false,
                            HTMLAttributes: {
                                class: "text-blue-600 underline hover:text-blue-800",
                                rel: "noopener noreferrer",
                            },
                        },
                        underline: {},
                    }),
                    TextAlign.configure({
                        types: ["heading", "paragraph"],
                    }),
                    TextStyle,
                    Color,
                    Highlight.configure({ multicolor: false }),
                ],
                content: value,
                onUpdate({ editor: e }) {
                    self.content = e.getHTML();
                    self._tick++;
                    const field = document.getElementById("hidden-" + self.fieldName);
                    if (field) field.value = self.content;
                },
                onSelectionUpdate() { self._tick++; },
                onTransaction()     { self._tick++; },
            });

            // Das ProseMirror-DOM-Element in den Container einhängen
            container.appendChild(this.editor.view.dom);

            // Cleanup via MutationObserver (Alpine v3 hat kein $cleanup)
            const observer = new MutationObserver(() => {
                if (!document.contains(container)) {
                    self.editor?.destroy();
                    observer.disconnect();
                }
            });
            observer.observe(document.body, { childList: true, subtree: true });
        },

        // ── Reaktives isActive ───────────────────────────────────────────────

        isActive(type, attrs = {}) {
            this._tick; // Abhängigkeit für Alpine-Reaktivität
            return this.editor?.isActive(type, attrs) ?? false;
        },

        // ── Formatierung ─────────────────────────────────────────────────────

        toggleBold()      { this.editor?.chain().focus().toggleBold().run(); },
        toggleItalic()    { this.editor?.chain().focus().toggleItalic().run(); },
        toggleUnderline() { this.editor?.chain().focus().toggleUnderline().run(); },
        toggleStrike()    { this.editor?.chain().focus().toggleStrike().run(); },
        toggleHighlight() { this.editor?.chain().focus().toggleHighlight().run(); },

        setHeading(level) { this.editor?.chain().focus().toggleHeading({ level }).run(); },
        setParagraph()    { this.editor?.chain().focus().setParagraph().run(); },

        toggleBulletList()  { this.editor?.chain().focus().toggleBulletList().run(); },
        toggleOrderedList() { this.editor?.chain().focus().toggleOrderedList().run(); },
        toggleBlockquote()  { this.editor?.chain().focus().toggleBlockquote().run(); },
        toggleCodeBlock()   { this.editor?.chain().focus().toggleCodeBlock().run(); },
        setHorizontalRule() { this.editor?.chain().focus().setHorizontalRule().run(); },
        setTextAlign(a)     { this.editor?.chain().focus().setTextAlign(a).run(); },

        // ── Link ─────────────────────────────────────────────────────────────

        openLinkDialog() {
            this.linkUrl = this.editor?.getAttributes("link").href ?? "";
            this.showLinkInput = true;
            this.$nextTick(() => this.$refs.linkInput?.focus());
        },

        confirmLink() {
            if (this.linkUrl) {
                this.editor?.chain().focus().extendMarkRange("link").setLink({ href: this.linkUrl }).run();
            } else {
                this.editor?.chain().focus().unsetLink().run();
            }
            this.showLinkInput = false;
            this.linkUrl = "";
        },

        removeLink() {
            this.editor?.chain().focus().unsetLink().run();
            this.showLinkInput = false;
        },

        // ── History ──────────────────────────────────────────────────────────

        undo() { this.editor?.chain().focus().undo().run(); },
        redo() { this.editor?.chain().focus().redo().run(); },
    };
}
