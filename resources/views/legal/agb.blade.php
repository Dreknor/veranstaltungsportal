@php
    $page = \App\Models\LegalPage::getByType('agb');
@endphp
<x-layouts.public title="AGB – {{ config('app.name') }}">
    @push('meta')
        <meta name="robots" content="noindex, follow">
    @endpush

    <div class="min-h-screen bg-gray-50 py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm p-8 lg:p-12">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $page?->title ?? 'Allgemeine Geschäftsbedingungen' }}</h1>
                @if($page?->last_updated_at)
                    <p class="text-sm text-gray-500 mb-8">Stand: {{ $page->last_updated_at->format('d. F Y') }}</p>
                @else
                    <p class="text-sm text-gray-500 mb-8">Stand: {{ date('F Y') }}</p>
                @endif

                <div class="prose prose-gray max-w-none">
                    @if($page?->content)
                        {!! $page->content !!}
                    @else
                        <p class="text-gray-500">Die AGB werden gerade bearbeitet. Bitte schauen Sie später wieder vorbei.</p>
                    @endif
                </div>

                @if($page?->last_updated_at)
                    <p class="mt-8 text-xs text-gray-400">Zuletzt aktualisiert: {{ $page->last_updated_at->format('d.m.Y') }}</p>
                @endif

                <div class="mt-10 pt-6 border-t border-gray-200">
                    <a href="{{ route('home') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Zurück zur Startseite
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.public>

