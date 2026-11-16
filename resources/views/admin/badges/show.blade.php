<x-layouts.app :title="'Abzeichen: ' . $badge->name">
    @php
        $typeLabels = ['attendance' => 'Teilnahme', 'achievement' => 'Erfolge', 'special' => 'Spezial'];
    @endphp

    <x-ui.page-header :title="'Abzeichen: ' . $badge->name" :back-href="route('admin.badges.index')" back-label="Zur Übersicht der Abzeichen">
        <x-slot:actions>
            <x-ui.button :href="route('admin.badges.edit', $badge)">Bearbeiten</x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="grid items-start gap-10 lg:grid-cols-[minmax(0,368px)_minmax(0,1fr)] lg:gap-x-12">
        <div class="flex flex-col gap-8">
            <!-- Badge Info -->
            <section aria-labelledby="badge-info" class="flex flex-col gap-6">
                <h2 id="badge-info" class="sr-only">Angaben zum Abzeichen</h2>
                <div class="flex items-center gap-4">
                    <x-badge-icon :badge="$badge" size="lg" class="flex-none" />
                    <div class="flex flex-col gap-2">
                        <span class="text-h3">{{ $badge->name }}</span>
                        <x-ui.status
                            tone="neutral">{{ $typeLabels[$badge->type] ?? ucfirst($badge->type) }}</x-ui.status>
                    </div>
                </div>

                <x-ui.dl layout="stack" class="!grid-cols-1">
                    <x-ui.dl-item label="Beschreibung">{{ $badge->description }}</x-ui.dl-item>
                    <x-ui.dl-item label="Punkte" num>{{ $badge->points }}</x-ui.dl-item>
                    <x-ui.dl-item label="Anforderung">{{ $badge->requirement_value }}
                        {{ \App\Models\Badge::REQUIREMENT_TYPES[\App\Models\Badge::normalizeRequirement((string) $badge->requirement_type)] ?? str_replace('_', ' ', $badge->requirement_type) }}</x-ui.dl-item>
                    <x-ui.dl-item label="Vergeben" num>{{ $badge->users->count() }} mal</x-ui.dl-item>
                </x-ui.dl>
            </section>

            <!-- Award Badge Form -->
            <section aria-labelledby="badge-award" class="border-t border-line pt-8">
                <h2 id="badge-award" class="m-0 mb-4 text-h3">Abzeichen manuell vergeben</h2>
                <form method="POST" action="{{ route('admin.badges.award', $badge) }}" class="flex flex-col gap-5">
                    @csrf
                    <x-ui.select label="Benutzer auswählen" name="user_id" placeholder="Benutzer auswählen..." required
                        :old="false">
                        @foreach (\App\Models\User::orderBy('name')->get() as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </x-ui.select>
                    <div>
                        <x-ui.button type="submit" full>Abzeichen vergeben</x-ui.button>
                    </div>
                </form>
            </section>
        </div>

        <!-- Awarded Users -->
        <section aria-labelledby="badge-users" class="min-w-0">
            <h2 id="badge-users" class="m-0 mb-4 text-h2">Benutzer mit diesem Abzeichen ({{ $badge->users->count() }})
            </h2>

            <x-ui.table label="Benutzer mit diesem Abzeichen">
                <thead>
                    <tr>
                        <th scope="col">Benutzer</th>
                        <th scope="col">E-Mail</th>
                        <th scope="col">Erhalten am</th>
                        <th scope="col" class="actions"><span class="sr-only">Aktionen</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($badge->users as $user)
                        <tr>
                            <th scope="row">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $user->profilePhotoUrl() }}" alt=""
                                        class="h-10 w-10 flex-none rounded-full">
                                    <div>
                                        {{ $user->name }}
                                        @if ($user->is_organizer)
                                            <span class="block text-small font-normal text-muted">Veranstalter</span>
                                        @endif
                                    </div>
                                </div>
                            </th>
                            <td>{{ $user->email }}</td>
                            <td class="tabular-nums whitespace-nowrap">
                                {{ $user->pivot->earned_at->format('d.m.Y H:i') }}</td>
                            <td class="actions">
                                <form method="POST" action="{{ route('admin.badges.revoke', $badge) }}"
                                    class="m-0 inline"
                                    onsubmit="return confirm('Sind Sie sicher, dass Sie dieses Abzeichen entfernen möchten?')">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                    <x-ui.button type="submit" size="sm"
                                        variant="secondary">Entfernen</x-ui.button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr class="hover:bg-transparent">
                            <td colspan="4" class="p-0">
                                <x-ui.empty :bordered="false">Dieses Abzeichen wurde noch nicht vergeben.</x-ui.empty>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-ui.table>
        </section>
    </div>
</x-layouts.app>
