{{-- Unternavigation der Einstellungen: Reiter als Links, auf dem Handy waagrecht scrollbar. --}}
<x-ui.tabs label="{{ __('Einstellungen') }}" class="mb-8">
    <x-ui.tab :href="route('settings.profile.edit')" :active="request()->routeIs('settings.profile.*')">{{ __('Profile') }}</x-ui.tab>
    <x-ui.tab :href="route('settings.password.edit')" :active="request()->routeIs('settings.password.*')">{{ __('Password') }}</x-ui.tab>
    <x-ui.tab :href="route('settings.notifications.edit')" :active="request()->routeIs('settings.notifications.*')">{{ __('Notifications') }}</x-ui.tab>
    <x-ui.tab :href="route('settings.interests.edit')" :active="request()->routeIs('settings.interests.*')">{{ __('Interessen & Newsletter') }}</x-ui.tab>
    <x-ui.tab :href="route('settings.privacy.edit')" :active="request()->routeIs('settings.privacy.*')">{{ __('Datenschutz') }}</x-ui.tab>
    <x-ui.tab :href="route('settings.appearance.edit')" :active="request()->routeIs('settings.appearance.*')">{{ __('Appearance') }}</x-ui.tab>
</x-ui.tabs>
