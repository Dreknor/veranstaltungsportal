<x-layouts.app title="Neue Kategorie erstellen" :header="false">
    <x-ui.page-header title="Neue Kategorie erstellen" subtitle="Erstellen Sie eine neue Veranstaltungskategorie."
        :back-href="route('admin.categories.index')" back-label="Zu den Kategorien" />

    <form method="POST" action="{{ route('admin.categories.store') }}" class="flex max-w-form flex-col gap-y-5">
        @csrf

        <p class="m-0 text-small text-muted">* Pflichtfeld</p>

        <x-ui.field label="Name" name="name"  required />

        <x-ui.field label="Slug" name="slug"  optional hint="Leer lassen, um automatisch zu generieren" />

        <x-ui.textarea label="Beschreibung" name="description"  rows="3" optional />

        <x-ui.field label="Icon (Symbolname)" name="icon"  placeholder="z. B. calendar" optional />

        <x-ui.field label="Farbe (Hex)" name="color" type="color" value="#3B82F6" input-class="!w-24 cursor-pointer !p-1" />

        <x-ui.checkbox name="is_active" :checked="true" label="Aktiv" />

        <div class="flex flex-wrap items-center gap-3 pt-2">
            <x-ui.button type="submit">Erstellen</x-ui.button>
            <x-ui.button variant="secondary" :href="route('admin.categories.index')">Abbrechen</x-ui.button>
        </div>
    </form>
</x-layouts.app>
