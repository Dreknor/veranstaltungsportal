<?php

namespace Database\Factories;

use App\Models\EventCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EventCategoryFactory extends Factory
{
    protected $model = EventCategory::class;

    public function definition(): array
    {
        $categories = [
            'Pädagogik & Didaktik',
            'Digitales Lehren',
            'Schulentwicklung',
            'Seelsorge',
            'Inklusion',
            'Fachfortbildung',
            'Persönlichkeitsentwicklung',
        ];

        $name = $this->faker->randomElement($categories);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->optional()->sentence(),
            'icon' => $this->faker->randomElement(['academic-cap', 'book-open', 'users', 'heart', 'sparkles', 'shield-check']),
            'color' => $this->faker->randomElement(['#2563eb', '#059669', '#7c3aed', '#dc2626', '#0891b2', '#ea580c']),
            'is_active' => true,
        ];
    }
}


