<x-layouts.app title="Administration" :header="false">
    <x-ui.page-header title="Administration" subtitle="Übersicht über das gesamte System." />

    <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-ui.stat label="Benutzer gesamt" :value="$stats['total_users']" />
        <x-ui.stat label="Veranstalter" :value="$stats['total_organizers']" />
        <x-ui.stat label="Veranstaltungen gesamt" :value="$stats['total_events']" :sub="$stats['published_events'] . ' veröffentlicht'" />
        <x-ui.stat label="Umsatz gesamt" :value="\App\Support\Ui::money($stats['total_revenue'])" />
    </div>

    <div class="mb-12 grid gap-4 sm:grid-cols-3">
        <x-ui.stat label="Ausstehende Veranstaltungen" :value="$stats['pending_events']" />
        <x-ui.stat label="Buchungen gesamt" :value="$stats['total_bookings']" />
        <x-ui.stat label="Neue Buchungen (7 Tage)" :value="$stats['recent_bookings_count']" />
    </div>

    <x-ui.section title="Bereiche" class="mb-12">
        <ul class="m-0 grid list-none grid-cols-1 gap-x-8 p-0 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ([['admin.users.index', 'Benutzer', 'Benutzer verwalten'], ['admin.events.index', 'Veranstaltungen', 'Veranstaltungen verwalten'], ['admin.reporting.index', 'Auswertungen', 'Berichte und Statistiken'], ['admin.roles.index', 'Rollen und Rechte', 'Rollen und Berechtigungen verwalten'], ['admin.audit-logs.index', 'Audit-Protokoll', 'Aktionen von Benutzern'], ['admin.system-logs.index', 'System-Protokoll', 'Fehler und Warnungen'], ['admin.newsletter.index', 'Newsletter', 'Newsletter versenden'], ['admin.categories.index', 'Kategorien', 'Kategorien verwalten'], ['admin.reviews.index', 'Bewertungen', 'Bewertungen moderieren'], ['admin.settings.index', 'Einstellungen', 'System konfigurieren']] as [$routeName, $title, $text])
                <li class="border-t border-line">
                    <a href="{{ route($routeName) }}"
                        class="group flex min-h-16 items-center justify-between gap-4 py-3 text-ink no-underline">
                        <span class="flex flex-col">
                            <span
                                class="text-base font-medium group-hover:underline group-hover:underline-offset-[3px]">{{ $title }}</span>
                            <span class="text-small text-muted">{{ $text }}</span>
                        </span>
                        <x-icon.arrow-right class="h-4 w-4 shrink-0" />
                    </a>
                </li>
            @endforeach
        </ul>
    </x-ui.section>

    <div class="grid gap-x-12 gap-y-12 lg:grid-cols-2">
        <x-ui.section class="min-w-0" title="Neueste Benutzer" level="h2" :link-href="route('admin.users.index')"
            link-label="Alle Benutzer">
            <x-ui.table label="Neueste Benutzer">
                <thead>
                    <tr>
                        <th scope="col">Benutzer</th>
                        <th scope="col">Registriert</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentUsers as $user)
                        <tr>
                            <th scope="row">
                                <span class="block">{{ $user->fullName() }}</span>
                                <span
                                    class="block text-small font-normal break-all text-muted">{{ $user->email }}</span>
                                @if ($user->hasRole('organizer'))
                                    <span class="block text-small font-normal text-ink-soft">Veranstalter</span>
                                @endif
                            </th>
                            <td class="whitespace-nowrap text-muted">{{ $user->created_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr class="hover:bg-transparent">
                            <td colspan="2" class="p-0">
                                <x-ui.empty :bordered="false">Keine Benutzer vorhanden.</x-ui.empty>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-ui.table>
        </x-ui.section>

        <x-ui.section class="min-w-0" title="Neueste Veranstaltungen" level="h2" :link-href="route('admin.events.index')"
            link-label="Alle Veranstaltungen">
            <x-ui.table label="Neueste Veranstaltungen">
                <thead>
                    <tr>
                        <th scope="col">Veranstaltung</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="max-sm:hidden">Angelegt</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentEvents as $event)
                        <tr>
                            <th scope="row">
                                <span class="block">{{ $event->title }}</span>
                                <span class="block text-small font-normal text-muted">von
                                    {{ $event->getOrganizerName() }}</span>
                            </th>
                            <td>
                                @if ($event->is_published)
                                    <x-ui.status tone="success">Veröffentlicht</x-ui.status>
                                @else
                                    <x-ui.status tone="warning">Entwurf</x-ui.status>
                                @endif
                            </td>
                            <td class="max-sm:hidden whitespace-nowrap text-muted">
                                {{ $event->created_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr class="hover:bg-transparent">
                            <td colspan="3" class="p-0">
                                <x-ui.empty :bordered="false">Keine Veranstaltungen vorhanden.</x-ui.empty>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-ui.table>
        </x-ui.section>
    </div>
</x-layouts.app>
