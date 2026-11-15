{{-- Entfällt in Phase 7 (B4): leitet auf das öffentliche Seitengerüst um. --}}
@props(['title' => null])
<x-layouts.public :title="$title">
    {{ $slot }}
</x-layouts.public>
