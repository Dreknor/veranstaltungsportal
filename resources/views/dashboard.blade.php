<x-layouts.app>
    <x-ui.page-header :title="__('Dashboard')" :subtitle="__('Welcome to the dashboard')" />

    <div class="mb-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-ui.stat :label="__('Total Users')" value="--" :sub="__('No data')" />
        <x-ui.stat :label="__('Total Revenue')" value="--" :sub="__('No data')" />
        <x-ui.stat :label="__('Total Orders')" value="--" :sub="__('No data')" />
        <x-ui.stat :label="__('Total Visitors')" value="--" :sub="__('No data')" />
    </div>

    @if (auth()->user()->badges()->count() > 0)
        <x-ui.section title="Ihre Abzeichen" :link-href="route('badges.index')" link-label="Alle ansehen">
            <ul class="m-0 grid list-none grid-cols-2 gap-x-8 p-0 sm:grid-cols-3 lg:grid-cols-6">
                @foreach (auth()->user()->badges()->latest('user_badges.earned_at')->limit(6)->get() as $badge)
                    <li class="border-t border-line">
                        <a href="{{ route('badges.show', $badge) }}"
                            class="flex flex-col items-start gap-2 py-4 text-ink no-underline hover:underline">
                            <x-badge-icon :badge="$badge" size="sm" />
                            <span class="text-small font-medium">{{ $badge->name }}</span>
                            <span class="text-small text-muted tabular-nums">+{{ $badge->points }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
            <p class="m-0 mt-4 border-t border-line pt-4 text-small text-muted">
                Gesamt: <span class="font-semibold text-ink tabular-nums">{{ auth()->user()->badges()->count() }} Abzeichen · {{ auth()->user()->getTotalBadgePoints() }} Punkte</span>
            </p>
        </x-ui.section>
    @endif
</x-layouts.app>
