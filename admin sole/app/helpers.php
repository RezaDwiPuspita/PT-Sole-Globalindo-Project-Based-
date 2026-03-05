<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;

if (!function_exists('isNavbarActive')) {
    function isNavbarActive($routes)
    {
        foreach ((array) $routes as $route) {
            if (Str::contains(Route::currentRouteName(), $route)) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('mapStatusOrder')) {
    function mapStatusOrder($status)
    {
        $statusMap = [
            'in_cart' => 'Dalam Keranjang',
            'processing' => 'Diproses',
            'received' => 'Diterima',
            'in_progress' => 'Sedang dikerjakan',
            'completed' => 'Selesai',
            'sending' => 'Dikirim',
            'cancelled' => 'Dibatalkan',
        ];

        return $statusMap[$status] ?? $status;
    }
}
