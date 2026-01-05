<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\Availability;
use App\Models\Service;
use Faker\Factory as Faker;
use Carbon\Carbon;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('vi_VN');
        $services = Service::pluck('id')->toArray();

        $availabilities = Availability::inRandomOrder()->take(10)->get();

        foreach ($availabilities as $availability) {

            $date = Carbon::parse($availability->available_date)->format('Y-m-d');
            $time = Carbon::parse($availability->time_of_day)->format('H:i');

            $bookingDateTime = Carbon::parse("$date $time");

            $booking = Booking::create([
                'name' => $faker->name(),
                'phone' => '09' . rand(10000000, 99999999),
                'email' => $faker->safeEmail(),
                'address_id' => $availability->user->address_id,
                'user_id' => $availability->user_id,
                'date' => $bookingDateTime,
                'notes' => $faker->boolean(30) ? $faker->sentence() : null,
                'status' => 'confirmed',
            ]);

            $booking->services()->attach(
                collect($services)->random(rand(1, 3))->toArray()
            );
        }
    }
}
