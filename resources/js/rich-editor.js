import { Editor } from "@tiptap/core";
import StarterKit from "@tiptap/starter-kit";
import TextAlign from "@tiptap/extension-text-align";
import { TextStyle } from "@tiptap/extension-text-style";
import Color from "@tiptap/extension-color";
import Highlight from "@tiptap/extension-highlight";
/**
 * Alpine.js Rich-Text-Editor mit Tiptap v3.
 *
 * Kernproblem: Alpine macht alle Properties eines x-data-Objekts reaktiv
 * (via Vue-Reactivity-Proxies). Ein reaktiver Proxy um den Tiptap-Editor
 * führt dazu, dass Alpine bei jedem Reaktivitätszyklus auf editor.state
 * zugreift und dabei ProseMirror's internen State-Sequenz bricht →
 * "Applying a mismatched transaction".
 *
 * Lösung: Den Editor in einer Map außerhalb von Alpine speichern,
 * die von Alpine nicht reaktiv gemacht wird. Nur primitive Werte
 * (content, _tick, etc.) bleiben reaktiv.
 */
// Globaler nicht-reaktiver Speicher für Editor-Instanzen (keyed by instanceId)
const editorInstances = new Map();
export function richEditor({ name, value = "" }) {
    // Eindeutige ID für diese Instanz
    const instanceId = Math.random().toString(36).slice(2, 9);
    return {
        // Nur primitive/einfache Werte – Alpine macht diese reaktiv
        content: value,
        fieldName: name,
        showLinkInput: false,
        linkUrl: "",
        _tick: 0,
        _instanceId: instanceId,
        // Getter: holt den Editor aus dem nicht-reaktiven Speicher
        get _editor() {
            return editorInstances.get(this._instanceId) ?? null;
        },
        init() {
            if (editorInstances.has(this._instanceId)) return;
            const self = this;
            const container = this.$refs.editorContent;
            const editor = new Editor({
                element: container,
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
                autofocus: false,
                onUpdate({ editor: e }) {
                    self.content = e.getHTML();
                    self._tick++;
                    const field = document.getElementById("hidden-" + self.fieldName);
                    if (field) field.value = self.content;
                },
                onSelectionUpdate() {
                    self._tick++;
                },
            });
            // Außerhalb von Alpine's Reaktivitätssystem speichern
            editorInstances.set(this._instanceId, editor);
        },
        destroy() {
            const editor = editorInstances.get(this._instanceId);
            if (editor) {
                editor.destroy();
                editorInstances.delete(this._instanceId);
            }
        },
        isActive(type, attrs = {}) {
            this._tick; // reaktive Abhängigkeit
            const editor = editorInstances.get(this._instanceId);
            return editor?.isActive(type, attrs) ?? false;
        },
        toggleBold()        { editorInstances.get(this._instanceId)?.chain().focus().toggleBold().run(); },
        toggleItalic()      { editorInstances.get(this._instanceId)?.chain().focus().toggleItalic().run(); },
        toggleUnderline()   { editorInstances.get(this._instanceId)?.chain().focus().toggleUnderline().run(); },
        toggleStrike()      { editorInstances.get(this._instanceId)?.chain().focus().toggleStrike().run(); },
        toggleHighlight()   { editorInstances.get(this._instanceId)?.chain().focus().toggleHighlight().run(); },
        setHeading(level)   { editorInstances.get(this._instanceId)?.chain().focus().toggleHeading({ level }).run(); },
        setParagraph()      { editorInstances.get(this._instanceId)?.chain().focus().setParagraph().run(); },
        toggleBulletList()  { editorInstances.get(this._instanceId)?.chain().focus().toggleBulletList().run(); },
        toggleOrderedList() { editorInstances.get(this._instanceId)?.chain().focus().toggleOrderedList().run(); },
        toggleBlockquote()  { editorInstances.get(this._instanceId)?.chain().focus().toggleBlockquote().run(); },
        toggleCodeBlock()   { editorInstances.get(this._instanceId)?.chain().focus().toggleCodeBlock().run(); },
        setHorizontalRule() { editorInstances.get(this._instanceId)?.chain().focus().setHorizontalRule().run(); },
        setTextAlign(a)     { editorInstances.get(this._instanceId)?.chain().focus().setTextAlign(a).run(); },
        openLinkDialog() {
            const editor = editorInstances.get(this._instanceId);
            this.linkUrl = editor?.getAttributes("link").href ?? "";
            this.showLinkInput = true;
            this.$nextTick(() => this.$refs.linkInput?.focus());
        },
        confirmLink() {
            const editor = editorInstances.get(this._instanceId);
            if (this.linkUrl) {
                editor?.chain().focus().extendMarkRange("link").setLink({ href: this.linkUrl }).run();
            } else {
                editor?.chain().focus().unsetLink().run();
            }
            this.showLinkInput = false;
            this.linkUrl = "";
        },
        removeLink() {
            editorInstances.get(this._instanceId)?.chain().focus().unsetLink().run();
            this.showLinkInput = false;
        },
        undo() { editorInstances.get(this._instanceId)?.chain().focus().undo().run(); },
        redo() { editorInstances.get(this._instanceId)?.chain().focus().redo().run(); },
    };
}
