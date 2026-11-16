<x-layouts.app title="Newsletter-Abonnenten">
    <x-ui.page-header title="Newsletter-Abonnenten" subtitle="Alle Newsletter-Abonnenten verwalten."
        :back-href="route('admin.newsletter.index')" back-label="Zurück zur Newsletter-Verwaltung">
        <x-slot:actions>
            <x-ui.button variant="secondary" :href="route('admin.newsletter.export')">
                <x-slot:icon><x-icon.download /></x-slot:icon>
                CSV-Export
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.section title="Registrierte Benutzer" :description="$subscribers->total() . ' Abonnenten'" class="mb-12">
        <x-ui.table label="Registrierte Abonnenten">
            <thead>
                <tr>
                    <th scope="col">Name</th>
                    <th scope="col">E-Mail</th>
                    <th scope="col">Abonniert seit</th>
                    <th scope="col">Rolle</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($subscribers as $subscriber)
                    <tr>
                        <th scope="row">
                            <span class="flex items-center gap-3">
                                <img class="h-10 w-10 flex-none rounded-full" src="{{ $subscriber->profilePhotoUrl() }}" alt="">
                                {{ $subscriber->fullName() }}
                            </span>
                        </th>
                        <td>{{ $subscriber->email }}</td>
                        <td class="tabular-nums whitespace-nowrap">
                            {{ $subscriber->newsletter_subscribed_at ? $subscriber->newsletter_subscribed_at->format('d.m.Y H:i') : '–' }}
                        </td>
                        <td>
                            <x-ui.status tone="neutral">{{ $subscriber->userTypeLabel() }}</x-ui.status>
                        </td>
                    </tr>
                @empty
                    <tr class="hover:bg-transparent">
                        <td colspan="4" class="p-0">
                            <x-ui.empty :bordered="false">Keine registrierten Abonnenten gefunden.</x-ui.empty>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-ui.table>
        @if ($subscribers->hasPages())
            <div class="mt-6">
                {{ $subscribers->links() }}
            </div>
        @endif
    </x-ui.section>

    <x-ui.section title="Gast-Abonnenten" :description="$guestSubscribers->total() . ' Abonnenten'">
        <x-ui.table label="Gast-Abonnenten">
            <thead>
                <tr>
                    <th scope="col">E-Mail</th>
                    <th scope="col">Abonniert seit</th>
                    <th scope="col">Typ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($guestSubscribers as $subscriber)
                    <tr>
                        <th scope="row">{{ $subscriber->email }}</th>
                        <td class="tabular-nums whitespace-nowrap">
                            {{ $subscriber->subscribed_at ? date('d.m.Y H:i', strtotime($subscriber->subscribed_at)) : '–' }}
                        </td>
                        <td>
                            <x-ui.status tone="neutral">Gast</x-ui.status>
                        </td>
                    </tr>
                @empty
                    <tr class="hover:bg-transparent">
                        <td colspan="3" class="p-0">
                            <x-ui.empty :bordered="false">Keine Gast-Abonnenten gefunden.</x-ui.empty>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-ui.table>
        @if ($guestSubscribers->hasPages())
            <div class="mt-6">
                {{ $guestSubscribers->links() }}
            </div>
        @endif
    </x-ui.section>
</x-layouts.app>
