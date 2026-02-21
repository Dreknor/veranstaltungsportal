@props(['email', 'class' => 'text-blue-600 hover:text-blue-800'])

@php
    // Obfuskiere die E-Mail-Adresse mehrfach für Bot-Schutz
    $encoded = base64_encode($email);
    $parts = explode('@', $email);

    // Erstelle eine verschleierte Anzeige
    $display = '';
    if (count($parts) === 2) {
        $localPart = $parts[0];
        $domainPart = $parts[1];

        // Zeige nur erste 2 und letzte 2 Zeichen des lokalen Teils
        if (strlen($localPart) > 4) {
            $display = substr($localPart, 0, 2) . str_repeat('●', min(6, strlen($localPart) - 4)) . substr($localPart, -2);
        } else {
            $display = str_repeat('●', strlen($localPart));
        }

        $display .= ' @ ';

        // Zeige nur erste und letzte 3 Zeichen der Domain
        if (strlen($domainPart) > 6) {
            $display .= substr($domainPart, 0, 3) . str_repeat('●', min(8, strlen($domainPart) - 6)) . substr($domainPart, -3);
        } else {
            $display .= str_repeat('●', strlen($domainPart));
        }
    } else {
        $display = str_repeat('●', 15);
    }

    // Zusätzlicher Schutz: Reverse für HTML-Attribut
    $reversed = strrev($encoded);
@endphp

<span class="protected-contact"
      data-contact="{{ $reversed }}"
      data-type="email"
      {{ $attributes->merge(['class' => $class]) }}>
    <button type="button" class="inline-flex items-center gap-2 cursor-pointer hover:underline focus:outline-none"
            onclick="revealContact(this)"
            aria-label="E-Mail-Adresse anzeigen">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
        </svg>
        <span class="contact-display">{{ $display }}</span>
        <span class="text-xs text-gray-500 italic">(klicken zum Anzeigen)</span>
    </button>
</span>

@once
    @push('scripts')
    <script>
        window.revealContact = function(element) {
            const parent = element.closest('.protected-contact');
            if (!parent) return;

            const reversed = parent.dataset.contact;
            const type = parent.dataset.type;

            // Dekodiere die umgekehrte Base64-Zeichenfolge
            const encoded = reversed.split('').reverse().join('');
            let contact;

            try {
                contact = atob(encoded);
            } catch (e) {
                console.error('Fehler beim Dekodieren der Kontaktdaten');
                return;
            }

            // Erstelle den Link basierend auf dem Typ
            let link = '';
            const classes = parent.className.replace('protected-contact', '').trim();

            if (type === 'email') {
                link = `<a href="mailto:${contact}" class="${classes}">${contact}</a>`;
            } else if (type === 'phone') {
                link = `<a href="tel:${contact}" class="${classes}">${contact}</a>`;
            } else if (type === 'url') {
                // Stelle sicher, dass die URL mit http:// oder https:// beginnt
                const url = contact.match(/^https?:\/\//) ? contact : 'https://' + contact;
                link = `<a href="${url}" target="_blank" rel="noopener noreferrer" class="${classes}">${contact}</a>`;
            }

            parent.innerHTML = link;
        }
    </script>
    @endpush
@endonce

