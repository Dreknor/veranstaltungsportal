<?php

use App\Models\Event;

test('event has correct attributes', function () {
    $event = Event::factory()->create([
        'title' => 'Test Event',
        'is_published' => true,
    ]);

    expect($event->title)->toBe('Test Event');
    expect($event->is_published)->toBeTrue();
    expect($event->slug)->toBeString();
});

test('event slug is generated correctly', function () {
    $event = Event::factory()->create([
        'title' => 'Test Event',
    ]);

    expect($event->slug)->toContain('test-event');
});

test('event has relationships', function () {
    $event = Event::factory()
        ->has(\App\Models\TicketType::factory()->count(2))
        ->has(\App\Models\Booking::factory()->count(3))
        ->create();

    expect($event->ticketTypes)->toHaveCount(2);
    expect($event->bookings)->toHaveCount(3);
});

test('event published scope works', function () {
    Event::factory()->create(['is_published' => true]);
    Event::factory()->create(['is_published' => false]);

    $publishedEvents = Event::published()->get();

    expect($publishedEvents)->toHaveCount(1);
});

test('event upcoming scope works', function () {
    Event::factory()->create([
        'start_date' => now()->addDays(7),
        'is_published' => true,
    ]);

    Event::factory()->create([
        'start_date' => now()->subDays(7),
        'is_published' => true,
    ]);

    $upcomingEvents = Event::upcoming()->get();

    expect($upcomingEvents)->toHaveCount(1);
});
use App\Models\User;

test('events index page displays published events', function () {
    $category = EventCategory::factory()->create();

    Event::factory()
        ->published()
        ->for($category)
        ->count(3)
        ->create();

    Event::factory()
        ->state(['is_published' => false])
        ->for($category)
        ->create();

    $response = $this->get(route('events.index'));

    $response->assertStatus(200);
    $response->assertViewHas('events');
    expect($response->viewData('events'))->toHaveCount(3);
});

test('event show page displays event details', function () {
    $event = Event::factory()
        ->published()
        ->create();

    $response = $this->get(route('events.show', $event->slug));

    $response->assertStatus(200);
    $response->assertSee($event->title);
    $response->assertSee($event->description);
});

test('unpublished event is not accessible to public', function () {
    $event = Event::factory()
        ->state(['is_published' => false])
        ->create();

    $response = $this->get(route('events.show', $event->slug));

    $response->assertStatus(404);
});

test('private event requires access code', function () {
    $event = Event::factory()
        ->private()
        ->published()
        ->create();

    $response = $this->get(route('events.show', $event->slug));

    $response->assertRedirect(route('events.access', $event->slug));
});

test('events can be filtered by category', function () {
    $category1 = EventCategory::factory()->create();
    $category2 = EventCategory::factory()->create();

    Event::factory()
        ->published()
        ->for($category1)
        ->count(2)
        ->create();

    Event::factory()
        ->published()
        ->for($category2)
        ->create();

    $response = $this->get(route('events.index', ['category' => $category1->id]));

    $response->assertStatus(200);
    expect($response->viewData('events'))->toHaveCount(2);
});

test('events can be searched by title', function () {
    Event::factory()
        ->published()
        ->create(['title' => 'Rock Concert 2024']);

    Event::factory()
        ->published()
        ->create(['title' => 'Jazz Festival']);

    $response = $this->get(route('events.index', ['search' => 'Rock']));

    $response->assertStatus(200);
    $response->assertSee('Rock Concert 2024');
    $response->assertDontSee('Jazz Festival');
});

