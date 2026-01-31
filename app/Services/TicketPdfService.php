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
        // Eager load relationships
        $booking->load(['event.organizer', 'items.ticketType']);

        $qrCodeDataUri = $this->qrCodeService->generateBookingQrCodeDataUri($booking, 200);

        $data = [
            'booking' => $booking,
            'event' => $booking->event,
            'qrCode' => $qrCodeDataUri,
            'items' => $booking->items,
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
     * Generate individual ticket for a specific booking item
     *
     * @param \App\Models\BookingItem $item
     * @return \Barryvdh\DomPDF\PDF
     */
    public function generateIndividualTicket(\App\Models\BookingItem $item): \Barryvdh\DomPDF\PDF
    {
        // Eager load relationships
        $item->load(['booking.event.organizer', 'ticketType']);

        // Generate QR code for this specific ticket
        $qrCodeDataUri = $this->qrCodeService->generateTicketQrCodeDataUri($item, 200);

        $data = [
            'item' => $item,
            'booking' => $item->booking,
            'event' => $item->booking->event,
            'qrCode' => $qrCodeDataUri,
        ];

        return Pdf::loadView('pdf.ticket-individual', $data)
            ->setPaper('a4', 'portrait')
            ->setOption('margin-top', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10);
    }

    /**
     * Generate all individual tickets for a booking as one PDF
     *
     * @param Booking $booking
     * @return \Barryvdh\DomPDF\PDF
     */
    public function generateAllIndividualTickets(Booking $booking): \Barryvdh\DomPDF\PDF
    {
        // Eager load relationships
        $booking->load(['event.organization', 'items.ticketType']);

        // Debug: Log the number of items
        \Log::info('Generating tickets for booking', [
            'booking_id' => $booking->id,
            'items_count' => $booking->items->count(),
        ]);

        $ticketsData = [];

        // Generate individual tickets - handle both quantity=1 items and quantity>1 items
        foreach ($booking->items as $item) {
            // If quantity is 1, create one ticket
            if ($item->quantity == 1) {
                $qrCodeDataUri = $this->qrCodeService->generateTicketQrCodeDataUri($item, 200);

                $ticketsData[] = [
                    'item' => $item,
                    'qrCode' => $qrCodeDataUri,
                ];
            } else {
                // If quantity > 1, create multiple tickets for this item
                // This handles legacy bookings where quantity might be > 1
                for ($i = 0; $i < $item->quantity; $i++) {
                    $qrCodeDataUri = $this->qrCodeService->generateTicketQrCodeDataUri($item, 200);

                    $ticketsData[] = [
                        'item' => $item,
                        'qrCode' => $qrCodeDataUri,
                        'ticket_index' => $i + 1,
                    ];
                }
            }
        }

        \Log::info('Tickets data prepared', [
            'tickets_count' => count($ticketsData),
        ]);

        $data = [
            'booking' => $booking,
            'event' => $booking->event,
            'tickets' => $ticketsData,
        ];

        return Pdf::loadView('pdf.tickets-individual-all', $data)
            ->setPaper('a4', 'portrait')
            ->setOption('margin-top', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10);
    }

    /**
     * Get content of all individual tickets for email attachment
     *
     * @param Booking $booking
     * @return string
     */
    public function getAllIndividualTicketsContent(Booking $booking): string
    {
        return $this->generateAllIndividualTickets($booking)->output();
    }

    /**
     * Generate invoice PDF (different from ticket)
     *
     * @param Booking $booking
     * @return \Barryvdh\DomPDF\PDF
     */
    public function generateInvoice(Booking $booking): \Barryvdh\DomPDF\PDF
    {
        // Eager load relationships
        $booking->load(['event.organizer', 'items.ticketType']);

        // Prepare items array
        $items = [];
        $grossTotal = 0; // Total including VAT
        $taxRate = 19; // Default VAT rate for Germany

        foreach ($booking->items as $item) {
            // Prices are stored as gross prices (including VAT)
            $itemTotal = $item->price * $item->quantity;
            $grossTotal += $itemTotal;

            $items[] = [
                'description' => $booking->event->title,
                'ticket_type' => $item->ticketType->name,
                'quantity' => $item->quantity,
                'unit_price' => $item->price, // This is the gross price (inkl. MwSt.)
                'tax_rate' => $taxRate,
                'total' => $itemTotal, // This is also gross (inkl. MwSt.)
            ];
        }

        $discountAmount = $booking->discount ?? 0;
        $totalAmount = $grossTotal - $discountAmount; // Final amount is gross

        // Calculate net amount and tax from gross amount
        $netAfterDiscount = $totalAmount / (1 + ($taxRate / 100));
        $taxAmount = $totalAmount - $netAfterDiscount;

        // Generate payment QR code if not paid and bank account is available
        $paymentQrCode = null;
        if ($booking->payment_status !== 'paid' && $booking->event->organizer->bank_account) {
            $bankAccount = is_string($booking->event->organizer->bank_account)
                ? json_decode($booking->event->organizer->bank_account, true)
                : $booking->event->organizer->bank_account;

            if (is_array($bankAccount) && !empty($bankAccount['iban'])) {
                $paymentQrCode = $this->qrCodeService->generatePaymentQrCode(
                    $bankAccount,
                    $totalAmount,
                    'Rechnung ' . $this->generateInvoiceNumber($booking),
                    $booking->event->organizer->name,
                    200
                );
            }
        }

        $data = [
            'booking' => $booking,
            'event' => $booking->event,
            'items' => $items,
            'grossTotal' => $grossTotal, // Gross total before discount
            'netTotal' => $netAfterDiscount, // Net amount (for reference)
            'discountAmount' => $discountAmount,
            'taxRate' => $taxRate,
            'taxAmount' => $taxAmount,
            'totalAmount' => $totalAmount, // Final gross amount
            'notes' => null,
            'invoiceNumber' => $this->generateInvoiceNumber($booking),
            'invoiceDate' => $booking->invoice_date ? $booking->invoice_date->format('d.m.Y') : now()->format('d.m.Y'),
            'paymentQrCode' => $paymentQrCode,
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
        $invoiceNumber = $this->generateInvoiceNumber($booking);
        $eventSlug = \Illuminate\Support\Str::slug($booking->event->title);
        $filename = "Rechnung_{$invoiceNumber}_{$eventSlug}.pdf";

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
        // Use invoice number from booking if available, otherwise fallback to legacy format
        return $booking->invoice_number ?? ('INV-' . date('Y') . '-' . str_pad($booking->id, 6, '0', STR_PAD_LEFT));
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

