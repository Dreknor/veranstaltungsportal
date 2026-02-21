<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('components.layouts.app.favicon')
    <title>{{ config('app.name') }}</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    @vite('resources/css/app.css')

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
        applyTheme();
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
            if (!('darkMode' in localStorage)) applyTheme();
        });
    </script>
</head>

<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 antialiased min-h-screen">
    {{ $slot }}
</body>

</html>

