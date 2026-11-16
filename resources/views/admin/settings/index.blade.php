<x-layouts.app title="System-Einstellungen">
    <x-ui.page-header title="System-Einstellungen" subtitle="Verwalten Sie alle System-Einstellungen.">
        <x-slot:actions>
            <form method="POST" action="{{ route('admin.settings.initialize') }}">
                @csrf
                <x-ui.button variant="secondary" type="submit">Standard-Einstellungen initialisieren</x-ui.button>
            </form>
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.tabs label="Einstellungsgruppen" class="mb-8">
        @foreach ($groups as $g)
            <x-ui.tab :href="route('admin.settings.index', ['group' => $g])" :active="$group === $g">{{ ucfirst($g) }}</x-ui.tab>
        @endforeach
    </x-ui.tabs>

    @if ($settings->isEmpty())
        <x-ui.empty>Keine Einstellungen in dieser Gruppe vorhanden.</x-ui.empty>
    @else
        <form method="POST" action="{{ route('admin.settings.update') }}" class="max-w-form">
            @csrf
            @method('PUT')

            @foreach ($settings as $setting)
                <div class="border-t border-line py-6">
                    @if ($setting->type === 'boolean')
                        <x-ui.checkbox name="settings[{{ $setting->key }}]" id="setting_{{ $setting->key }}"
                            :checked="(bool) $setting->getTypedValue()" :label="$setting->label"
                            :hint="$setting->description" />
                    @elseif ($setting->type === 'integer')
                        <x-ui.field :label="$setting->label" name="settings[{{ $setting->key }}]"
                            id="setting_{{ $setting->key }}" type="number" :value="$setting->getTypedValue()"
                            :hint="$setting->description" input-class="sm:max-w-64" />
                    @elseif ($setting->key === 'primary_color')
                        <x-ui.label for="setting_{{ $setting->key }}">{{ $setting->label }}</x-ui.label>
                        <div class="flex flex-wrap items-center gap-3">
                            <input type="color" name="settings[{{ $setting->key }}]" id="setting_{{ $setting->key }}"
                                value="{{ $setting->value }}"
                                class="h-12 w-20 cursor-pointer rounded-control border border-line-strong bg-surface p-1">
                            <x-ui.input :value="$setting->value" readonly :old="false" aria-label="Hex-Code der Farbe"
                                class="w-32" />
                        </div>
                        @if ($setting->description)
                            <x-ui.hint>{{ $setting->description }}</x-ui.hint>
                        @endif
                    @else
                        <x-ui.field :label="$setting->label" name="settings[{{ $setting->key }}]"
                            id="setting_{{ $setting->key }}" :value="$setting->value" :hint="$setting->description" />
                    @endif

                    <p class="m-0 mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-small text-muted">
                        <span>Schlüssel: <code class="font-mono text-ink">{{ $setting->key }}</code></span>
                        <span>Typ: {{ $setting->type }}</span>
                        @if ($setting->is_public)
                            <span>Öffentlich</span>
                        @endif
                    </p>
                </div>
            @endforeach

            <div class="border-t border-line pt-8">
                <x-ui.button type="submit">Einstellungen speichern</x-ui.button>
            </div>
        </form>
    @endif
</x-layouts.app>
