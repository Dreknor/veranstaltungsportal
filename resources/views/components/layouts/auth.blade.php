<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('components.layouts.app.favicon')
    <title>Login - {{ config('app.name') }}</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- reCAPTCHA site-key als Meta-Tag: Script wird erst nach Cookie-Einwilligung geladen --}}
    @if(config('recaptcha.enabled') && config('recaptcha.site_key'))
        <meta name="recaptcha-site-key" content="{{ config('recaptcha.site_key') }}">
    @endif

    <script>
        function applyTheme() {
            const userPref = localStorage.getItem('darkMode');
            const systemPref = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (userPref === 'true' || (userPref === null && systemPref)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
        // Initial theme application
        applyTheme();
        // Listen for system theme changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (!('darkMode' in localStorage)) {
                applyTheme();
            }
        });
    </script>
</head>

<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200 antialiased" x-data="{
    darkMode: localStorage.getItem('darkMode') === 'true',
    toggleDarkMode() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('darkMode', this.darkMode);
    }
}"
    :class="{ 'dark': darkMode }">

    <div class="min-h-screen flex flex-col">
        <!-- Logo Header -->
        <div class="flex justify-center pt-8 pb-4">
            <a href="/" class="flex flex-col items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }} Logo" class="h-20 w-20 object-contain">
                <span class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                    {{ config('app.name') }}
                </span>
            </a>
        </div>

        <!-- Main Content -->
        <main class="flex-1 flex items-center justify-center p-6">
            <div class="w-full max-w-md">
                {{ $slot }}
            </div>
        </main>

        <!-- Footer -->
        <x-footer />
    </div>

    @stack('scripts')
</body>

</html>
