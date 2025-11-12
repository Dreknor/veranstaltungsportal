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
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('allow_networking')->default(true)->after('newsletter_subscribed');
                $table->boolean('show_profile_public')->default(false)->after('allow_networking');
                $table->boolean('allow_data_analytics')->default(true)->after('show_profile_public');
            });
        } catch (\Exception $e) {
            // Log the error message
            \Log::error('Fehler beim Hinzufügen der Datenschutzfelder zur Users-Tabelle: ' . $e->getMessage());
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['allow_networking', 'show_profile_public', 'allow_data_analytics']);
        });
    }
};
