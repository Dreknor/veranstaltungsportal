@props(['badge'])

<div x-data="{ show: true }"
     x-show="show"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform scale-90 translate-y-4"
     x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed bottom-4 right-4 z-50 max-w-sm w-full bg-white dark:bg-gray-800 shadow-2xl rounded-lg overflow-hidden border-2"
     style="border-color: {{ $badge->color }};">

    <div class="p-4">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <div class="w-16 h-16 rounded-full flex items-center justify-center animate-bounce"
                     style="background-color: {{ $badge->color }}20;">
                    <i class="fas fa-medal text-4xl" style="color: {{ $badge->color }};"></i>
                </div>
            </div>
            <div class="ml-4 flex-1">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        🎉 Neues Abzeichen!
                    </h3>
                    <button @click="show = false"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <p class="mt-1 text-sm font-semibold text-gray-800 dark:text-gray-200">
                    {{ $badge->name }}
                </p>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ $badge->description }}
                </p>
                <div class="mt-2 flex items-center text-xs text-gray-500 dark:text-gray-400">
                    <span class="inline-flex items-center px-2 py-1 rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">
                        <i class="fas fa-star mr-1"></i> +{{ $badge->points }} Punkte
                    </span>
                </div>
                <div class="mt-3">
                    <a href="{{ route('badges.index') }}"
                       class="text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400">
                        Alle Abzeichen ansehen <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress bar -->
    <div class="h-1 bg-gray-200 dark:bg-gray-700">
        <div class="h-full transition-all duration-[5000ms] ease-linear"
             style="background-color: {{ $badge->color }}; width: 0%"
             x-init="setTimeout(() => $el.style.width = '100%', 100); setTimeout(() => show = false, 5000)">
        </div>
    </div>
</div>

