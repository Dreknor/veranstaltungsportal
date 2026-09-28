<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\BookingWorkflowService;
use App\Services\PayPalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PayPalController extends Controller
{
    /**
     * Handle successful PayPal return
     */
    public function success(Request $request, $bookingNumber)
    {
        $booking = Booking::where('booking_number', $bookingNumber)
            ->with(['event.organization', 'items.ticketType'])
            ->firstOrFail();

        $token = $request->query('token');

        if (!$token) {
            Log::error('PayPal success callback: No token provided', [
                'booking_number' => $bookingNumber,
            ]);

            return redirect()->route('bookings.show', $bookingNumber)
                ->with('error', 'Fehler bei der PayPal-Zahlung. Bitte kontaktieren Sie den Support.');
        }

        // Bereits bezahlt (z. B. Webhook war schneller) → nichts erneut abbuchen
        if ($booking->isPaymentComplete()) {
            return redirect()->route('bookings.show', $bookingNumber)
                ->with('success', 'Ihre Zahlung ist bereits eingegangen. Tickets bzw. Zugangsdaten wurden per E-Mail versendet.');
        }

        // Stornierte Buchungen (z. B. automatisch freigegeben) dürfen nicht mehr bezahlt werden
        if ($booking->status === 'cancelled') {
            return redirect()->route('bookings.show', $bookingNumber)
                ->with('error', 'Diese Buchung wurde bereits storniert. Es wurde keine Zahlung ausgeführt.');
        }

        try {
            $paypalService = $this->paypalFor($booking);

            if (!$paypalService->isAvailable()) {
                throw new \Exception('PayPal ist für diesen Veranstalter nicht konfiguriert.');
            }

            // Capture the payment
            $captureResponse = $paypalService->captureOrder($token);

            if (!$captureResponse || !isset($captureResponse['status'])) {
                throw new \Exception('Invalid PayPal capture response');
            }

            // Check if capture was successful
            if ($captureResponse['status'] !== 'COMPLETED') {
                Log::warning('PayPal capture not completed', [
                    'booking_number' => $bookingNumber,
                    'status' => $captureResponse['status'],
                    'response' => $captureResponse,
                ]);

                return redirect()->route('bookings.show', $bookingNumber)
                    ->with('warning', 'Zahlung noch nicht abgeschlossen. Status: ' . $captureResponse['status']);
            }

            // Extract transaction ID
            $transactionId = $captureResponse['purchase_units'][0]['payments']['captures'][0]['id'] ?? null;

            $this->markPaid($booking, $transactionId, 'redirect');

            return redirect()->route('bookings.show', $bookingNumber)
                ->with('success', 'Zahlung erfolgreich! Ihre Tickets wurden per E-Mail versendet.');

        } catch (\Exception $e) {
            Log::error('PayPal success callback error', [
                'booking_number' => $bookingNumber,
                'token' => $token,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('bookings.show', $bookingNumber)
                ->with('error', 'Fehler bei der Zahlungsbestätigung. Bitte kontaktieren Sie den Support mit Ihrer Buchungsnummer.');
        }
    }

    /**
     * Handle PayPal cancellation
     */
    public function cancel(Request $request, $bookingNumber)
    {
        $booking = Booking::where('booking_number', $bookingNumber)->firstOrFail();

        Log::info('PayPal payment cancelled by user', [
            'booking_number' => $bookingNumber,
        ]);

        // Optionally update booking status to cancelled
        // Or just redirect back to checkout with a message
        // For now, we keep the booking as pending

        return redirect()->route('bookings.show', $bookingNumber)
            ->with('warning', 'Zahlung abgebrochen. Sie können die Zahlung jederzeit fortsetzen oder eine andere Zahlungsmethode wählen.');
    }

    /**
     * Handle PayPal webhook
     */
    public function webhook(Request $request)
    {
        // Get raw body for signature verification
        $rawBody = $request->getContent();
        $headers = [
            'paypal-auth-algo' => $request->header('paypal-auth-algo'),
            'paypal-cert-url' => $request->header('paypal-cert-url'),
            'paypal-transmission-id' => $request->header('paypal-transmission-id'),
            'paypal-transmission-sig' => $request->header('paypal-transmission-sig'),
            'paypal-transmission-time' => $request->header('paypal-transmission-time'),
        ];

        Log::info('PayPal webhook received', [
            'event_type' => $request->input('event_type'),
        ]);

        $eventType = $request->input('event_type');

        // Handle different webhook events
        switch ($eventType) {
            case 'CHECKOUT.ORDER.APPROVED':
                // Order approved but not yet captured
                // We don't need to do anything here as we capture immediately
                break;

            case 'PAYMENT.CAPTURE.COMPLETED':
                return $this->handlePaymentCaptured($request, $headers, $rawBody);

            case 'PAYMENT.CAPTURE.DENIED':
            case 'PAYMENT.CAPTURE.DECLINED':
                return $this->handlePaymentFailed($request);

            default:
                Log::info('Unhandled PayPal webhook event', [
                    'event_type' => $eventType,
                ]);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Handle PAYMENT.CAPTURE.COMPLETED webhook
     */
    protected function handlePaymentCaptured(Request $request, array $headers, string $rawBody)
    {
        try {
            $resource = $request->input('resource');
            $captureId = $resource['id'] ?? null;

            // Extract booking number from reference_id
            $referenceId = $resource['supplementary_data']['related_ids']['order_id'] ?? null;

            if (!$referenceId) {
                Log::error('PayPal webhook: No order ID in capture', [
                    'resource' => $resource,
                ]);
                return response()->json(['error' => 'No order ID'], 400);
            }

            // We need to get the booking first to get the organization
            // Try to extract booking number from custom_id or search by transaction
            $bookingNumber = null;

            // Try to find booking by stored paypal_order_id in additional_data
            $booking = Booking::whereJsonContains('additional_data->paypal_order_id', $referenceId)->first();

            if (!$booking) {
                Log::error('PayPal webhook: Booking not found by order ID', [
                    'order_id' => $referenceId,
                ]);
                return response()->json(['error' => 'Booking not found'], 404);
            }

            // Load organization for PayPal verification
            $booking->load('event.organization');

            // Verify webhook signature (CRITICAL FOR SECURITY)
            // Signature verification is enforced in live mode.
            // In sandbox mode it is also enforced when a webhook ID is configured,
            // so local development without a configured webhook ID is still possible.
            $paypalService = $this->paypalFor($booking);
            $webhookId = $booking->event->organization->paypal_webhook_id ?? null;

            if (!empty($webhookId)) {
                if (!$paypalService->verifyWebhook($headers, $rawBody)) {
                    Log::error('PayPal webhook signature verification failed', [
                        'mode' => $booking->event->organization->paypal_mode,
                        'booking_number' => $booking->booking_number,
                    ]);
                    return response()->json(['error' => 'Unauthorized'], 401);
                }
            } else {
                // No webhook ID configured – only tolerated in sandbox/testing environments
                if ($booking->event->organization->paypal_mode === 'live') {
                    Log::critical('PayPal webhook: Live mode without webhook ID configured – rejecting request', [
                        'organization_id' => $booking->event->organization->id,
                    ]);
                    return response()->json(['error' => 'Webhook not properly configured'], 500);
                }

                Log::warning('PayPal webhook: Signature verification skipped (no webhook ID configured, sandbox mode)', [
                    'organization_id' => $booking->event->organization->id,
                ]);
            }

            $this->markPaid($booking, $captureId, 'webhook');

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('PayPal webhook processing error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Processing error'], 500);
        }
    }

    /**
     * Handle payment failed webhook
     */
    protected function handlePaymentFailed(Request $request)
    {
        try {
            $resource = $request->input('resource');

            Log::warning('PayPal payment failed', [
                'resource' => $resource,
            ]);

            // You could update booking status to 'failed' if needed
            // For now, we just log it

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('PayPal failed payment webhook error', [
                'message' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Processing error'], 500);
        }
    }

    /**
     * Zahlung idempotent verbuchen (Redirect und Webhook können gleichzeitig eintreffen)
     * und anschließend genau einmal Tickets/Zugangsdaten versenden.
     */
    protected function markPaid(Booking $booking, ?string $transactionId, string $source): void
    {
        $processed = DB::transaction(function () use ($booking, $transactionId) {
            $locked = Booking::whereKey($booking->id)->lockForUpdate()->first();

            if (!$locked || $locked->isPaymentComplete()) {
                return false;
            }

            $locked->update([
                'payment_status' => 'paid',
                'status' => $locked->status === 'pending' ? 'confirmed' : $locked->status,
                'confirmed_at' => $locked->confirmed_at ?? now(),
                'payment_transaction_id' => $transactionId,
            ]);

            return true;
        });

        if (!$processed) {
            Log::info('PayPal: Zahlung war bereits verbucht (idempotent)', [
                'booking_number' => $booking->booking_number,
                'source' => $source,
            ]);
            return;
        }

        Log::info('PayPal: Zahlung verbucht', [
            'booking_number' => $booking->booking_number,
            'transaction_id' => $transactionId,
            'source' => $source,
        ]);

        $workflow = app(BookingWorkflowService::class);
        $workflow->deliverTickets($booking);
        $workflow->notifyOrganizers($booking, new \App\Notifications\NewBookingNotification($booking));
    }

    /**
     * PayPal-Instanz (im Test über den Container austauschbar).
     */
    protected function paypalFor(Booking $booking): PayPalService
    {
        return app()->bound(PayPalService::class)
            ? app(PayPalService::class)
            : new PayPalService($booking->event->organization);
    }
}
