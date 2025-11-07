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
               "X-WR-TIMEZONE:Europe/Berlin\r\n";
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
        $dtstart = $this->formatDateTime($event->start_date);
        $dtend = $this->formatDateTime($event->end_date);
        $dtstamp = $this->formatDateTime(now());
        $created = $this->formatDateTime($event->created_at);
        $modified = $this->formatDateTime($event->updated_at);

        $location = $this->formatLocation($event);
        $description = $this->formatDescription($event->description);
        $url = route('events.show', $event->slug);

        return "BEGIN:VEVENT\r\n" .
               "UID:{$uid}\r\n" .
               "DTSTAMP:{$dtstamp}\r\n" .
               "DTSTART:{$dtstart}\r\n" .
               "DTEND:{$dtend}\r\n" .
               "CREATED:{$created}\r\n" .
               "LAST-MODIFIED:{$modified}\r\n" .
               "SUMMARY:{$this->escapeString($event->title)}\r\n" .
               "DESCRIPTION:{$description}\r\n" .
               "LOCATION:{$location}\r\n" .
               "URL:{$url}\r\n" .
               "STATUS:CONFIRMED\r\n" .
               "SEQUENCE:0\r\n" .
               "CATEGORIES:{$event->category->name}\r\n" .
               "ORGANIZER;CN={$this->escapeString($event->user->name)}:mailto:{$event->user->email}\r\n" .
               "END:VEVENT\r\n";
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
        $dtstart = $this->formatDateTime($event->start_date);
        $dtend = $this->formatDateTime($event->end_date);
        $dtstamp = $this->formatDateTime(now());

        $location = $this->formatLocation($event);
        $description = $this->formatBookingDescription($booking);
        $url = route('bookings.show', $booking->booking_number);

        // Add reminder 1 day before
        $alarm = "BEGIN:VALARM\r\n" .
                 "TRIGGER:-P1D\r\n" .
                 "ACTION:DISPLAY\r\n" .
                 "DESCRIPTION:Erinnerung: {$this->escapeString($event->title)} morgen\r\n" .
                 "END:VALARM\r\n";

        return "BEGIN:VEVENT\r\n" .
               "UID:{$uid}\r\n" .
               "DTSTAMP:{$dtstamp}\r\n" .
               "DTSTART:{$dtstart}\r\n" .
               "DTEND:{$dtend}\r\n" .
               "SUMMARY:📚 {$this->escapeString($event->title)}\r\n" .
               "DESCRIPTION:{$description}\r\n" .
               "LOCATION:{$location}\r\n" .
               "URL:{$url}\r\n" .
               "STATUS:CONFIRMED\r\n" .
               "SEQUENCE:0\r\n" .
               "CATEGORIES:Fortbildung\r\n" .
               "ORGANIZER;CN={$this->escapeString($event->user->name)}:mailto:{$event->user->email}\r\n" .
               "ATTENDEE;CN={$this->escapeString($booking->customer_name)};RSVP=FALSE:mailto:{$booking->customer_email}\r\n" .
               $alarm .
               "END:VEVENT\r\n";
    }

    /**
     * Format DateTime for iCal
     *
     * @param \Carbon\Carbon $dateTime
     * @return string
     */
    protected function formatDateTime($dateTime): string
    {
        return $dateTime->setTimezone('Europe/Berlin')->format('Ymd\THis');
    }

    /**
     * Format location for iCal
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
            $event->venue_country
        ]);

        return $this->escapeString(implode(', ', $parts));
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
     * Generate unique UID
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
     * Escape string for iCal format
     *
     * @param string $string
     * @return string
     */
    protected function escapeString(string $string): string
    {
        $string = str_replace(["\r\n", "\n", "\r"], '\n', $string);
        $string = str_replace([',', ';', '\\'], ['\\,', '\\;', '\\\\'], $string);
        return $string;
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
     * Get Google Calendar URL for event
     *
     * @param Event $event
     * @return string
     */
    public function getGoogleCalendarUrl(Event $event): string
    {
        $params = [
            'action' => 'TEMPLATE',
            'text' => $event->title,
            'dates' => $this->formatDateTime($event->start_date) . '/' . $this->formatDateTime($event->end_date),
            'details' => Str::limit(strip_tags($event->description), 1000),
            'location' => $this->formatLocation($event),
        ];

        return 'https://calendar.google.com/calendar/render?' . http_build_query($params);
    }
}

