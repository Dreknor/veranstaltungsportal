<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Event;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class CertificateService
{
    /**
     * Generate attendance certificate for a booking
     *
     * @param Booking $booking
     * @return \Barryvdh\DomPDF\PDF
     */
    public function generateCertificate(Booking $booking): \Barryvdh\DomPDF\PDF
    {
        // Only generate certificate if booking is confirmed and event has ended
        if ($booking->status !== 'confirmed' || $booking->event->end_date->isFuture()) {
            throw new \Exception('Zertifikat kann nur für bestätigte und abgeschlossene Veranstaltungen erstellt werden.');
        }

        $data = [
            'booking' => $booking,
            'event' => $booking->event,
            'attendee_name' => $booking->customer_name,
            'event_title' => $booking->event->title,
            'event_date' => $booking->event->start_date,
            'duration' => $this->calculateDuration($booking->event),
            'certificate_number' => $this->generateCertificateNumber($booking),
            'issue_date' => now(),
        ];

        return Pdf::loadView('pdf.certificate', $data)
            ->setPaper('a4', 'landscape')
            ->setOption('margin-top', 0)
            ->setOption('margin-bottom', 0)
            ->setOption('margin-left', 0)
            ->setOption('margin-right', 0);
    }

    /**
     * Download certificate
     *
     * @param Booking $booking
     * @return \Illuminate\Http\Response
     */
    public function downloadCertificate(Booking $booking): \Illuminate\Http\Response
    {
        $filename = $this->getCertificateFilename($booking);
        return $this->generateCertificate($booking)->download($filename);
    }

    /**
     * Stream certificate (for inline viewing)
     *
     * @param Booking $booking
     * @return \Illuminate\Http\Response
     */
    public function streamCertificate(Booking $booking): \Illuminate\Http\Response
    {
        return $this->generateCertificate($booking)->stream();
    }

    /**
     * Save certificate to storage
     *
     * @param Booking $booking
     * @param string|null $path
     * @return string Path to saved file
     */
    public function saveCertificate(Booking $booking, ?string $path = null): string
    {
        $path = $path ?? "certificates/booking-{$booking->id}-" . time() . ".pdf";

        $pdf = $this->generateCertificate($booking);
        Storage::put($path, $pdf->output());

        return $path;
    }

    /**
     * Get certificate filename
     *
     * @param Booking $booking
     * @return string
     */
    protected function getCertificateFilename(Booking $booking): string
    {
        $eventSlug = \Illuminate\Support\Str::slug($booking->event->title);
        $attendeeName = \Illuminate\Support\Str::slug($booking->customer_name);
        return "teilnahmezertifikat-{$attendeeName}-{$eventSlug}.pdf";
    }

    /**
     * Generate unique certificate number
     *
     * @param Booking $booking
     * @return string
     */
    protected function generateCertificateNumber(Booking $booking): string
    {
        return 'CERT-' . date('Y') . '-' . str_pad($booking->id, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Calculate event duration in hours
     *
     * @param Event $event
     * @return float
     */
    protected function calculateDuration(Event $event): float
    {
        $diffInMinutes = $event->start_date->diffInMinutes($event->end_date);
        return round($diffInMinutes / 60, 1);
    }

    /**
     * Get certificate content for email attachment
     *
     * @param Booking $booking
     * @return string
     */
    public function getCertificateContent(Booking $booking): string
    {
        return $this->generateCertificate($booking)->output();
    }

    /**
     * Generate certificates for all attendees of an event
     *
     * @param Event $event
     * @return array Array of certificate paths
     */
    public function generateEventCertificates(Event $event): array
    {
        $paths = [];

        $confirmedBookings = $event->bookings()
            ->where('status', 'confirmed')
            ->get();

        foreach ($confirmedBookings as $booking) {
            try {
                $paths[] = $this->saveCertificate($booking);
            } catch (\Exception $e) {
                // Log error but continue with other certificates
                \Log::error("Failed to generate certificate for booking {$booking->id}: " . $e->getMessage());
            }
        }

        return $paths;
    }

    /**
     * Check if certificate is available for booking
     *
     * @param Booking $booking
     * @return bool
     */
    public function canGenerateCertificate(Booking $booking): bool
    {
        return $booking->status === 'confirmed' && $booking->event->end_date->isPast();
    }
}

