<?php

namespace Database\Factories;

use App\Models\Badge;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Badge>
 */
class BadgeFactory extends Factory
{
    protected $model = Badge::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(2, true);
        $types = ['attendance', 'achievement', 'special'];
        $colors = ['#10B981', '#3B82F6', '#8B5CF6', '#F59E0B', '#EC4899', '#14B8A6', '#EF4444'];

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name) . '-' . Str::random(5),
            'description' => fake()->sentence(),
            'icon' => null,
            'color' => fake()->randomElement($colors),
            'type' => fake()->randomElement($types),
            'requirements' => $this->generateRequirements(),
            'points' => fake()->numberBetween(5, 100),
            'is_active' => true,
        ];
    }

    /**
     * Generate random requirements
     */
    protected function generateRequirements(): array
    {
        $possibleRequirements = [
            'bookings_count',
            'events_attended',
            'events_organized',
            'reviews_written',
            'total_hours_attended',
            'categories_explored',
            'early_bird_bookings',
        ];

        $requirementKey = fake()->randomElement($possibleRequirements);

        $value = match($requirementKey) {
            'bookings_count', 'events_attended' => fake()->numberBetween(1, 25),
            'events_organized' => fake()->numberBetween(1, 10),
            'reviews_written' => fake()->numberBetween(1, 20),
            'total_hours_attended' => fake()->numberBetween(10, 200),
            'categories_explored' => fake()->numberBetween(3, 10),
            'early_bird_bookings' => fake()->numberBetween(5, 15),
            default => 1,
        };

        return [$requirementKey => $value];
    }

    /**
     * Indicate the badge is for attendance
     */
    public function attendance(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'attendance',
            'requirements' => ['events_attended' => fake()->numberBetween(1, 25)],
        ]);
    }

    /**
     * Indicate the badge is for achievement
     */
    public function achievement(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'achievement',
        ]);
    }

    /**
     * Indicate the badge is special
     */
    public function special(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'special',
            'requirements' => [],
        ]);
    }

    /**
     * Indicate the badge is inactive
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}

