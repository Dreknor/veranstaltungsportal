<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'log';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'message',
        'channel',
        'level',
        'level_name',
        'unix_time',
        'datetime',
        'context',
        'extra',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'context' => 'array',
        'extra' => 'array',
        'unix_time' => 'integer',
        'level' => 'integer',
    ];

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = true;

    /**
     * Check if this log level is critical
     */
    public function isCritical(): bool
    {
        return in_array($this->level_name, ['ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY']);
    }

    /**
     * Get formatted datetime
     */
    public function getFormattedDatetimeAttribute(): string
    {
        if (!$this->datetime) {
            return '-';
        }

        try {
            return \Carbon\Carbon::parse($this->datetime)->format('d.m.Y H:i:s');
        } catch (\Exception $e) {
            // Bereinige fehlerhaftes Format
            $cleanDate = preg_replace('/:\d{4}$/', '', $this->datetime);
            try {
                return \Carbon\Carbon::parse($cleanDate)->format('d.m.Y H:i:s');
            } catch (\Exception $e2) {
                return $this->datetime;
            }
        }
    }
}

