{{-- Ungenutzt, entfällt in Phase 7 (B4): leitet auf das öffentliche Seitengerüst um. --}}
@props(['title' => null])
<x-layouts.public :title="$title ?? __('Anmelden') . ' – ' . config('app.name')">
    <div class="mx-auto max-w-form px-page py-8">
        {{ $slot }}
    </div>
</x-layouts.public>
