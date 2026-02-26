<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegalPage extends Model
{
    protected $fillable = [
        'type',
        'title',
        'content',
        'last_updated_at',
        'updated_by',
    ];

    protected $casts = [
        'last_updated_at' => 'datetime',
    ];

    public const TYPES = [
        'impressum'  => 'Impressum',
        'datenschutz' => 'Datenschutzerklärung',
        'agb'        => 'AGB',
    ];

    /**
     * The admin who last updated this page.
     */
    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get a legal page by type or return null.
     */
    public static function getByType(string $type): ?self
    {
        return static::where('type', $type)->first();
    }
}

