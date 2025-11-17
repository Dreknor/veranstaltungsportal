@php($org = isset($currentOrganization) ? $currentOrganization : auth()->user()->currentOrganization())
<div class="relative">
    <button type="button" class="inline-flex items-center gap-2 px-3 py-2 rounded bg-gray-100 hover:bg-gray-200" onclick="document.getElementById('orgMenu').classList.toggle('hidden')">
        @if($org)
            <span class="font-medium">{{ $org->name }}</span>
        @else
            <span class="text-gray-500">Keine Organisation</span>
        @endif
        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
    </button>
    <div id="orgMenu" class="absolute right-0 mt-2 w-64 bg-white border rounded shadow hidden z-50">
        <div class="p-3 border-b flex justify-between items-center">
            <span class="text-sm font-semibold">Organisation wechseln</span>
            <button class="text-gray-400 hover:text-gray-600" onclick="document.getElementById('orgMenu').classList.add('hidden')">×</button>
        </div>
        <div class="max-h-60 overflow-y-auto">
            @foreach(auth()->user()->activeOrganizations as $o)
                <form method="POST" action="{{ route('organizer.organizations.switch', $o) }}" class="block">
                    @csrf
                    <button class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 {{ $org && $org->id === $o->id ? 'bg-gray-50 font-medium' : '' }}">
                        {{ $o->name }}
                        @if($org && $org->id === $o->id)
                            <span class="text-xs text-primary-600">(aktiv)</span>
                        @endif
                    </button>
                </form>
            @endforeach
        </div>
        <div class="p-3 border-t">
            <a href="{{ route('organizer.organizations.create') }}" class="btn btn-sm btn-outline-primary w-full">Neue Organisation</a>
        </div>
    </div>
</div>

