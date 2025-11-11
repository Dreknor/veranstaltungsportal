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
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('icon')->nullable(); // Icon/Image path
            $table->string('color', 7)->default('#3B82F6'); // Hex color
            $table->enum('type', ['attendance', 'achievement', 'special'])->default('achievement');
            $table->json('requirements')->nullable(); // JSON criteria for earning
            $table->integer('points')->default(0); // Gamification points
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('badges');
    }
};

