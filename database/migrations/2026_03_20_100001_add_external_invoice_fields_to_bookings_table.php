<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->boolean('externally_invoiced')->default(false)->after('invoice_date');
            $table->timestamp('externally_invoiced_at')->nullable()->after('externally_invoiced');
            $table->string('external_invoice_number')->nullable()->after('externally_invoiced_at');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['externally_invoiced', 'externally_invoiced_at', 'external_invoice_number']);
        });
    }
};

