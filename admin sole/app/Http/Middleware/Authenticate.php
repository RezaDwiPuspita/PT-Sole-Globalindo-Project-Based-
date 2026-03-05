<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware; // Middleware bawaan Laravel untuk cek autentikasi
use Illuminate\Http\Request;                               // Representasi HTTP request

class Authenticate extends Middleware
{
    /**
     * Menentukan ke mana user akan diarahkan (redirect)
     * ketika dia BELUM terautentikasi (belum login).
     *
     * Catatan penting:
     * - Jika request "mengharapkan JSON" (mis. header Accept: application/json
     *   atau berasal dari route group 'api'), fungsi ini harus mengembalikan NULL
     *   supaya Laravel mengirimkan HTTP 401 (Unauthorized) tanpa redirect (cocok untuk API/AJAX).
     * - Jika TIDAK mengharapkan JSON (umumnya request web biasa), kembalikan URL/route login
     *   agar browser diarahkan ke halaman login.
     */
    protected function redirectTo(Request $request): ?string
    {
        // $request->expectsJson() TRUE jika:
        // - Header "Accept: application/json", atau
        // - Ini route API (default Laravel men-set preferensi JSON).
        // Ketika TRUE -> return NULL, biarkan Laravel mengeluarkan 401 JSON response.
        // Ketika FALSE -> redirect ke route bernama 'login'.
        return $request->expectsJson() ? null : route('login');

        // Alternatif jika tidak menggunakan penamaan route 'login':
        // return $request->expectsJson() ? null : url('/login');
    }
}
