import { Editor } from "@tiptap/core";
import StarterKit from "@tiptap/starter-kit";
import Link from "@tiptap/extension-link";
import Underline from "@tiptap/extension-underline";
import TextAlign from "@tiptap/extension-text-align";
import { TextStyle } from "@tiptap/extension-text-style";
import Color from "@tiptap/extension-color";
import Highlight from "@tiptap/extension-highlight";

/**
 * Alpine.js-Komponente für den Tiptap Rich-Text-Editor
 * Verwendung: x-data="richEditor({ name: 'content', value: '...' })"
 */
export function richEditor({ name, value = "" }) {
    return {
        editor: null,
        content: value,
        fieldName: name,
        showLinkInput: false,
        linkUrl: "",

        init() {
            const self = this;

            this.editor = new Editor({
                element: this.$refs.editorContent,
                extensions: [
                    StarterKit.configure({
                        heading: { levels: [1, 2, 3, 4] },
                    }),
                    Underline,
                    Link.configure({
                        openOnClick: false,
                        HTMLAttributes: {
                            class: "text-blue-600 underline hover:text-blue-800",
                            rel: "noopener noreferrer",
                        },
                    }),
                    TextAlign.configure({
                        types: ["heading", "paragraph"],
                    }),
                    TextStyle,
                    Color,
                    Highlight.configure({ multicolor: false }),
                ],
                content: value,
                onUpdate({ editor }) {
                    self.content = editor.getHTML();
                    // Sync zu verstecktem Textarea/Input
                    const field = document.getElementById(
                        "hidden-" + self.fieldName,
                    );
                    if (field) field.value = self.content;
                },
            });
        },

        destroy() {
            this.editor?.destroy();
        },

        // ── Formatierungshelfer ──────────────────────────────────────────────

        isActive(type, attrs = {}) {
            return this.editor?.isActive(type, attrs) ?? false;
        },

        toggleBold() {
            this.editor?.chain().focus().toggleBold().run();
        },
        toggleItalic() {
            this.editor?.chain().focus().toggleItalic().run();
        },
        toggleUnderline() {
            this.editor?.chain().focus().toggleUnderline().run();
        },
        toggleStrike() {
            this.editor?.chain().focus().toggleStrike().run();
        },
        toggleHighlight() {
            this.editor?.chain().focus().toggleHighlight().run();
        },

        setHeading(level) {
            this.editor?.chain().focus().toggleHeading({ level }).run();
        },
        setParagraph() {
            this.editor?.chain().focus().setParagraph().run();
        },

        toggleBulletList() {
            this.editor?.chain().focus().toggleBulletList().run();
        },
        toggleOrderedList() {
            this.editor?.chain().focus().toggleOrderedList().run();
        },
        toggleBlockquote() {
            this.editor?.chain().focus().toggleBlockquote().run();
        },
        toggleCodeBlock() {
            this.editor?.chain().focus().toggleCodeBlock().run();
        },
        setHorizontalRule() {
            this.editor?.chain().focus().setHorizontalRule().run();
        },

        setTextAlign(alignment) {
            this.editor?.chain().focus().setTextAlign(alignment).run();
        },

        // ── Link ─────────────────────────────────────────────────────────────

        openLinkDialog() {
            const prev = this.editor?.getAttributes("link").href ?? "";
            this.linkUrl = prev;
            this.showLinkInput = true;
            this.$nextTick(() => this.$refs.linkInput?.focus());
        },

        confirmLink() {
            if (this.linkUrl) {
                this.editor
                    ?.chain()
                    .focus()
                    .extendMarkRange("link")
                    .setLink({ href: this.linkUrl })
                    .run();
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

        undo() {
            this.editor?.chain().focus().undo().run();
        },
        redo() {
            this.editor?.chain().focus().redo().run();
        },
    };
}

window.richEditor = richEditor;


