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
        Schema::table('users', function (Blueprint $table) {
            $table->json('interested_category_ids')->nullable()->after('notification_preferences');
            $table->boolean('newsletter_subscribed')->default(false)->after('interested_category_ids');
            $table->timestamp('newsletter_subscribed_at')->nullable()->after('newsletter_subscribed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['interested_category_ids', 'newsletter_subscribed', 'newsletter_subscribed_at']);
        });
    }
};

