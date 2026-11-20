{{--
    Formular „Bewertung abgeben“ bzw. „Bewertung ändern“: Sterne (Radio-Gruppe), Kommentar,
    Freigabe des vollen Namens. Ohne review: neue Bewertung (POST), mit review: Änderung (PUT).
--}}
@props([
    'action',
    'review' => null,
    'submitLabel' => null,
    'commentId' => 'review-comment',
    'placeholder' => 'Was war hilfreich? Was ließe sich verbessern?',
])

@php
    $rating = (int) old('rating', $review?->rating ?? 0);
    $submitLabel ??= $review ? 'Bewertung ändern' : 'Bewertung abgeben';
    $nameId = $commentId . '-name';
@endphp

<form method="POST" action="{{ $action }}" x-data="{ rating: {{ $rating }} }"
    {{ $attributes->class('flex max-w-form flex-col gap-5') }}>
    @csrf
    @if ($review)
        @method('PUT')
    @endif

    <fieldset class="m-0 min-w-0 border-0 p-0">
        <x-ui.label as="legend" required>Bewertung</x-ui.label>
        <div class="flex gap-1">
            @for ($i = 1; $i <= 5; $i++)
                <label
                    class="inline-flex h-11 w-11 cursor-pointer items-center justify-center rounded-control has-focus-visible:outline-2 has-focus-visible:outline-offset-2 has-focus-visible:outline-ink">
                    <input type="radio" name="rating" value="{{ $i }}" class="sr-only"
                        x-model.number="rating" @checked($rating === $i) required>
                    <span aria-hidden="true" :class="rating >= {{ $i }} ? 'text-ink' : 'text-line-strong'"
                        @class([
                            'transition-colors duration-150',
                            'text-ink' => $rating >= $i,
                            'text-line-strong' => $rating < $i,
                        ])>
                        <x-icon.star-filled class="h-7 w-7" />
                    </span>
                    <span class="sr-only">{{ $i }} von 5 Sternen</span>
                </label>
            @endfor
        </div>
        <x-ui.field-error name="rating" />
    </fieldset>

    <x-ui.textarea label="Kommentar" name="comment" :id="$commentId" optional rows="4" maxlength="1000"
        :value="$review?->comment" :placeholder="$placeholder" />

    <x-ui.checkbox name="show_full_name" :id="$nameId" :checked="(bool) $review?->show_full_name" label="Meinen vollständigen Namen anzeigen"
        hint="Sonst erscheint Ihr Vorname mit dem Anfangsbuchstaben des Nachnamens, z. B. „Daniela R.“." />

    <p class="m-0 max-w-prose text-small text-muted">Bewertungen werden vor der Veröffentlichung geprüft. Abgelehnt
        wird nur, was beleidigt, persönliche Daten Dritter nennt, Werbung enthält oder nichts mit der Veranstaltung zu
        tun hat – nicht eine schlechte Note. Wenn Sie Ihre Bewertung ändern, wird sie erneut geprüft.</p>

    <div>
        <x-ui.button type="submit">{{ $submitLabel }}</x-ui.button>
    </div>
</form>
