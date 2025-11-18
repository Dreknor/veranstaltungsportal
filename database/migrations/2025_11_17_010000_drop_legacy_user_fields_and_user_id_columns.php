<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop legacy organizer fields from users table
        Schema::table('users', function (Blueprint $table) {
            $drops = [
                'organization_name', 'organization_website', 'organization_description',
                'organizer_billing_data', 'bank_account', 'payout_settings', 'custom_platform_fee', 'invoice_settings',
                'invoice_counter_booking', 'invoice_counter_booking_year', 'billing_company', 'billing_address', 'billing_address_line2', 'billing_postal_code', 'billing_city', 'billing_state', 'billing_country', 'tax_id'
            ];
            foreach ($drops as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        // Drop user_id columns from events and event_series (hard migration)
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });

        Schema::table('event_series', function (Blueprint $table) {
            if (Schema::hasColumn('event_series', 'user_id')) {
                // Drop indexes first (SQLite compatibility)
                try {
                    $table->dropIndex(['user_id', 'is_active']);
                } catch (\Exception $e) {
                    // Index might not exist
                }

                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }

    public function down(): void
    {
        // Not fully reversible without data loss; re-add columns nullable
        Schema::table('users', function (Blueprint $table) {
            $table->string('organization_name')->nullable();
            $table->string('organization_website')->nullable();
            $table->text('organization_description')->nullable();
            $table->json('organizer_billing_data')->nullable();
            $table->json('bank_account')->nullable();
            $table->json('payout_settings')->nullable();
            $table->json('custom_platform_fee')->nullable();
            $table->json('invoice_settings')->nullable();
            $table->integer('invoice_counter_booking')->nullable();
            $table->integer('invoice_counter_booking_year')->nullable();
            $table->string('billing_company')->nullable();
            $table->string('billing_address')->nullable();
            $table->string('billing_address_line2')->nullable();
            $table->string('billing_postal_code')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_state')->nullable();
            $table->string('billing_country')->nullable();
            $table->string('tax_id')->nullable();
        });

        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
        });

        Schema::table('event_series', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
        });
    }
};

