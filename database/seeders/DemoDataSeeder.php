<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\EventReview;
use App\Models\TicketType;
use App\Models\User;
use App\Models\UserConnection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Erstelle Demo-Benutzer...');

        // Hauptbenutzer mit ID 5
        $mainUser = User::factory()->create([
            'id' => 5,
            'name' => 'Max Mustermann',
            'first_name' => 'Max',
            'last_name' => 'Mustermann',
            'email' => 'max.mustermann@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'phone' => '+49 176 12345678',
            'bio' => 'Erfahrener Veranstalter und begeisterter Teilnehmer von Bildungsveranstaltungen.',
            'organization_name' => 'Bildungszentrum Mustermann',
            'allow_connections' => true,
            'show_profile_publicly' => true,
            'newsletter_subscribed' => true,
        ]);

        $mainUser->assignRole('organizer');
        $mainUser->assignRole('user');

        // Weitere Benutzer
        $users = User::factory()->count(15)->create([
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        foreach ($users->take(7) as $user) {
            $user->assignRole('organizer');
        }
        foreach ($users as $user) {
            $user->assignRole('user');
        }

        // User-Verbindungen
        $this->command->info('Erstelle User-Verbindungen...');
        foreach ($users->random(5) as $user) {
            UserConnection::create([
                'follower_id' => 5,
                'following_id' => $user->id,
                'status' => 'accepted',
                'accepted_at' => now(),
            ]);
        }

        $categories = EventCategory::all();

        // Events für User 5 erstellen
        $this->command->info('Erstelle Events für User 5...');

        // Vergangene Events
        for ($i = 0; $i < 3; $i++) {
            $event = Event::factory()->create([
                'user_id' => 5,
                'event_category_id' => $categories->random()->id,
                'is_published' => true,
                'start_date' => now()->subMonths(rand(1, 6)),
                'max_attendees' => 30,
            ]);

            $ticketType = TicketType::factory()->create([
                'event_id' => $event->id,
                'price' => fake()->randomElement([0, 29.99, 49.99]),
                'quantity' => 30,
                'is_available' => false,
            ]);

            // Buchungen
            foreach ($users->random(min(8, $users->count())) as $attendee) {
                $booking = Booking::factory()->create([
                    'event_id' => $event->id,
                    'user_id' => $attendee->id,
                    'customer_name' => $attendee->name,
                    'customer_email' => $attendee->email,
                    'status' => 'confirmed',
                    'payment_status' => 'paid',
                    'confirmed_at' => now()->subDays(rand(7, 60)),
                ]);

                $bookingItem = BookingItem::factory()->create([
                    'booking_id' => $booking->id,
                    'ticket_type_id' => $ticketType->id,
                    'quantity' => 1,
                    'price' => $ticketType->price,
                ]);

                // Bewertungen
                if (rand(0, 2) == 1) {
                    EventReview::factory()->create([
                        'event_id' => $event->id,
                        'user_id' => $attendee->id,
                        'booking_id' => $booking->id,
                        'rating' => rand(4, 5),
                        'is_approved' => true,
                    ]);
                }
            }
        }

        // Zukünftige Events
        for ($i = 0; $i < 10; $i++) {
            $event = Event::factory()->create([
                'user_id' => 5,
                'event_category_id' => $categories->random()->id,
                'is_published' => true,
                'start_date' => now()->addWeeks(rand(1, 12)),
                'max_attendees' => rand(15, 40),
            ]);

            $ticketType = TicketType::factory()->create([
                'event_id' => $event->id,
                'price' => fake()->randomElement([0, 19.99, 39.99, 69.99]),
                'quantity' => $event->max_attendees,
                'is_available' => true,
            ]);

            foreach ($users->random(min(6, $users->count())) as $attendee) {
                $booking = Booking::factory()->create([
                    'event_id' => $event->id,
                    'user_id' => $attendee->id,
                    'customer_name' => $attendee->name,
                    'customer_email' => $attendee->email,
                    'status' => 'confirmed',
                    'payment_status' => 'paid',
                    'confirmed_at' => now()->subDays(rand(1, 14)),
                ]);

                BookingItem::factory()->create([
                    'booking_id' => $booking->id,
                    'ticket_type_id' => $ticketType->id,
                    'quantity' => 1,
                    'price' => $ticketType->price,
                ]);
            }
        }

        // User 5 als Teilnehmer
        $this->command->info('Erstelle Buchungen für User 5...');
        $organizerUsers = $users->take(3);
        foreach ($organizerUsers as $organizer) {
            for ($i = 0; $i < 2; $i++) {
                $event = Event::factory()->create([
                    'user_id' => $organizer->id,
                    'event_category_id' => $categories->random()->id,
                    'is_published' => true,
                    'start_date' => now()->addWeeks(rand(2, 8)),
                    'max_attendees' => 25,
                ]);

                $ticketType = TicketType::factory()->create([
                    'event_id' => $event->id,
                    'price' => 39.99,
                    'quantity' => 25,
                    'is_available' => true,
                ]);

                $booking = Booking::factory()->create([
                    'event_id' => $event->id,
                    'user_id' => 5,
                    'customer_name' => $mainUser->name,
                    'customer_email' => $mainUser->email,
                    'status' => 'confirmed',
                    'payment_status' => 'paid',
                    'confirmed_at' => now()->subDays(rand(1, 14)),
                ]);

                BookingItem::factory()->create([
                    'booking_id' => $booking->id,
                    'ticket_type_id' => $ticketType->id,
                    'quantity' => 1,
                    'price' => $ticketType->price,
                ]);
            }

            // Vergangenes Event mit Bewertung
            $pastEvent = Event::factory()->create([
                'user_id' => $organizer->id,
                'event_category_id' => $categories->random()->id,
                'is_published' => true,
                'start_date' => now()->subMonths(rand(1, 3)),
                'max_attendees' => 20,
            ]);

            $ticketType = TicketType::factory()->create([
                'event_id' => $pastEvent->id,
                'price' => 39.99,
                'quantity' => 20,
                'is_available' => false,
            ]);

            $booking = Booking::factory()->create([
                'event_id' => $pastEvent->id,
                'user_id' => 5,
                'customer_name' => $mainUser->name,
                'customer_email' => $mainUser->email,
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'confirmed_at' => now()->subMonths(2),
                'checked_in' => true,
            ]);

            BookingItem::factory()->create([
                'booking_id' => $booking->id,
                'ticket_type_id' => $ticketType->id,
                'quantity' => 1,
                'price' => $ticketType->price,
            ]);

            EventReview::factory()->create([
                'event_id' => $pastEvent->id,
                'user_id' => 5,
                'booking_id' => $booking->id,
                'rating' => 5,
                'comment' => 'Sehr informative und gut strukturierte Veranstaltung!',
                'is_approved' => true,
            ]);
        }

        // Badges
        // $badges = Badge::all();
        // if ($badges->isNotEmpty()) {
        //     $mainUser->badges()->attach($badges->random(min(3, $badges->count())));
        // }

        // Events von anderen Organizern
        $this->command->info('Erstelle weitere Events...');
        foreach ($organizerUsers as $organizer) {
            for ($i = 0; $i < 3; $i++) {
                $event = Event::factory()->create([
                    'user_id' => $organizer->id,
                    'event_category_id' => $categories->random()->id,
                    'is_published' => true,
                    'start_date' => now()->addWeeks(rand(1, 10)),
                    'max_attendees' => rand(20, 40),
                ]);

                $ticketType = TicketType::factory()->create([
                    'event_id' => $event->id,
                    'price' => fake()->randomElement([0, 25.00, 45.00]),
                    'quantity' => $event->max_attendees,
                    'is_available' => true,
                ]);

                foreach ($users->random(min(5, $users->count())) as $attendee) {
                    $booking = Booking::factory()->create([
                        'event_id' => $event->id,
                        'user_id' => $attendee->id,
                        'customer_name' => $attendee->name,
                        'customer_email' => $attendee->email,
                        'status' => 'confirmed',
                        'payment_status' => 'paid',
                        'confirmed_at' => now()->subDays(rand(1, 14)),
                    ]);

                    BookingItem::factory()->create([
                        'booking_id' => $booking->id,
                        'ticket_type_id' => $ticketType->id,
                        'quantity' => 1,
                        'price' => $ticketType->price,
                    ]);
                }
            }
        }

        $this->command->info('✓ Demo-Daten erfolgreich erstellt!');
        $this->command->info('');
        $this->command->info('=== Zusammenfassung ===');
        $this->command->info('Hauptbenutzer (ID 5): ' . $mainUser->email . ' / Passwort: password');
        $this->command->info('- ' . Event::where('user_id', 5)->count() . ' Events als Organizer');
        $this->command->info('- ' . Booking::where('user_id', 5)->count() . ' Buchungen als Teilnehmer');
        $this->command->info('- ' . UserConnection::where('follower_id', 5)->orWhere('following_id', 5)->count() . ' User-Verbindungen');
        $this->command->info('- ' . EventReview::where('user_id', 5)->count() . ' Bewertungen geschrieben');
        $this->command->info('');
        $this->command->info('Gesamt:');
        $this->command->info('- ' . User::count() . ' Benutzer');
        $this->command->info('- ' . Event::count() . ' Events');
        $this->command->info('- ' . Booking::count() . ' Buchungen');
        $this->command->info('- ' . EventReview::count() . ' Bewertungen');
    }
}

