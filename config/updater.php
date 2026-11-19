<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Online-Updater
    |--------------------------------------------------------------------------
    |
    | Aktualisiert die Anwendung aus dem Git-Repository (git fetch + fast-forward
    | merge), installiert Abhängigkeiten, führt Migrationen aus und baut die
    | Frontend-Assets. Auslösbar über /admin/updates oder `php artisan app:update`.
    |
    | Voraussetzung: Die Anwendung liegt als Git-Checkout auf dem Server und der
    | Benutzer des Webservers darf git, composer (und ggf. npm) ausführen.
    |
    */

    // Updates über die Admin-Oberfläche erlauben
    'enabled' => env('UPDATER_ENABLED', true),

    'remote' => env('UPDATER_REMOTE', 'origin'),
    // Leer = aus dem aktuell ausgecheckten Branch aktualisieren
    'branch' => env('UPDATER_BRANCH'),

    // Pfade zu den Programmen (leer = automatisch / aus PATH)
    'php_binary' => env('UPDATER_PHP_BINARY'),
    'git_binary' => env('UPDATER_GIT_BINARY', 'git'),
    'composer_binary' => env('UPDATER_COMPOSER_BINARY', 'composer'),
    'npm_binary' => env('UPDATER_NPM_BINARY', 'npm'),

    // Frontend-Assets (Vite) nach dem Update neu bauen, wenn sich Quellen geändert haben
    'build_assets' => env('UPDATER_BUILD_ASSETS', true),

    // Wartungsmodus während des Updates aktivieren
    'maintenance_mode' => env('UPDATER_MAINTENANCE_MODE', true),

    // Maximale Laufzeit eines einzelnen Schritts in Sekunden
    'timeout' => (int) env('UPDATER_TIMEOUT', 900),

];
