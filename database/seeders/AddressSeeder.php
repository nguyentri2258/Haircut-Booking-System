<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Address;

class AddressSeeder extends Seeder
{
    public function run(): void
    {
        $addresses = [
            [
                'name' => 'CN1',
                'address' => '213 Nguyễn Tri Phương',
                'note' => null,
            ],
            [
                'name' => 'CN2',
                'address' => '350 Hoàng Hoa Thám',
                'note' => 'new',
            ]
        ];

        foreach ($addresses as $address) {
            Address::create($address);
        }
    }
}
