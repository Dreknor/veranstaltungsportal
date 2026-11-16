@props(['url'])
{{-- Kopf mit Wortmarke als Text (A6): Zeichen aus reinem HTML, keine Bilder von außen. --}}
<tr>
<td class="header">
<table class="header-inner" align="center" width="600" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td class="header-cell">
<a href="{{ $url }}" class="wordmark">
<table class="wordmark-table" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td class="wordmark-sign" width="36" height="36" align="center" valign="middle"><span class="wordmark-leaf"></span></td>
<td class="wordmark-text"><span class="wordmark-name">{!! trim($slot) !!}</span><span class="wordmark-tag">Hauptfach Mensch</span></td>
</tr>
</table>
</a>
</td>
</tr>
</table>
</td>
</tr>
