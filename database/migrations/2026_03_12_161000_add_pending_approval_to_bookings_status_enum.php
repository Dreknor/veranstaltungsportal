<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ENUM-Erweiterung ist nur auf MySQL nötig; SQLite nutzt TEXT und akzeptiert alle Werte
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `bookings` MODIFY COLUMN `status` ENUM('pending', 'confirmed', 'cancelled', 'completed', 'pending_approval') NOT NULL DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            // Erst alle pending_approval Datensätze auf 'pending' setzen, damit kein Datenverlust entsteht
            DB::statement("UPDATE `bookings` SET `status` = 'pending' WHERE `status` = 'pending_approval'");
            DB::statement("ALTER TABLE `bookings` MODIFY COLUMN `status` ENUM('pending', 'confirmed', 'cancelled', 'completed') NOT NULL DEFAULT 'pending'");
        }
    }
};

