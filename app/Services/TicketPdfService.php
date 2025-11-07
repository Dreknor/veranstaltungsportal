<?php

namespace App\Services;

use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class TicketPdfService
{
    public function __construct(
        protected QrCodeService $qrCodeService
    ) {}

    /**
     * Generate PDF ticket for a booking
     *
     * @param Booking $booking
     * @return \Barryvdh\DomPDF\PDF
     */
    public function generateTicket(Booking $booking): \Barryvdh\DomPDF\PDF
    {
        $qrCodeDataUri = $this->qrCodeService->generateBookingQrCodeDataUri($booking, 200);

        $data = [
            'booking' => $booking,
            'event' => $booking->event,
            'qrCode' => $qrCodeDataUri,
            'bookingItems' => $booking->items()->with('ticketType')->get(),
        ];

        return Pdf::loadView('pdf.ticket', $data)
            ->setPaper('a4', 'portrait')
            ->setOption('margin-top', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10);
    }

    /**
     * Generate and download PDF ticket
     *
     * @param Booking $booking
     * @param string|null $filename
     * @return \Illuminate\Http\Response
     */
    public function downloadTicket(Booking $booking, ?string $filename = null): \Illuminate\Http\Response
    {
        $filename = $filename ?? $this->getTicketFilename($booking);

        return $this->generateTicket($booking)->download($filename);
    }

    /**
     * Generate and stream PDF ticket (for inline viewing)
     *
     * @param Booking $booking
     * @return \Illuminate\Http\Response
     */
    public function streamTicket(Booking $booking): \Illuminate\Http\Response
    {
        return $this->generateTicket($booking)->stream();
    }

    /**
     * Save PDF ticket to storage
     *
     * @param Booking $booking
     * @param string|null $path
     * @return string Path to saved file
     */
    public function saveTicket(Booking $booking, ?string $path = null): string
    {
        $path = $path ?? "tickets/booking-{$booking->id}-" . time() . ".pdf";

        $pdf = $this->generateTicket($booking);

        Storage::put($path, $pdf->output());

        return $path;
    }

    /**
     * Get ticket filename
     *
     * @param Booking $booking
     * @return string
     */
    protected function getTicketFilename(Booking $booking): string
    {
        $eventSlug = \Illuminate\Support\Str::slug($booking->event->title);
        return "ticket-{$booking->booking_number}-{$eventSlug}.pdf";
    }

    /**
     * Generate PDF ticket and return as base64 string (for email attachments)
     *
     * @param Booking $booking
     * @return string
     */
    public function getTicketBase64(Booking $booking): string
    {
        return base64_encode($this->generateTicket($booking)->output());
    }

    /**
     * Generate PDF ticket and return raw content (for email attachments)
     *
     * @param Booking $booking
     * @return string
     */
    public function getTicketContent(Booking $booking): string
    {
        return $this->generateTicket($booking)->output();
    }

    /**
     * Generate invoice PDF (different from ticket)
     *
     * @param Booking $booking
     * @return \Barryvdh\DomPDF\PDF
     */
    public function generateInvoice(Booking $booking): \Barryvdh\DomPDF\PDF
    {
        $data = [
            'booking' => $booking,
            'event' => $booking->event,
            'bookingItems' => $booking->items()->with('ticketType')->get(),
            'invoiceNumber' => $this->generateInvoiceNumber($booking),
            'invoiceDate' => now(),
        ];

        return Pdf::loadView('pdf.invoice', $data)
            ->setPaper('a4', 'portrait');
    }

    /**
     * Download invoice PDF
     *
     * @param Booking $booking
     * @return \Illuminate\Http\Response
     */
    public function downloadInvoice(Booking $booking): \Illuminate\Http\Response
    {
        $filename = "invoice-{$booking->booking_reference}.pdf";

        return $this->generateInvoice($booking)->download($filename);
    }

    /**
     * Generate invoice number
     *
     * @param Booking $booking
     * @return string
     */
    protected function generateInvoiceNumber(Booking $booking): string
    {
        return 'INV-' . date('Y') . '-' . str_pad($booking->id, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Generate multiple tickets for a booking (one per attendee if needed)
     *
     * @param Booking $booking
     * @param bool $separatePerTicket
     * @return \Barryvdh\DomPDF\PDF
     */
    public function generateMultipleTickets(Booking $booking, bool $separatePerTicket = false): \Barryvdh\DomPDF\PDF
    {
        if (!$separatePerTicket) {
            return $this->generateTicket($booking);
        }

        // Generate separate ticket pages for each ticket type/quantity
        $qrCodeDataUri = $this->qrCodeService->generateBookingQrCodeDataUri($booking, 200);

        $tickets = [];
        foreach ($booking->items as $item) {
            for ($i = 0; $i < $item->quantity; $i++) {
                $tickets[] = [
                    'booking' => $booking,
                    'event' => $booking->event,
                    'ticketType' => $item->ticketType,
                    'ticketNumber' => count($tickets) + 1,
                    'qrCode' => $qrCodeDataUri,
                ];
            }
        }

        $data = [
            'tickets' => $tickets,
            'totalTickets' => count($tickets),
        ];

        return Pdf::loadView('pdf.tickets-multiple', $data)
            ->setPaper('a4', 'portrait');
    }
}

