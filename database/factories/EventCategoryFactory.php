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

    {
        $title = $this->faker->sentence(3);
        $startDate = $this->faker->dateTimeBetween('+1 week', '+3 months');
        $endDate = (clone $startDate)->modify('+' . $this->faker->numberBetween(2, 8) . ' hours');

        return [
            'user_id' => User::factory(),
            'event_category_id' => EventCategory::inRandomOrder()->first()?->id ?? EventCategory::factory(),
            'title' => rtrim($title, '.'),
            'slug' => Str::slug($title) . '-' . Str::random(6),
            'description' => $this->faker->paragraphs(3, true),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'venue_name' => $this->faker->company() . ' Arena',
            'venue_address' => $this->faker->streetAddress(),
            'venue_city' => $this->faker->city(),
            'venue_postal_code' => $this->faker->postcode(),
            'venue_country' => 'Deutschland',
            'venue_latitude' => $this->faker->latitude(47, 55),
            'venue_longitude' => $this->faker->longitude(5, 15),
            'directions' => $this->faker->optional()->sentence(),
            'featured_image' => null,
            'video_url' => $this->faker->optional(0.3)->url(),
            'livestream_url' => $this->faker->optional(0.2)->url(),
            'price_from' => $this->faker->randomFloat(2, 0, 100),
            'max_attendees' => $this->faker->optional(0.7)->numberBetween(50, 1000),
            'is_published' => $this->faker->boolean(80),
            'is_featured' => $this->faker->boolean(20),
            'is_private' => $this->faker->boolean(10),
            'access_code' => null,
            'organizer_info' => $this->faker->optional()->paragraph(),
            'organizer_email' => $this->faker->optional()->email(),
            'organizer_phone' => $this->faker->optional()->phoneNumber(),
            'organizer_website' => $this->faker->optional()->url(),
            'average_rating' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
            'is_published' => true,
        ]);
    }

    public function private(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_private' => true,
            'access_code' => Str::random(8),
        ]);
    }
}

