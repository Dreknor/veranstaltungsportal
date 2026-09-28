<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fügt 'extern' als möglichen Wert für payment_status in der bookings-Tabelle hinzu.
     * Extern fakturierte Buchungen werden intern wie 'bezahlt' behandelt,
     * sollen aber für den Kunden keinen sichtbaren Zahlungsstatus haben.
     */
    public function up(): void
    {
        // ENUM-Änderung ist nur auf MySQL nötig/möglich (SQLite kennt kein MODIFY COLUMN)
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE bookings MODIFY COLUMN payment_status ENUM('pending', 'paid', 'refunded', 'failed', 'extern') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        // Erst alle 'extern' Werte auf 'paid' zurücksetzen, da sonst der Constraint verletzt wird
        DB::statement("UPDATE bookings SET payment_status = 'paid' WHERE payment_status = 'extern'");
        DB::statement("ALTER TABLE bookings MODIFY COLUMN payment_status ENUM('pending', 'paid', 'refunded', 'failed') NOT NULL DEFAULT 'pending'");
    }
};

