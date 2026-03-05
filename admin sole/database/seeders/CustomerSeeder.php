<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\City;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $city = City::first();
        if (!$city) {
            return;
        }

        Customer::create([
            'name' => 'Konsumen Offline 1',
            'phone' => '081234567101',
            'address' => 'Jl. Pelanggan Offline 1 No. 10',
            'city_id' => $city->id,
            'type' => 'offline',
        ]);

        Customer::create([
            'name' => 'Konsumen Online 1',
            'phone' => '081234567102',
            'address' => 'Jl. Pelanggan Online 1 No. 2',
            'city_id' => $city->id,
            'type' => 'online',
        ]);
    }
}
