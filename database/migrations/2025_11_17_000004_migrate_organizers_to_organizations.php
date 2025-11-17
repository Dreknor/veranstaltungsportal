<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Organization;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Migrate existing organizer users to organizations
     */
    public function up(): void
    {
        try {
            $organizers = User::role('organizer')->get();

            foreach ($organizers as $organizer) {
                // Create an organization for each organizer
                $organization = Organization::create([
                    'name' => $organizer->organization_name ?: $organizer->fullName() . "'s Organization",
                    'slug' => \Illuminate\Support\Str::slug($organizer->organization_name ?: $organizer->fullName() . '-org-' . $organizer->id),
                    'description' => $organizer->organization_description,
                    'website' => $organizer->organization_website,
                    'email' => $organizer->email,
                    'phone' => $organizer->phone,
                    'billing_data' => $organizer->organizer_billing_data,
                    'billing_company' => $organizer->billing_company,
                    'billing_address' => $organizer->billing_address,
                    'billing_address_line2' => $organizer->billing_address_line2,
                    'billing_postal_code' => $organizer->billing_postal_code,
                    'billing_city' => $organizer->billing_city,
                    'billing_state' => $organizer->billing_state,
                    'billing_country' => $organizer->billing_country,
                    'tax_id' => $organizer->tax_id,
                    'bank_account' => $organizer->bank_account,
                    'payout_settings' => $organizer->payout_settings,
                    'custom_platform_fee' => $organizer->custom_platform_fee,
                    'invoice_settings' => $organizer->invoice_settings,
                    'invoice_counter_booking' => $organizer->invoice_counter_booking ?? 1,
                    'invoice_counter_booking_year' => $organizer->invoice_counter_booking_year ?? date('Y'),
                    'is_active' => true,
                    'is_verified' => true,
                    'verified_at' => now(),
                ]);

                // Attach user to organization as owner
                $organization->users()->attach($organizer->id, [
                    'role' => 'owner',
                    'is_active' => true,
                    'joined_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Migrate all events from this user to the organization
                \App\Models\Event::where('user_id', $organizer->id)
                    ->update(['organization_id' => $organization->id]);
            }
        } catch (\Exception $e) {
            Log::error('Error migrating organizers to organizations: ' . $e->getMessage());
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset organization_id on all events
        \App\Models\Event::whereNotNull('organization_id')
            ->update(['organization_id' => null]);

        // Delete all organization-user relationships
        \DB::table('organization_user')->truncate();

        // Delete all organizations
        Organization::truncate();
    }
};

