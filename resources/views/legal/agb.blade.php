@php
    $page = \App\Models\LegalPage::getByType('agb');
@endphp
<x-layouts.public title="AGB – {{ config('app.name') }}">
    @push('meta')
        <meta name="robots" content="noindex, follow">
    @endpush

    <div class="mx-auto max-w-page px-page py-[clamp(32px,6vw,64px)]">
        <x-ui.page-header :title="$page?->title ?? 'Allgemeine Geschäftsbedingungen'"
            :subtitle="$page?->last_updated_at ? 'Stand: ' . $page->last_updated_at->format('d. F Y') : 'Stand: ' . date('F Y')"
            :back-href="route('home')" back-label="Zurück zur Startseite" />

        <x-legal.prose>
            @if ($page?->content)
                {!! $page->content !!}
            @else
                <p class="text-muted">Die AGB werden gerade bearbeitet. Bitte schauen Sie später wieder vorbei.</p>
            @endif
        </x-legal.prose>

        @if ($page?->last_updated_at)
            <p class="mt-8 mb-0 text-small text-muted">Zuletzt aktualisiert: {{ $page->last_updated_at->format('d.m.Y') }}</p>
        @endif
    </div>
</x-layouts.public>
