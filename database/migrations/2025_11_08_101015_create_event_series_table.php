<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates event_dates table for events with multiple dates
     */
    public function up(): void
    {
        // Create event_dates table - one event can have multiple dates
        Schema::create('event_dates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->string('venue_name')->nullable(); // Optional: override event venue
            $table->text('venue_address')->nullable();
            $table->string('venue_city')->nullable();
            $table->string('venue_postal_code')->nullable();
            $table->string('venue_country')->nullable();
            $table->decimal('venue_latitude', 10, 7)->nullable();
            $table->decimal('venue_longitude', 10, 7)->nullable();
            $table->text('notes')->nullable(); // Special notes for this date
            $table->boolean('is_cancelled')->default(false);
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();

            $table->index(['event_id', 'start_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_dates');
    }
};

