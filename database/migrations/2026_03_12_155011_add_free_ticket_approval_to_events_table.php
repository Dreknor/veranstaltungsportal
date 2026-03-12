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
        Schema::table('events', function (Blueprint $table) {
            $table->boolean('free_ticket_auto_confirm')->default(true)
                ->after('cancellation_days_before')
                ->comment('true = kostenfreie Buchungen automatisch bestaetigen, false = manuelle Bestaetigung durch Veranstalter');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('free_ticket_auto_confirm');
        });
    }
};
