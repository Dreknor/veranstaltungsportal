{{--
    Button für E-Mails (B7): <a> in einer Tabelle, damit er in allen Mail-Programmen trägt.
    @include('emails.partials.button', ['url' => …, 'label' => …])
    Optional: 'variant' => 'primary' (Petrol, Standard) | 'secondary' (weiß, Kontur) | 'danger' (Löschen),
              'margin' => CSS-Außenabstand (Standard '24px 0').
--}}
@php
    $variant = $variant ?? 'primary';
    $styles = [
        'primary' => ['bg' => '#2B4347', 'border' => '#2B4347', 'text' => '#FFFFFF'],
        'secondary' => ['bg' => '#FFFFFF', 'border' => '#7F9091', 'text' => '#1E3135'],
        'danger' => ['bg' => '#9B1C1C', 'border' => '#9B1C1C', 'text' => '#FFFFFF'],
    ][$variant] ?? ['bg' => '#2B4347', 'border' => '#2B4347', 'text' => '#FFFFFF'];
@endphp
<table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin: {{ $margin ?? '24px 0' }}; border-collapse: separate;">
    <tr>
        <td align="center" style="border-radius: 8px; background-color: {{ $styles['bg'] }};">
            <a href="{{ $url }}" target="_blank" rel="noopener"
                style="display: inline-block; padding: 12px 20px; border: 1px solid {{ $styles['border'] }}; border-radius: 8px; background-color: {{ $styles['bg'] }}; color: {{ $styles['text'] }}; font-family: -apple-system, 'Segoe UI', Roboto, Arial, sans-serif; font-size: 15px; line-height: 20px; font-weight: 600; text-decoration: none;">{{ $label }}</a>
        </td>
    </tr>
</table>
