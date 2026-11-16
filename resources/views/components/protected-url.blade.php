@props(['url', 'class' => 'text-ink underline underline-offset-[3px]'])

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
    <button type="button" class="inline-flex min-h-11 cursor-pointer flex-wrap items-center gap-x-2 text-left"
            onclick="revealContact(this)"
            aria-label="Website anzeigen">
        <x-icon.globe class="h-4 w-4 flex-shrink-0" />
        <span class="contact-display whitespace-nowrap">{{ $display }}</span>
        <span class="text-small whitespace-nowrap text-muted">(zum Anzeigen klicken)</span>
    </button>
</span>

