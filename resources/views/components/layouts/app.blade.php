<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('components.layouts.app.favicon')
    <title>{{ config('app.name') }}</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <script>
        window.setAppearance = function(appearance) {
            let setDark = () => document.documentElement.classList.add('dark')
            let setLight = () => document.documentElement.classList.remove('dark')
            let setButtons = (appearance) => {
                document.querySelectorAll('button[onclick^="setAppearance"]').forEach((button) => {
                    button.setAttribute('aria-pressed', String(appearance === button.value))
                })
            }
            if (appearance === 'system') {
                let media = window.matchMedia('(prefers-color-scheme: dark)')
                window.localStorage.removeItem('appearance')
                media.matches ? setDark() : setLight()
            } else if (appearance === 'dark') {
                window.localStorage.setItem('appearance', 'dark')
                setDark()
            } else if (appearance === 'light') {
                window.localStorage.setItem('appearance', 'light')
                setLight()
            }
            if (document.readyState === 'complete') {
                setButtons(appearance)
            } else {
                document.addEventListener("DOMContentLoaded", () => setButtons(appearance))
            }
        }
        window.setAppearance(window.localStorage.getItem('appearance') || 'system')
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 antialiased" x-data="{
    sidebarOpen: localStorage.getItem('sidebarOpen') === null ? window.innerWidth >= 768 : localStorage.getItem('sidebarOpen') === 'true',
    toggleSidebar() {
        this.sidebarOpen = !this.sidebarOpen;
        localStorage.setItem('sidebarOpen', this.sidebarOpen);
    },
    temporarilyOpenSidebar() {
        if (!this.sidebarOpen) {
            this.sidebarOpen = true;
            localStorage.setItem('sidebarOpen', true);
        }
    },
    formSubmitted: false,
}" x-init="
    // Auto-close sidebar on mobile when clicking outside
    $watch('sidebarOpen', value => {
        if (value && window.innerWidth < 768) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    })
">

    <!-- Main Container with Modern Layout -->
    <div class="min-h-screen flex flex-col">

        <x-layouts.app.header />

        {{-- Organizer Navigation Bar (nur auf Organizer-Routen) --}}
        @auth
            @if (request()->routeIs('organizer.*'))
                @include('components.organizer-navigation')
            @endif
        @endauth

        <!-- Impersonation Banner -->
        @if(session()->has('impersonator'))
            <div class="bg-yellow-500 text-white py-2 px-4">
                <div class="container mx-auto flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <span class="font-semibold">Sie verkörpern gerade: {{ auth()->user()->name }}</span>
                    </div>
                    <form action="{{ route('impersonate.leave') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-white text-yellow-600 px-4 py-1 rounded font-semibold hover:bg-yellow-50 transition">
                            Verkörperung beenden
                        </button>
                    </form>
                </div>
            </div>
        @endif

        <!-- Main Content Area with Sidebar -->
        <div class="flex flex-1 overflow-hidden relative">

            <x-layouts.app.sidebar />

            <!-- Main Content with Enhanced Styling -->
            <main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-900 transition-all duration-300 ease-in-out">
                <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6 max-w-7xl">
                    <!-- Success/Status Message with Modern Design -->
                    @session('status')
                        <div x-data="{ showStatusMessage: true }"
                             x-show="showStatusMessage"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 transform scale-95 -translate-y-2"
                             x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 transform scale-95 -translate-y-2"
                             class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border-l-4 border-green-500 p-4 rounded-lg shadow-sm">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center flex-1">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-green-100 dark:bg-green-800/50 flex items-center justify-center">
                                            <svg class="h-6 w-6 text-green-600 dark:text-green-400"
                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('status') }}</p>
                                    </div>
                                </div>
                                <div class="ml-4 flex-shrink-0">
                                    <button @click="showStatusMessage = false"
                                        class="inline-flex rounded-lg p-2 text-green-600 dark:text-green-400 hover:bg-green-100 dark:hover:bg-green-800/50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                                        <span class="sr-only">{{ __('Dismiss') }}</span>
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endsession

                    <!-- Error Messages with Modern Design -->
                    @if ($errors->any())
                        <div x-data="{ showErrors: true }"
                             x-show="showErrors"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 transform scale-95 -translate-y-2"
                             x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
                             class="mb-6 bg-gradient-to-r from-red-50 to-pink-50 dark:from-red-900/20 dark:to-pink-900/20 border-l-4 border-red-500 p-4 rounded-lg shadow-sm">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-red-100 dark:bg-red-800/50 flex items-center justify-center">
                                        <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4 flex-1">
                                    <h3 class="text-sm font-medium text-red-800 dark:text-red-200 mb-2">
                                        {{ __('Whoops! Something went wrong.') }}
                                    </h3>
                                    <ul class="list-disc list-inside text-sm text-red-700 dark:text-red-300 space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="ml-4 flex-shrink-0">
                                    <button @click="showErrors = false"
                                        class="inline-flex rounded-lg p-2 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-800/50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Optional Page Header -->
                    @isset($header)
                        <header class="mb-6">
                            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                                <div class="px-4 py-5 sm:px-6">
                                    {{ $header }}
                                </div>
                            </div>
                        </header>
                    @endisset

                    <!-- Page Content -->
                    <div class="animate-fadeIn">
                        {{ $slot }}
                    </div>

                </div>
            </main>
        </div>

        <!-- Footer -->
        <x-footer />
    </div>

    @stack('styles')
    @stack('scripts')

    <!-- Custom Styles for Animations -->
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.3s ease-out;
        }

        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(156, 163, 175, 0.5);
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(107, 114, 128, 0.7);
        }

        /* Dark mode scrollbar */
        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(75, 85, 99, 0.5);
        }

        .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(107, 114, 128, 0.7);
        }
    </style>
</body>

</html>
