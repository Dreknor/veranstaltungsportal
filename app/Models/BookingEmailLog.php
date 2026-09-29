<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Protokolliert jede E-Mail, die im Zusammenhang mit einer Buchung versendet wurde.
 * Wird automatisch über den MessageSent-Listener befüllt.
 * @property \Illuminate\Support\Carbon|null $sent_at
 */
class BookingEmailLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'booking_id',
        'type',
        'subject',
        'recipient',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }

    /**
     * Lesbare Bezeichnungen der E-Mail-Typen für Veranstalter.
     */
    public const TYPE_LABELS = [
        'BookingConfirmation' => 'Buchungsbestätigung',
        'BookingPendingApproval' => 'Eingangsbestätigung (wartet auf Freigabe)',
        'PaymentConfirmed' => 'Zahlungsbestätigung mit Tickets/Zugangsdaten',
        'BookingCancellation' => 'Stornierungsbestätigung',
        'BookingRejected' => 'Ablehnung der Anmeldung',
        'EventReminderMail' => 'Erinnerung vor Veranstaltungsbeginn',
        'EventCancelledMail' => 'Absage der Veranstaltung',
        'EventUpdatedNotification' => 'Änderung der Veranstaltung',
        'PaymentStatusChangedNotification' => 'Zahlungsstatus (z. B. Erstattung)',
        'AttendeeTicketMail' => 'Persönliches Ticket an Teilnehmer:in',
        'EventFollowUpMail' => 'Nachbereitung (Bescheinigung & Feedback)',
        'AttendeeMessageMail' => 'Nachricht des Veranstalters',
    ];

    /**
     * @return BelongsTo<Booking, $this>
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function typeLabel(): string
    {
        return self::TYPE_LABELS[$this->type] ?? $this->type;
    }
}
