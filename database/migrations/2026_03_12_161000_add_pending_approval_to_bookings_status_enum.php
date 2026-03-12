<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `bookings` MODIFY COLUMN `status` ENUM('pending', 'confirmed', 'cancelled', 'completed', 'pending_approval') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        // Erst alle pending_approval Datensätze auf 'pending' setzen, damit kein Datenverlust entsteht
        DB::statement("UPDATE `bookings` SET `status` = 'pending' WHERE `status` = 'pending_approval'");
        DB::statement("ALTER TABLE `bookings` MODIFY COLUMN `status` ENUM('pending', 'confirmed', 'cancelled', 'completed') NOT NULL DEFAULT 'pending'");
    }
};

