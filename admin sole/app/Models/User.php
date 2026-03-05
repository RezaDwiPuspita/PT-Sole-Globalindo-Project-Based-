<?php

namespace App\Models; // ← Namespace harus mengikuti struktur folder (app/Models) agar autoload Composer (PSR-4) bekerja.

use Illuminate\Database\Eloquent\Factories\HasFactory; // ← Trait untuk mengaktifkan factory() (seeding & testing).
use Illuminate\Foundation\Auth\User as Authenticatable; // ← Base class user yang sudah include fitur autentikasi (password).
use Illuminate\Notifications\Notifiable; // ← Trait untuk mengirim notifikasi (mail, database, dll).
use Laravel\Sanctum\HasApiTokens; // ← Trait token-based auth via Laravel Sanctum (personal access tokens).
use Tymon\JWTAuth\Contracts\JWTSubject; // ← Interface agar model bisa dikelola oleh tymon/jwt-auth (JWT).

/**
 * Model User
 * - Mewarisi Authenticatable → otomatis punya integrasi dengan Auth (guard "web" / "api").
 * - Mengimplementasikan JWTSubject → agar bisa dipakai login via JWT (tymon/jwt-auth).
 * - Memakai HasApiTokens → jika juga butuh Sanctum (bisa ko-eksis, tapi pilih salah satu di runtime).
 *
 * Catatan penting:
 * - Memakai dua mekanisme token (Sanctum + JWT) sekaligus itu boleh, tapi pastikan guard & middleware
 *   tidak “silang-sengkarut”. Di controller, gunakan guard eksplisit: auth('api') untuk JWT, atau sanctum
 *   middleware untuk Sanctum. Jangan campur satu endpoint validasi Sanctum, endpoint lain JWT, tanpa rencana.
 */
class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable; 
    // HasApiTokens   : generator/validasi token untuk Sanctum.
    // HasFactory     : aktifkan User::factory() (uji/unit test & seeding).
    // Notifiable     : dukungan kirim notifikasi (via Notification system).

    /**
     * $guarded = []
     * - Artinya: tidak ada kolom yang “diblokir” saat mass-assignment (create/update dengan array).
     * - PRO: Praktis saat pengembangan.
     * - KONTRA: WAJIB validasi request ketat agar kolom sensitif (mis. role, is_admin, dsb.) tidak bisa diisi sembarangan.
     * - Alternatif aman: gunakan $fillable = ['name','email','password', ...]; dan HINDARI $guarded = [] di produksi.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * $hidden
     * - Kolom yang disembunyikan saat model diserialisasi ke array/JSON (mis. response API).
     * - 'password' : jangan pernah tampilkan hash password ke klien.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * $casts
     * - Casting atribut ke tipe PHP saat diambil/di-set.
     * - 'password' => 'hashed' : (Laravel 10+) setter otomatis hash nilai yang di-set (bcrypt) saat di-assign.
     *   Contoh: $user->password = 'secret'; // otomatis di-hash, tidak perlu manual bcrypt().
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    /* =========================================================================
     | Relasi Eloquent
     |==========================================================================
     | Catatan umum:
     | - Semua method relasi harus "return" objek relasi Eloquent (HasMany, HasOne, BelongsTo, dll).
     | - Akses relasi: $user->carts (Collection), $user->orders (Collection), dst.
     | - Eager load untuk hindari N+1: User::with('carts.items')->find($id)
     |========================================================================= */

    /**
     * Relasi: User → Cart (one-to-many)
     * - Satu user bisa punya banyak cart (riwayat keranjang).
     * - FK default: carts.user_id → users.id
     * - Return tipe: Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * Relasi: User → Cart (one-to-one bersyarat: cart aktif saja)
     * - hasOne(Cart::class) lalu dibatasi where('is_active', true) → hanya 1 cart aktif per user.
     * - Perhatikan: method relasi ini me-return Builder relasi (HasOne) dengan kondisi → saat akses $user->activeCart
     *   tanpa eager load, Eloquent akan mengeksekusi query untuk mengambil baris yang match kondisi.
     * - Jika bisa ada >1 “aktif” (seharusnya tidak), pertimbangkan menambahkan unique constraint di DB.
     */
    public function activeCart()
    {
        return $this->hasOne(Cart::class)->where('is_active', true);
    }

    /**
     * Relasi: User → Order (one-to-many)
     * - Satu user punya banyak order.
     * - FK default: orders.user_id → users.id
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Relasi: User → CustomProduct (one-to-many)
     * - Jika user dapat menyimpan/merancang banyak produk custom.
     * - FK default: custom_products.user_id → users.id
     */
    public function customProducts()
    {
        return $this->hasMany(CustomProduct::class);
    }

    /* =========================================================================
     | Implementasi JWTSubject (untuk tymon/jwt-auth)
     |==========================================================================
     | Dua method di bawah wajib ada saat implements JWTSubject:
     | 1) getJWTIdentifier()     : nilai unik yang disimpan di claim "sub" token JWT (biasanya primary key user).
     | 2) getJWTCustomClaims()   : array claim tambahan kustom (opsional, seringnya [] saja).
     |========================================================================= */

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     * - “return” di sini adalah nilai unik user (biasanya primary key).
     * - $this->getKey() → helper bawaan Eloquent untuk ambil PK model.
     */
    public function getJWTIdentifier()
    {
        return $this->getKey(); // ← sama dengan $this->id
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     * - Tambahkan claim kustom jika diperlukan, mis. ['role' => $this->role]
     * - Kosongkan jika tidak perlu (aman default []).
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
