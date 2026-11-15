{{--
    Hinweis auf ein neu erhaltenes Abzeichen (unten rechts, schließt sich nach 5 Sekunden).
    Die Farbe des Abzeichens ist Inhalt und bleibt auf das Abzeichen-Icon beschränkt (A2).
--}}
@props(['badge'])

<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" role="status"
    class="fixed right-4 bottom-4 z-50 w-[calc(100%-32px)] max-w-sm rounded-box border border-line bg-surface p-4 text-ink shadow-overlay">
    <div class="flex items-start gap-4">
        <span
            class="inline-flex h-12 w-12 flex-none items-center justify-center rounded-control border border-line bg-zone"
            style="color: {{ $badge->color }};">
            <x-icon.medal class="h-6 w-6" />
        </span>
        <div class="min-w-0 flex-1">
            <div class="flex items-start justify-between gap-2">
                <p class="m-0 pt-0.5 text-h3">Neues Abzeichen</p>
                <button type="button" @click="show = false" aria-label="Hinweis schließen"
                    class="-mt-2 -mr-2 inline-flex h-11 w-11 flex-none cursor-pointer items-center justify-center rounded-control border-0 bg-transparent text-ink transition-colors duration-150 hover:bg-zone">
                    <x-icon.x class="h-4 w-4" />
                </button>
            </div>
            <p class="m-0 mt-1 text-small font-semibold">{{ $badge->name }}</p>
            <p class="m-0 mt-1 text-small text-muted">{{ $badge->description }}</p>
            <p class="m-0 mt-2 text-small text-ink-soft tabular-nums">+{{ $badge->points }} Punkte</p>
            <a href="{{ route('badges.index') }}"
                class="mt-1 inline-flex min-h-11 items-center gap-2 text-small font-medium text-ink underline-offset-[3px] hover:underline">
                Alle Abzeichen ansehen
                <x-icon.arrow-right class="h-4 w-4" />
            </a>
        </div>
    </div>
</div>
