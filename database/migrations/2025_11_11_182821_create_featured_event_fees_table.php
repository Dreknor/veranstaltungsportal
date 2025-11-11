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
        Schema::create('featured_event_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Event-Ersteller
            $table->enum('duration_type', ['daily', 'weekly', 'monthly', 'custom'])->default('weekly');
            $table->integer('duration_days')->nullable(); // Für custom duration
            $table->date('featured_start_date');
            $table->date('featured_end_date');
            $table->decimal('fee_amount', 10, 2); // Gebühr für Featured Status
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_method')->nullable(); // z.B. 'stripe', 'paypal', 'invoice'
            $table->string('payment_reference')->nullable(); // Transaktions-ID
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['event_id', 'payment_status']);
            $table->index(['user_id']);
            $table->index(['featured_start_date', 'featured_end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('featured_event_fees');
    }
};

