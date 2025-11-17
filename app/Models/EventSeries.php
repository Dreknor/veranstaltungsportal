<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class EventSeries extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'event_category_id',
        'recurrence_type',
        'recurrence_interval',
        'recurrence_days',
        'recurrence_count',
        'recurrence_end_date',
        'template_data',
        'is_active',
        'total_events',
    ];

    protected $casts = [
        'recurrence_interval' => 'integer',
        'recurrence_count' => 'integer',
        'recurrence_days' => 'array',
        'template_data' => 'array',
        'recurrence_end_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user that owns the series
     */
    public function user()
    {
        return $this->organization?->owners()->first()
            ?? $this->organization?->admins()->first()
            ?? $this->organization?->users()->first();
    }

    /**
     * Set the user ID attribute and assign the organization
     */
    public function setUserIdAttribute($value): void
    {
        $user = \App\Models\User::find($value);
        if ($user) {
            $org = $user->activeOrganizations()->first();
            if (!$org) {
                $org = \App\Models\Organization::factory()->create();
                $org->users()->attach($user->id, [
                    'role' => 'owner',
                    'is_active' => true,
                    'joined_at' => now(),
                ]);
            }
            $this->attributes['organization_id'] = $org->id;
        }
    }

    /**
     * Get the organization that owns the series
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the category
     */
    public function category()
    {
        return $this->belongsTo(EventCategory::class, 'event_category_id');
    }

    /**
     * Get all events in this series
     */
    public function events()
    {
        return $this->hasMany(Event::class, 'series_id')->orderBy('series_position');
    }

    /**
     * Get active events in series
     */
    public function activeEvents()
    {
        return $this->events()->where('is_published', true);
    }

    /**
     * Get upcoming events in series
     */
    public function upcomingEvents()
    {
        return $this->events()->where('start_date', '>', now());
    }

    /**
     * Generate event dates based on recurrence pattern
     */
    public function generateEventDates(Carbon $startDate, $duration = 60): array
    {
        $dates = [];
        $currentDate = $startDate->copy();
        $count = 0;
        $maxIterations = 1000; // Safety limit

        // Determine end condition
        $hasEndDate = $this->recurrence_end_date !== null;
        $hasCount = $this->recurrence_count !== null;

        while ($count < $maxIterations) {
            // Check if we should stop
            if ($hasEndDate && $currentDate->gt($this->recurrence_end_date)) {
                break;
            }

            if ($hasCount && count($dates) >= $this->recurrence_count) {
                break;
            }

            // Add current date
            $endDate = $currentDate->copy()->addMinutes($duration);
            $dates[] = [
                'start_date' => $currentDate->copy(),
                'end_date' => $endDate,
            ];

            // Calculate next occurrence
            $currentDate = $this->getNextOccurrence($currentDate);

            if (!$currentDate) {
                break;
            }

            $count++;
        }

        return $dates;
    }

    /**
     * Get next occurrence date based on recurrence type
     */
    protected function getNextOccurrence(Carbon $currentDate): ?Carbon
    {
        switch ($this->recurrence_type) {
            case 'daily':
                return $currentDate->copy()->addDays($this->recurrence_interval);

            case 'weekly':
                if (empty($this->recurrence_days)) {
                    return $currentDate->copy()->addWeeks($this->recurrence_interval);
                }

                // Find next day of week in recurrence_days
                return $this->getNextWeeklyOccurrence($currentDate);

            case 'monthly':
                return $currentDate->copy()->addMonths($this->recurrence_interval);

            case 'yearly':
                return $currentDate->copy()->addYears($this->recurrence_interval);

            default:
                return null;
        }
    }

    /**
     * Get next weekly occurrence based on selected days
     */
    protected function getNextWeeklyOccurrence(Carbon $currentDate): ?Carbon
    {
        $days = $this->recurrence_days;
        sort($days); // Ensure sorted (0=Sunday, 1=Monday, ..., 6=Saturday)

        $nextDate = $currentDate->copy()->addDay();
        $currentDayOfWeek = $nextDate->dayOfWeek;

        // Find next day in current week
        foreach ($days as $day) {
            if ($day > $currentDayOfWeek) {
                return $nextDate->copy()->next($this->getDayName($day));
            }
        }

        // Go to next week and use first day
        $weeksToAdd = $this->recurrence_interval;
        $nextDate->addWeeks($weeksToAdd);
        return $nextDate->copy()->next($this->getDayName($days[0]));
    }

    /**
     * Get day name from number
     */
    protected function getDayName(int $day): string
    {
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        return $days[$day] ?? 'Monday';
    }

    /**
     * Update total events count
     */
    public function updateTotalEvents(): void
    {
        $this->update(['total_events' => $this->events()->count()]);
    }

    /**
     * Get recurrence description in human-readable format
     */
    public function getRecurrenceDescription(): string
    {
        $count = $this->total_events ?? $this->recurrence_count ?? 0;
        $termineText = $count > 0 ? " ({$count} Termine)" : '';

        switch ($this->recurrence_type) {
            case 'daily':
                if ($this->recurrence_interval == 1) {
                    return 'Täglich' . $termineText;
                }
                return "Alle {$this->recurrence_interval} Tage" . $termineText;

            case 'weekly':
                if (empty($this->recurrence_days)) {
                    if ($this->recurrence_interval == 1) {
                        return 'Wöchentlich' . $termineText;
                    }
                    return "Alle {$this->recurrence_interval} Wochen" . $termineText;
                }

                $dayNames = ['So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa'];
                $selectedDays = array_map(fn($d) => $dayNames[$d], $this->recurrence_days);

                if ($this->recurrence_interval == 1) {
                    return 'Jede Woche: ' . implode(', ', $selectedDays) . $termineText;
                }
                return "Alle {$this->recurrence_interval} Wochen: " . implode(', ', $selectedDays) . $termineText;

            case 'monthly':
                if ($this->recurrence_interval == 1) {
                    return 'Monatlich' . $termineText;
                }
                return "Alle {$this->recurrence_interval} Monate" . $termineText;

            case 'yearly':
                if ($this->recurrence_interval == 1) {
                    return 'Jährlich' . $termineText;
                }
                return "Alle {$this->recurrence_interval} Jahre" . $termineText;

            default:
                return 'Individuelle Termine' . $termineText;
        }
    }
}
