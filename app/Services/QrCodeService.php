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
        // Load items if not already loaded
        if (!$booking->relationLoaded('items')) {
            $booking->load('items');
        }

        return json_encode([
            'booking_id' => $booking->id,
            'reference' => $booking->booking_number,
            'event_id' => $booking->event_id,
            'event_name' => $booking->event->title ?? 'Event',
            'attendee_name' => $booking->customer_name,
            'attendee_email' => $booking->customer_email,
            'total_tickets' => $booking->items->sum('quantity') ?: 1,
            'verification_url' => route('bookings.verify', ['bookingNumber' => $booking->booking_number]),
            'check_in_url' => route('organizer.bookings.check-in', ['booking' => $booking->id]),
        ]);
    }

    /**
     * Generate QR code for an individual ticket (BookingItem)
     *
     * @param \App\Models\BookingItem $item
     * @param int $size
     * @return string
     */
    public function generateTicketQrCodeDataUri(\App\Models\BookingItem $item, int $size = 300): string
    {
        $data = $this->getTicketQrData($item);

        $qrCode = QrCode::format('svg')
            ->errorCorrection('H')
            ->generate($data);

        return 'data:image/svg+xml;base64,' . base64_encode($qrCode);
    }

    /**
     * Get QR code data for an individual ticket
     *
     * @param \App\Models\BookingItem $item
     * @return string JSON encoded ticket data
     */
    protected function getTicketQrData(\App\Models\BookingItem $item): string
    {
        // Load relationships if not already loaded
        if (!$item->relationLoaded('booking')) {
            $item->load('booking.event');
        }

        return json_encode([
            'ticket_id' => $item->id,
            'ticket_number' => $item->ticket_number,
            'booking_id' => $item->booking_id,
            'booking_reference' => $item->booking->booking_number,
            'event_id' => $item->booking->event_id,
            'event_name' => $item->booking->event->title ?? 'Event',
            'attendee_name' => $item->attendee_name ?? $item->booking->customer_name,
            'attendee_email' => $item->attendee_email ?? $item->booking->customer_email,
            'ticket_type' => $item->ticketType->name ?? 'Standard',
            'check_in_url' => route('organizer.bookings.check-in', ['booking' => $item->booking_id]),
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

            // Require at least one identifier (booking_id, reference, or booking_number)
            if (!isset($data['booking_id']) && !isset($data['reference']) && !isset($data['booking_number'])) {
                return null;
            }

            return $data;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Generate QR code for a booking (alias for backward compatibility)
     *
     * @param Booking $booking
     * @return string
     */
    public function generateForBooking(Booking $booking): string
    {
        return $this->generateBookingQrCode($booking, 'svg', 300);
    }

    /**
     * Verify QR code and return booking (alias for backward compatibility)
     *
     * @param string $qrData
     * @return Booking|null
     */
    public function verifyQrCode(string $qrData): ?Booking
    {
        $data = $this->verifyQrCodeData($qrData);

        if (!$data) {
            return null;
        }

        // Support both old format (booking_number/verification_code) and new format (booking_id/reference)
        if (isset($data['booking_id'])) {
            return Booking::find($data['booking_id']);
        }

        if (isset($data['reference'])) {
            return Booking::where('booking_number', $data['reference'])->first();
        }

        if (isset($data['booking_number'])) {
            return Booking::where('booking_number', $data['booking_number'])->first();
        }

        return null;
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

    /**
     * Generate EPC/GiroCode QR code for bank transfer (SEPA)
     *
     * @param array $bankAccount Bank account data
     * @param float $amount Amount to transfer
     * @param string $reference Payment reference
     * @param string $recipientName Recipient name
     * @param int $size QR code size
     * @return string Data URI for embedding in HTML/PDF
     */
    public function generatePaymentQrCode(array $bankAccount, float $amount, string $reference, string $recipientName, int $size = 200): string
    {
        // EPC QR Code format (GiroCode) - SEPA Credit Transfer
        // Format: https://www.europeanpaymentscouncil.eu/sites/default/files/kb/file/2018-05/EPC069-12%20v2.1%20Quick%20Response%20Code%20-%20Guidelines%20to%20Enable%20the%20Data%20Capture%20for%20the%20Initiation%20of%20a%20SCT.pdf

        $iban = $bankAccount['iban'] ?? '';
        $bic = $bankAccount['bic'] ?? '';
        $accountHolder = $bankAccount['account_holder'] ?? $recipientName;

        // Remove spaces from IBAN
        $iban = str_replace(' ', '', $iban);

        // Build EPC QR Code data
        $epcData = [
            'BCD',                                      // Service Tag
            '002',                                      // Version
            '1',                                        // Character Set (1 = UTF-8)
            'SCT',                                      // Identification (SEPA Credit Transfer)
            $bic,                                       // BIC (can be empty for SEPA zone)
            substr($accountHolder, 0, 70),             // Beneficiary Name (max 70 chars)
            $iban,                                      // Beneficiary Account (IBAN)
            'EUR' . number_format($amount, 2, '.', ''), // Amount (EUR123.45)
            '',                                         // Purpose (empty)
            substr($reference, 0, 140),                // Remittance Information (max 140 chars)
            '',                                         // Beneficiary to Originator Information (empty)
        ];

        // Join with line breaks
        $qrContent = implode("\n", $epcData);

        // Generate QR code
        $qrCode = QrCode::format('svg')
            ->size($size)
            ->errorCorrection('M')
            ->generate($qrContent);

        return 'data:image/svg+xml;base64,' . base64_encode($qrCode);
    }
}

