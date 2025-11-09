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
        Schema::create('platform_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->decimal('fee_percentage', 5, 2)->default(5.00);
            $table->decimal('fee_amount', 10, 2);
            $table->string('status')->default('pending'); // pending, paid, refunded
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['event_id', 'status']);
            $table->index(['booking_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('platform_fees');
    }
};

