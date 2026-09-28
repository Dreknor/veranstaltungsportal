<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property 'pending'|'confirmed'|'cancelled'|'completed'|'pending_approval' $status
 * @property 'pending'|'paid'|'refunded'|'failed'|'extern' $payment_status
 */
class Booking extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'booking_number',
        'invoice_number',
        'invoice_date',
        'externally_invoiced',
        'externally_invoiced_at',
        'external_invoice_number',
        'event_id',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_organization',
        'billing_company',
        'billing_vat_id',
        'billing_address',
        'billing_postal_code',
        'billing_city',
        'billing_country',
        'email_verification_token',
        'email_verified_at',
        'subtotal',
        'discount',
        'total',
        'status',
        'payment_status',
        'release_tickets_before_payment',
        'follow_up_sent_at',
        'anonymized_at',
        'tickets_personalized',
        'tickets_personalized_at',
        'payment_method',
        'payment_transaction_id',
        'discount_code_id',
        'additional_data',
        'confirmed_at',
        'cancelled_at',
        'cancelled_by',
        'cancellation_reason',
        'reminders_sent',
        'certificate_generated_at',
        'certificate_path',
        'checked_in',
        'checked_in_at',
        'checked_in_by',
        'check_in_method',
        'check_in_notes',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'total' => 'decimal:2',
            'additional_data' => 'array',
            'email_verified_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'reminders_sent' => 'array',
            'release_tickets_before_payment' => 'boolean',
            'follow_up_sent_at' => 'datetime',
            'anonymized_at' => 'datetime',
            'certificate_generated_at' => 'datetime',
            'tickets_personalized' => 'boolean',
            'tickets_personalized_at' => 'datetime',
            'checked_in' => 'boolean',
            'checked_in_at' => 'datetime',
            'invoice_date' => 'datetime',
            'externally_invoiced' => 'boolean',
            'externally_invoiced_at' => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (!$booking->booking_number) {
                $booking->booking_number = 'BK-' . strtoupper(uniqid());
            }
        });
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function discountCode(): BelongsTo
    {
        return $this->belongsTo(DiscountCode::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BookingItem::class);
    }
    public function platformFee(): HasOne
    {
        return $this->hasOne(PlatformFee::class);
    }

    public function checkedInBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }

    public function review(): HasOne
    {
        return $this->hasOne(EventReview::class);
    }

    /**
     * Anzahl der Plätze – nutzt die bereits geladene Relation, um N+1-Abfragen in Listen zu vermeiden.
     */
    public function itemCount(): int
    {
        return $this->relationLoaded('items') ? $this->items->count() : $this->items()->count();
    }

    public function attendances(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(BookingItemAttendance::class, BookingItem::class);
    }

    public function emailLogs(): HasMany
    {
        return $this->hasMany(BookingEmailLog::class)->orderByDesc('sent_at')->orderByDesc('id');
    }

    public const STATUS_LABELS = [
        'pending' => 'Zahlung ausstehend',
        'pending_approval' => 'Wartet auf Freigabe',
        'confirmed' => 'Bestätigt',
        'completed' => 'Abgeschlossen',
        'cancelled' => 'Storniert',
    ];

    public const PAYMENT_STATUS_LABELS = [
        'pending' => 'Ausstehend',
        'paid' => 'Bezahlt',
        'extern' => 'Extern fakturiert',
        'refunded' => 'Erstattet',
        'failed' => 'Fehlgeschlagen',
    ];

    public function statusLabel(): string
    {
        // Kostenfreie oder bezahlte, aber noch nicht bestätigte Buchungen sind nicht "Zahlung ausstehend"
        if ($this->status === 'pending' && ($this->isPaymentComplete() || (float) $this->total == 0.0)) {
            return 'Ausstehend';
        }

        return self::STATUS_LABELS[$this->status] ?? ucfirst((string) $this->status);
    }

    public function paymentStatusLabel(): string
    {
        return self::PAYMENT_STATUS_LABELS[$this->payment_status] ?? ucfirst((string) $this->payment_status);
    }

    public function isFree(): bool
    {
        return (float) $this->total == 0.0;
    }

    /**
     * Aktive Buchungen belegen Plätze (alles außer storniert).
     */
    public function isActive(): bool
    {
        return in_array($this->status, ['pending', 'pending_approval', 'confirmed', 'completed']);
    }

    /**
     * Buchung ist vollständig abgeschlossen: bestätigt und bezahlt (bzw. kostenfrei/extern).
     * Erst dann werden Tickets und Online-Zugangsdaten freigegeben.
     */
    public function isReadyForParticipation(): bool
    {
        return in_array($this->status, ['confirmed', 'completed'])
            && ($this->isPaymentComplete() || $this->ticketsReleasedBeforePayment());
    }

    /**
     * Werden Tickets/Zugangsdaten schon vor Zahlung bzw. Rechnungsstellung freigegeben?
     *
     * Reihenfolge: Einstellung an der Buchung (Ausnahme durch den Veranstalter) vor der
     * Event-Einstellung "Tickets vor Rechnungsstellung versenden" (nur bei externer Fakturierung).
     * PayPal-Buchungen sind ausgenommen – dort erfolgt die Zahlung sofort.
     */
    public function ticketsReleasedBeforePayment(): bool
    {
        if ($this->isPaymentComplete() || $this->isFree() || $this->payment_method === 'paypal') {
            return false;
        }

        if ($this->release_tickets_before_payment !== null) {
            return (bool) $this->release_tickets_before_payment;
        }

        return (bool) $this->event?->releasesTicketsBeforeInvoice();
    }

    /**
     * Buchungen, die an der Veranstaltung teilnehmen dürfen (Tickets/Zugang freigegeben).
     */
    public function scopeReadyForParticipation($query)
    {
        return $query->whereIn('bookings.status', ['confirmed', 'completed'])
            ->where(function ($q) {
                $q->whereIn('bookings.payment_status', ['paid', 'extern'])
                    ->orWhere('bookings.release_tickets_before_payment', true)
                    ->orWhere(function ($q) {
                        $q->whereNull('bookings.release_tickets_before_payment')
                            ->where(fn ($q) => $q->whereNull('bookings.payment_method')->orWhere('bookings.payment_method', '!=', 'paypal'))
                            ->whereHas('event', fn ($e) => $e->where('tickets_before_invoice', true)
                                ->whereHas('organization', fn ($o) => $o->where('invoice_mode', 'external')));
                    });
            });
    }

    /**
     * Darf der Kunde die Online-Zugangsdaten (Link/Code) sehen?
     */
    public function canAccessOnlineContent(): bool
    {
        return $this->isReadyForParticipation()
            && $this->event
            && $this->event->requiresOnlineInfo();
    }

    /**
     * Gibt es für diese Buchung ein Ticket-PDF (Präsenz/Hybrid mit Ticketpflicht)?
     */
    public function hasTicketDocument(): bool
    {
        return $this->event
            && $this->event->requires_ticket
            && !$this->event->isOnline();
    }

    /**
     * Kann eine Online-Zahlung (PayPal) für diese Buchung (erneut) gestartet werden?
     */
    public function canRetryOnlinePayment(): bool
    {
        return $this->status === 'pending'
            && $this->payment_status === 'pending'
            && $this->payment_method === 'paypal'
            && !$this->isFree()
            && $this->event
            && !$this->event->is_cancelled
            && $this->event->organization?->hasPayPalConfigured()
            && !$this->event->organization?->hasExternalInvoicing();
    }

    /**
     * Schritte des Buchungsablaufs für die Fortschrittsanzeige (Kunde & Veranstalter).
     *
     * @return array<int, array{key: string, label: string, state: string, hint: ?string}>
     *               state: done | current | upcoming
     */
    public function progressSteps(): array
    {
        $event = $this->event;
        $steps = [];
        $blocked = false; // ab dem ersten offenen Schritt sind alle folgenden "upcoming"

        $push = function (string $key, string $label, bool $done, ?string $hint = null, bool $blocking = true) use (&$steps, &$blocked) {
            $state = $done && !$blocked ? 'done' : ($blocked || !$blocking ? 'upcoming' : 'current');
            if ($state === 'current') {
                $blocked = true;
            }
            $steps[] = compact('key', 'label', 'state', 'hint');
        };
        $invoiceLater = $this->ticketsReleasedBeforePayment();

        $push('booked', 'Gebucht', true, $this->created_at?->format('d.m.Y'));

        if ($this->status === 'pending_approval' || ($this->isFree() && $event && !$event->free_ticket_auto_confirm)) {
            $push('approval', 'Freigabe', in_array($this->status, ['confirmed', 'completed']),
                $this->status === 'pending_approval' ? 'wird vom Veranstalter geprüft' : $this->confirmed_at?->format('d.m.Y'));
        }

        if (!$this->isFree() && !$invoiceLater) {
            $push('payment', $this->payment_status === 'extern' || $event?->organization?->hasExternalInvoicing() ? 'Rechnung' : 'Zahlung',
                $this->isPaymentComplete(),
                $this->isPaymentComplete() ? $this->paymentStatusLabel() : ($this->payment_method === 'paypal' ? 'PayPal offen' : 'offen'));
        }

        if ($this->items->count() > 1) {
            $push('personalize', 'Teilnehmende', (bool) $this->tickets_personalized,
                $this->tickets_personalized ? 'eingetragen' : 'noch eintragen');
        }

        $accessLabel = match (true) {
            $event && $event->isOnline() => 'Zugangsdaten',
            $this->hasTicketDocument() && $event?->requiresOnlineInfo() => 'Tickets & Zugang',
            $this->hasTicketDocument() => 'Tickets',
            default => 'Bestätigung',
        };
        $push('access', $accessLabel, $this->canSendTickets(), $this->canSendTickets() ? 'per E-Mail erhalten' : null);

        if ($event) {
            $push('event', 'Veranstaltung', $event->end_date?->isPast() ?? false, $event->start_date->format('d.m.Y'));
        }

        if ($invoiceLater) {
            $push('payment', 'Rechnung', false, 'folgt separat', false);
        }

        return $steps;
    }

    /**
     * Link zur Buchungsübersicht für E-Mails.
     * Gäste erhalten einen persönlichen Link, der ohne erneute E-Mail-Abfrage Zugriff gewährt.
     */
    public function manageUrl(): string
    {
        if (!$this->user_id && $this->email_verification_token) {
            return route('bookings.verify-email-token', [$this->booking_number, $this->email_verification_token]);
        }

        return route('bookings.show', $this->booking_number);
    }

    public function reminderWasSent(string $key): bool
    {
        return isset(($this->reminders_sent ?? [])[$key]);
    }

    /**
     * Prüft ob der Zahlungsstatus als "bezahlt" gilt.
     * 'extern' wird intern wie 'paid' behandelt (externe Rechnungsstellung).
     */
    public function isPaymentComplete(): bool
    {
        return in_array($this->payment_status, ['paid', 'extern']);
    }

    /**
     * Check in this booking
     */
    public function checkIn(?User $checkedInBy = null, string $method = 'manual', ?string $notes = null): void
    {
        $this->update([
            'checked_in' => true,
            'checked_in_at' => now(),
            'checked_in_by' => $checkedInBy?->id,
            'check_in_method' => $method,
            'check_in_notes' => $notes,
        ]);
    }

    /**
     * Undo check-in
     */
    public function undoCheckIn(): void
    {
        $this->update([
            'checked_in' => false,
            'checked_in_at' => null,
            'checked_in_by' => null,
            'check_in_method' => null,
            'check_in_notes' => null,
        ]);
    }

    /**
     * Check if booking can be checked in
     */
    public function canCheckIn(): bool
    {
        // Must be confirmed and paid (or extern = externally invoiced)
        if ($this->status !== 'confirmed' || !$this->isReadyForParticipation()) {
            return false;
        }

        // Cannot check in if already checked in
        if ($this->checked_in) {
            return false;
        }

        // Event must not be in the future (allow check-in on event day)
        // Allow check-in up to 24 hours before the event for testing/early access
        if ($this->event && $this->event->start_date) {
            $eventStart = $this->event->start_date;
            $now = now();

            // Allow check-in if event is today or in the past, or within 24 hours
            if ($eventStart->isFuture() && !$eventStart->isToday() && $eventStart->diffInHours($now) > 24) {
                return false;
            }
        }

        return true;
    }


    public function getTotalAmountAttribute(): float
    {
        return $this->total;
    }

    public function getVerificationCodeAttribute(): string
    {
        // Generate a verification code from booking_number
        // Ensure we have at least 8 characters, pad if necessary
        $code = str_replace(['BK-', '-'], '', $this->booking_number);
        return strtoupper(substr($code, -8));
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePendingApproval($query)
    {
        return $query->where('status', 'pending_approval');
    }

    /**
     * Prüft ob die Buchung auf manuelle Bestätigung wartet
     */
    public function isPendingApproval(): bool
    {
        return $this->status === 'pending_approval';
    }

    /**
     * Check if this booking needs ticket personalization
     */
    public function needsPersonalization(): bool
    {
        // Only needs personalization if:
        // 1. Booking is confirmed (not pending_approval!)
        // 2. Payment is confirmed (paid)
        // 3. Has more than one ticket item
        // 4. Not yet personalized
        return $this->isReadyForParticipation()
            && $this->itemCount() > 1
            && !$this->tickets_personalized;
    }

    /**
     * Können Teilnehmende (noch) eingetragen bzw. geändert werden?
     * Möglich ab Buchung bis zum Veranstaltungsbeginn – unabhängig vom Zahlungsstand.
     */
    public function canBePersonalized(): bool
    {
        return in_array($this->status, ['pending', 'pending_approval', 'confirmed'])
            && $this->itemCount() > 1
            && $this->event
            && $this->event->start_date->isFuture();
    }

    /**
     * Check if all tickets are personalized
     */
    public function allTicketsPersonalized(): bool
    {
        // If only one ticket, it's automatically personalized with buyer's name
        if ($this->itemCount() <= 1) {
            return true;
        }

        // Check if all items have attendee names using collection filter
        $unpersonalizedCount = $this->items->filter(function ($item) {
            return empty($item->attendee_name);
        })->count();

        return $unpersonalizedCount === 0;
    }

    /**
     * Check if tickets can be sent (confirmed, paid and personalized if needed)
     */
    public function canSendTickets(): bool
    {
        return $this->isReadyForParticipation()
            && (!$this->needsPersonalization() || $this->tickets_personalized);
    }
}
