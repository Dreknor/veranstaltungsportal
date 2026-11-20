{{--
    Moderationsstand einer Bewertung: Freigegeben | Abgelehnt | Wartet (auf Freigabe).
--}}
@props([
    'review',
    'long' => false, // „Wartet auf Freigabe“ statt „Wartet“
])

@if ($review->is_approved)
    <x-ui.status tone="success" {{ $attributes }}>Freigegeben</x-ui.status>
@elseif ($review->isRejected())
    <x-ui.status tone="danger" {{ $attributes }}>Abgelehnt</x-ui.status>
@else
    <x-ui.status tone="warning" {{ $attributes }}>{{ $long ? 'Wartet auf Freigabe' : 'Wartet' }}</x-ui.status>
@endif
