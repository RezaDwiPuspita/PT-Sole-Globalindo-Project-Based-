<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        
        // add one user admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin'),
            'role' => 'admin',
        ]);
        User::create([
            'name' => 'Owner',
            'email' => 'owner@gmail.com',
            'password' => bcrypt('owner'),
            'role' => 'owner',
        ]);

        $this->call([
            CitySeeder::class,
            CustomerSeeder::class,
            ShippingOriginSeeder::class,
            ShippingConfigSeeder::class,
        ]);
    }
}
