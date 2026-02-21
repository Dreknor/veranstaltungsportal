@props(['phone', 'class' => 'text-blue-600 hover:text-blue-800'])

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
    <button type="button" class="inline-flex items-center gap-2 cursor-pointer hover:underline focus:outline-none"
            onclick="revealContact(this)"
            aria-label="Telefonnummer anzeigen">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
        </svg>
        <span class="contact-display">{{ $display }}</span>
        <span class="text-xs text-gray-500 italic">(klicken zum Anzeigen)</span>
    </button>
</span>

