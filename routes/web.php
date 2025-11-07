<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FavoriteController;
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
Route::get('/bookings/{bookingNumber}/certificate', [BookingController::class, 'downloadCertificate'])->name('bookings.certificate');
Route::get('/bookings/{bookingNumber}/verify', [BookingController::class, 'verify'])->name('bookings.verify');
Route::post('/bookings/{bookingNumber}/verify', [BookingController::class, 'verifyEmail'])->name('bookings.verify-email');
Route::post('/bookings/{bookingNumber}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
Route::post('/api/validate-discount-code', [BookingController::class, 'validateDiscountCode'])->name('api.validate-discount-code');

// User Dashboard Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/my-bookings', [DashboardController::class, 'bookingHistory'])->name('user.bookings');
    Route::get('/my-events/upcoming', [DashboardController::class, 'upcomingEvents'])->name('user.events.upcoming');
    Route::get('/my-events/past', [DashboardController::class, 'pastEvents'])->name('user.events.past');

    // Favorites
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/events/{event}/favorite', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{id}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');
});

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
    Route::get('/events/{event}/ticket-types', [Organizer\TicketTypeController::class, 'index'])->name('events.ticket-types.index');
    Route::get('/events/{event}/ticket-types/create', [Organizer\TicketTypeController::class, 'create'])->name('events.ticket-types.create');
    Route::post('/events/{event}/ticket-types', [Organizer\TicketTypeController::class, 'store'])->name('events.ticket-types.store');
    Route::get('/events/{event}/ticket-types/{ticketType}/edit', [Organizer\TicketTypeController::class, 'edit'])->name('events.ticket-types.edit');
    Route::put('/events/{event}/ticket-types/{ticketType}', [Organizer\TicketTypeController::class, 'update'])->name('events.ticket-types.update');
    Route::delete('/events/{event}/ticket-types/{ticketType}', [Organizer\TicketTypeController::class, 'destroy'])->name('events.ticket-types.destroy');
    Route::post('/events/{event}/ticket-types/reorder', [Organizer\TicketTypeController::class, 'reorder'])->name('events.ticket-types.reorder');

    // Discount Code Management
    Route::get('/events/{event}/discount-codes', [Organizer\DiscountCodeController::class, 'index'])->name('events.discount-codes.index');
    Route::get('/events/{event}/discount-codes/create', [Organizer\DiscountCodeController::class, 'create'])->name('events.discount-codes.create');
    Route::post('/events/{event}/discount-codes', [Organizer\DiscountCodeController::class, 'store'])->name('events.discount-codes.store');
    Route::get('/events/{event}/discount-codes/{discountCode}/edit', [Organizer\DiscountCodeController::class, 'edit'])->name('events.discount-codes.edit');
    Route::put('/events/{event}/discount-codes/{discountCode}', [Organizer\DiscountCodeController::class, 'update'])->name('events.discount-codes.update');
    Route::delete('/events/{event}/discount-codes/{discountCode}', [Organizer\DiscountCodeController::class, 'destroy'])->name('events.discount-codes.destroy');
    Route::patch('/events/{event}/discount-codes/{discountCode}/toggle', [Organizer\DiscountCodeController::class, 'toggle'])->name('events.discount-codes.toggle');
    Route::post('/events/{event}/discount-codes/generate', [Organizer\DiscountCodeController::class, 'generate'])->name('events.discount-codes.generate');

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

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // User Management
    Route::get('/users', [\App\Http\Controllers\Admin\UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/edit', [\App\Http\Controllers\Admin\UserManagementController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [\App\Http\Controllers\Admin\UserManagementController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [\App\Http\Controllers\Admin\UserManagementController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/toggle-organizer', [\App\Http\Controllers\Admin\UserManagementController::class, 'toggleOrganizer'])->name('users.toggle-organizer');
    Route::post('/users/{user}/toggle-admin', [\App\Http\Controllers\Admin\UserManagementController::class, 'toggleAdmin'])->name('users.toggle-admin');

    // Event Management
    Route::get('/events', [\App\Http\Controllers\Admin\EventManagementController::class, 'index'])->name('events.index');
    Route::post('/events/{event}/toggle-publish', [\App\Http\Controllers\Admin\EventManagementController::class, 'togglePublish'])->name('events.toggle-publish');
    Route::post('/events/{event}/toggle-featured', [\App\Http\Controllers\Admin\EventManagementController::class, 'toggleFeatured'])->name('events.toggle-featured');
    Route::delete('/events/{event}', [\App\Http\Controllers\Admin\EventManagementController::class, 'destroy'])->name('events.destroy');
});

require __DIR__.'/auth.php';
