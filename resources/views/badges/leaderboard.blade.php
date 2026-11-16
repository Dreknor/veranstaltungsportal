<x-layouts.app title="Bestenliste">
    <x-ui.page-header title="Bestenliste" :back-href="route('badges.index')" back-label="Zurück zu Abzeichen" />

    <!-- User's Rank (if applicable) -->
    @if ($userRank)
        <div class="mb-12 grid gap-4 sm:grid-cols-2">
            <x-ui.stat label="Ihre Position" :value="'Platz ' . $userRank" />
            <x-ui.stat label="Ihre Punkte" :value="\App\Support\Ui::number(auth()->user()->getTotalBadgePoints())" />
        </div>
    @endif

    <!-- Leaderboard -->
    <x-ui.section title="Platzierungen">
        @if (count($leaderboard) === 0)
            <x-ui.empty :action-href="route('events.index')" action-label="Veranstaltungen entdecken">
                Noch keine Einträge in der Bestenliste.
            </x-ui.empty>
        @else
            <x-ui.table label="Bestenliste">
                <thead>
                    <tr>
                        <th scope="col" class="num">Platz</th>
                        <th scope="col">Teilnehmer</th>
                        <th scope="col" class="num">Abzeichen</th>
                        <th scope="col" class="num">Punkte</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($leaderboard as $index => $entry)
                        @php
                            $rank = $index + 1;
                            $isCurrentUser = auth()->check() && $entry['user']->id === auth()->id();
                        @endphp
                        <tr @class(['bg-zone' => $isCurrentUser])>
                            <td class="num">{{ $rank }}</td>
                            <th scope="row">
                                <span class="flex items-center gap-3">
                                    @if (
                                        $entry['user']->show_profile_public ||
                                            ($entry['user']->allow_networking && auth()->check() && auth()->user()->isFollowing($entry['user'])))
                                        <img class="h-10 w-10 flex-none rounded-full border border-line object-cover"
                                            src="{{ $entry['user']->profilePhotoUrl() }}"
                                            alt="{{ $entry['user']->displayName(auth()->user()) }}">
                                    @else
                                        <span
                                            class="flex h-10 w-10 flex-none items-center justify-center rounded-full bg-zone text-muted">
                                            <x-icon.user class="h-5 w-5" />
                                        </span>
                                    @endif
                                    <span class="min-w-0">
                                        {{ $entry['user']->displayName(auth()->user()) }}
                                        @if ($isCurrentUser)
                                            <span class="font-normal text-muted">(Sie)</span>
                                        @endif
                                        @if (!$entry['user']->show_profile_public)
                                            <span class="block text-caption font-normal text-muted">Privates
                                                Profil</span>
                                        @endif
                                    </span>
                                </span>
                            </th>
                            <td class="num">{{ $entry['badges_count'] }}</td>
                            <td class="num">{{ \App\Support\Ui::number($entry['total_points']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </x-ui.table>
        @endif
    </x-ui.section>

    <!-- Info Box -->
    <x-ui.alert tone="info" title="Wie funktioniert die Bestenliste?" class="mt-12 max-w-prose">
        <div class="flex flex-col gap-3">
            <p>
                Die Bestenliste basiert auf den Gesamtpunkten, die Sie durch das Verdienen von Abzeichen sammeln.
                Jedes Abzeichen hat einen bestimmten Punktewert. Je mehr Abzeichen Sie verdienen, desto höher steigen
                Sie in der Bestenliste. Nehmen Sie an Fortbildungen teil, schreiben Sie Bewertungen und engagieren Sie
                sich, um weitere Abzeichen zu verdienen.
            </p>
            <p>
                <strong>Datenschutz:</strong> In der Bestenliste erscheinen nur Personen mit öffentlichem Profil oder
                aktiviertem Networking. Bei privaten Profilen wird der Name gekürzt angezeigt (z. B. „Max M.“).
                Ihre Sichtbarkeit stellen Sie in den
                <a href="{{ route('settings.privacy.edit') }}">Datenschutz-Einstellungen</a> ein.
            </p>
        </div>
    </x-ui.alert>
</x-layouts.app>
