@props(['url', 'class' => 'text-blue-600 hover:text-blue-800'])

@php
    // Obfuskiere die URL
    $encoded = base64_encode($url);
    $parsed = parse_url($url);
    $domain = $parsed['host'] ?? $url;

    // Erstelle verschleierte Anzeige
    if (strlen($domain) > 10) {
        $display = substr($domain, 0, 4) . str_repeat('●', min(10, strlen($domain) - 8)) . substr($domain, -4);
    } else {
        $display = $domain;
    }

    // Zusätzlicher Schutz: Reverse für HTML-Attribut
    $reversed = strrev($encoded);
@endphp

<span class="protected-contact"
      data-contact="{{ $reversed }}"
      data-type="url"
      {{ $attributes->merge(['class' => $class]) }}>
    <button type="button" class="inline-flex items-center gap-2 cursor-pointer hover:underline focus:outline-none"
            onclick="revealContact(this)"
            aria-label="Website anzeigen">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
        </svg>
        <span class="contact-display">{{ $display }}</span>
        <span class="text-xs text-gray-500 italic">(klicken zum Anzeigen)</span>
    </button>
</span>

