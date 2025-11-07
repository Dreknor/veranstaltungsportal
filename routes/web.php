<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Organizer;
use App\Http\Controllers\Settings;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Event Routes (Public)
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/calendar', [EventController::class, 'calendar'])->name('events.calendar');
Route::get('/events/{slug}', [EventController::class, 'show'])->name('events.show');
Route::get('/events/{slug}/access', [EventController::class, 'access'])->name('events.access');
Route::post('/events/{slug}/access', [EventController::class, 'verifyAccess'])->name('events.verify-access');

// Event Review Routes
Route::middleware(['auth'])->group(function () {
    Route::post('/events/{event}/reviews', [\App\Http\Controllers\EventReviewController::class, 'store'])->name('events.reviews.store');
    Route::put('/reviews/{review}', [\App\Http\Controllers\EventReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [\App\Http\Controllers\EventReviewController::class, 'destroy'])->name('reviews.destroy');
});

// Booking Routes
Route::get('/events/{event}/book', [BookingController::class, 'create'])->name('bookings.create');
Route::post('/events/{event}/book', [BookingController::class, 'store'])->name('bookings.store');
Route::get('/bookings/{bookingNumber}', [BookingController::class, 'show'])->name('bookings.show');
Route::get('/bookings/{bookingNumber}/invoice', [BookingController::class, 'downloadInvoice'])->name('bookings.invoice');
Route::get('/bookings/{bookingNumber}/ticket', [BookingController::class, 'downloadTicket'])->name('bookings.ticket');
Route::get('/bookings/{bookingNumber}/verify', [BookingController::class, 'verify'])->name('bookings.verify');
Route::post('/bookings/{bookingNumber}/verify', [BookingController::class, 'verifyEmail'])->name('bookings.verify-email');
Route::post('/bookings/{bookingNumber}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
Route::post('/api/validate-discount-code', [BookingController::class, 'validateDiscountCode'])->name('api.validate-discount-code');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Organizer Routes
Route::middleware(['auth'])->prefix('organizer')->name('organizer.')->group(function () {
    Route::get('/dashboard', [Organizer\DashboardController::class, 'index'])->name('dashboard');

    // Event Management
    Route::get('/events', [Organizer\EventManagementController::class, 'index'])->name('events.index');
    Route::get('/events/create', [Organizer\EventManagementController::class, 'create'])->name('events.create');
    Route::post('/events', [Organizer\EventManagementController::class, 'store'])->name('events.store');
    Route::get('/events/{event}/edit', [Organizer\EventManagementController::class, 'edit'])->name('events.edit');
    Route::put('/events/{event}', [Organizer\EventManagementController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [Organizer\EventManagementController::class, 'destroy'])->name('events.destroy');

    // Ticket Type Management
    Route::post('/events/{event}/tickets', [Organizer\EventManagementController::class, 'addTicketType'])->name('events.tickets.add');
    Route::put('/events/{event}/tickets/{ticketType}', [Organizer\EventManagementController::class, 'updateTicketType'])->name('events.tickets.update');
    Route::delete('/events/{event}/tickets/{ticketType}', [Organizer\EventManagementController::class, 'deleteTicketType'])->name('events.tickets.delete');

    // Booking Management
    Route::get('/bookings', [Organizer\BookingManagementController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [Organizer\BookingManagementController::class, 'show'])->name('bookings.show');
    Route::put('/bookings/{booking}/status', [Organizer\BookingManagementController::class, 'updateStatus'])->name('bookings.update-status');
    Route::put('/bookings/{booking}/payment', [Organizer\BookingManagementController::class, 'updatePaymentStatus'])->name('bookings.update-payment');
    Route::get('/bookings/export', [Organizer\BookingManagementController::class, 'export'])->name('bookings.export');
    Route::post('/bookings/{booking}/check-in', [Organizer\BookingManagementController::class, 'checkIn'])->name('bookings.check-in');
});

Route::middleware(['auth'])->group(function () {
    Route::get('settings/profile', [Settings\ProfileController::class, 'edit'])->name('settings.profile.edit');
    Route::put('settings/profile', [Settings\ProfileController::class, 'update'])->name('settings.profile.update');
    Route::delete('settings/profile', [Settings\ProfileController::class, 'destroy'])->name('settings.profile.destroy');
    Route::get('settings/password', [Settings\PasswordController::class, 'edit'])->name('settings.password.edit');
    Route::put('settings/password', [Settings\PasswordController::class, 'update'])->name('settings.password.update');
    Route::get('settings/appearance', [Settings\AppearanceController::class, 'edit'])->name('settings.appearance.edit');
});

require __DIR__.'/auth.php';
