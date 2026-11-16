{{--
    Unternavigation der Einstellungen: Reiter als Links, auf dem Handy umbrechend.
    „Darstellung" ist bis zur Abnahme einer dunklen Palette ausgeblendet (Richtlinien B2); die Route bleibt.
--}}
<x-ui.tabs label="Einstellungen" wrap class="mb-8">
    <x-ui.tab :href="route('settings.profile.edit')" :active="request()->routeIs('settings.profile.*')">Profil</x-ui.tab>
    <x-ui.tab :href="route('settings.password.edit')" :active="request()->routeIs('settings.password.*')">Passwort</x-ui.tab>
    <x-ui.tab :href="route('settings.notifications.edit')" :active="request()->routeIs('settings.notifications.*')">Benachrichtigungen</x-ui.tab>
    <x-ui.tab :href="route('settings.interests.edit')" :active="request()->routeIs('settings.interests.*')">Interessen &amp; Newsletter</x-ui.tab>
    <x-ui.tab :href="route('settings.privacy.edit')" :active="request()->routeIs('settings.privacy.*')">Datenschutz</x-ui.tab>
</x-ui.tabs>
