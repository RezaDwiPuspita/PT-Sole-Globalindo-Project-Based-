<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Koordinat Asal (Gudang Jepara)
    |--------------------------------------------------------------------------
    |
    | Titik asal pengiriman. Dipakai sebagai "origin" untuk perhitungan
    | jarak Haversine. Default diisi ke Jepara, bisa kamu ganti lewat .env.
    |
    */

    'origin_lat' => env('SHIPPING_ORIGIN_LAT', -6.5841000),
    'origin_lng' => env('SHIPPING_ORIGIN_LNG', 110.6700000),

    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Perhitungan Volumetrik & Tarif
    |--------------------------------------------------------------------------
    |
    | volume_divisor : pembagi berat volumetrik (cm³ → kg), standar domestik 6000
    |                  (digunakan sebagai fallback jika database kosong)
    | tarif_per_km   : tarif rupiah per km
    |                  (digunakan sebagai fallback jika database kosong)
    |
    | Note: Tarif sekarang disimpan di database (shipping_config)
    |       Config ini hanya sebagai fallback safety net
    |
    */

    'volume_divisor' => env('SHIPPING_VOLUME_DIVISOR', 6000),
    'tarif_per_km'   => env('SHIPPING_TARIF_PER_KM', 2500),

    /*
    |--------------------------------------------------------------------------
    | Geocoding Provider
    |--------------------------------------------------------------------------
    |
    | base_url : endpoint layanan geocoding.
    | Di sini contoh pakai Nominatim (OpenStreetMap).
    | Di .env bisa diganti kalau perlu.
    |
    */

    'geocoding' => [
        'base_url' => env('GEOCODING_BASE_URL', 'https://nominatim.openstreetmap.org/search'),
    ],

];



