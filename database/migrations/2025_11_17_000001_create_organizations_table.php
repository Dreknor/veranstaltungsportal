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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('website')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('logo')->nullable();

            // Billing Information
            $table->json('billing_data')->nullable();
            $table->string('billing_company')->nullable();
            $table->string('billing_address')->nullable();
            $table->string('billing_address_line2')->nullable();
            $table->string('billing_postal_code')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_state')->nullable();
            $table->string('billing_country')->nullable();
            $table->string('tax_id')->nullable();

            // Payment & Settings
            $table->json('bank_account')->nullable();
            $table->json('payout_settings')->nullable();
            $table->json('custom_platform_fee')->nullable();
            $table->json('invoice_settings')->nullable();
            $table->integer('invoice_counter_booking')->default(1);
            $table->integer('invoice_counter_booking_year')->default(date('Y'));

            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
