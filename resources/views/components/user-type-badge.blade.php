@props(['user'])

<x-ui.status tone="neutral" {{ $attributes }}>{{ $user->isOrganizer() ? __('Organisator') : __('Teilnehmer') }}</x-ui.status>
