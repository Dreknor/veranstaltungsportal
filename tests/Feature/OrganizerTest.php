<?php

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('organizer can view their dashboard', function () {
    Event::factory()->for($this->user, 'user')->count(3)->create();

    $response = $this->get(route('organizer.dashboard'));

    $response->assertStatus(200);
    $response->assertViewHas('stats');
    $response->assertViewHas('upcomingEvents');
});

test('organizer can create an event', function () {
    $category = EventCategory::factory()->create();

    $response = $this->post(route('organizer.events.store'), [
        'title' => 'Test Event',
        'event_category_id' => $category->id,
        'description' => 'This is a test event',
        'start_date' => now()->addDays(7)->format('Y-m-d\TH:i'),
        'end_date' => now()->addDays(7)->addHours(3)->format('Y-m-d\TH:i'),
        'venue_name' => 'Test Venue',
        'venue_address' => 'Test Street 1',
        'venue_city' => 'Test City',
        'venue_postal_code' => '12345',
        'venue_country' => 'Deutschland',
        'is_published' => true,
    ]);

    $response->assertRedirect();
    expect(Event::count())->toBe(1);

    $event = Event::first();
    expect($event->title)->toBe('Test Event');
    expect($event->user_id)->toBe($this->user->id);
});

test('organizer can update their event', function () {
    $event = Event::factory()->for($this->user, 'user')->create([
        'title' => 'Old Title',
    ]);

    $response = $this->put(route('organizer.events.update', $event), [
        'title' => 'New Title',
        'event_category_id' => $event->event_category_id,
        'description' => $event->description,
        'start_date' => $event->start_date->format('Y-m-d\TH:i'),
        'end_date' => $event->end_date->format('Y-m-d\TH:i'),
        'venue_name' => $event->venue_name,
        'venue_address' => $event->venue_address,
        'venue_city' => $event->venue_city,
        'venue_postal_code' => $event->venue_postal_code,
        'venue_country' => $event->venue_country,
        'is_published' => true,
    ]);

    $event->refresh();
    expect($event->title)->toBe('New Title');
});

test('organizer cannot update another users event', function () {
    $otherUser = User::factory()->create();
    $event = Event::factory()->for($otherUser, 'user')->create();

    $response = $this->put(route('organizer.events.update', $event), [
        'title' => 'Hacked Title',
    ]);

    $response->assertStatus(403);
});

test('organizer can delete their event', function () {
    $event = Event::factory()->for($this->user, 'user')->create();

    $response = $this->delete(route('organizer.events.destroy', $event));

    $response->assertRedirect();
    expect(Event::count())->toBe(0);
});

test('organizer can view bookings for their events', function () {
    $event = Event::factory()->for($this->user, 'user')->create();
    \App\Models\Booking::factory()->for($event)->count(5)->create();

    $response = $this->get(route('organizer.bookings.index'));

    $response->assertStatus(200);
    $response->assertViewHas('bookings');
});

