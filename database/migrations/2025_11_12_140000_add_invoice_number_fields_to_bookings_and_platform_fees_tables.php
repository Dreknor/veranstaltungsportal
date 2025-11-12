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
        // Add invoice number to bookings table
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('invoice_number')->nullable()->after('booking_number');
            $table->timestamp('invoice_date')->nullable()->after('invoice_number');
        });

        // Add invoice number to platform_fees table
        Schema::table('platform_fees', function (Blueprint $table) {
            $table->string('invoice_number')->nullable()->after('id');
            $table->timestamp('invoice_date')->nullable()->after('invoice_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['invoice_number', 'invoice_date']);
        });

        Schema::table('platform_fees', function (Blueprint $table) {
            $table->dropColumn(['invoice_number', 'invoice_date']);
        });
    }
};

