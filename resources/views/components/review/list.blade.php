{{--
    Liste veröffentlichter Bewertungen (Linie oben, Trennlinien zwischen den Einträgen).
--}}
@props(['reviews', 'showEvent' => false, 'linkEvent' => true, 'organizationName' => null])

<ul {{ $attributes->class('m-0 flex list-none flex-col divide-y divide-line border-t border-line p-0') }}>
    @foreach ($reviews as $review)
        <x-review.item :review="$review" :show-event="$showEvent" :link-event="$linkEvent" :organization-name="$organizationName" />
    @endforeach
</ul>
