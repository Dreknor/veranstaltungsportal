{{--
    Listenzeile für eine Person (Verbindungen, Follower, Suche) im Raster von <x-ui.row>:
    Avatar (56 px) | Typ, Name, Bio, Standard-Slot | meta | aside | action.
    In <x-ui.row-list> setzen.

    user:      App\Models\User – liefert Avatar, Name, Typ ("Organisator") und Bio.
               Alternativ einzeln: name, type, bio, avatar (Foto-URL), initials.
    href:      Ziel des Namens. Standard bei user: Profil (users.show). false: Name ohne Link.
    bio-lines: Zeilen der Bio, danach abgeschnitten (2 | 3). show-bio="false" blendet sie aus.

    Slots: Standard (weitere Zeilen unter der Bio), meta (z. B. Zahlen, Datum), aside, action
    (Buttons und Formulare; Buttons mit full).
--}}
@props([
    'user' => null,
    'name' => null,
    'type' => null,
    'bio' => null,
    'avatar' => null,
    'initials' => null,
    'href' => null,
    'bioLines' => 2,
    'showBio' => true,
    'meta' => null,
    'aside' => null,
    'action' => null,
])

@php
    if ($user) {
        $name ??= $user->fullName();
        $type ??= $user->userTypeLabel();
        $bio ??= $user->bio;
        $href ??= route('users.show', $user);
    }
    $href = $href === false ? null : $href;

    $has = fn($part) => $part instanceof \Illuminate\View\ComponentSlot ? $part->isNotEmpty() : filled($part);
    $slotClass = fn($part) => $part instanceof \Illuminate\View\ComponentSlot ? $part->attributes->get('class') : null;
@endphp

<x-ui.row {{ $attributes }}>
    <x-slot:lead>
        <x-ui.avatar :user="$user" :src="$avatar" :name="$name" :initials="$initials" />
    </x-slot:lead>

    @if ($type)
        <span class="text-small text-muted">{{ $type }}</span>
    @endif
    @if ($href)
        <a href="{{ $href }}" class="text-h3 text-ink no-underline hover:underline">{{ $name }}</a>
    @else
        <span class="text-h3 text-ink">{{ $name }}</span>
    @endif
    @if ($showBio && filled($bio))
        <span @class([
            'text-small text-ink-soft',
            'line-clamp-2' => (int) $bioLines <= 2,
            'line-clamp-3' => (int) $bioLines >= 3,
        ])>{{ $bio }}</span>
    @endif
    {{ $slot }}

    @if ($has($meta))
        <x-slot:meta class="{{ $slotClass($meta) }}">
            {{ $meta }}
        </x-slot:meta>
    @endif
    @if ($has($aside))
        <x-slot:aside class="{{ $slotClass($aside) }}">
            {{ $aside }}
        </x-slot:aside>
    @endif
    @if ($has($action))
        <x-slot:action class="{{ $slotClass($action) }}">
            {{ $action }}
        </x-slot:action>
    @endif
</x-ui.row>
