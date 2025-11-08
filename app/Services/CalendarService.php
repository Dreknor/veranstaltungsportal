<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Booking;
use Illuminate\Support\Str;

class CalendarService
{
    /**
     * Generate iCal file for an event
     *
     * @param Event $event
     * @return string iCal content
     */
    public function generateEventIcal(Event $event): string
    {
        $ical = $this->getIcalHeader();
        $ical .= $this->generateEventEntry($event);
        $ical .= $this->getIcalFooter();

        return $ical;
    }

    /**
     * Generate iCal file for a booking
     *
     * @param Booking $booking
     * @return string iCal content
     */
    public function generateBookingIcal(Booking $booking): string
    {
        $ical = $this->getIcalHeader();
        $ical .= $this->generateBookingEntry($booking);
        $ical .= $this->getIcalFooter();

        return $ical;
    }

    /**
     * Generate iCal header
     *
     * @return string
     */
    protected function getIcalHeader(): string
    {
        return "BEGIN:VCALENDAR\r\n" .
               "VERSION:2.0\r\n" .
               "PRODID:-//Bildungsportal//Event Calendar//DE\r\n" .
               "CALSCALE:GREGORIAN\r\n" .
               "METHOD:PUBLISH\r\n" .
               "X-WR-CALNAME:Bildungsportal Events\r\n" .
               "X-WR-TIMEZONE:Europe/Berlin\r\n" .
               "X-WR-CALDESC:Fort- und Weiterbildungen\r\n";
    }

    /**
     * Generate iCal footer
     *
     * @return string
     */
    protected function getIcalFooter(): string
    {
        return "END:VCALENDAR\r\n";
    }

    /**
     * Generate event entry for iCal
     *
     * @param Event $event
     * @return string
     */
    protected function generateEventEntry(Event $event): string
    {
        $uid = $this->generateUid($event->id, 'event');
        $summary = $this->escapeString($event->title);
        $description = $this->escapeString(strip_tags($event->description));
        $location = $this->escapeString($this->formatLocation($event));
        $url = route('events.show', $event->slug);

        $dtstart = $this->formatDateTime($event->start_date);
        $dtend = $this->formatDateTime($event->end_date);
        $dtstamp = $this->formatDateTime(now());
        $created = $this->formatDateTime($event->created_at);
        $modified = $this->formatDateTime($event->updated_at);

        $ical = "BEGIN:VEVENT\r\n";
        $ical .= "UID:{$uid}\r\n";
        $ical .= "DTSTAMP:{$dtstamp}\r\n";
        $ical .= "DTSTART:{$dtstart}\r\n";
        $ical .= "DTEND:{$dtend}\r\n";
        $ical .= "SUMMARY:{$summary}\r\n";
        $ical .= "DESCRIPTION:{$description}\r\n";
        $ical .= "LOCATION:{$location}\r\n";
        $ical .= "URL:{$url}\r\n";
        $ical .= "STATUS:CONFIRMED\r\n";
        $ical .= "CREATED:{$created}\r\n";
        $ical .= "LAST-MODIFIED:{$modified}\r\n";

        if ($event->organizer_email) {
            $ical .= "ORGANIZER;CN=\"{$event->user->fullName()}\":MAILTO:{$event->organizer_email}\r\n";
        }

        $ical .= "CATEGORIES:{$event->category->name}\r\n";
        $ical .= "END:VEVENT\r\n";

        return $ical;
    }

    /**
     * Generate booking entry for iCal
     *
     * @param Booking $booking
     * @return string
     */
    protected function generateBookingEntry(Booking $booking): string
    {
        $event = $booking->event;
        $uid = $this->generateUid($booking->id, 'booking');
        $summary = $this->escapeString($event->title . ' - ' . $booking->booking_number);
        $description = $this->escapeString(
            strip_tags($event->description) . "\n\n" .
            "Buchungsnummer: {$booking->booking_number}\n" .
            "Teilnehmer: {$booking->customer_name}"
        );
        $location = $this->escapeString($this->formatLocation($event));

        $dtstart = $this->formatDateTime($event->start_date);
        $dtend = $this->formatDateTime($event->end_date);
        $dtstamp = $this->formatDateTime(now());

        $ical = "BEGIN:VEVENT\r\n";
        $ical .= "UID:{$uid}\r\n";
        $ical .= "DTSTAMP:{$dtstamp}\r\n";
        $ical .= "DTSTART:{$dtstart}\r\n";
        $ical .= "DTEND:{$dtend}\r\n";
        $ical .= "SUMMARY:{$summary}\r\n";
        $ical .= "DESCRIPTION:{$description}\r\n";
        $ical .= "LOCATION:{$location}\r\n";
        $ical .= "STATUS:CONFIRMED\r\n";

        // Reminder 24h before
        $ical .= "BEGIN:VALARM\r\n";
        $ical .= "TRIGGER:-PT24H\r\n";
        $ical .= "ACTION:DISPLAY\r\n";
        $ical .= "DESCRIPTION:Erinnerung: {$summary}\r\n";
        $ical .= "END:VALARM\r\n";

        $ical .= "END:VEVENT\r\n";

        return $ical;
    }

    /**
     * Format location string
     *
     * @param Event $event
     * @return string
     */
    protected function formatLocation(Event $event): string
    {
        $parts = array_filter([
            $event->venue_name,
            $event->venue_address,
            $event->venue_postal_code . ' ' . $event->venue_city,
            $event->venue_country,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Format datetime for iCal
     *
     * @param \Carbon\Carbon $datetime
     * @return string
     */
    protected function formatDateTime($datetime): string
    {
        return $datetime->format('Ymd\THis\Z');
    }

    /**
     * Generate unique ID for calendar entry
     *
     * @param int $id
     * @param string $type
     * @return string
     */
    protected function generateUid(int $id, string $type): string
    {
        return "{$type}-{$id}@bildungsportal.de";
    }

    /**
     * Escape special characters for iCal
     *
     * @param string $string
     * @return string
     */
    protected function escapeString(string $string): string
    {
        // Replace line breaks with \n
        $string = str_replace(["\r\n", "\n", "\r"], "\\n", $string);

        // Escape special characters
        $string = str_replace(['\\', ',', ';'], ['\\\\', '\\,', '\\;'], $string);

        // Limit length and fold long lines
        if (strlen($string) > 75) {
            $string = wordwrap($string, 75, "\r\n ", true);
        }

        return $string;
    }

    /**
     * Get Google Calendar URL
     *
     * @param Event $event
     * @return string
     */
    public function getGoogleCalendarUrl(Event $event): string
    {
        $params = [
            'action' => 'TEMPLATE',
            'text' => $event->title,
            'dates' => $event->start_date->format('Ymd\THis\Z') . '/' . $event->end_date->format('Ymd\THis\Z'),
            'details' => strip_tags($event->description),
            'location' => $this->formatLocation($event),
            'ctz' => 'Europe/Berlin',
        ];

        return 'https://calendar.google.com/calendar/render?' . http_build_query($params);
    }

    /**
     * Get Outlook Calendar URL
     *
     * @param Event $event
     * @return string
     */
    public function getOutlookCalendarUrl(Event $event): string
    {
        $params = [
            'path' => '/calendar/action/compose',
            'rru' => 'addevent',
            'subject' => $event->title,
            'startdt' => $event->start_date->toIso8601String(),
            'enddt' => $event->end_date->toIso8601String(),
            'body' => strip_tags($event->description),
            'location' => $this->formatLocation($event),
        ];

        return 'https://outlook.live.com/calendar/0/deeplink/compose?' . http_build_query($params);
    }

    /**
     * Download iCal file for event
     *
     * @param Event $event
     * @return \Illuminate\Http\Response
     */
    public function downloadEventIcal(Event $event)
    {
        $ical = $this->generateEventIcal($event);
        $filename = Str::slug($event->title) . '.ics';

        return response($ical, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, must-revalidate',
        ]);
    }

    /**
     * Download iCal file for booking
     *
     * @param Booking $booking
     * @return \Illuminate\Http\Response
     */
    public function downloadBookingIcal(Booking $booking)
    {
        $ical = $this->generateBookingIcal($booking);
        $filename = 'booking-' . $booking->booking_number . '.ics';

        return response($ical, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, must-revalidate',
        ]);
    }



    /**
     * Format description for iCal
     *
     * @param string $description
     * @return string
     */
    protected function formatDescription(string $description): string
    {
        // Limit to 1000 characters and escape
        $desc = Str::limit(strip_tags($description), 1000);
        return $this->escapeString($desc);
    }

    /**
     * Format booking description
     *
     * @param Booking $booking
     * @return string
     */
    protected function formatBookingDescription(Booking $booking): string
    {
        $desc = "Ihre Buchung für: {$booking->event->title}\n\n";
        $desc .= "Buchungsnummer: {$booking->booking_number}\n";
        $desc .= "Tickets: {$booking->items->sum('quantity')}\n";
        $desc .= "Status: {$booking->status}\n\n";
        $desc .= strip_tags($booking->event->description);

        return $this->formatDescription($desc);
    }



    /**
     * Get proper filename for iCal download
     *
     * @param Event|Booking $model
     * @return string
     */
    public function getFilename($model): string
    {
        if ($model instanceof Event) {
            $slug = Str::slug($model->title);
            return "event-{$slug}.ics";
        } elseif ($model instanceof Booking) {
            return "booking-{$model->booking_number}.ics";
        }

        return "calendar-event.ics";
    }

    /**
     * Generate iCal download response for a booking
     *
     * @param Booking $booking
     * @return \Illuminate\Http\Response
     */
    public function generateIcal(Booking $booking): \Illuminate\Http\Response
    {
        $icalContent = $this->generateBookingIcal($booking);
        $filename = $this->getFilename($booking);

        return response($icalContent, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    /**
     * Generate iCal download response for an event
     *
     * @param Event $event
     * @return \Illuminate\Http\Response
     */
    public function generateEventIcalResponse(Event $event): \Illuminate\Http\Response
    {
        $icalContent = $this->generateEventIcal($event);
        $filename = $this->getFilename($event);

        return response($icalContent, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}


