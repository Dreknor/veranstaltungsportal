{{--
    Session-Meldungen an einer Stelle (ein Aufruf im Layout genügt):
    success, status → success; error → danger; warning → warning; info → info.
    Bekannte Status-Schlüssel (z. B. "verification-link-sent") werden in Sätze übersetzt.
    show-errors zeigt zusätzlich die Fehlerliste des Formulars (für Seiten, deren Felder die Fehler
    nicht selbst anzeigen); Standard ist false, weil <x-ui.field> Fehler direkt am Feld zeigt.
--}}
@props([
    'showErrors' => false,
    'dismissible' => true,
])

@php
    $statusTexts = [
        'verification-link-sent' => 'Wir haben Ihnen einen neuen Bestätigungslink per E-Mail geschickt.',
        'password-updated' => 'Ihr Passwort wurde geändert.',
        'profile-updated' => 'Ihr Profil wurde gespeichert.',
    ];

    $messages = [];
    foreach (['success' => 'success', 'status' => 'success', 'error' => 'danger', 'warning' => 'warning', 'info' => 'info'] as $key => $tone) {
        $value = session($key);
        if (is_string($value) && $value !== '') {
            $messages[] = ['tone' => $tone, 'text' => $statusTexts[$value] ?? $value];
        }
    }

    $bag = $showErrors ? session('errors') : null;
    $hasErrors = $bag instanceof \Illuminate\Support\ViewErrorBag && $bag->any();
@endphp

@if ($messages || $hasErrors)
    <div {{ $attributes->class('flex flex-col gap-3') }}>
        @foreach ($messages as $message)
            <x-ui.alert :tone="$message['tone']" :dismissible="$dismissible">{{ $message['text'] }}</x-ui.alert>
        @endforeach
        @if ($hasErrors)
            <x-ui.alert tone="danger" title="Bitte prüfen Sie Ihre Eingaben.">
                <ul class="mt-1">
                    @foreach ($bag->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-ui.alert>
        @endif
    </div>
@endif
