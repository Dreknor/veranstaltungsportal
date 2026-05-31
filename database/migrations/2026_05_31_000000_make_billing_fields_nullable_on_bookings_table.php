<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Die Rechnungsadresse ist bei Buchungen optional (z. B. für Privatpersonen
     * ohne Firmenrechnung). Die Spalten wurden ursprünglich als NOT NULL angelegt,
     * was beim Erstellen einer Buchung ohne Rechnungsadresse zu einem
     * Integrity-Constraint-Fehler führt. Hier werden sie nullable gemacht.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('billing_address')->nullable()->change();
            $table->string('billing_postal_code')->nullable()->change();
            $table->string('billing_city')->nullable()->change();
            $table->string('billing_country')->nullable()->default('Germany')->change();
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('billing_address')->nullable(false)->change();
            $table->string('billing_postal_code')->nullable(false)->change();
            $table->string('billing_city')->nullable(false)->change();
            $table->string('billing_country')->default('Germany')->nullable(false)->change();
        });
    }
};

