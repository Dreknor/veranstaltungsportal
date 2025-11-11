@props(['active' => false, 'href' => '#', 'icon' => null])
<li>
    <a href="{{ $href }}" @class([
        'group relative flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 ease-in-out',
        'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md shadow-blue-500/30 dark:shadow-blue-400/20' => $active,
        'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400' => !$active,
    ])
    :class="{ 'justify-center': !sidebarOpen, 'justify-start': sidebarOpen }">
        <!-- Active Indicator -->
        @if($active)
        <span class="absolute left-0 w-1 h-8 bg-white rounded-r-full"></span>
        @endif

        <!-- Icon Container -->
        <span @class([
            'flex items-center justify-center w-5 h-5 transition-transform duration-200',
            'group-hover:scale-110' => !$active
        ])>
            @svg($icon, $active ? 'w-5 h-5 text-white' : 'w-5 h-5 text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400')
        </span>

        <!-- Text -->
        <span x-show="sidebarOpen"
              x-transition:enter="transition ease-out duration-200"
              x-transition:enter-start="opacity-0 -translate-x-2"
              x-transition:enter-end="opacity-100 translate-x-0"
              x-transition:leave="transition ease-in duration-150"
              x-transition:leave-start="opacity-100 translate-x-0"
              x-transition:leave-end="opacity-0 -translate-x-2"
              class="ml-3 font-medium text-sm whitespace-nowrap">
            {{ $slot }}
        </span>

        <!-- Hover Effect -->
        @if(!$active)
        <span class="absolute inset-0 rounded-lg bg-gradient-to-r from-blue-500/0 to-purple-500/0 group-hover:from-blue-500/5 group-hover:to-purple-500/5 dark:group-hover:from-blue-500/10 dark:group-hover:to-purple-500/10 transition-all duration-200"></span>
        @endif
    </a>
</li>
