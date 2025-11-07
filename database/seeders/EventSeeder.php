<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\DiscountCode;
use App\Models\Event;
use App\Models\EventReview;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        // Erstelle Bildungsreferenten und Fortbildner
        $organizer1 = User::factory()->create([
            'name' => 'Dr. Thomas Hoffmann',
            'email' => 'hoffmann@bildungsportal.de',
            'is_organizer' => true,
        ]);

        $organizer2 = User::factory()->create([
            'name' => 'Prof. Dr. Sarah Müller',
            'email' => 'mueller@bildungsportal.de',
            'is_organizer' => true,
        ]);

        $organizer3 = User::factory()->create([
            'name' => 'Pfarrerin Anna Schmidt',
            'email' => 'schmidt@ev-schulen-sachsen.de',
            'is_organizer' => true,
        ]);

        // Erstelle Teilnehmer (Lehrkräfte)
        $participants = User::factory(20)->create();

        // Erstelle Fortbildungsveranstaltungen
        Event::factory(8)->for($organizer1, 'user')->published()->create();
        Event::factory(6)->for($organizer2, 'user')->published()->create();
        Event::factory(4)->for($organizer3, 'user')->published()->create();

        // Füge empfohlene Fortbildungen hinzu (Featured = Hauptfach Mensch)
        Event::factory(3)->for($organizer3, 'user')->featured()->create();

        // Füge eine interne Schulung hinzu (nur für Kollegium)
        Event::factory(1)->for($organizer1, 'user')->private()->create();

        // Hole alle Events für weitere Verarbeitung
        $events = Event::all();

        // Erstelle Teilnahmeplätze für jede Fortbildung
        $events->each(function ($event) use ($participants) {
            // Standard-Teilnahme
            $standardTicket = TicketType::factory()->create([
                'event_id' => $event->id,
                'name' => 'Teilnahme',
                'price' => fake()->randomFloat(2, 0, 50), // Viele Fortbildungen sind kostenlos oder günstig
                'quantity' => fake()->numberBetween(15, 40), // Kleinere Gruppen für Fortbildungen
            ]);

            // Zusätzliche Teilnahme mit Materialien
            if (fake()->boolean(50)) {
                TicketType::factory()->create([
                    'event_id' => $event->id,
                    'name' => 'Teilnahme mit Materialpaket',
                    'price' => fake()->randomFloat(2, 20, 80),
                    'quantity' => fake()->numberBetween(10, 25),
                ]);
            }

            // Frühbucher-Tarif (optional)
            if (fake()->boolean(40)) {
                TicketType::factory()->create([
                    'event_id' => $event->id,
                    'name' => 'Frühbucher',
                    'price' => fake()->randomFloat(2, 0, 30),
                    'quantity' => fake()->numberBetween(15, 30),
                    'sale_end' => $event->start_date->copy()->subWeeks(4),
                ]);
            }

            // Erstelle Anmeldungen von Lehrkräften
            $bookingCount = fake()->numberBetween(3, 12); // Weniger Teilnehmer pro Fortbildung
            for ($i = 0; $i < $bookingCount; $i++) {
                $participant = $participants->random();
                $ticketType = $standardTicket;
                $quantity = 1; // In der Regel 1 Platz pro Person

                $subtotal = $ticketType->price * $quantity;
                $discount = fake()->boolean(15) ? fake()->randomFloat(2, 0, $subtotal * 0.15) : 0;
                $total = $subtotal - $discount;

                $booking = Booking::factory()->create([
                    'event_id' => $event->id,
                    'user_id' => $participant->id,
                    'customer_name' => $participant->name,
                    'customer_email' => $participant->email,
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'total' => $total,
                    'status' => fake()->randomElement(['confirmed', 'confirmed', 'confirmed', 'pending']),
                    'payment_status' => $total == 0 ? 'paid' : fake()->randomElement(['paid', 'paid', 'pending']),
                ]);

                // Erstelle Booking Items
                for ($j = 0; $j < $quantity; $j++) {
                    BookingItem::factory()->create([
                        'booking_id' => $booking->id,
                        'ticket_type_id' => $ticketType->id,
                        'price' => $ticketType->price,
                        'quantity' => 1,
                        'checked_in' => fake()->boolean(30),
                    ]);
                }

                // Aktualisiere verkaufte Tickets
                $ticketType->increment('quantity_sold', $quantity);

                // Füge Bewertungen für abgeschlossene Fortbildungen hinzu
                if ($event->start_date->isPast() && fake()->boolean(70)) {
                    EventReview::factory()->create([
                        'event_id' => $event->id,
                        'user_id' => $participant->id,
                        'rating' => fake()->numberBetween(4, 5), // Fortbildungen werden meist gut bewertet
                    ]);
                }
            }

            // Aktualisiere Event average_rating
            $averageRating = $event->reviews()->avg('rating');
            if ($averageRating) {
                $event->update(['average_rating' => $averageRating]);
            }

            // Aktualisiere price_from
            $minPrice = $event->ticketTypes()->min('price');
            $event->update(['price_from' => $minPrice]);
        });

        // Erstelle Rabattcodes für Schulen und Kollegien
        DiscountCode::factory()->create([
            'event_id' => null, // Global
            'discount_type' => 'percentage',
            'discount_value' => 10,
            'code' => 'HAUPTFACHMENSCH',
            'description' => 'Rabatt für Teilnehmer der Aktion Hauptfach Mensch',
        ]);

        DiscountCode::factory()->create([
            'event_id' => null,
            'discount_type' => 'percentage',
            'discount_value' => 15,
            'code' => 'KOLLEGIUM',
            'description' => 'Rabatt für Kollegiumsanmeldungen',
        ]);

        DiscountCode::factory(5)->create();

        $this->command->info('Events, Tickets, Bookings und Reviews wurden erstellt!');
    }
}

