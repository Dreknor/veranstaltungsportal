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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'invoice_settings')) {
                $table->json('invoice_settings')->nullable()->after('organizer_billing_data');
            }
            if (!Schema::hasColumn('users', 'invoice_counter_booking')) {
                $table->integer('invoice_counter_booking')->default(1)->after('invoice_settings');
            }
            if (!Schema::hasColumn('users', 'invoice_counter_platform_fee')) {
                $table->integer('invoice_counter_platform_fee')->default(1)->after('invoice_counter_booking');
            }
            if (!Schema::hasColumn('users', 'invoice_counter_booking_year')) {
                $table->string('invoice_counter_booking_year', 4)->nullable()->after('invoice_counter_platform_fee');
            }
            if (!Schema::hasColumn('users', 'invoice_counter_platform_fee_year')) {
                $table->string('invoice_counter_platform_fee_year', 4)->nullable()->after('invoice_counter_booking_year');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'invoice_settings')) {
                $table->dropColumn('invoice_settings');
            }
            if (Schema::hasColumn('users', 'invoice_counter_booking')) {
                $table->dropColumn('invoice_counter_booking');
            }
            if (Schema::hasColumn('users', 'invoice_counter_platform_fee')) {
                $table->dropColumn('invoice_counter_platform_fee');
            }
            if (Schema::hasColumn('users', 'invoice_counter_booking_year')) {
                $table->dropColumn('invoice_counter_booking_year');
            }
            if (Schema::hasColumn('users', 'invoice_counter_platform_fee_year')) {
                $table->dropColumn('invoice_counter_platform_fee_year');
            }
        });
    }
};

