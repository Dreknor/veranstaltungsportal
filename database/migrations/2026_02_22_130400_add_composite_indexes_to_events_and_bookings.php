<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Zusammengesetzte Indizes für häufig gefilterte Spalten.
     *
     * - events: EventController::index filtert nach is_published + start_date + event_category_id
     * - events: Organizer-Dashboard filtert nach organization_id + is_published
     * - bookings: Veranstalter filtert nach event_id + status / payment_status
     * - bookings: User-Dashboard filtert nach user_id + status
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (!$this->hasIndex('events', 'events_published_start_category_index')) {
                $table->index(
                    ['is_published', 'start_date', 'event_category_id'],
                    'events_published_start_category_index'
                );
            }

            if (!$this->hasIndex('events', 'events_organization_published_index')) {
                $table->index(
                    ['organization_id', 'is_published'],
                    'events_organization_published_index'
                );
            }
        });

        Schema::table('bookings', function (Blueprint $table) {
            if (!$this->hasIndex('bookings', 'bookings_event_status_index')) {
                $table->index(
                    ['event_id', 'status'],
                    'bookings_event_status_index'
                );
            }

            if (!$this->hasIndex('bookings', 'bookings_event_payment_status_index')) {
                $table->index(
                    ['event_id', 'payment_status'],
                    'bookings_event_payment_status_index'
                );
            }

            if (!$this->hasIndex('bookings', 'bookings_user_status_index')) {
                $table->index(
                    ['user_id', 'status'],
                    'bookings_user_status_index'
                );
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if ($this->hasIndex('events', 'events_published_start_category_index')) {
                $table->dropIndex('events_published_start_category_index');
            }
            if ($this->hasIndex('events', 'events_organization_published_index')) {
                $table->dropIndex('events_organization_published_index');
            }
        });

        Schema::table('bookings', function (Blueprint $table) {
            if ($this->hasIndex('bookings', 'bookings_event_status_index')) {
                $table->dropIndex('bookings_event_status_index');
            }
            if ($this->hasIndex('bookings', 'bookings_event_payment_status_index')) {
                $table->dropIndex('bookings_event_payment_status_index');
            }
            if ($this->hasIndex('bookings', 'bookings_user_status_index')) {
                $table->dropIndex('bookings_user_status_index');
            }
        });
    }

    /**
     * Prüft ob ein Index existiert – unterstützt MySQL/MariaDB und SQLite (Tests).
     */
    private function hasIndex(string $table, string $indexName): bool
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            return DB::scalar(
                "SELECT COUNT(*) FROM sqlite_master WHERE type='index' AND tbl_name=? AND name=?",
                [$table, $indexName]
            ) > 0;
        }

        // MySQL / MariaDB
        return DB::scalar(
            "SELECT COUNT(*) FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND INDEX_NAME = ?",
            [$table, $indexName]
        ) > 0;
    }
};

