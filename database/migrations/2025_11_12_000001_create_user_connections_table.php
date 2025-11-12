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
            Schema::create('user_connections', function (Blueprint $table) {
                $table->id();
                $table->foreignId('follower_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('following_id')->constrained('users')->onDelete('cascade');
                $table->enum('status', ['pending', 'accepted', 'blocked'])->default('pending');
                $table->timestamp('accepted_at')->nullable();
                $table->timestamps();

                // Prevent duplicate connections
                $table->unique(['follower_id', 'following_id']);

                // Indexes for better performance
                $table->index('follower_id');
                $table->index('following_id');
                $table->index('status');
            });
        } catch (\Exception $e) {
            // Log the error message
            \Log::error('Fehler beim Erstellen der user_connections-Tabelle: ' . $e->getMessage());
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_connections');
    }
};

