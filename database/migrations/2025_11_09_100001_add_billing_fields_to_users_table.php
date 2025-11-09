<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'bank_account')) {
                $table->json('bank_account')->nullable()->after('payout_settings');
            }
            if (!Schema::hasColumn('users', 'organizer_billing_data')) {
                $table->json('organizer_billing_data')->nullable()->after('bank_account');
            }
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('event_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('booking_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Organizer
            $table->enum('type', ['platform_fee', 'participant']);
            $table->string('recipient_name');
            $table->string('recipient_email');
            $table->text('recipient_address')->nullable();
            $table->decimal('amount', 10, 2);
            $table->decimal('tax_rate', 5, 2)->default(19.00);
            $table->decimal('tax_amount', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->string('currency', 3)->default('EUR');
            $table->date('invoice_date');
            $table->date('due_date');
            $table->timestamp('paid_at')->nullable();
            $table->enum('status', ['sent', 'paid', 'overdue', 'cancelled'])->default('sent');
            $table->json('billing_data')->nullable();
            $table->string('pdf_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['event_id', 'type']);
            $table->index(['user_id', 'status']);
            $table->index('invoice_number');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'bank_account')) {
                $table->dropColumn('bank_account');
            }
            if (Schema::hasColumn('users', 'organizer_billing_data')) {
                $table->dropColumn('organizer_billing_data');
            }
            Schema::dropIfExists('invoices');
        });
    }
};

