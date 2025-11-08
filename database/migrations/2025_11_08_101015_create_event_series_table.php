<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('event_series', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('event_category_id')->constrained()->onDelete('cascade');

            // Recurrence Settings
            $table->enum('recurrence_type', ['none', 'daily', 'weekly', 'monthly', 'yearly', 'custom'])->default('none');
            $table->integer('recurrence_interval')->default(1); // Every X days/weeks/months
            $table->json('recurrence_days')->nullable(); // For weekly: [1,3,5] = Mon, Wed, Fri
            $table->integer('recurrence_count')->nullable(); // Number of occurrences
            $table->date('recurrence_end_date')->nullable(); // End date for series

            // Template data for all events in series
            $table->json('template_data')->nullable(); // Venue, price, ticket types, etc.

            // Meta
            $table->boolean('is_active')->default(true);
            $table->integer('total_events')->default(0);
            $table->timestamps();

            $table->index(['user_id', 'is_active']);
        });

        // Add series_id to events table
        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('series_id')->nullable()->after('user_id')->constrained('event_series')->onDelete('set null');
            $table->integer('series_position')->nullable()->after('series_id'); // Position in series (1, 2, 3...)
            $table->boolean('is_series_exception')->default(false)->after('series_position'); // Modified from series template
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['series_id']);
            $table->dropColumn(['series_id', 'series_position', 'is_series_exception']);
        });

        Schema::dropIfExists('event_series');
    }
};

