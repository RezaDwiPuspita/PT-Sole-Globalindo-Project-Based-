<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;   // Tipe balikan standar HTTP
use Illuminate\Support\Facades\Auth;             // Facade untuk autentikasi
use App\Models\User;                             // (Opsional) Jika butuh konstanta/enum role

class AdminMiddleware
{
    /**
     * Middleware untuk membatasi akses hanya bagi non-user biasa.
     *
     * Ide logika:
     * - Izinkan jika pengguna sudah login (Auth::check())
     *   DAN role-nya BUKAN 'user' (artinya: 'admin', 'owner', 'super_admin', dsb. boleh lewat).
     * - Jika gagal, redirect ke route 'home'.
     *
     * @param  \Illuminate\Http\Request $request  Objek request yang masuk
     * @param  \Closure                 $next     Closure untuk meneruskan ke tahap berikutnya
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Pastikan sudah login DAN role ≠ 'user'
        // Dengan kondisi ini, semua role selain 'user' (mis. 'admin', 'owner') diizinkan.
        if (Auth::check() && Auth::user()->role !== 'user') {
            // Lolos verifikasi -> teruskan ke controller / middleware berikutnya
            return $next($request);
        }

        // Tidak memenuhi syarat:
        // - Belum login, atau
        // - Sudah login tapi role == 'user'
        // Arahkan balik ke halaman aman (home). Alternatif: abort(403) untuk "Forbidden".
        return redirect()->route('home');
        // return abort(403, 'Forbidden'); // gunakan ini jika ingin menolak tanpa redirect
    }
}
