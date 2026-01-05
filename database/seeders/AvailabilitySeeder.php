<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Availability;
use App\Models\Holiday;
use Carbon\Carbon;

class AvailabilitySeeder extends Seeder
{
    public function run(): void
    {
        $users = User::whereIn('name', [
            'Staff 1', 'Staff 2', 'Staff 3', 'Staff 4'
        ])->get();

        $startDate = now()->startOfDay();
        $possibleHours = range(9, 19);

        for ($day = 0; $day < 14; $day++) {

            $date = $startDate->copy()->addDays($day)->toDateString();

            foreach ($users as $user) {
                $isHoliday = Holiday::where('date', $date)
                    ->where(function ($q) use ($user) {
                        $q->whereNull('address_id')
                          ->orWhere('address_id', $user->address_id);
                    })
                    ->exists();

                if ($isHoliday) {
                    continue;
                }

                $slotsForUser = [];
                $numberOfSlots = rand(1, 2);

                shuffle($possibleHours);

                foreach ($possibleHours as $hour) {
                    if (count($slotsForUser) >= $numberOfSlots) {
                        break;
                    }

                    $overlap = false;
                    foreach ($slotsForUser as $existingHour) {
                        if (abs($hour - $existingHour) < 3) {
                            $overlap = true;
                            break;
                        }
                    }

                    if ($overlap) continue;

                    $slotsForUser[] = $hour;

                    Availability::create([
                        'user_id'        => $user->id,
                        'available_date'=> $date,
                        'time_of_day'   => sprintf('%02d:00', $hour),
                    ]);
                }
            }
        }
    }
}
