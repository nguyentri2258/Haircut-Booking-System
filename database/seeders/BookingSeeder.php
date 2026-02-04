<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\Availability;
use App\Models\Service;
use Carbon\Carbon;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        if (Booking::exists()) {
            return;
        }

        $services = Service::pluck('id')->toArray();

        if (empty($services)) {
            return;
        }

        $availabilities = Availability::inRandomOrder()
            ->take(10)
            ->get();

        foreach ($availabilities as $availability) {

            $bookingDateTime = Carbon::parse(
                $availability->available_date . ' ' . $availability->time_of_day
            );

            if (
                Booking::where('user_id', $availability->user_id)
                    ->where('date', $bookingDateTime)
                    ->exists()
            ) {
                continue;
            }

            $booking = Booking::create([
                'name'       => 'Demo Customer',
                'phone'      => '0900000000',
                'email'      => 'demo@booking.test',
                'address_id' => $availability->user->address_id,
                'user_id'    => $availability->user_id,
                'date'       => $bookingDateTime,
                'notes'      => null,
                'status'     => 'confirmed',
            ]);

            $booking->services()->attach(
                collect($services)->random(rand(1, 3))->toArray()
            );
        }
    }
}
