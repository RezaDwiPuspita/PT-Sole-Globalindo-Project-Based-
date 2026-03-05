<?php

namespace App\Http\Controllers\Api; 
// ← namespace: "alamat" class ini di dalam project.
//    Harus cocok dengan struktur folder (app/Http/Controllers/Api).
//    Composer autoload pakai ini supaya bisa resolve kelas ini saat kamu `use App\Http\Controllers\Api\AuthController`.

use App\Http\Controllers\Controller; 
// ← Controller base dari Laravel. Ngasih fitur helper kayak middleware().

use App\Models\User; 
// ← Import model User, supaya kita bisa akses User::where(), User::create(), dll.

use Illuminate\Http\Request; 
// ← Representasi HTTP request yang masuk (body, header, query, file upload, dll).
//    Objek ini di-inject otomatis sama Laravel ketika method dipanggil.

use Illuminate\Support\Facades\Validator; 
// ← Facade untuk class Validator. Dipakai untuk validasi manual input request.

/**
 * AuthController
 * -----------------------------------------------------------------
 * Controller ini ngurus:
 * - login   : verifikasi email/password → balikin JWT token
 * - register: daftar user baru → lalu langsung login
 * - logout  : invalidate token
 * - user    : info user yang sedang login
 *
 * Catatan tentang autentikasi:
 * - Di sini kita pakai guard 'api' (lihat auth('api')->...).
 * - Guard 'api' ini kemungkinan sudah di-setup untuk pakai JWT
 *   via tymon/jwt-auth (bukan session).
 *
 * Penting:
 * - Semua endpoint KECUALI login & register butuh token (lihat constructor).
 */
class AuthController extends Controller
{
    public function __construct()
    {
        /**
         * MIDDLEWARE AUTH
         * -----------------------------------------------------------------
         * $this->middleware('auth:api', ['except' => ['login', 'register']]);
         *
         * - middleware('auth:api'):
         *     → Pasang middleware auth dengan guard 'api'.
         *       Guard 'api' biasanya di-setup untuk JWT, jadi endpoint
         *       yang kena middleware ini hanya boleh diakses kalau request
         *       mengandung token Authorization: Bearer <token>.
         *
         * - ['except' => ['login', 'register']]:
         *     → login & register boleh diakses TANPA token (guest).
         *       Semua method lain wajib pakai token.
         */
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * login(Request $request)
     * -----------------------------------------------------------------
     * Tujuan:
     * - Validasi email & password dari request.
     * - Cek apakah email terdaftar.
     * - Coba autentikasi via guard 'api' (JWT).
     * - Kalau berhasil, balikin token JWT plus info user & expired time.
     *
     * Parameter:
     * - Request $request:
     *     Objek berisi semua data request dari client.
     *     Contoh akses:
     *       $request->email
     *       $request->password
     *
     * return:
     * - Selalu return response()->json(...), yaitu HTTP response JSON
     *   yang akan dikirim balik ke client (mobile app, frontend, dll).
     */
    public function login(Request $request)
    {
        /**
         * VALIDASI INPUT
         * -----------------------------------------------------------------
         * Validator::make($request->all(), [...])
         * - $request->all()  : ambil semua field input dari body request jadi array.
         * - Rules:
         *     'email'    => 'required|email'
         *         → wajib ada, harus format email valid.
         *     'password' => 'required|string|min:6'
         *         → wajib ada, harus string, min panjang 6.
         *
         * $validator:
         * - Objek validator berisi status validasi, error message, dll.
         */
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        // Jika validasi gagal → kita langsung balikin HTTP 422 Unprocessable Entity
        // Dengan isi error detail (field mana yang salah).
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        /**
         * CEK APA EMAIL ADA DI DATABASE
         * -----------------------------------------------------------------
         * User::where('email', $request->email)->first();
         *
         * - User::where(...):
         *     bikin query SELECT * FROM users WHERE email = ?
         *
         * - ->first():
         *     ambil row pertama sebagai instance User, atau null kalau tidak ada.
         *
         * $user:
         * - Akan berisi object User kalau ketemu,
         *   atau null kalau email tsb belum terdaftar.
         */
        $user = User::where('email', $request->email)->first();

        // Jika user tidak ditemukan → kirim pesan "Email tidak terdaftar" dengan status 401 (Unauthorized).
        // Kenapa 401? Karena kredensial dianggap tidak valid.
        if (! $user) {
            return response()->json([
                'message' => 'Email yang Anda masukkan tidak terdaftar.',
            ], 401);
        }

        /**
         * COBA LOGIN DENGAN PASSWORD
         * -----------------------------------------------------------------
         * auth('api')->attempt($credentials)
         *
         * - auth('api'):
         *     Panggil guard 'api'. Guard ini di-setup untuk JWT.
         *
         * - ->attempt([...]):
         *     Coba cocokkan email & password dengan data DB:
         *       1. Cari user dengan email tsb.
         *       2. Check bcrypt password.
         *     Kalau cocok: return string token JWT.
         *     Kalau salah : return false.
         *
         * $validator->validated():
         * - Ambil hanya field yang sudah divalidasi (email & password),
         *   bukan seluruh $request->all().
         */
        if (! $token = auth('api')->attempt($validator->validated())) {
            // Kalau password salah (tapi email valid),
            // kita balikin 401 lagi tapi pesannya beda.
            return response()->json([
                'message' => 'Kata sandi yang Anda masukkan salah.',
            ], 401);
        }

        /**
         * JIKA SUKSES LOGIN
         * -----------------------------------------------------------------
         * $token akan berisi string JWT seperti "eyJ0eXAiOiJKV1QiLCJhbGciOi..."
         *
         * Kita tidak langsung return $token mentah.
         * Kita bungkus pakai createNewToken($token),
         * supaya format respons ke client konsisten.
         */
        return $this->createNewToken($token);
    }

    /**
     * register(Request $request)
     * -----------------------------------------------------------------
     * Tujuan:
     * - Validasi data pendaftaran.
     * - Simpan user baru ke DB (password di-hash).
     * - Lalu langsung login-kan user baru itu (auto-generate JWT),
     *   supaya frontend cukup panggil /register lalu langsung dapat token.
     *
     * Kenapa auto login?
     * - Pattern UX umum: setelah daftar, user tidak perlu login ulang manual.
     */
    public function register(Request $request)
    {
        // VALIDASI DATA REGISTER
        // -------------------------------------------------------------
        // name     : wajib, string, min 2 max 100 karakter
        // email    : wajib, format email valid, max 100 char, harus unik di tabel users
        // password : wajib, min 6 karakter
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|between:2,100',
            'email'    => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
        ]);

        // Kalau validasi gagal → 400 Bad Request (bisa juga pakai 422, tergantung style API).
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        /**
         * SIMPAN USER BARU KE DATABASE
         * -----------------------------------------------------------------
         * User::create([...])
         * - bikin row baru di tabel users pakai mass assignment.
         *
         * bcrypt($request->password)
         * - password HARUS dihash. Jangan pernah simpan plaintext.
         *
         * $user:
         * - berisi object User yang baru dibuat (sudah punya id, timestamps, dll.)
         */
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
        ]);

        /**
         * LANGSUNG LOGINKAN USER BARU
         * -----------------------------------------------------------------
         * auth('api')->attempt([...]):
         * - Kita coba lagi proses login memakai kredensial baru.
         * - Kalau sukses, dapat token JWT, lalu kita return dengan format yang sama seperti login().
         *
         * Kenapa tidak pakai $validator->validated() di sini?
         * - Karena $validator di atas untuk register punya field 'name' juga.
         *   attempt() cuma perlu 'email' dan 'password'.
         */
        if ($token = auth('api')->attempt([
            'email'    => $request->email,
            'password' => $request->password,
        ])) {
            return $this->createNewToken($token);
        }

        /**
         * FALLBACK (JARANG TERJADI)
         * -----------------------------------------------------------------
         * Kalau entah kenapa attempt() gagal (misal guard salah config),
         * kita masih balikin info user baru + message sukses.
         *
         * HTTP status 201 → "Created"
         */
        return response()->json([
            'message' => 'User successfully registered',
            'user'    => $user,
        ], 201);
    }

    /**
     * logout()
     * -----------------------------------------------------------------
     * Tujuan:
     * - "Keluar" / sign-out user.
     *
     * auth()->logout():
     * - Untuk JWT (tymon/jwt-auth), ini biasanya akan menandai token saat ini
     *   sebagai invalid/blacklisted sehingga tidak bisa dipakai lagi.
     *
     * return:
     * - JSON message konfirmasi.
     */
    public function logout()
    {
        // Note: ini pakai guard default. 
        // Kalau mau lebih eksplisit dan kamu pasti pakai guard 'api', boleh pakai:
        // auth('api')->logout();
        auth()->logout();

        return response()->json([
            'message' => 'User successfully signed out',
        ]);
    }

    /**
     * user()
     * -----------------------------------------------------------------
     * Tujuan:
     * - Balikin data user yang saat ini terautentikasi pakai token JWT.
     *
     * auth('api')->user():
     * - Ambil user aktif di guard 'api' dari token Authorization Bearer.
     *
     * Penting:
     * - Method ini dilindungi oleh middleware auth:api (constructor),
     *   jadi hanya bisa diakses kalau kirim token yang valid.
     */
    public function user()
    {
        return response()->json(
            auth('api')->user() // ← instance App\Models\User dari token JWT
        );
    }

    /**
     * createNewToken($token)
     * -----------------------------------------------------------------
     * Helper private/protected untuk menyatukan format respons token.
     *
     * Parameter:
     * - $token (string):
     *     Token JWT yang dihasilkan oleh auth('api')->attempt().
     *
     * Proses:
     * - Ambil TTL (time to live) token dari config('jwt.ttl').
     *   Nilai TTL biasanya dalam MENIT.
     * - Konversi TTL ke detik (TTL * 60) → frontend/API umumnya pakai detik.
     *
     * return:
     * - response()->json([...])
     *   Kembalikan JSON berisi:
     *     access_token : token JWT mentah untuk dipakai di header Authorization
     *     token_type   : biasanya "bearer"
     *     expires_in   : waktu kedaluwarsa (detik)
     *     user         : profil user saat ini
     *
     * Cara pakai di frontend:
     * - Simpan access_token.
     * - Kirim di header setiap request berikutnya:
     *     Authorization: Bearer <access_token>
     */
    protected function createNewToken($token)
    {
        /**
         * Ambil TTL (menit) dari config
         * -----------------------------------------------------------------
         * config('jwt.ttl', 60):
         * - Ambil nilai 'ttl' dari file config/jwt.php.
         * - Kalau tidak ada, fallback default 60 (60 menit).
         *
         * Kenapa disimpan di config?
         * - Supaya gampang diubah tanpa ubah kode (cukup ubah config/env).
         */
        $ttlMinutes = (int) config('jwt.ttl', 60);

        return response()->json([
            'access_token' => $token,              // ← string JWT
            'token_type'   => 'bearer',            // ← standar penamaan token tipe Bearer
            'expires_in'   => $ttlMinutes * 60,    // ← konversi menit → detik
            'user'         => auth('api')->user(), // ← data user yang barusan login / sedang aktif
        ]);
    }
}
