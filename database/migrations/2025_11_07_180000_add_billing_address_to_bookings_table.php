<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Rechnungsadresse
            $table->string('billing_address')->after('customer_phone');
            $table->string('billing_postal_code')->after('billing_address');
            $table->string('billing_city')->after('billing_postal_code');
            $table->string('billing_country')->default('Germany')->after('billing_city');

            // E-Mail Verifizierung für Gäste
            $table->string('email_verification_token')->nullable()->after('billing_country');
            $table->timestamp('email_verified_at')->nullable()->after('email_verification_token');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'billing_address',
                'billing_postal_code',
                'billing_city',
                'billing_country',
                'email_verification_token',
                'email_verified_at',
            ]);
        });
    }
};

