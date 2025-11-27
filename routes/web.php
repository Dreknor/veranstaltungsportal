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

// SEO Routes
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [\App\Http\Controllers\SitemapController::class, 'robots'])->name('robots');

// Account Types Info Page
Route::get('/account-types', function () {
    return view('account-types');
})->name('account-types');

// Help Center Routes
Route::get('/help', [\App\Http\Controllers\HelpController::class, 'index'])->name('help.index');
Route::get('/help/search', [\App\Http\Controllers\HelpController::class, 'search'])->name('help.search');
Route::get('/help/{category}', [\App\Http\Controllers\HelpController::class, 'category'])->name('help.category');
Route::get('/help/{category}/{article}', [\App\Http\Controllers\HelpController::class, 'show'])->name('help.article');

// Event Routes (Public)
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/calendar', [EventController::class, 'calendar'])->name('events.calendar');
Route::get('/events/{slug}', [EventController::class, 'show'])->name('events.show');
Route::get('/events/{slug}/calendar', [EventController::class, 'exportToCalendar'])->name('events.calendar.export');
Route::get('/events/{slug}/access', [EventController::class, 'access'])->name('events.access');
Route::post('/events/{slug}/access', [EventController::class, 'verifyAccess'])->middleware('recaptcha:access_code')->name('events.verify-access');


// Waitlist Routes
Route::post('/events/{event}/waitlist/join', [\App\Http\Controllers\WaitlistController::class, 'join'])->name('waitlist.join');
Route::post('/events/{event}/waitlist/leave', [\App\Http\Controllers\WaitlistController::class, 'leave'])->name('waitlist.leave');

// Event Review Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/events/{event}/reviews', [\App\Http\Controllers\EventReviewController::class, 'store'])->name('events.reviews.store');
    Route::put('/reviews/{review}', [\App\Http\Controllers\EventReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [\App\Http\Controllers\EventReviewController::class, 'destroy'])->name('reviews.destroy');
});

// Booking Routes
Route::get('/events/{event}/book', [BookingController::class, 'create'])->name('bookings.create');
Route::post('/events/{event}/book', [BookingController::class, 'store'])->middleware('recaptcha:booking')->name('bookings.store');
Route::get('/bookings/{bookingNumber}', [BookingController::class, 'show'])->name('bookings.show');
Route::get('/bookings/{bookingNumber}/invoice', [BookingController::class, 'downloadInvoice'])->name('bookings.invoice');
Route::get('/bookings/{bookingNumber}/ticket', [BookingController::class, 'downloadTicket'])->name('bookings.ticket');
Route::get('/bookings/{bookingNumber}/certificate', [BookingController::class, 'downloadCertificate'])->name('bookings.certificate');
Route::get('/bookings/{bookingNumber}/ical', [BookingController::class, 'downloadIcal'])->name('bookings.ical');
Route::get('/bookings/{bookingNumber}/verify', [BookingController::class, 'verify'])->name('bookings.verify');
Route::post('/bookings/{bookingNumber}/verify', [BookingController::class, 'verifyEmail'])->name('bookings.verify-email');
Route::get('/bookings/{bookingNumber}/verify-email/{token}', [BookingController::class, 'verifyEmailToken'])->name('bookings.verify-email-token');
Route::post('/bookings/{bookingNumber}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
Route::post('/api/validate-discount-code', [BookingController::class, 'validateDiscountCode'])->name('api.validate-discount-code');

// User Dashboard Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/my-bookings', [DashboardController::class, 'bookingHistory'])->name('user.bookings');
    Route::get('/my-events/upcoming', [DashboardController::class, 'upcomingEvents'])->name('user.events.upcoming');
    Route::get('/my-events/past', [DashboardController::class, 'pastEvents'])->name('user.events.past');
    Route::get('/my-statistics', [DashboardController::class, 'statistics'])->name('user.statistics');

    // Favorites
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/events/{event}/favorite', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{id}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Badges
    Route::get('/badges', [\App\Http\Controllers\BadgeController::class, 'index'])->name('badges.index');
    Route::get('/badges/leaderboard', [\App\Http\Controllers\BadgeController::class, 'leaderboard'])->name('badges.leaderboard');
    Route::get('/badges/{badge}', [\App\Http\Controllers\BadgeController::class, 'show'])->name('badges.show');
    Route::post('/badges/{badge}/toggle-highlight', [\App\Http\Controllers\BadgeController::class, 'toggleHighlight'])->name('badges.toggle-highlight');

    // Connections (Social Features)
    Route::get('/connections', [\App\Http\Controllers\ConnectionController::class, 'index'])->name('connections.index');
    Route::get('/connections/requests', [\App\Http\Controllers\ConnectionController::class, 'requests'])->name('connections.requests');
    Route::get('/connections/suggestions', [\App\Http\Controllers\ConnectionController::class, 'suggestions'])->name('connections.suggestions');
    Route::get('/connections/search', [\App\Http\Controllers\ConnectionController::class, 'search'])->name('connections.search');
    Route::get('/connections/blocked', [\App\Http\Controllers\ConnectionController::class, 'blocked'])->name('connections.blocked');
    Route::post('/connections/{user}/send', [\App\Http\Controllers\ConnectionController::class, 'send'])->name('connections.send');
    Route::post('/connections/{user}/accept', [\App\Http\Controllers\ConnectionController::class, 'accept'])->name('connections.accept');
    Route::post('/connections/{user}/decline', [\App\Http\Controllers\ConnectionController::class, 'decline'])->name('connections.decline');
    Route::post('/connections/{user}/cancel', [\App\Http\Controllers\ConnectionController::class, 'cancel'])->name('connections.cancel');
    Route::delete('/connections/{user}/remove', [\App\Http\Controllers\ConnectionController::class, 'remove'])->name('connections.remove');
    Route::post('/connections/{user}/block', [\App\Http\Controllers\ConnectionController::class, 'block'])->name('connections.block');
    Route::delete('/connections/{user}/unblock', [\App\Http\Controllers\ConnectionController::class, 'unblock'])->name('connections.unblock');
});

// User Profiles (public, no auth required for viewing)
Route::get('/users/{user}', [\App\Http\Controllers\UserProfileController::class, 'show'])->name('users.show');
Route::get('/users/{user}/followers', [\App\Http\Controllers\UserProfileController::class, 'followers'])->name('users.followers');
Route::get('/users/{user}/following', [\App\Http\Controllers\UserProfileController::class, 'following'])->name('users.following');

// Organizer Routes
Route::middleware(['auth', 'verified', 'organizer'])->prefix('organizer')->name('organizer.')->group(function () {
    // Organization Management (no org context required)
    Route::get('/organizations/select', [Organizer\OrganizationController::class, 'select'])->name('organizations.select');
    Route::post('/organizations/switch/{organization}', [Organizer\OrganizationController::class, 'switch'])->name('organizations.switch');
    Route::get('/organizations/create', [Organizer\OrganizationController::class, 'create'])->name('organizations.create');
    Route::post('/organizations', [Organizer\OrganizationController::class, 'store'])->name('organizations.store');

    // Routes requiring active organization context
    Route::middleware(['organization_context'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [Organizer\DashboardController::class, 'index'])->name('dashboard');

        // Organization Settings & Team
        Route::get('/organization', [Organizer\OrganizationController::class, 'edit'])->name('organization.edit');
        Route::put('/organization', [Organizer\OrganizationController::class, 'update'])->name('organization.update');
        Route::delete('/organization/logo', [Organizer\OrganizationController::class, 'deleteLogo'])->name('organization.delete-logo');
        Route::get('/team', [Organizer\OrganizationController::class, 'team'])->name('team.index');
        Route::post('/team/invite', [Organizer\OrganizationController::class, 'inviteMember'])->name('team.invite');
        Route::put('/team/{user}/role', [Organizer\OrganizationController::class, 'updateMemberRole'])->name('team.update-role');
        Route::delete('/team/{user}', [Organizer\OrganizationController::class, 'removeMember'])->name('team.remove');
        Route::get('/team/import', [Organizer\OrganizationController::class, 'importForm'])->name('team.import');
        Route::post('/team/import', [Organizer\OrganizationController::class, 'importMembers'])->name('team.import.process');
        Route::get('/team/import/template', [Organizer\OrganizationController::class, 'downloadTemplate'])->name('team.import.template');

        // Personal Profile Management
        Route::get('/profile', [Organizer\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [Organizer\ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile/photo', [Organizer\ProfileController::class, 'deletePhoto'])->name('profile.delete-photo');

        // Event Management
        Route::get('/events', [Organizer\EventManagementController::class, 'index'])->name('events.index');
        Route::get('/events/create', [Organizer\EventManagementController::class, 'create'])->name('events.create');
        Route::post('/events', [Organizer\EventManagementController::class, 'store'])->name('events.store');
        Route::get('/events/{event}/edit', [Organizer\EventManagementController::class, 'edit'])->name('events.edit');
        Route::put('/events/{event}', [Organizer\EventManagementController::class, 'update'])->name('events.update');
        Route::delete('/events/{event}', [Organizer\EventManagementController::class, 'destroy'])->name('events.destroy');
        Route::post('/events/{event}/duplicate', [Organizer\EventManagementController::class, 'duplicate'])->name('events.duplicate');
        Route::post('/events/{event}/cancel', [Organizer\EventManagementController::class, 'cancel'])->name('events.cancel');
        Route::get('/events/{event}/attendees/download', [Organizer\EventManagementController::class, 'downloadAttendees'])->name('events.attendees.download');
        Route::get('/events/{event}/attendees/contact', [Organizer\EventManagementController::class, 'contactAttendeesForm'])->name('events.attendees.contact');
        Route::post('/events/{event}/attendees/contact', [Organizer\EventManagementController::class, 'contactAttendees'])->name('events.attendees.contact.send');
        Route::post('/events/{event}/calculate-costs', [Organizer\EventManagementController::class, 'calculateCosts'])->name('events.calculate-costs');

        // Event Dates (for events with multiple dates)
        Route::post('/events/{event}/multiple-dates/toggle', [Organizer\EventMultipleDatesController::class, 'toggle'])->name('events.multiple-dates.toggle');
        Route::post('/events/{event}/dates', [Organizer\EventDateController::class, 'store'])->name('events.dates.store');
        Route::put('/events/{event}/dates/{eventDate}', [Organizer\EventDateController::class, 'update'])->name('events.dates.update');
        Route::delete('/events/{event}/dates/{eventDate}', [Organizer\EventDateController::class, 'destroy'])->name('events.dates.destroy');
        Route::post('/events/{event}/dates/{eventDate}/cancel', [Organizer\EventDateController::class, 'cancel'])->name('events.dates.cancel');
        Route::post('/events/{event}/dates/{eventDate}/reactivate', [Organizer\EventDateController::class, 'reactivate'])->name('events.dates.reactivate');

        // Media Upload Routes
        Route::post('/events/{event}/upload-image', [Organizer\EventManagementController::class, 'uploadImage'])->name('events.upload-image');
        Route::post('/events/{event}/upload-gallery', [Organizer\EventManagementController::class, 'uploadGalleryImage'])->name('events.upload-gallery');
        Route::post('/profile/upload-photo', [Organizer\ProfileController::class, 'uploadPhoto'])->name('profile.upload-photo');

        // Ticket Types
        Route::get('/events/{event}/ticket-types', [Organizer\TicketTypeController::class, 'index'])->name('events.ticket-types.index');
        Route::get('/events/{event}/ticket-types/create', [Organizer\TicketTypeController::class, 'create'])->name('events.ticket-types.create');
        Route::post('/events/{event}/ticket-types', [Organizer\TicketTypeController::class, 'store'])->name('events.ticket-types.store');
        Route::get('/events/{event}/ticket-types/{ticketType}/edit', [Organizer\TicketTypeController::class, 'edit'])->name('events.ticket-types.edit');
        Route::put('/events/{event}/ticket-types/{ticketType}', [Organizer\TicketTypeController::class, 'update'])->name('events.ticket-types.update');
        Route::delete('/events/{event}/ticket-types/{ticketType}', [Organizer\TicketTypeController::class, 'destroy'])->name('events.ticket-types.destroy');
        Route::post('/events/{event}/ticket-types/reorder', [Organizer\TicketTypeController::class, 'reorder'])->name('events.ticket-types.reorder');

        // Discount Codes
        Route::get('/events/{event}/discount-codes', [Organizer\DiscountCodeController::class, 'index'])->name('events.discount-codes.index');
        Route::get('/events/{event}/discount-codes/create', [Organizer\DiscountCodeController::class, 'create'])->name('events.discount-codes.create');
        Route::post('/events/{event}/discount-codes', [Organizer\DiscountCodeController::class, 'store'])->name('events.discount-codes.store');
        Route::get('/events/{event}/discount-codes/{discountCode}/edit', [Organizer\DiscountCodeController::class, 'edit'])->name('events.discount-codes.edit');
        Route::put('/events/{event}/discount-codes/{discountCode}', [Organizer\DiscountCodeController::class, 'update'])->name('events.discount-codes.update');
        Route::delete('/events/{event}/discount-codes/{discountCode}', [Organizer\DiscountCodeController::class, 'destroy'])->name('events.discount-codes.destroy');
        Route::patch('/events/{event}/discount-codes/{discountCode}/toggle', [Organizer\DiscountCodeController::class, 'toggle'])->name('events.discount-codes.toggle');
        Route::post('/events/{event}/discount-codes/generate', [Organizer\DiscountCodeController::class, 'generate'])->name('events.discount-codes.generate');

        // Bookings
        Route::get('/bookings', [Organizer\BookingManagementController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/export', [Organizer\BookingManagementController::class, 'export'])->name('bookings.export');
        Route::get('/bookings/{booking}', [Organizer\BookingManagementController::class, 'show'])->name('bookings.show');
        Route::put('/bookings/{booking}/status', [Organizer\BookingManagementController::class, 'updateStatus'])->name('bookings.update-status');
        Route::put('/bookings/{booking}/payment', [Organizer\BookingManagementController::class, 'updatePaymentStatus'])->name('bookings.update-payment');
        Route::post('/bookings/{booking}/check-in', [Organizer\CheckInController::class, 'checkInByBooking'])->name('bookings.check-in');

        // Check-In
        Route::get('/events/{event}/check-in', [Organizer\CheckInController::class, 'index'])->name('check-in.index');
        Route::post('/events/{event}/check-in', [Organizer\CheckInController::class, 'scanQr'])->name('check-in.process');
        Route::post('/events/{event}/check-in/scan', [Organizer\CheckInController::class, 'scanQr'])->name('check-in.scan');
        Route::post('/events/{event}/check-in/manual', [Organizer\CheckInController::class, 'checkIn'])->name('check-in.manual');
        Route::get('/events/{event}/check-in/stats', [Organizer\CheckInController::class, 'stats'])->name('check-in.stats');
        Route::post('/events/{event}/check-in/bulk', [Organizer\CheckInController::class, 'bulkCheckIn'])->name('check-in.bulk');
        Route::get('/events/{event}/check-in/export', [Organizer\CheckInController::class, 'exportCheckInList'])->name('check-in.export');
        Route::post('/events/{event}/check-in/{booking}/store', [Organizer\CheckInController::class, 'checkIn'])->name('check-in.store');
        Route::delete('/events/{event}/check-in/{booking}/undo', [Organizer\CheckInController::class, 'undoCheckIn'])->name('check-in.undo');

        // Statistics
        Route::get('/statistics', [Organizer\StatisticsController::class, 'index'])->name('statistics.index');
        Route::get('/statistics/event/{event}', [Organizer\StatisticsController::class, 'eventStatistics'])->name('statistics.event');

        // Waitlist
        Route::get('/events/{event}/waitlist', [\App\Http\Controllers\WaitlistController::class, 'index'])->name('events.waitlist.index');
        Route::post('/events/{event}/waitlist/notify', [\App\Http\Controllers\WaitlistController::class, 'notifyNext'])->name('events.waitlist.notify');
        Route::delete('/events/{event}/waitlist/{waitlist}', [\App\Http\Controllers\WaitlistController::class, 'remove'])->name('events.waitlist.remove');

        // Certificates
        Route::get('/events/{event}/certificates', [Organizer\CertificateController::class, 'index'])->name('events.certificates.index');
        Route::post('/events/{event}/certificates/{booking}/generate', [Organizer\CertificateController::class, 'generate'])->name('events.certificates.generate');
        Route::post('/events/{event}/certificates/bulk', [Organizer\CertificateController::class, 'generateBulk'])->name('events.certificates.bulk');
        Route::get('/events/{event}/certificates/{booking}/download', [Organizer\CertificateController::class, 'download'])->name('events.certificates.download');
        Route::post('/events/{event}/certificates/{booking}/email', [Organizer\CertificateController::class, 'sendEmail'])->name('events.certificates.email');
        Route::delete('/events/{event}/certificates/{booking}', [Organizer\CertificateController::class, 'destroy'])->name('events.certificates.destroy');


        // Bank & Billing
        Route::get('/bank-account', [Organizer\BankAccountController::class, 'index'])->name('bank-account.index');
        Route::put('/bank-account', [Organizer\BankAccountController::class, 'update'])->name('bank-account.update');
        Route::get('/bank-account/billing-data', [Organizer\BankAccountController::class, 'billingData'])->name('bank-account.billing-data');
        Route::put('/bank-account/billing-data', [Organizer\BankAccountController::class, 'updateBillingData'])->name('bank-account.billing-data.update');

        // Invoice Settings & Invoices
        Route::get('/settings/invoice', [Organizer\InvoiceSettingsController::class, 'index'])->name('settings.invoice.index');
        Route::put('/settings/invoice', [Organizer\InvoiceSettingsController::class, 'update'])->name('settings.invoice.update');
        Route::post('/settings/invoice/preview', [Organizer\InvoiceSettingsController::class, 'preview'])->name('settings.invoice.preview');
        Route::get('/invoices', [Organizer\InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/invoices/{invoice}', [Organizer\InvoiceController::class, 'show'])->name('invoices.show');
        Route::get('/invoices/{invoice}/download', [Organizer\InvoiceController::class, 'download'])->name('invoices.download');
        Route::get('/invoices/export', [Organizer\InvoiceController::class, 'export'])->name('invoices.export');
        Route::get('/platform-fees', [Organizer\InvoiceController::class, 'platformFees'])->name('invoices.platform-fees');

        // Reviews
        Route::get('/reviews', [Organizer\ReviewController::class, 'index'])->name('reviews.index');
        Route::get('/reviews/{review}/moderate', [Organizer\ReviewController::class, 'moderate'])->name('reviews.moderate');
        Route::patch('/reviews/{review}/approve', [Organizer\ReviewController::class, 'approve'])->name('reviews.approve');
        Route::patch('/reviews/{review}/reject', [Organizer\ReviewController::class, 'reject'])->name('reviews.reject');
        Route::delete('/reviews/{review}', [Organizer\ReviewController::class, 'destroy'])->name('reviews.destroy');

        // Featured Events
        Route::get('/events/{event}/featured', [\App\Http\Controllers\FeaturedEventController::class, 'create'])->name('featured-events.create');
        Route::post('/events/{event}/featured', [\App\Http\Controllers\FeaturedEventController::class, 'store'])->name('featured-events.store');
        Route::get('/featured-events/{featuredEventFee}/payment', [\App\Http\Controllers\FeaturedEventController::class, 'payment'])->name('featured-events.payment');
        Route::post('/featured-events/{featuredEventFee}/payment', [\App\Http\Controllers\FeaturedEventController::class, 'processPayment'])->name('featured-events.process-payment');
        Route::get('/featured-events/history', [\App\Http\Controllers\FeaturedEventController::class, 'history'])->name('featured-events.history');
        Route::get('/events/{event}/featured/extend', [\App\Http\Controllers\FeaturedEventController::class, 'extend'])->name('featured-events.extend');
        Route::post('/events/{event}/featured/extend', [\App\Http\Controllers\FeaturedEventController::class, 'processExtension'])->name('featured-events.process-extension');
        Route::delete('/events/{event}/featured', [\App\Http\Controllers\FeaturedEventController::class, 'cancel'])->name('featured-events.cancel');
        // End of organizer routes requiring organization_context
    });
});

// Settings
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('settings/profile', [Settings\ProfileController::class, 'edit'])->name('settings.profile.edit');
    Route::put('settings/profile', [Settings\ProfileController::class, 'update'])->name('settings.profile.update');
    Route::delete('settings/profile/photo', [Settings\ProfileController::class, 'deletePhoto'])->name('settings.profile.photo.delete');
    Route::delete('settings/profile', [Settings\ProfileController::class, 'destroy'])->name('settings.profile.destroy');
    Route::get('settings/password', [Settings\PasswordController::class, 'edit'])->name('settings.password.edit');
    Route::put('settings/password', [Settings\PasswordController::class, 'update'])->name('settings.password.update');
    Route::get('settings/appearance', [Settings\AppearanceController::class, 'edit'])->name('settings.appearance.edit');
    Route::get('settings/notifications', [Settings\NotificationController::class, 'edit'])->name('settings.notifications.edit');
    Route::put('settings/notifications', [Settings\NotificationController::class, 'update'])->name('settings.notifications.update');
    Route::get('settings/privacy', [Settings\PrivacyController::class, 'edit'])->name('settings.privacy.edit');
    Route::put('settings/privacy', [Settings\PrivacyController::class, 'update'])->name('settings.privacy.update');

    // Newsletter & Interests
    Route::get('settings/interests', [\App\Http\Controllers\NewsletterController::class, 'edit'])->name('settings.interests.edit');
    Route::post('newsletter/subscribe', [\App\Http\Controllers\NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
    Route::post('newsletter/unsubscribe', [\App\Http\Controllers\NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');
    Route::post('newsletter/interests', [\App\Http\Controllers\NewsletterController::class, 'updateInterests'])->name('newsletter.interests');
});

// Admin Routes
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // User Management
    Route::get('/users', [\App\Http\Controllers\Admin\UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/edit', [\App\Http\Controllers\Admin\UserManagementController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [\App\Http\Controllers\Admin\UserManagementController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [\App\Http\Controllers\Admin\UserManagementController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/toggle-organizer', [\App\Http\Controllers\Admin\UserManagementController::class, 'toggleOrganizer'])->name('users.toggle-organizer');
    Route::post('/users/{user}/toggle-admin', [\App\Http\Controllers\Admin\UserManagementController::class, 'toggleAdmin'])->name('users.toggle-admin');
    Route::post('/users/{user}/assign-role', [\App\Http\Controllers\Admin\UserManagementController::class, 'assignRole'])->name('users.assign-role');
    Route::post('/users/{user}/remove-role', [\App\Http\Controllers\Admin\UserManagementController::class, 'removeRole'])->name('users.remove-role');
    Route::post('/users/{user}/promote-organizer', [\App\Http\Controllers\Admin\UserManagementController::class, 'promoteToOrganizer'])->name('users.promote-organizer');
    Route::post('/users/{user}/demote-participant', [\App\Http\Controllers\Admin\UserManagementController::class, 'demoteToParticipant'])->name('users.demote-participant');

    // Role & Permission Management
    Route::get('/roles', [\App\Http\Controllers\Admin\RoleManagementController::class, 'index'])->name('roles.index');
    Route::get('/roles/{role}/edit', [\App\Http\Controllers\Admin\RoleManagementController::class, 'edit'])->name('roles.edit');
    Route::put('/roles/{role}', [\App\Http\Controllers\Admin\RoleManagementController::class, 'update'])->name('roles.update');
    Route::get('/permissions', [\App\Http\Controllers\Admin\RoleManagementController::class, 'permissions'])->name('roles.permissions');

    // Event Management
    Route::get('/events', [\App\Http\Controllers\Admin\EventManagementController::class, 'index'])->name('events.index');
    Route::post('/events/{event}/toggle-publish', [\App\Http\Controllers\Admin\EventManagementController::class, 'togglePublish'])->name('events.toggle-publish');
    Route::post('/events/{event}/toggle-featured', [\App\Http\Controllers\Admin\EventManagementController::class, 'toggleFeatured'])->name('events.toggle-featured');
    Route::delete('/events/{event}', [\App\Http\Controllers\Admin\EventManagementController::class, 'destroy'])->name('events.destroy');

    // Category Management
    Route::get('/categories', [\App\Http\Controllers\Admin\CategoryManagementController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [\App\Http\Controllers\Admin\CategoryManagementController::class, 'create'])->name('categories.create');
    Route::post('/categories', [\App\Http\Controllers\Admin\CategoryManagementController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [\App\Http\Controllers\Admin\CategoryManagementController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [\App\Http\Controllers\Admin\CategoryManagementController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [\App\Http\Controllers\Admin\CategoryManagementController::class, 'destroy'])->name('categories.destroy');
    Route::post('/categories/{category}/toggle-active', [\App\Http\Controllers\Admin\CategoryManagementController::class, 'toggleActive'])->name('categories.toggle-active');

    // Settings Management
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'store'])->name('settings.store');
    Route::delete('/settings/{setting}', [\App\Http\Controllers\Admin\SettingsController::class, 'destroy'])->name('settings.destroy');
    Route::post('/settings/initialize', [\App\Http\Controllers\Admin\SettingsController::class, 'initializeDefaults'])->name('settings.initialize');

    // Invoice Number Settings (Platform Fee Invoices)
    Route::get('/settings/invoice', [\App\Http\Controllers\Admin\InvoiceSettingsController::class, 'index'])->name('settings.invoice.index');
    Route::put('/settings/invoice', [\App\Http\Controllers\Admin\InvoiceSettingsController::class, 'update'])->name('settings.invoice.update');
    Route::post('/settings/invoice/preview', [\App\Http\Controllers\Admin\InvoiceSettingsController::class, 'preview'])->name('settings.invoice.preview');

    // Monetization Settings (Admin Only)
    // Monetization Settings (Admin Only)
    Route::get('/monetization', [\App\Http\Controllers\Admin\MonetizationSettingsController::class, 'index'])->name('monetization.index');
    Route::put('/monetization', [\App\Http\Controllers\Admin\MonetizationSettingsController::class, 'update'])->name('monetization.update');
    Route::get('/monetization/billing-data', [\App\Http\Controllers\Admin\MonetizationSettingsController::class, 'billingData'])->name('monetization.billing-data');
    Route::put('/monetization/billing-data', [\App\Http\Controllers\Admin\MonetizationSettingsController::class, 'updateBillingData'])->name('monetization.billing-data.update');
    Route::get('/monetization/featured-events', [\App\Http\Controllers\Admin\MonetizationSettingsController::class, 'featuredEvents'])->name('monetization.featured-events');

    // Individual Organizer Fees
    Route::get('/organizer-fees/{user}', [\App\Http\Controllers\Admin\OrganizerFeeController::class, 'edit'])->name('organizer-fees.edit');
    Route::put('/organizer-fees/{user}', [\App\Http\Controllers\Admin\OrganizerFeeController::class, 'update'])->name('organizer-fees.update');
    Route::delete('/organizer-fees/{user}', [\App\Http\Controllers\Admin\OrganizerFeeController::class, 'destroy'])->name('organizer-fees.destroy');

    // Invoice Management
    Route::get('/invoices', [\App\Http\Controllers\Admin\InvoiceManagementController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/export', [\App\Http\Controllers\Admin\InvoiceManagementController::class, 'export'])->name('invoices.export');
    Route::get('/invoices/{invoice}', [\App\Http\Controllers\Admin\InvoiceManagementController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{invoice}/download', [\App\Http\Controllers\Admin\InvoiceManagementController::class, 'download'])->name('invoices.download');
    Route::patch('/invoices/{invoice}/mark-paid', [\App\Http\Controllers\Admin\InvoiceManagementController::class, 'markAsPaid'])->name('invoices.mark-paid');
    Route::patch('/invoices/{invoice}/mark-overdue', [\App\Http\Controllers\Admin\InvoiceManagementController::class, 'markAsOverdue'])->name('invoices.mark-overdue');
    Route::post('/invoices/{invoice}/resend', [\App\Http\Controllers\Admin\InvoiceManagementController::class, 'resend'])->name('invoices.resend');
    Route::delete('/invoices/{invoice}', [\App\Http\Controllers\Admin\InvoiceManagementController::class, 'destroy'])->name('invoices.destroy');

    // Review Management
    Route::get('/reviews', [\App\Http\Controllers\Admin\ReviewManagementController::class, 'index'])->name('reviews.index');
    Route::patch('/reviews/{review}/approve', [\App\Http\Controllers\Admin\ReviewManagementController::class, 'approve'])->name('reviews.approve');
    Route::patch('/reviews/{review}/reject', [\App\Http\Controllers\Admin\ReviewManagementController::class, 'reject'])->name('reviews.reject');
    Route::delete('/reviews/{review}', [\App\Http\Controllers\Admin\ReviewManagementController::class, 'destroy'])->name('reviews.destroy');
    Route::post('/reviews/bulk-approve', [\App\Http\Controllers\Admin\ReviewManagementController::class, 'bulkApprove'])->name('reviews.bulk-approve');
    Route::post('/reviews/bulk-reject', [\App\Http\Controllers\Admin\ReviewManagementController::class, 'bulkReject'])->name('reviews.bulk-reject');

    // Newsletter Management
    Route::get('/newsletter', [\App\Http\Controllers\Admin\NewsletterController::class, 'index'])->name('newsletter.index');
    Route::get('/newsletter/compose', [\App\Http\Controllers\Admin\NewsletterController::class, 'compose'])->name('newsletter.compose');
    Route::get('/newsletter/preview', [\App\Http\Controllers\Admin\NewsletterController::class, 'preview'])->name('newsletter.preview');
    Route::post('/newsletter/send', [\App\Http\Controllers\Admin\NewsletterController::class, 'send'])->name('newsletter.send');
    Route::get('/newsletter/subscribers', [\App\Http\Controllers\Admin\NewsletterController::class, 'subscribers'])->name('newsletter.subscribers');
    Route::get('/newsletter/export', [\App\Http\Controllers\Admin\NewsletterController::class, 'export'])->name('newsletter.export');

    // Reporting & Analytics
    Route::get('/reporting', [\App\Http\Controllers\Admin\ReportingController::class, 'index'])->name('reporting.index');
    Route::get('/reporting/users', [\App\Http\Controllers\Admin\ReportingController::class, 'users'])->name('reporting.users');
    Route::get('/reporting/events', [\App\Http\Controllers\Admin\ReportingController::class, 'events'])->name('reporting.events');
    Route::get('/reporting/revenue', [\App\Http\Controllers\Admin\ReportingController::class, 'revenue'])->name('reporting.revenue');
    Route::get('/reporting/export', [\App\Http\Controllers\Admin\ReportingController::class, 'export'])->name('reporting.export');

    // Audit Logs
    Route::get('/audit-logs', [\App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('/audit-logs/export', [\App\Http\Controllers\Admin\AuditLogController::class, 'export'])->name('audit-logs.export');
    Route::post('/audit-logs/clear', [\App\Http\Controllers\Admin\AuditLogController::class, 'clear'])->name('audit-logs.clear');
    Route::get('/audit-logs/{auditLog}', [\App\Http\Controllers\Admin\AuditLogController::class, 'show'])->name('audit-logs.show');
    Route::delete('/audit-logs/{auditLog}', [\App\Http\Controllers\Admin\AuditLogController::class, 'destroy'])->name('audit-logs.destroy');

    // System Logs
    Route::get('/system-logs', [\App\Http\Controllers\Admin\SystemLogController::class, 'index'])->name('system-logs.index');
    Route::get('/system-logs/statistics', [\App\Http\Controllers\Admin\SystemLogController::class, 'statistics'])->name('system-logs.statistics');
    Route::get('/system-logs/export', [\App\Http\Controllers\Admin\SystemLogController::class, 'export'])->name('system-logs.export');
    Route::post('/system-logs/clear', [\App\Http\Controllers\Admin\SystemLogController::class, 'clear'])->name('system-logs.clear');
    Route::post('/system-logs/clear-by-level', [\App\Http\Controllers\Admin\SystemLogController::class, 'clearByLevel'])->name('system-logs.clear-by-level');
    Route::get('/system-logs/{id}', [\App\Http\Controllers\Admin\SystemLogController::class, 'show'])->name('system-logs.show');
    Route::delete('/system-logs/{id}', [\App\Http\Controllers\Admin\SystemLogController::class, 'destroy'])->name('system-logs.destroy');

    // Log Notification Settings
    Route::get('/log-notifications', [\App\Http\Controllers\Admin\LogNotificationSettingsController::class, 'index'])->name('log-notifications.index');
    Route::post('/log-notifications/give', [\App\Http\Controllers\Admin\LogNotificationSettingsController::class, 'givePermission'])->name('log-notifications.give');
    Route::post('/log-notifications/revoke', [\App\Http\Controllers\Admin\LogNotificationSettingsController::class, 'revokePermission'])->name('log-notifications.revoke');
    Route::post('/log-notifications/test', [\App\Http\Controllers\Admin\LogNotificationSettingsController::class, 'testNotification'])->name('log-notifications.test');

    // Impersonate User
    Route::post('/users/{user}/impersonate', [\App\Http\Controllers\Admin\ImpersonateController::class, 'impersonate'])->name('users.impersonate');
});

// Impersonate Leave (available for all authenticated users)
Route::middleware(['auth', 'verified'])->post('/impersonate/leave', [\App\Http\Controllers\Admin\ImpersonateController::class, 'leave'])->name('impersonate.leave');

// Protected Profile Photos Route (requires authentication)
Route::get('/profile-photo/{user}', [\App\Http\Controllers\ProfilePhotoController::class, 'show'])
    ->middleware('auth')
    ->name('profile-photo.show');

// Data Privacy & DSGVO
Route::middleware(['auth', 'verified'])->prefix('data-privacy')->name('data-privacy.')->group(function () {
    Route::get('/', [\App\Http\Controllers\DataPrivacyController::class, 'index'])->name('index');
    Route::get('/export', [\App\Http\Controllers\DataPrivacyController::class, 'exportData'])->name('export');
    Route::get('/download-files', [\App\Http\Controllers\DataPrivacyController::class, 'downloadFiles'])->name('download-files');
    Route::post('/request-deletion', [\App\Http\Controllers\DataPrivacyController::class, 'requestDeletion'])->name('request-deletion');
    Route::get('/settings', [\App\Http\Controllers\DataPrivacyController::class, 'settings'])->name('settings');
    Route::put('/settings', [\App\Http\Controllers\DataPrivacyController::class, 'updateSettings'])->name('settings.update');
});


require __DIR__.'/auth.php';

// Notifications (für eingeloggte Benutzer)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread', [\App\Http\Controllers\NotificationController::class, 'unread'])->name('notifications.unread');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::get('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead']); // GET für Direktlinks
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{id}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('/notifications/read/all', [\App\Http\Controllers\NotificationController::class, 'deleteRead'])->name('notifications.delete-read');
});

