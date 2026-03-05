<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShippingConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Default rates (tarif per kg berdasarkan tier berat)
        $rates = [
            [
                'type' => 'rate',
                'key' => null,
                'min_weight_kg' => 0,
                'max_weight_kg' => 500,
                'tarif_per_kg' => 6000,
                'order' => 1,
                'is_active' => true,
                'description' => 'Tarif untuk berat 0-500 kg',
            ],
            [
                'type' => 'rate',
                'key' => null,
                'min_weight_kg' => 500,
                'max_weight_kg' => 2000,
                'tarif_per_kg' => 4000,
                'order' => 2,
                'is_active' => true,
                'description' => 'Tarif untuk berat 500-2000 kg',
            ],
            [
                'type' => 'rate',
                'key' => null,
                'min_weight_kg' => 2000,
                'max_weight_kg' => null,
                'tarif_per_kg' => 2000,
                'order' => 3,
                'is_active' => true,
                'description' => 'Tarif untuk berat >2000 kg',
            ],
        ];

        // Default settings (konfigurasi global)
        $settings = [
            [
                'type' => 'setting',
                'key' => 'tarif_per_km',
                'value' => '2500',
                'description' => 'Tarif per kilometer dalam rupiah',
                'is_active' => true,
            ],
            [
                'type' => 'setting',
                'key' => 'volume_divisor',
                'value' => '6000',
                'description' => 'Pembagi berat volumetrik (cm³ → kg), standar domestik 6000',
                'is_active' => true,
            ],
        ];

        // Insert rates
        foreach ($rates as $rate) {
            \App\Models\ShippingConfig::updateOrCreate(
                [
                    'type' => 'rate',
                    'min_weight_kg' => $rate['min_weight_kg'],
                    'max_weight_kg' => $rate['max_weight_kg'],
                ],
                $rate
            );
        }

        // Insert settings
        foreach ($settings as $setting) {
            \App\Models\ShippingConfig::updateOrCreate(
                [
                    'type' => 'setting',
                    'key' => $setting['key'],
                ],
                $setting
            );
        }
    }
}
