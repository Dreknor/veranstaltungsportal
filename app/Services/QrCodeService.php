<?php

namespace App\Services;

use App\Models\Booking;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeService
{
    /**
     * Generate QR code for a booking
     *
     * @param Booking $booking
     * @param string $format Format: 'svg', 'png', 'eps', 'pdf'
     * @param int $size Size in pixels (for PNG/SVG)
     * @return string
     */
    public function generateBookingQrCode(Booking $booking, string $format = 'svg', int $size = 300): string
    {
        $data = $this->getBookingQrData($booking);

        return match($format) {
            'svg' => QrCode::format('svg')->size($size)->generate($data),
            'eps' => QrCode::format('eps')->generate($data),
            default => QrCode::format('svg')->size($size)->generate($data),
        };
    }

    /**
     * Generate QR code as data URI for embedding in HTML/PDF
     *
     * @param Booking $booking
     * @param int $size
     * @return string
     */
    public function generateBookingQrCodeDataUri(Booking $booking, int $size = 300): string
    {
        $data = $this->getBookingQrData($booking);

        // Generate SVG instead of PNG (no Imagick required)
        // Generate SVG instead of PNG (no Imagick required)
        $qrCode = QrCode::format('svg')
            ->errorCorrection('H')
            ->generate($data);

        // Return as data URI for SVG
        // Return as data URI for SVG
        return 'data:image/svg+xml;base64,' . base64_encode($qrCode);
    }

    /**
     * Generate verification URL QR code
     *
     * @param Booking $booking
     * @param string $format
     * @param int $size
     * @return string
     */
    public function generateVerificationQrCode(Booking $booking, string $format = 'svg', int $size = 300): string
    {
        $url = route('bookings.verify', ['bookingNumber' => $booking->booking_number]);

        return match($format) {
            'svg' => QrCode::format('svg')->size($size)->generate($url),
            'eps' => QrCode::format('eps')->generate($url),
            default => QrCode::format('svg')->size($size)->generate($url),
        };
    }

    /**
     * Get QR code data for a booking
     *
     * @param Booking $booking
     * @return string JSON encoded booking data
     */
    protected function getBookingQrData(Booking $booking): string
    {
        return json_encode([
            'booking_id' => $booking->id,
            'reference' => $booking->booking_number,
            'event_id' => $booking->event_id,
            'event_name' => $booking->event->title,
            'attendee_name' => $booking->customer_name,
            'attendee_email' => $booking->customer_email,
            'total_tickets' => $booking->items->sum('quantity'),
            'verification_url' => route('bookings.verify', ['bookingNumber' => $booking->booking_number]),
            'check_in_url' => route('organizer.bookings.check-in', ['booking' => $booking->id]),
        ]);
    }

    /**
     * Verify QR code data
     *
     * @param string $qrData JSON encoded data
     * @return array|null
     */
    public function verifyQrCodeData(string $qrData): ?array
    {
        try {
            $data = json_decode($qrData, true);

            if (!isset($data['booking_id'], $data['reference'])) {
                return null;
            }

            return $data;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Generate QR code for check-in
     *
     * @param Booking $booking
     * @param string $format
     * @param int $size
     * @return string
     */
    public function generateCheckInQrCode(Booking $booking, string $format = 'svg', int $size = 300): string
    {
        $url = route('organizer.bookings.check-in', ['booking' => $booking->id]);

        return match($format) {
            'png' => QrCode::format('png')->size($size)->generate($url),
            'eps' => QrCode::format('eps')->generate($url),
            'pdf' => QrCode::format('pdf')->generate($url),
            default => QrCode::format('svg')->size($size)->generate($url),
        };
    }

    /**
     * Save QR code to storage
     *
     * @param Booking $booking
     * @param string $format
     * @param int $size
     * @return string Path to saved file
     */
    public function saveBookingQrCode(Booking $booking, string $format = 'png', int $size = 300): string
    {
        $qrCode = $this->generateBookingQrCode($booking, $format, $size);

        $filename = "qrcodes/booking-{$booking->id}-" . time() . ".{$format}";
        $path = storage_path("app/public/{$filename}");

        // Create directory if it doesn't exist
        $directory = dirname($path);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($path, $qrCode);

        return $filename;
    }
}

