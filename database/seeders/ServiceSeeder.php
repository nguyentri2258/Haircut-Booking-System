<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name' => 'Cắt gội',
                'price' => 80000,
                'description' => 'Best seller',
            ],
            [
                'name' => 'Uốn',
                'price' => 150000,
                'description' => null,
            ],
            [
                'name' => 'Nhuộm',
                'price' => 200000,
                'description' => null,
            ],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(
                ['name' => $service['name']],
                [
                    'price' => $service['price'],
                    'description' => $service['description'],
                ]
            );
        }
    }
}
