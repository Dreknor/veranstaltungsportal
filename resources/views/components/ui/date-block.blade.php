{{--
    Datumsblock 80 × 80 px als <time>. Ein Tag: "Do / 26 / Nov 2026"; mehrtägig: "Fr–Sa / 22–23 / Jan 2027".
    tone="white" auf Zonen-Hintergrund (z. B. im Kasten "Gebuchte Veranstaltung").
--}}
@props([
    'date',
    'end' => null,
    'tone' => 'zone', // zone | white
])

@php
    use App\Support\Ui;

    $start = $date instanceof \Carbon\CarbonInterface ? $date : \Illuminate\Support\Carbon::parse($date);
    $end = $end && ! $end instanceof \Carbon\CarbonInterface ? \Illuminate\Support\Carbon::parse($end) : $end;
    $multi = $end && ! $end->isSameDay($start);

    if ($multi) {
        $weekday = Ui::weekday($start).'–'.Ui::weekday($end);
        $day = $start->format('d').'–'.$end->format('d');
        $month = match (true) {
            $start->isSameMonth($end) => Ui::month($start).' '.$start->year,
            $start->year === $end->year => Ui::month($start).'–'.Ui::month($end).' '.$end->format('y'),
            default => Ui::month($start).' '.$start->format('y').'–'.Ui::month($end).' '.$end->format('y'),
        };
        $spoken = Ui::date($start).' bis '.Ui::date($end);
    } else {
        $weekday = Ui::weekday($start);
        $day = $start->format('d');
        $month = Ui::month($start).' '.$start->year;
        $spoken = Ui::date($start);
    }
@endphp

<time
    datetime="{{ $start->toDateString() }}"
    {{ $attributes->class([
        'flex h-20 w-20 flex-none flex-col items-center justify-center gap-0.5 rounded-control border border-line leading-[1.1] text-ink tabular-nums',
        'bg-zone' => $tone !== 'white',
        'bg-surface' => $tone === 'white',
    ]) }}
>
    <span class="sr-only">{{ $spoken }}</span>
    <span aria-hidden="true" class="text-caption leading-[1.1] text-muted">{{ $weekday }}</span>
    <span aria-hidden="true" @class(['font-semibold tracking-[-0.02em]', 'text-[24px]' => ! $multi, 'text-[22px]' => $multi])>{{ $day }}</span>
    <span aria-hidden="true" class="text-caption leading-[1.1] font-medium whitespace-nowrap">{{ $month }}</span>
</time>
