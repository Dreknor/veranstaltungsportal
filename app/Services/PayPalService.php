<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Organization;
use Illuminate\Support\Facades\Log;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PayPalService
{
    protected ?PayPalClient $provider = null;
    protected ?Organization $organization = null;

    /**
     * Initialize PayPal service with organization credentials
     *
     * @param Organization|null $organization
     */
    public function __construct(?Organization $organization = null)
    {
        if ($organization && $organization->hasPayPalConfigured()) {
            $this->organization = $organization;
            $this->initializeProvider($organization);
        }
    }

    /**
     * Initialize PayPal provider with organization's credentials
     */
    protected function initializeProvider(Organization $organization): void
    {
        try {
            $credentials = $organization->getPayPalCredentials();
            if (!$credentials) {
                Log::warning('PayPal credentials not available', [
                    'organization_id' => $organization->id,
                ]);
                return;
            }

            // Build config array based on mode
            $mode = $credentials['mode'] ?? 'sandbox';

            // WICHTIG: Das SDK erwartet die Credentials immer im entsprechenden Mode-Array
            $config = [
                'mode' => $mode,
                'sandbox' => [
                    'client_id' => $mode === 'sandbox' ? $credentials['client_id'] : '',
                    'client_secret' => $mode === 'sandbox' ? $credentials['client_secret'] : '',
                    'app_id' => 'APP-80W284485P519543T',
                ],
                'live' => [
                    'client_id' => $mode === 'live' ? $credentials['client_id'] : '',
                    'client_secret' => $mode === 'live' ? $credentials['client_secret'] : '',
                    'app_id' => '',
                ],
                'payment_action' => 'CAPTURE',
                'currency' => config('paypal.currency', 'EUR'),
                'notify_url' => '',
                'locale' => config('paypal.locale', 'de_DE'),
                'validate_ssl' => config('paypal.validate_ssl', true),
            ];

            Log::info('PayPal config prepared', [
                'organization_id' => $organization->id,
                'mode' => $mode,
                'client_id_length' => strlen($config[$mode]['client_id']),
                'client_secret_length' => strlen($config[$mode]['client_secret']),
            ]);

            // Erstelle den Provider mit der Konfiguration
            $this->provider = new PayPalClient($config);

            // Versuche Access Token zu erhalten
            $token = $this->provider->getAccessToken();

            if (!$token || !isset($token['access_token'])) {
                throw new \Exception('Failed to obtain PayPal access token');
            }

            Log::info('PayPal provider initialized successfully', [
                'organization_id' => $organization->id,
                'mode' => $mode,
            ]);
        } catch (\Exception $e) {
            Log::error('PayPal initialization failed', [
                'organization_id' => $organization->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Setze provider auf null, damit isAvailable() false zurückgibt
            $this->provider = null;
        }
    }

    /**
     * Check if PayPal is available
     */
    public function isAvailable(): bool
    {
        return $this->provider !== null && $this->organization !== null;
    }

    /**
     * Create PayPal order from booking
     *
     * @param Booking $booking
     * @return array|null PayPal order response or null on error
     */
    public function createOrder(Booking $booking): ?array
    {
        if (!$this->isAvailable()) {
            Log::error('PayPal not available', [
                'booking_number' => $booking->booking_number,
            ]);
            return null;
        }

        try {
            // Calculate amount from database - NEVER trust frontend
            $amount = $this->calculateBookingAmount($booking);

            $data = [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'reference_id' => $booking->booking_number,
                        'amount' => [
                            'currency_code' => config('paypal.currency', 'EUR'),
                            'value' => number_format($amount, 2, '.', ''),
                            'breakdown' => [
                                'item_total' => [
                                    'currency_code' => config('paypal.currency', 'EUR'),
                                    'value' => number_format($booking->subtotal, 2, '.', ''),
                                ],
                                'discount' => [
                                    'currency_code' => config('paypal.currency', 'EUR'),
                                    'value' => number_format($booking->discount, 2, '.', ''),
                                ],
                            ],
                        ],
                        'description' => $this->getOrderDescription($booking),
                        'items' => $this->getOrderItems($booking),
                    ],
                ],
                'application_context' => [
                    'cancel_url' => route('paypal.cancel', ['booking' => $booking->booking_number]),
                    'return_url' => route('paypal.success', ['booking' => $booking->booking_number]),
                    'brand_name' => config('app.name'),
                    'locale' => config('paypal.locale', 'de-DE'),
                    'landing_page' => 'BILLING',
                    'shipping_preference' => 'NO_SHIPPING',
                    'user_action' => 'PAY_NOW',
                ],
            ];

            Log::info('Creating PayPal order', [
                'booking_number' => $booking->booking_number,
                'amount' => $amount,
            ]);

            $response = $this->provider->createOrder($data);

            if (isset($response['error'])) {
                Log::error('PayPal createOrder error', [
                    'booking_number' => $booking->booking_number,
                    'error' => $response,
                ]);
                return null;
            }

            Log::info('PayPal order created successfully', [
                'booking_number' => $booking->booking_number,
                'paypal_order_id' => $response['id'] ?? null,
            ]);

            return $response;
        } catch (\Exception $e) {
            Log::error('PayPal createOrder exception', [
                'booking_number' => $booking->booking_number,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Capture PayPal order payment
     *
     * @param string $orderId PayPal order ID
     * @return array|null Capture response or null on error
     */
    public function captureOrder(string $orderId): ?array
    {
        if (!$this->isAvailable()) {
            Log::error('PayPal not available for capture');
            return null;
        }

        try {
            Log::info('Capturing PayPal order', ['order_id' => $orderId]);

            $response = $this->provider->capturePaymentOrder($orderId);

            if (isset($response['error'])) {
                Log::error('PayPal captureOrder error', [
                    'order_id' => $orderId,
                    'error' => $response,
                ]);
                return null;
            }

            Log::info('PayPal order captured successfully', [
                'order_id' => $orderId,
                'status' => $response['status'] ?? null,
            ]);

            return $response;
        } catch (\Exception $e) {
            Log::error('PayPal captureOrder exception', [
                'order_id' => $orderId,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Verify webhook signature
     *
     * @param array $headers Request headers
     * @param string $body Raw request body
     * @return bool
     */
    public function verifyWebhook(array $headers, string $body): bool
    {
        if (!$this->isAvailable()) {
            Log::warning('PayPal not available for webhook verification');
            return false;
        }

        try {
            // Get webhook ID from organization
            $webhookId = $this->organization->paypal_webhook_id;

            if (empty($webhookId)) {
                Log::warning('PayPal webhook ID not configured', [
                    'organization_id' => $this->organization->id,
                ]);
                return false;
            }

            $signatureVerification = [
                'auth_algo' => $headers['paypal-auth-algo'] ?? '',
                'cert_url' => $headers['paypal-cert-url'] ?? '',
                'transmission_id' => $headers['paypal-transmission-id'] ?? '',
                'transmission_sig' => $headers['paypal-transmission-sig'] ?? '',
                'transmission_time' => $headers['paypal-transmission-time'] ?? '',
                'webhook_id' => $webhookId,
                'webhook_event' => json_decode($body, true),
            ];

            $response = $this->provider->verifyWebHook($signatureVerification);

            $verified = isset($response['verification_status'])
                && $response['verification_status'] === 'SUCCESS';

            if (!$verified) {
                Log::warning('PayPal webhook verification failed', [
                    'response' => $response,
                ]);
            }

            return $verified;
        } catch (\Exception $e) {
            Log::error('PayPal webhook verification exception', [
                'message' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Calculate booking amount from database
     *
     * @param Booking $booking
     * @return float
     */
    protected function calculateBookingAmount(Booking $booking): float
    {
        // Reload booking with items to ensure fresh data
        $booking->load('items.ticketType');

        $subtotal = 0;

        foreach ($booking->items as $item) {
            // Use price from database, not from request
            $subtotal += $item->price * $item->quantity;
        }

        // Apply discount if applicable
        $discount = $booking->discount ?? 0;
        $total = $subtotal - $discount;

        return max(0, $total);
    }

    /**
     * Get order description for PayPal
     *
     * @param Booking $booking
     * @return string
     */
    protected function getOrderDescription(Booking $booking): string
    {
        $booking->load('event');

        return sprintf(
            'Buchung %s - %s',
            $booking->booking_number,
            $booking->event->title ?? 'Veranstaltung'
        );
    }

    /**
     * Get order items for PayPal
     *
     * @param Booking $booking
     * @return array
     */
    protected function getOrderItems(Booking $booking): array
    {
        $booking->load('items.ticketType');

        $items = [];

        foreach ($booking->items as $item) {
            $items[] = [
                'name' => $item->ticketType->name ?? 'Ticket',
                'description' => $item->ticketType->description ?? '',
                'quantity' => $item->quantity,
                'unit_amount' => [
                    'currency_code' => config('paypal.currency', 'EUR'),
                    'value' => number_format($item->price, 2, '.', ''),
                ],
            ];
        }

        return $items;
    }

    /**
     * Get PayPal order details
     *
     * @param string $orderId
     * @return array|null
     */
    public function getOrderDetails(string $orderId): ?array
    {
        try {
            $response = $this->provider->showOrderDetails($orderId);

            if (isset($response['error'])) {
                Log::error('PayPal getOrderDetails error', [
                    'order_id' => $orderId,
                    'error' => $response,
                ]);
                return null;
            }

            return $response;
        } catch (\Exception $e) {
            Log::error('PayPal getOrderDetails exception', [
                'order_id' => $orderId,
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
