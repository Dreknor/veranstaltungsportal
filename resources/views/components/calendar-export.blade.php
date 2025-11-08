<!-- Calendar Export Component -->
@props(['event'])

@php
    $calendarService = app(\App\Services\CalendarService::class);
    $googleUrl = $calendarService->getGoogleCalendarUrl($event);
    $outlookUrl = $calendarService->getOutlookCalendarUrl($event);
    $icalUrl = route('events.calendar.export', $event->slug);
@endphp

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        Zum Kalender hinzufügen
    </h3>

    <div class="space-y-2">
        <!-- Google Calendar -->
        <a href="{{ $googleUrl }}"
           target="_blank"
           rel="noopener noreferrer"
           class="flex items-center gap-3 px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors group">
            <svg class="w-5 h-5 text-blue-600" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 0C5.372 0 0 5.373 0 12s5.372 12 12 12c6.627 0 12-5.373 12-12S18.627 0 12 0zm5.696 14.943c-1.747 2.328-5.025 3.271-7.828 2.187-2.804-1.084-4.453-4.085-3.947-7.176.506-3.09 3.153-5.394 6.336-5.516 2.013-.077 3.943.682 5.344 2.1l-2.168 2.167c-.672-.639-1.558-.988-2.483-.983-1.93.012-3.5 1.566-3.517 3.496-.017 1.931 1.532 3.507 3.462 3.53 1.421.016 2.648-.838 3.085-2.072H12v-3.004h8.035c.116.61.173 1.234.168 1.858-.006 3.565-2.089 6.797-5.507 8.413z"/>
            </svg>
            <span class="flex-1 text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-gray-100">
                Google Calendar
            </span>
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
            </svg>
        </a>

        <!-- Outlook Calendar -->
        <a href="{{ $outlookUrl }}"
           target="_blank"
           rel="noopener noreferrer"
           class="flex items-center gap-3 px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors group">
            <svg class="w-5 h-5 text-blue-500" viewBox="0 0 24 24" fill="currentColor">
                <path d="M24 7.387v9.226a3.387 3.387 0 01-3.387 3.387h-5.226V3.387A3.387 3.387 0 0118.774 0H20.613A3.387 3.387 0 0124 3.387v4zM0 7.387v9.226A3.387 3.387 0 003.387 20h11.226V4H3.387A3.387 3.387 0 000 7.387zm8 7.226c-1.867 0-3.387-1.52-3.387-3.387S6.133 7.84 8 7.84s3.387 1.52 3.387 3.387-1.52 3.387-3.387 3.387z"/>
            </svg>
            <span class="flex-1 text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-gray-100">
                Outlook Calendar
            </span>
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
            </svg>
        </a>

        <!-- iCal Download -->
        <a href="{{ $icalUrl }}"
           class="flex items-center gap-3 px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors group">
            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            <span class="flex-1 text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-gray-100">
                iCal herunterladen (.ics)
            </span>
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
        </a>
    </div>

    <p class="mt-4 text-xs text-gray-500 dark:text-gray-400">
        Fügen Sie diese Veranstaltung zu Ihrem bevorzugten Kalender hinzu, um keine Termine zu verpassen.
    </p>
</div>

