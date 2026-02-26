<x-layouts.app>
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Rechtliche Seiten</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Impressum, Datenschutzerklärung und AGB verwalten</p>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-md bg-green-50 p-4 dark:bg-green-900/20">
            <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
        @foreach(\App\Models\LegalPage::TYPES as $type => $label)
            @php $page = isset($pages) ? $pages->get($type) : null; @endphp
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $label }}</h2>
                        @if($page)
                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900/40 dark:text-green-300">
                                Gepflegt
                            </span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300">
                                Nicht angelegt
                            </span>
                        @endif
                    </div>

                    @if($page)
                        <div class="text-sm text-gray-500 dark:text-gray-400 space-y-1 mb-4">
                            <p><span class="font-medium">Titel:</span> {{ $page->title }}</p>
                            @if($page->last_updated_at)
                                <p><span class="font-medium">Zuletzt bearbeitet:</span> {{ $page->last_updated_at->format('d.m.Y H:i') }} Uhr</p>
                            @endif
                            @if($page->editor)
                                <p><span class="font-medium">Von:</span> {{ $page->editor->name }}</p>
                            @endif
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                            Diese Seite wurde noch nicht bearbeitet.
                        </p>
                    @endif

                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.legal-pages.edit', $type) }}"
                           class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                            <svg class="mr-1.5 -ml-0.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Bearbeiten
                        </a>
                        @php
                            $routeName = match($type) {
                                'agb'        => 'agb',
                                'datenschutz' => 'datenschutz',
                                'impressum'  => 'impressum',
                            };
                        @endphp
                        <a href="{{ route($routeName) }}"
                           target="_blank"
                           class="inline-flex items-center rounded-md bg-gray-100 px-3 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                            <svg class="mr-1.5 -ml-0.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            Vorschau
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</x-layouts.app>


