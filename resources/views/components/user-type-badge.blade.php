@props(['user'])

<x-ui.status tone="neutral" {{ $attributes }}>{{ $user->isOrganizer() ? __('Veranstalter') : __('Teilnehmer') }}</x-ui.status>
