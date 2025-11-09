<?php

namespace Database\Seeders;

use App\Models\PlatformFee;
use App\Models\Booking;
use Illuminate\Database\Seeder;

class PlatformFeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all confirmed and paid bookings that don't have platform fees yet
        $bookings = Booking::whereIn('status', ['confirmed', 'completed'])
            ->where('payment_status', 'paid')
            ->whereDoesntHave('platformFee')
            ->with('event')
            ->get();

        foreach ($bookings as $booking) {
            $feePercentage = 5.00; // 5% platform fee
            $feeAmount = $booking->total * ($feePercentage / 100);

            PlatformFee::create([
                'event_id' => $booking->event_id,
                'booking_id' => $booking->id,
                'fee_percentage' => $feePercentage,
                'fee_amount' => $feeAmount,
                'status' => 'pending',
            ]);
        }

        $this->command->info('Platform fees created for ' . $bookings->count() . ' bookings.');
    }
}

