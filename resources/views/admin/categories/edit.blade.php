<x-layouts.app title="Kategorie bearbeiten" :header="false">
    <x-ui.page-header title="Kategorie bearbeiten" subtitle="Bearbeiten Sie die Veranstaltungskategorie „{{ $category->name }}“."
        :back-href="route('admin.categories.index')" back-label="Zu den Kategorien" />

    <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="flex max-w-form flex-col gap-y-5">
        @csrf
        @method('PUT')
        <p class="m-0 text-small text-muted">* Pflichtfeld</p>

        <x-ui.field label="Name" name="name" :value="$category->name" required />

        <x-ui.field label="Slug" name="slug" :value="$category->slug" optional hint="Leer lassen, um automatisch zu generieren" />

        <x-ui.textarea label="Beschreibung" name="description" :value="$category->description" rows="3" optional />

        <x-ui.field label="Icon (Symbolname)" name="icon" :value="$category->icon" placeholder="fa-calendar" optional />

        <x-ui.field label="Farbe (Hex)" name="color" type="color" :value="$category->color ?? '#3B82F6'" input-class="h-12 w-24 cursor-pointer p-1" />

        <x-ui.checkbox name="is_active" :checked="$category->is_active" label="Aktiv" />

        <div class="flex flex-wrap items-center gap-3 pt-2">
            <x-ui.button type="submit">Aktualisieren</x-ui.button>
            <x-ui.button variant="secondary" :href="route('admin.categories.index')">Abbrechen</x-ui.button>
        </div>
    </form>
</x-layouts.app>
