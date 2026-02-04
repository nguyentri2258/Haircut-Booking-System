<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Address;
use App\Models\Holiday;

class HolidaySeeder extends Seeder
{
    public function run(): void
    {
        $cn1 = Address::where('name', 'CN1')->first();
        $cn2 = Address::where('name', 'CN2')->first();

        $today = now()->startOfDay();

        $holidays = [
            [
                'date' => '2026-02-17',
                'note' => 'Tết Nguyên Đán',
                'address_id' => null,
            ],
            [
                'date' => '2026-02-18',
                'note' => 'Tết Nguyên Đán',
                'address_id' => null,
            ],
            [
                'date' => '2026-02-19',
                'note' => 'Tết Nguyên Đán',
                'address_id' => null,
            ],
            [
                'date' => $today->copy()->addDays(5),
                'note' => 'Maintenance',
                'address_id' => $cn1?->id,
            ],
            [
                'date' => $today->copy()->addDays(9),
                'note' => 'Maintenance',
                'address_id' => $cn2?->id,
            ],
            [
                'date' => $today->copy()->addDays(13),
                'note' => 'System maintenance',
                'address_id' => null,
            ]
        ];

        foreach ($holidays as $holiday) {
            Holiday::updateOrCreate(
                ['date' => $holiday['date']],
                [
                    'note' => $holiday['note'],
                    'address_id' => $holiday['address_id'],
                ]
            );
        }
    }
}
