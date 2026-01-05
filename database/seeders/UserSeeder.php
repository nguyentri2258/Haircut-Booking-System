<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Address;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $cn1 = Address::where('name', 'CN1')->first();
        $cn2 = Address::where('name', 'CN2')->first();

        $users = [
            [
                'email' => 'owner123@gmail.com',
                'name' => 'Owner System',
                'password' => Hash::make('Owner123@'),
                'role' => 'owner',
                'address_id' => null,
            ],
            [
                'email' => 'manager1@gmail.com',
                'name' => 'Manager CN1',
                'password' => Hash::make('Manager1@'),
                'role' => 'manager',
                'address_id' => $cn1?->id,
            ],
            [
                'email' => 'manager2@gmail.com',
                'name' => 'Manager CN2',
                'password' => Hash::make('Manager2@'),
                'role' => 'manager',
                'address_id' => $cn2?->id,
            ],
            [
                'email' => 'staff1@gmail.com',
                'name' => 'Staff 1',
                'password' => Hash::make('Staff1@'),
                'role' => 'staff',
                'address_id' => $cn1?->id,
            ],
            [
                'email' => 'staff2@gmail.com',
                'name' => 'Staff 2',
                'password' => Hash::make('Staff2@'),
                'role' => 'staff',
                'address_id' => $cn1?->id,
            ],
            [
                'email' => 'staff3@gmail.com',
                'name' => 'Staff 3',
                'password' => Hash::make('Staff3@'),
                'role' => 'staff',
                'address_id' => $cn2?->id,
            ],
            [
                'email' => 'staff4@gmail.com',
                'name' => 'Staff 4',
                'password' => Hash::make('Staff4@'),
                'role' => 'staff',
                'address_id' => $cn2?->id,
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}