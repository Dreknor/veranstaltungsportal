<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        // Bildungsrelevante Fortbildungstitel
        $titlePrefixes = [
            'Workshop:',
            'Fortbildung:',
            'Seminar:',
            'Schulung:',
            'Impulsvortrag:',
            'Fachtag:',
            'Kollegiale Beratung:',
        ];

        $topics = [
            'Digitale Medien im Unterricht',
            'Inklusive Pädagogik in der Praxis',
            'Konfliktlösung im Schulalltag',
            'Neue Methoden für den Deutschunterricht',
            'Stressmanagement für Lehrkräfte',
            'Feedback-Kultur entwickeln',
            'Projektarbeit im Unterricht',
            'Differenzierung im Klassenzimmer',
            'Schulseelsorge und spirituelle Begleitung',
            'Elterngespräche professionell führen',
            'Soziales Lernen fördern',
            'Digitale Tools für den Fernunterricht',
            'Classroom Management',
            'Kreative Unterrichtsmethoden',
            'Werte vermitteln im Schulalltag',
        ];

        $prefix = $this->faker->randomElement($titlePrefixes);
        $topic = $this->faker->randomElement($topics);
        $title = $prefix . ' ' . $topic;

        $startDate = $this->faker->dateTimeBetween('+1 week', '+6 months');
        $duration = $this->faker->randomElement([2, 3, 4, 6, 8]);
        $endDate = (clone $startDate)->modify('+' . $duration . ' hours');

        $locations = [
            ['name' => 'Evangelisches Schulzentrum Leipzig', 'city' => 'Leipzig', 'postal' => '04109'],
            ['name' => 'Ev. Schulstiftung Dresden', 'city' => 'Dresden', 'postal' => '01067'],
            ['name' => 'Fortbildungszentrum Chemnitz', 'city' => 'Chemnitz', 'postal' => '09111'],
            ['name' => 'Online (Zoom)', 'city' => 'Online', 'postal' => '00000'],
            ['name' => 'Ev. Akademie Meißen', 'city' => 'Meißen', 'postal' => '01662'],
        ];

        $location = $this->faker->randomElement($locations);

        $startDateCarbon = \Carbon\Carbon::instance($startDate);
        $endDateCarbon = \Carbon\Carbon::instance($endDate);

        return [
            'organization_id' => Organization::factory(),
            'event_category_id' => EventCategory::factory(),
            'event_type' => 'physical',
            'title' => $title,
            'description' => $this->faker->paragraphs(3, true),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'duration' => $startDateCarbon->diffInMinutes($endDateCarbon),
            'venue_name' => $location['name'],
            'venue_address' => $this->faker->streetAddress(),
            'venue_city' => $location['city'],
            'venue_postal_code' => $location['postal'],
            'venue_country' => 'Deutschland',
            'venue_latitude' => $this->faker->latitude(50, 52),
            'venue_longitude' => $this->faker->longitude(12, 14),
            'online_url' => null,
            'online_access_code' => null,
            'organizer_info' => $this->faker->optional(0.6)->sentence(),
            'organizer_email' => $this->faker->optional(0.7)->email(),
            'organizer_phone' => $this->faker->optional(0.5)->phoneNumber(),
            'organizer_website' => $this->faker->optional(0.4)->url(),
            'max_attendees' => $this->faker->numberBetween(15, 40),
            'is_featured' => false,
            'is_published' => false,
            'is_private' => false,
            'is_cancelled' => false,
            'cancelled_at' => null,
            'cancellation_reason' => null,
            'registration_required' => true,
            'views' => 0,
        ];
    }

    public function configure()
    {
        return $this
            ->afterMaking(function (Event $event) {
                // If user_id is set (from ->for($user, 'user')), create/use organization
                if (isset($event->getAttributes()['user_id']) && !$event->organization_id) {
                    $userId = $event->getAttributes()['user_id'];
                    $user = \App\Models\User::find($userId);
                    if ($user) {
                        $org = $user->activeOrganizations()->first();
                        if (!$org) {
                            $org = \App\Models\Organization::factory()->create();
                            // attach user as owner
                            $org->users()->attach($user->id, [
                                'role' => 'owner',
                                'is_active' => true,
                                'joined_at' => now(),
                            ]);
                        }
                        $event->organization_id = $org->id;
                    }
                    // Remove user_id from attributes as it's not a column
                    unset($event->getAttributes()['user_id']);
                }
            })
            ->afterCreating(function (Event $event) {
                // Ensure organization_id is saved if it was set during making
                if ($event->organization_id) {
                    $event->save();
                }
            });
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

    /**
     * Event with multiple dates
     */
    public function withMultipleDates(int $numberOfDates = 5): static
    {
        return $this->state(fn (array $attributes) => [
            'has_multiple_dates' => true,
        ])->afterCreating(function (Event $event) use ($numberOfDates) {
            for ($i = 0; $i < $numberOfDates; $i++) {
                \App\Models\EventDate::factory()->create([
                    'event_id' => $event->id,
                    'start_date' => $event->start_date->copy()->addWeeks($i),
                    'end_date' => $event->end_date->copy()->addWeeks($i),
                ]);
            }
        });
    }
}
