<!--
TEMPLATE FÜR ORGANIZER/ADMIN HILFE-ARTIKEL
Kopieren Sie dieses Template und passen Sie es an
-->
<x-layouts.app>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center mb-2">
                    <a href="{{ route('help.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 ml-4">ARTIKEL-TITEL HIER</h1>
                </div>
                <p class="text-gray-600 dark:text-gray-400 ml-10">Kurze Beschreibung des Artikels</p>
            </div>

            <!-- Content Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900 dark:text-gray-100">

                    <!-- Introduction -->
                    <div class="mb-8">
                        <p class="text-lg text-gray-700 dark:text-gray-300">
                            Einleitungstext der den Nutzer abholt und erklärt, was in diesem Artikel behandelt wird.
                        </p>
                    </div>

                    <!-- Table of Contents -->
                    <div class="mb-8 bg-blue-50 dark:bg-blue-900/20 p-6 rounded-lg">
                        <h2 class="text-xl font-semibold mb-4">Inhalt</h2>
                        <ul class="space-y-2">
                            <li><a href="#section1" class="text-blue-600 dark:text-blue-400 hover:underline">1. Erste Sektion</a></li>
                            <li><a href="#section2" class="text-blue-600 dark:text-blue-400 hover:underline">2. Zweite Sektion</a></li>
                            <li><a href="#section3" class="text-blue-600 dark:text-blue-400 hover:underline">3. Dritte Sektion</a></li>
                        </ul>
                    </div>

                    <!-- Section 1 -->
                    <section id="section1" class="mb-12">
                        <h2 class="text-2xl font-bold mb-4 flex items-center">
                            <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">1</span>
                            Erste Sektion Titel
                        </h2>

                        <div class="ml-11 space-y-4">
                            <h3 class="text-xl font-semibold">Unterüberschrift</h3>
                            <p class="text-gray-700 dark:text-gray-300">
                                Beschreibungstext mit Erklärungen.
                            </p>

                            <!-- Schritt-für-Schritt Anleitung -->
                            <ol class="list-decimal list-inside space-y-3 text-gray-700 dark:text-gray-300">
                                <li>Erster Schritt mit Beschreibung</li>
                                <li>Zweiter Schritt
                                    <ul class="list-disc list-inside ml-6 mt-2 space-y-1">
                                        <li>Unter-Punkt A</li>
                                        <li>Unter-Punkt B</li>
                                    </ul>
                                </li>
                                <li>Dritter Schritt</li>
                            </ol>

                            <!-- Tipp-Box (Blau) -->
                            <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-400 p-4 mt-4">
                                <div class="flex">
                                    <svg class="h-5 w-5 text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    <p><strong>Tipp:</strong> Hilfreicher Hinweis für den Nutzer.</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Section 2 -->
                    <section id="section2" class="mb-12">
                        <h2 class="text-2xl font-bold mb-4 flex items-center">
                            <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">2</span>
                            Zweite Sektion Titel
                        </h2>

                        <div class="ml-11 space-y-4">
                            <p class="text-gray-700 dark:text-gray-300">
                                Text für die zweite Sektion.
                            </p>

                            <!-- Feature-Grid -->
                            <h3 class="text-xl font-semibold mt-6">Feature-Übersicht</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2">📋 Feature 1</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Beschreibung des Features
                                    </p>
                                </div>
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2">⚙️ Feature 2</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Beschreibung des Features
                                    </p>
                                </div>
                            </div>

                            <!-- Warnung-Box (Gelb) -->
                            <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-4 mt-4">
                                <div class="flex">
                                    <svg class="h-5 w-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <p><strong>Wichtig:</strong> Wichtiger Hinweis oder Warnung.</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Section 3 -->
                    <section id="section3" class="mb-12">
                        <h2 class="text-2xl font-bold mb-4 flex items-center">
                            <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">3</span>
                            Dritte Sektion Titel
                        </h2>

                        <div class="ml-11 space-y-4">
                            <p class="text-gray-700 dark:text-gray-300">
                                Text für die dritte Sektion.
                            </p>

                            <!-- Erfolgs-Box (Grün) -->
                            <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-400 p-4">
                                <div class="flex">
                                    <svg class="h-5 w-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <p><strong>Empfehlung:</strong> Best Practice oder Erfolgs-Tipp.</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Optional: Navigation zu anderen Artikeln -->
                    <div class="mt-12 pt-8 border-t dark:border-gray-700">
                        <div class="flex justify-between items-center">
                            <a href="{{ route('help.article', ['category' => 'CATEGORY', 'article' => 'previous-article']) }}"
                               class="text-blue-600 dark:text-blue-400 hover:underline flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Vorheriger Artikel: Titel
                            </a>
                            <a href="{{ route('help.article', ['category' => 'CATEGORY', 'article' => 'next-article']) }}"
                               class="text-blue-600 dark:text-blue-400 hover:underline flex items-center">
                                Nächster Artikel: Titel
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

