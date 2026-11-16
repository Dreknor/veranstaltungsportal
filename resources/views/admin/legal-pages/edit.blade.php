<x-layouts.app :title="\App\Models\LegalPage::TYPES[$page->type] . ' bearbeiten'">
    @php
        $routeName = match ($page->type) {
            'agb' => 'agb',
            'datenschutz' => 'datenschutz',
            'impressum' => 'impressum',
        };
    @endphp

    <x-ui.page-header :title="\App\Models\LegalPage::TYPES[$page->type] . ' bearbeiten'" subtitle="Inhalt der rechtlichen Seite bearbeiten." :back-href="route('admin.legal-pages.index')"
        back-label="Zurück zur Übersicht" />

    <form method="POST" action="{{ route('admin.legal-pages.update', $page->type) }}" id="legal-page-form"
        class="max-w-prose">
        @csrf
        @method('PUT')

        <div class="flex flex-col gap-y-6">
            <x-ui.field label="Seitentitel" name="title" :value="$page->title" required />

            {{-- Verstecktes Feld für den HTML-Inhalt --}}
            <input type="hidden" name="content" id="hidden-content" value="{{ old('content', $page->content) }}">

            {{-- Rich-Text-Editor --}}
            <div x-data="richEditor({ name: 'content', value: {{ Js::from(old('content', $page->content)) }} })">
                <x-ui.label as="span">Inhalt</x-ui.label>

                {{-- Werkzeugleiste --}}
                <div role="toolbar" aria-label="Textformatierung"
                    class="flex flex-wrap items-center gap-2 rounded-t-control border border-b-0 border-line-strong bg-zone p-2">
                    <x-ui.button variant="secondary" size="sm" x-on:click="undo()"
                        title="Rückgängig (Strg+Z)">Rückgängig</x-ui.button>
                    <x-ui.button variant="secondary" size="sm" x-on:click="redo()"
                        title="Wiederholen (Strg+Y)">Wiederholen</x-ui.button>
                    <x-ui.button variant="secondary" size="sm" x-on:click="setParagraph()" title="Absatz"
                        x-bind:aria-pressed="isActive('paragraph')"
                        class="aria-pressed:border-ink aria-pressed:bg-zone aria-pressed:font-semibold">Absatz</x-ui.button>
                    <x-ui.button variant="secondary" size="sm" x-on:click="setHeading(1)" title="Überschrift 1"
                        aria-label="Überschrift 1" x-bind:aria-pressed="isActive('heading', { level: 1 })"
                        class="aria-pressed:border-ink aria-pressed:bg-zone aria-pressed:font-semibold">H1</x-ui.button>
                    <x-ui.button variant="secondary" size="sm" x-on:click="setHeading(2)" title="Überschrift 2"
                        aria-label="Überschrift 2" x-bind:aria-pressed="isActive('heading', { level: 2 })"
                        class="aria-pressed:border-ink aria-pressed:bg-zone aria-pressed:font-semibold">H2</x-ui.button>
                    <x-ui.button variant="secondary" size="sm" x-on:click="setHeading(3)" title="Überschrift 3"
                        aria-label="Überschrift 3" x-bind:aria-pressed="isActive('heading', { level: 3 })"
                        class="aria-pressed:border-ink aria-pressed:bg-zone aria-pressed:font-semibold">H3</x-ui.button>
                    <x-ui.button variant="secondary" size="sm" x-on:click="toggleBold()" title="Fett (Strg+B)"
                        x-bind:aria-pressed="isActive('bold')"
                        class="aria-pressed:border-ink aria-pressed:bg-zone aria-pressed:font-semibold">Fett</x-ui.button>
                    <x-ui.button variant="secondary" size="sm" x-on:click="toggleItalic()" title="Kursiv (Strg+I)"
                        x-bind:aria-pressed="isActive('italic')"
                        class="aria-pressed:border-ink aria-pressed:bg-zone aria-pressed:font-semibold">Kursiv</x-ui.button>
                    <x-ui.button variant="secondary" size="sm" x-on:click="toggleUnderline()"
                        title="Unterstrichen (Strg+U)" x-bind:aria-pressed="isActive('underline')"
                        class="aria-pressed:border-ink aria-pressed:bg-zone aria-pressed:font-semibold">Unterstrichen</x-ui.button>
                    <x-ui.button variant="secondary" size="sm" x-on:click="toggleStrike()" title="Durchgestrichen"
                        x-bind:aria-pressed="isActive('strike')"
                        class="aria-pressed:border-ink aria-pressed:bg-zone aria-pressed:font-semibold">Durchgestrichen</x-ui.button>
                    <x-ui.button variant="secondary" size="sm" x-on:click="toggleHighlight()" title="Hervorheben"
                        x-bind:aria-pressed="isActive('highlight')"
                        class="aria-pressed:border-ink aria-pressed:bg-zone aria-pressed:font-semibold">Hervorheben</x-ui.button>
                    <x-ui.button variant="secondary" size="sm" x-on:click="setTextAlign('left')"
                        title="Linksbündig" x-bind:aria-pressed="isActive({ textAlign: 'left' })"
                        class="aria-pressed:border-ink aria-pressed:bg-zone aria-pressed:font-semibold">Linksbündig</x-ui.button>
                    <x-ui.button variant="secondary" size="sm" x-on:click="setTextAlign('center')"
                        title="Zentriert" x-bind:aria-pressed="isActive({ textAlign: 'center' })"
                        class="aria-pressed:border-ink aria-pressed:bg-zone aria-pressed:font-semibold">Zentriert</x-ui.button>
                    <x-ui.button variant="secondary" size="sm" x-on:click="setTextAlign('right')"
                        title="Rechtsbündig" x-bind:aria-pressed="isActive({ textAlign: 'right' })"
                        class="aria-pressed:border-ink aria-pressed:bg-zone aria-pressed:font-semibold">Rechtsbündig</x-ui.button>
                    <div class="relative">
                        <x-ui.button variant="secondary" size="sm" x-on:click="openLinkDialog()"
                            x-bind:aria-pressed="isActive('link')"
                            class="aria-pressed:border-ink aria-pressed:bg-zone aria-pressed:font-semibold"
                            title="Link einfügen/bearbeiten">Link</x-ui.button>

                        {{-- Link-Eingabe --}}
                        <div x-show="showLinkInput" x-cloak x-on:click.outside="showLinkInput = false"
                            class="absolute left-0 top-full z-50 mt-1 flex w-80 max-w-[calc(100vw-48px)] flex-col gap-3 rounded-control border border-line bg-surface p-3 shadow-overlay">
                            <x-ui.input x-ref="linkInput" type="url" x-model="linkUrl" :old="false"
                                placeholder="https://..." aria-label="Adresse des Links"
                                x-on:keydown.enter.prevent="confirmLink()"
                                x-on:keydown.escape="showLinkInput = false" />
                            <div class="flex flex-wrap gap-2">
                                <x-ui.button size="sm" x-on:click="confirmLink()">Übernehmen</x-ui.button>
                                <x-ui.button variant="secondary" size="sm"
                                    x-on:click="removeLink()">Entfernen</x-ui.button>
                            </div>
                        </div>
                    </div>
                    <x-ui.button variant="secondary" size="sm" x-on:click="setHorizontalRule()"
                        title="Trennlinie">Trennlinie</x-ui.button>
                </div>

                {{-- Editor-Bereich: kein Whitespace darin – Tiptap mountet via mount() --}}
                <div x-ref="editorContent"
                    class="rich-editor-content richtext min-h-96 rounded-b-control border border-line-strong bg-surface [&_.tiptap]:min-h-96 [&_.tiptap]:p-4">
                </div>
                <x-ui.field-error name="content" />
            </div>

            {{-- Meta-Info --}}
            @if ($page->last_updated_at)
                <p class="m-0 text-small text-muted">
                    Zuletzt gespeichert am <span
                        class="tabular-nums">{{ $page->last_updated_at->format('d.m.Y') }}</span>
                    um <span class="tabular-nums">{{ $page->last_updated_at->format('H:i') }} Uhr</span>
                    @if ($page->editor)
                        von {{ $page->editor->fullName() }}
                    @endif
                </p>
            @endif
        </div>

        {{-- Aktionen --}}
        <div class="mt-8 flex flex-wrap gap-3 border-t border-line pt-8">
            <x-ui.button type="submit">Speichern</x-ui.button>
            <x-ui.button variant="secondary" :href="route('admin.legal-pages.index')">Abbrechen</x-ui.button>
            <x-ui.button variant="link" :href="route($routeName)" target="_blank" rel="noopener" class="sm:ml-auto">
                <x-slot:icon><x-icon.external-link /></x-slot:icon>
                Vorschau
            </x-ui.button>
        </div>
    </form>
</x-layouts.app>
