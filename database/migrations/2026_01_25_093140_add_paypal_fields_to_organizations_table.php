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
        Schema::table('organizations', function (Blueprint $table) {
            // PayPal aktivieren/deaktivieren
            $table->boolean('paypal_enabled')->default(false)->after('is_verified');

            // PayPal Credentials (verschlüsselt gespeichert)
            $table->text('paypal_client_id')->nullable()->after('paypal_enabled');
            $table->text('paypal_client_secret')->nullable()->after('paypal_client_id');

            // PayPal Mode (sandbox/live)
            $table->string('paypal_mode')->default('sandbox')->after('paypal_client_secret');

            // Webhook ID (optional, für Signatur-Verifizierung)
            $table->string('paypal_webhook_id')->nullable()->after('paypal_mode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn([
                'paypal_enabled',
                'paypal_client_id',
                'paypal_client_secret',
                'paypal_mode',
                'paypal_webhook_id',
            ]);
        });
    }
};
