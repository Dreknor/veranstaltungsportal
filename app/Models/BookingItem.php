<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'booking_id',
        'ticket_type_id',
        'ticket_number',
        'attendee_name',
        'attendee_email',
        'price',
        'quantity',
        'custom_fields',
        'checked_in',
        'checked_in_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'custom_fields' => 'array',
            'checked_in' => 'boolean',
            'checked_in_at' => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            if (!$item->ticket_number) {
                $item->ticket_number = 'TK-' . strtoupper(uniqid());
            }
        });
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function ticketType(): BelongsTo
    {
        return $this->belongsTo(TicketType::class);
    }
}
