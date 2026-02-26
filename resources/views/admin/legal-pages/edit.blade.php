<x-layouts.app>
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.legal-pages.index') }}"
               class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition">
                <svg class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Zurück zur Übersicht
            </a>
        </div>
        <div class="mt-3">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ \App\Models\LegalPage::TYPES[$page->type] }} bearbeiten</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Inhalt der rechtlichen Seite bearbeiten</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-md bg-red-50 p-4 dark:bg-red-900/20">
            <ul class="list-disc list-inside text-sm text-red-800 dark:text-red-200 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
        <div class="p-6">
            <form method="POST" action="{{ route('admin.legal-pages.update', $page->type) }}" id="legal-page-form">
                @csrf
                @method('PUT')

                <!-- Titel -->
                <div class="mb-6">
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Seitentitel
                    </label>
                    <input type="text"
                           id="title"
                           name="title"
                           value="{{ old('title', $page->title) }}"
                           required
                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 sm:text-sm">
                </div>

                <!-- Verstecktes Feld für den HTML-Inhalt -->
                <input type="hidden" name="content" id="hidden-content" value="{{ old('content', $page->content) }}">

                <!-- Rich-Text-Editor -->
                <div class="mb-6"
                     x-data="richEditor({ name: 'content', value: {{ Js::from(old('content', $page->content)) }} })"
                     x-init="init()"
                     x-destroy="destroy()">

                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Inhalt</label>

                    <!-- Toolbar -->
                    <div class="border border-b-0 border-gray-300 dark:border-gray-600 rounded-t-md bg-gray-50 dark:bg-gray-700 p-2 flex flex-wrap gap-1 items-center">

                        <!-- Undo / Redo -->
                        <div class="flex gap-1 pr-2 border-r border-gray-300 dark:border-gray-500">
                            <button type="button" @click="undo()" title="Rückgängig (Strg+Z)"
                                    class="toolbar-btn">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                            </button>
                            <button type="button" @click="redo()" title="Wiederholen (Strg+Y)"
                                    class="toolbar-btn">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10H11a8 8 0 00-8 8v2m18-10l-6 6m6-6l-6-6"/></svg>
                            </button>
                        </div>

                        <!-- Überschriften -->
                        <div class="flex gap-1 pr-2 border-r border-gray-300 dark:border-gray-500">
                            <button type="button" @click="setParagraph()"
                                    :class="isActive('paragraph') ? 'toolbar-btn-active' : 'toolbar-btn'"
                                    title="Absatz">
                                <span class="text-xs font-medium px-0.5">P</span>
                            </button>
                            <button type="button" @click="setHeading(1)"
                                    :class="isActive('heading', {level:1}) ? 'toolbar-btn-active' : 'toolbar-btn'"
                                    title="Überschrift 1">
                                <span class="text-xs font-bold px-0.5">H1</span>
                            </button>
                            <button type="button" @click="setHeading(2)"
                                    :class="isActive('heading', {level:2}) ? 'toolbar-btn-active' : 'toolbar-btn'"
                                    title="Überschrift 2">
                                <span class="text-xs font-bold px-0.5">H2</span>
                            </button>
                            <button type="button" @click="setHeading(3)"
                                    :class="isActive('heading', {level:3}) ? 'toolbar-btn-active' : 'toolbar-btn'"
                                    title="Überschrift 3">
                                <span class="text-xs font-bold px-0.5">H3</span>
                            </button>
                        </div>

                        <!-- Formatierung -->
                        <div class="flex gap-1 pr-2 border-r border-gray-300 dark:border-gray-500">
                            <button type="button" @click="toggleBold()"
                                    :class="isActive('bold') ? 'toolbar-btn-active' : 'toolbar-btn'"
                                    title="Fett (Strg+B)">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M6 4h8a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"/><path d="M6 12h9a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"/></svg>
                            </button>
                            <button type="button" @click="toggleItalic()"
                                    :class="isActive('italic') ? 'toolbar-btn-active' : 'toolbar-btn'"
                                    title="Kursiv (Strg+I)">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><line x1="19" y1="4" x2="10" y2="4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><line x1="14" y1="20" x2="5" y2="20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><line x1="15" y1="4" x2="9" y2="20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                            </button>
                            <button type="button" @click="toggleUnderline()"
                                    :class="isActive('underline') ? 'toolbar-btn-active' : 'toolbar-btn'"
                                    title="Unterstrichen (Strg+U)">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 3v7a6 6 0 0 0 12 0V3M4 21h16"/></svg>
                            </button>
                            <button type="button" @click="toggleStrike()"
                                    :class="isActive('strike') ? 'toolbar-btn-active' : 'toolbar-btn'"
                                    title="Durchgestrichen">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><line x1="18" y1="12" x2="6" y2="12" stroke-width="2" stroke-linecap="round"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 6C16 6 14.5 4 12 4s-4 1.5-4 4c0 1.5.8 2.5 2 3"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 18c0 0 1.5 2 4 2s4-1.5 4-4c0-1.5-.8-2.5-2-3"/></svg>
                            </button>
                            <button type="button" @click="toggleHighlight()"
                                    :class="isActive('highlight') ? 'toolbar-btn-active' : 'toolbar-btn'"
                                    title="Hervorheben">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </button>
                        </div>

                        <!-- Listen -->
                        <div class="flex gap-1 pr-2 border-r border-gray-300 dark:border-gray-500">
                            <button type="button" @click="toggleBulletList()"
                                    :class="isActive('bulletList') ? 'toolbar-btn-active' : 'toolbar-btn'"
                                    title="Aufzählungsliste">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><line x1="9" y1="6" x2="20" y2="6" stroke-width="2" stroke-linecap="round"/><line x1="9" y1="12" x2="20" y2="12" stroke-width="2" stroke-linecap="round"/><line x1="9" y1="18" x2="20" y2="18" stroke-width="2" stroke-linecap="round"/><circle cx="4" cy="6" r="1" fill="currentColor"/><circle cx="4" cy="12" r="1" fill="currentColor"/><circle cx="4" cy="18" r="1" fill="currentColor"/></svg>
                            </button>
                            <button type="button" @click="toggleOrderedList()"
                                    :class="isActive('orderedList') ? 'toolbar-btn-active' : 'toolbar-btn'"
                                    title="Nummerierte Liste">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><line x1="10" y1="6" x2="21" y2="6" stroke-width="2" stroke-linecap="round"/><line x1="10" y1="12" x2="21" y2="12" stroke-width="2" stroke-linecap="round"/><line x1="10" y1="18" x2="21" y2="18" stroke-width="2" stroke-linecap="round"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h1v4"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 10H5"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16l2-2a1.5 1.5 0 1 1 2 2l-3 3h4"/></svg>
                            </button>
                            <button type="button" @click="toggleBlockquote()"
                                    :class="isActive('blockquote') ? 'toolbar-btn-active' : 'toolbar-btn'"
                                    title="Zitat">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1zm12 0c3 0 7-1 7-8V5c0-1.25-.757-2.017-2-2h-4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2h.75c0 2.25.25 4-2.75 4v3c0 1 0 1 1 1z"/></svg>
                            </button>
                        </div>

                        <!-- Ausrichtung -->
                        <div class="flex gap-1 pr-2 border-r border-gray-300 dark:border-gray-500">
                            <button type="button" @click="setTextAlign('left')"
                                    :class="isActive({textAlign:'left'}) ? 'toolbar-btn-active' : 'toolbar-btn'"
                                    title="Linksbündig">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><line x1="3" y1="6" x2="21" y2="6" stroke-width="2" stroke-linecap="round"/><line x1="3" y1="12" x2="15" y2="12" stroke-width="2" stroke-linecap="round"/><line x1="3" y1="18" x2="18" y2="18" stroke-width="2" stroke-linecap="round"/></svg>
                            </button>
                            <button type="button" @click="setTextAlign('center')"
                                    :class="isActive({textAlign:'center'}) ? 'toolbar-btn-active' : 'toolbar-btn'"
                                    title="Zentriert">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><line x1="3" y1="6" x2="21" y2="6" stroke-width="2" stroke-linecap="round"/><line x1="6" y1="12" x2="18" y2="12" stroke-width="2" stroke-linecap="round"/><line x1="4" y1="18" x2="20" y2="18" stroke-width="2" stroke-linecap="round"/></svg>
                            </button>
                            <button type="button" @click="setTextAlign('right')"
                                    :class="isActive({textAlign:'right'}) ? 'toolbar-btn-active' : 'toolbar-btn'"
                                    title="Rechtsbündig">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><line x1="3" y1="6" x2="21" y2="6" stroke-width="2" stroke-linecap="round"/><line x1="9" y1="12" x2="21" y2="12" stroke-width="2" stroke-linecap="round"/><line x1="6" y1="18" x2="21" y2="18" stroke-width="2" stroke-linecap="round"/></svg>
                            </button>
                        </div>

                        <!-- Link -->
                        <div class="flex gap-1 items-center relative">
                            <button type="button" @click="openLinkDialog()"
                                    :class="isActive('link') ? 'toolbar-btn-active' : 'toolbar-btn'"
                                    title="Link einfügen/bearbeiten">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            </button>

                            <!-- Link-Eingabe Popup -->
                            <div x-show="showLinkInput"
                                 @click.outside="showLinkInput = false"
                                 x-transition
                                 class="absolute top-full left-0 mt-1 z-50 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg p-3 flex gap-2 min-w-72">
                                <input x-ref="linkInput"
                                       type="url"
                                       x-model="linkUrl"
                                       placeholder="https://..."
                                       @keydown.enter.prevent="confirmLink()"
                                       @keydown.escape="showLinkInput = false"
                                       class="flex-1 text-sm rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500">
                                <button type="button" @click="confirmLink()"
                                        class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded hover:bg-blue-500">
                                    OK
                                </button>
                                <button type="button" @click="removeLink()"
                                        class="px-3 py-1.5 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm rounded hover:bg-gray-300">
                                    Entfernen
                                </button>
                            </div>
                        </div>

                        <!-- Trennlinie -->
                        <div class="pr-2 border-r border-gray-300 dark:border-gray-500">
                            <button type="button" @click="setHorizontalRule()"
                                    class="toolbar-btn" title="Horizontale Linie">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><line x1="3" y1="12" x2="21" y2="12" stroke-width="2" stroke-linecap="round"/></svg>
                            </button>
                        </div>

                    </div>

                    <!-- Editor-Bereich -->
                    <div x-ref="editorContent"
                         class="rich-editor-content border border-gray-300 dark:border-gray-600 rounded-b-md bg-white dark:bg-gray-900 min-h-96 p-4 focus:outline-none prose prose-sm dark:prose-invert max-w-none">
                    </div>
                </div>

                <!-- Meta-Info -->
                @if($page->last_updated_at)
                    <div class="mb-6 rounded-md bg-gray-50 p-4 dark:bg-gray-700/50">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Zuletzt gespeichert am <strong>{{ $page->last_updated_at->format('d.m.Y') }}</strong>
                            um <strong>{{ $page->last_updated_at->format('H:i') }} Uhr</strong>
                            @if($page->editor)
                                von <strong>{{ $page->editor->name }}</strong>
                            @endif
                        </p>
                    </div>
                @endif

                <!-- Aktionen -->
                <div class="flex items-center justify-between">
                    <a href="{{ route('admin.legal-pages.index') }}"
                       class="inline-flex items-center rounded-md bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                        Abbrechen
                    </a>
                    <div class="flex items-center gap-3">
                        @php
                            $routeName = match($page->type) {
                                'agb'        => 'agb',
                                'datenschutz' => 'datenschutz',
                                'impressum'  => 'impressum',
                            };
                        @endphp
                        <a href="{{ route($routeName) }}"
                           target="_blank"
                           class="inline-flex items-center rounded-md bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                            <svg class="mr-1.5 -ml-0.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            Vorschau
                        </a>
                        <button type="submit"
                                class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                            <svg class="mr-1.5 -ml-0.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Speichern
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('styles')
    <style>
        .toolbar-btn {
            @apply p-1.5 rounded text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-150 flex items-center justify-center;
        }
        .toolbar-btn-active {
            @apply p-1.5 rounded bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 flex items-center justify-center;
        }
        /* Tiptap Editor Styles */
        .rich-editor-content .tiptap {
            @apply outline-none min-h-96;
        }
        .rich-editor-content .tiptap h1 { @apply text-2xl font-bold mt-4 mb-2; }
        .rich-editor-content .tiptap h2 { @apply text-xl font-bold mt-4 mb-2; }
        .rich-editor-content .tiptap h3 { @apply text-lg font-bold mt-3 mb-1; }
        .rich-editor-content .tiptap h4 { @apply text-base font-bold mt-3 mb-1; }
        .rich-editor-content .tiptap p  { @apply my-2; }
        .rich-editor-content .tiptap ul { @apply list-disc list-inside my-2 ml-4; }
        .rich-editor-content .tiptap ol { @apply list-decimal list-inside my-2 ml-4; }
        .rich-editor-content .tiptap li { @apply my-0.5; }
        .rich-editor-content .tiptap blockquote { @apply border-l-4 border-gray-300 dark:border-gray-600 pl-4 italic text-gray-600 dark:text-gray-400 my-4; }
        .rich-editor-content .tiptap pre  { @apply bg-gray-100 dark:bg-gray-800 rounded p-3 font-mono text-sm my-3 overflow-x-auto; }
        .rich-editor-content .tiptap code { @apply bg-gray-100 dark:bg-gray-800 rounded px-1 py-0.5 font-mono text-sm; }
        .rich-editor-content .tiptap hr  { @apply border-t border-gray-300 dark:border-gray-600 my-4; }
        .rich-editor-content .tiptap a  { @apply text-blue-600 dark:text-blue-400 underline hover:text-blue-800; }
        .rich-editor-content .tiptap mark { @apply bg-yellow-200 dark:bg-yellow-700/50; }
        .rich-editor-content .tiptap p.is-editor-empty:first-child::before {
            content: attr(data-placeholder);
            @apply text-gray-400 float-left h-0 pointer-events-none;
        }
    </style>
    @endpush
</x-layouts.app>

