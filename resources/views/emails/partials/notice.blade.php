{{--
    Hinweis-/Statuskasten für E-Mails (B7) mit den Meldungsfarben aus A2.
    @component('emails.partials.notice', ['type' => 'warning', 'title' => 'Überschrift'])
        Inhalt
    @endcomponent
    type: success | warning | error | info (Standard; auch für ruhige Zusammenfassungen).
    Der Titel trägt die Bedeutung in Worten – Farbe allein reicht nicht (A2).
--}}
@php
    $tone = [
        'success' => ['text' => '#2C5E1A', 'bg' => '#EEF6E0', 'border' => '#B9D98A'],
        'warning' => ['text' => '#6B4A0A', 'bg' => '#FBF3DF', 'border' => '#E2C88A'],
        'error' => ['text' => '#9B1C1C', 'bg' => '#FCEEEE', 'border' => '#E3A9A9'],
        'info' => ['text' => '#1E3135', 'bg' => '#F3F6F5', 'border' => '#DCE3E2'],
    ][$type ?? 'info'] ?? ['text' => '#1E3135', 'bg' => '#F3F6F5', 'border' => '#DCE3E2'];
@endphp
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
    style="width: 100%; margin: {{ $margin ?? '0 0 24px 0' }}; border-collapse: separate;">
    <tr>
        <td
            style="padding: 16px 20px; background-color: {{ $tone['bg'] }}; border: 1px solid {{ $tone['border'] }}; border-radius: 12px; color: {{ $tone['text'] }}; font-family: -apple-system, 'Segoe UI', Roboto, Arial, sans-serif; font-size: 15px; line-height: 1.5;">
            @if (!empty($title))
                <p
                    style="margin: 0 0 4px 0; font-size: 16px; line-height: 1.4; font-weight: 600; color: {{ $tone['text'] }};">
                    {{ $title }}</p>
            @endif
            {{ $slot }}
        </td>
    </tr>
</table>
