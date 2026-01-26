<?php

namespace App\Http\Controllers;

use App\Models\Booking;
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

        try {
            // Initialize PayPal service with organization credentials
            $paypalService = new PayPalService($booking->event->organization);

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

            // Update booking in transaction to prevent race conditions with webhook
            DB::transaction(function () use ($booking, $transactionId, $captureResponse) {
                // Check if already processed (by webhook)
                if ($booking->payment_status === 'paid') {
                    Log::info('Booking already marked as paid (webhook was faster)', [
                        'booking_number' => $booking->booking_number,
                    ]);
                    return;
                }

                // Update booking status
                $booking->update([
                    'payment_status' => 'paid',
                    'status' => 'confirmed',
                    'confirmed_at' => now(),
                    'payment_transaction_id' => $transactionId,
                ]);

                Log::info('Booking marked as paid via PayPal redirect', [
                    'booking_number' => $booking->booking_number,
                    'transaction_id' => $transactionId,
                ]);

                // Send confirmation email
                $this->sendPaymentConfirmation($booking);
            });

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
            $paypalService = new PayPalService($booking->event->organization);

            if ($booking->event->organization->paypal_mode === 'live') {
                if (!$paypalService->verifyWebhook($headers, $rawBody)) {
                    Log::error('PayPal webhook signature verification failed');
                    return response()->json(['error' => 'Unauthorized'], 401);
                }
            }

            // Update booking in atomic transaction (prevent race condition with redirect)
            DB::transaction(function () use ($booking, $captureId) {
                // Check if already processed (idempotency)
                if ($booking->payment_status === 'paid') {
                    Log::info('Booking already marked as paid (idempotent)', [
                        'booking_number' => $booking->booking_number,
                    ]);
                    return;
                }

                // Update booking status
                $booking->update([
                    'payment_status' => 'paid',
                    'status' => 'confirmed',
                    'confirmed_at' => now(),
                    'payment_transaction_id' => $captureId,
                ]);

                Log::info('Booking marked as paid via PayPal webhook', [
                    'booking_number' => $booking->booking_number,
                    'transaction_id' => $captureId,
                ]);

                // Send confirmation email
                $this->sendPaymentConfirmation($booking);
            });

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
     * Send payment confirmation email
     */
    protected function sendPaymentConfirmation(Booking $booking)
    {
        try {
            $booking->load(['event', 'items.ticketType']);

            // Send confirmation email with tickets
            Mail::to($booking->customer_email)
                ->send(new \App\Mail\PaymentConfirmed($booking));

            // Notify organizer
            if ($booking->event->user) {
                $notificationPreferences = $booking->event->user->notification_preferences ?? [];
                if (is_array($notificationPreferences) && ($notificationPreferences['booking_notifications'] ?? true)) {
                    $booking->event->user->notify(new \App\Notifications\BookingConfirmedNotification($booking));
                }
            }

        } catch (\Exception $e) {
            Log::error('Failed to send payment confirmation email', [
                'booking_number' => $booking->booking_number,
                'message' => $e->getMessage(),
            ]);
            // Don't throw - payment is already confirmed
        }
    }
}
