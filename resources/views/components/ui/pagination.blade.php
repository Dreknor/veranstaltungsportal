{{--
    Seitennavigation unter einer Liste oder Tabelle. Rendert $paginator->links() mit den angepassten
    Vorlagen (resources/views/vendor/pagination). Ohne weitere Seiten wird nichts ausgegeben.
--}}
@props(['paginator', 'onEachSide' => 1])

@if ($paginator->hasPages())
    <div {{ $attributes->class('mt-6') }}>
        @if ($paginator instanceof \Illuminate\Pagination\LengthAwarePaginator)
            {{ $paginator->onEachSide($onEachSide)->links() }}
        @else
            {{ $paginator->links() }}
        @endif
    </div>
@endif
