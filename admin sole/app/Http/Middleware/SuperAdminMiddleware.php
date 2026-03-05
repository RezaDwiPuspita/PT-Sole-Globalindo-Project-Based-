<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Facade untuk autentikasi user yang sedang login
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * $request  : objek HTTP Request yang masuk (URL, method, header, user, dll)
     * $next     : closure yang melanjutkan request ke middleware/handler berikutnya
     * return    : Response (boleh lanjut atau diblok/redirect)
     *
     * Catatan:
     * - Middleware ini memeriksa role user. Jika role sesuai, request diteruskan.
     * - Jika tidak, user diarahkan (redirect) ke route 'admin.index'.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Auth::check()  -> memastikan ada user yang sudah login
        // Auth::user()   -> mengambil instance user yang sedang login
        // ->role === 'admin' -> hanya izinkan user dengan role 'admin'
        if (Auth::check() && Auth::user()->role === 'admin') {
            // Jika syarat terpenuhi, lanjut ke proses berikutnya (controller/next middleware)
            return $next($request);
        }

        // Jika tidak memenuhi syarat, arahkan user ke dashboard admin (atau halaman lain yang aman)
        // Kamu juga bisa ganti dengan abort(403) bila ingin menolak akses tanpa redirect:
        // return abort(403, 'Unauthorized');
        return redirect()->route('admin.index');
    }
}
