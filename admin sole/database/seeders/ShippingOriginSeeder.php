<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ShippingOrigin;

class ShippingOriginSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ShippingOrigin::updateOrCreate(
            ['name' => 'Gudang Jepara'],
            [
                'lat' => config('shipping.origin_lat', -6.5841000),
                'lng' => config('shipping.origin_lng', 110.6700000),
                'is_active' => true,
            ]
        );
    }
}
