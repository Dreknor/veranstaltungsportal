<?php

namespace Database\Factories;

use App\Models\EventSeries;
use App\Models\Organization;
use App\Models\EventCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventSeriesFactory extends Factory
{
    protected $model = EventSeries::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'event_category_id' => EventCategory::factory(),
            'recurrence_type' => $this->faker->randomElement(['daily', 'weekly', 'monthly']),
            'recurrence_interval' => 1,
            'recurrence_days' => null,
            'recurrence_count' => $this->faker->numberBetween(5, 20),
            'recurrence_end_date' => $this->faker->dateTimeBetween('+1 month', '+6 months'),
            'template_data' => [],
            'is_active' => true,
            'total_events' => 0,
        ];
    }

    public function configure()
    {
        return $this
            ->afterMaking(function (EventSeries $series, array $attributes) {
                if (array_key_exists('user_id', $attributes) && empty($attributes['organization_id'])) {
                    $user = \App\Models\User::find($attributes['user_id']);
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
                        $series->organization_id = $org->id;
                    }
                }
            })
            ->afterCreating(function (EventSeries $series, array $attributes) {
                if (!$series->organization_id && array_key_exists('user_id', $attributes)) {
                    $user = \App\Models\User::find($attributes['user_id']);
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
                        $series->organization_id = $org->id;
                        $series->save();
                    }
                }
            });
    }
}
