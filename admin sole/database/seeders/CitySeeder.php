<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        City::create([
            'kabupaten' => 'Kabupaten Jepara',
            'province' => 'Jawa Tengah',
            'lat'      => -6.5841000,   // koordinat contoh Jepara
            'lng'      => 110.6700000,
        ]);

        // Tambahkan kota/kabupaten lain kalau perlu
        // City::create([...]);
    }
}

