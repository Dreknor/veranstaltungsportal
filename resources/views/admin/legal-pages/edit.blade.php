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
            <form method="POST" action="{{ route('admin.legal-pages.update', $page->type) }}">
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

                <!-- Inhalt (WYSIWYG Editor) -->
                <div class="mb-6">
                    <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Inhalt
                    </label>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                        Sie können HTML-Formatierungen verwenden (z.B. &lt;h2&gt;, &lt;p&gt;, &lt;strong&gt;, &lt;a&gt;, &lt;ul&gt;, &lt;li&gt;).
                    </p>
                    <textarea id="content"
                              name="content"
                              rows="30"
                              required
                              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 sm:text-sm font-mono text-sm">{{ old('content', $page->content) }}</textarea>
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
                                class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
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
</x-layouts.app>

