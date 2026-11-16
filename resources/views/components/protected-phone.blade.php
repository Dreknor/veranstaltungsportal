@props(['phone', 'class' => 'text-ink underline underline-offset-[3px]'])

@php
    // Obfuskiere die Telefonnummer
    $encoded = base64_encode($phone);
    $cleaned = preg_replace('/[^0-9+]/', '', $phone);

    // Erstelle verschleierte Anzeige
    if (strlen($cleaned) > 6) {
        $display = substr($cleaned, 0, 3) . ' ' . str_repeat('●', min(8, strlen($cleaned) - 6)) . ' ' . substr($cleaned, -3);
    } else {
        $display = str_repeat('●', strlen($cleaned));
    }

    // Zusätzlicher Schutz: Reverse für HTML-Attribut
    $reversed = strrev($encoded);
@endphp

<span class="protected-contact"
      data-contact="{{ $reversed }}"
      data-type="phone"
      {{ $attributes->merge(['class' => $class]) }}>
    <button type="button" class="inline-flex min-h-11 cursor-pointer flex-wrap items-center gap-x-2 text-left"
            onclick="revealContact(this)"
            aria-label="Telefonnummer anzeigen">
        <x-icon.phone class="h-4 w-4 flex-shrink-0" />
        <span class="contact-display whitespace-nowrap">{{ $display }}</span>
        <span class="text-small whitespace-nowrap text-muted">(zum Anzeigen klicken)</span>
    </button>
</span>

