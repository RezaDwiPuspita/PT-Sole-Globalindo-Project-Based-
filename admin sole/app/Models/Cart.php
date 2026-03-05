<?php

namespace App\Models;  
// ← namespace harus sesuai struktur folder (app/Models) supaya autoload Composer (PSR-4) bisa nemuin class Cart.

// Import bawaan Laravel yang kita pakai di model ini:
use Illuminate\Database\Eloquent\Factories\HasFactory; // ← trait untuk bikin factory model (seeding/testing otomatis).
use Illuminate\Database\Eloquent\Model;                // ← base class Eloquent ORM (fitur query builder, relasi, accessor, dsb).

class Cart extends Model
{
    use HasFactory; 
    /**
     * HasFactory:
     * - Memberi method static factory() → dipakai di seeding & unit test.
     *   Contoh:
     *      Cart::factory()->create([
     *          'user_id'   => 5,
     *          'is_active' => true,
     *      ]);
     */

    /**
     * $guarded = []
     * ---------------------------------------------------------------------------------
     * protected $guarded = [];
     *
     * Artinya:
     * - "Mass assignment" itu saat kamu langsung lempar array ke create()/update(), contoh:
     *
     *      Cart::create([
     *          'user_id'   => 123,
     *          'is_active' => true,
     *      ]);
     *
     * - Dengan $guarded = [], TIDAK ADA kolom yang diblokir untuk mass assignment.
     *   Alias: semua kolom boleh diisi lewat create([...]) / update([...]).
     *
     * Kelebihan:
     * - Simpel banget buat prototype / API internal yang sudah tervalidasi.
     *
     * Risiko keamanan:
     * - Kalau kamu langsung pakai user input mentah ($request->all()) tanpa filter,
     *   user bisa "nyuntik" field yang seharusnya tidak boleh mereka atur.
     *   Misal: mereka ngirim is_active=true buat "mengaktifkan" cart yang bukan miliknya.
     *
     * Alternatif yang lebih aman di production:
     * ---------------------------------------------------------------------------------
     * Daripada pakai $guarded = [], kamu bisa whitelist kolom yang boleh diisi:
     *
     *     protected $fillable = [
     *         'user_id',
     *         'is_active',
     *     ];
     *
     * Kalau pakai $fillable, field lain diabaikan walaupun dikirim.
     */
    protected $guarded = [];

    /**
     * RELASI: Cart -> User  (many-to-one / banyak ke satu)
     * ---------------------------------------------------------------------------------
     * public function user()
     * {
     *     return $this->belongsTo(User::class);
     * }
     *
     * Makna relasi:
     * - Satu Cart dimiliki oleh SATU user.
     * - Secara default Laravel akan cari kolom 'user_id' di tabel carts sebagai foreign key
     *   yang menunjuk ke kolom 'id' di tabel users.
     *
     * Tipe return:
     * - belongsTo(...) akan mengembalikan instance
     *   Illuminate\Database\Eloquent\Relations\BelongsTo.
     *
     * Cara pakai:
     * - $cart->user → ngasih model User yang punya keranjang ini.
     *   (lazy load: akan jalanin query tambahan kalau belum di-eager-load).
     *
     * - Untuk efisiensi saat ambil banyak cart sekaligus:
     *      $carts = Cart::with('user')->get();
     *   Jadi semua user langsung diambil sekalian (menghindari N+1 query problem).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * RELASI: Cart -> CartItem (one-to-many / satu ke banyak)
     * ---------------------------------------------------------------------------------
     * public function items()
     * {
     *     return $this->hasMany(CartItem::class);
     * }
     *
     * Makna relasi:
     * - Satu Cart berisi banyak baris item (produk yang ditaruh user ke keranjang).
     * - Secara default Laravel mengasumsikan tabel cart_items punya kolom 'cart_id'
     *   yang refer ke carts.id.
     *
     * Tipe return:
     * - hasMany(...) akan mengembalikan
     *   Illuminate\Database\Eloquent\Relations\HasMany.
     *
     * Cara pakai:
     * - $cart->items → Collection (bisa berisi 0..n CartItem).
     * - Loop:
     *      foreach ($cart->items as $item) {
     *          echo $item->quantity;
     *      }
     *
     * Optimasi query:
     * - Kalau kamu mau menampilkan cart beserta item dan product-nya:
     *      $cart = Cart::with('items.product')->find($idUserCart);
     *   Ini cegah query berulang-ulang.
     */
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * ACCESSOR (Attribute Virtual): getTotalAttribute()
     * ---------------------------------------------------------------------------------
     * public function getTotalAttribute()
     * {
     *     return ...;
     * }
     *
     * Konvensi nama accessor:
     * - get{StudlyName}Attribute → dipanggil sebagai properti snake_case.
     *   Jadi getTotalAttribute() otomatis bikin properti virtual $cart->total.
     *
     * Tujuan:
     * - Menghitung total belanja dalam keranjang ini.
     * - Rumus: jumlahkan price * quantity untuk setiap item di keranjang.
     *
     * Implementasi:
     * - $this->items adalah Collection<CartItem>.
     * - Collection::sum(callback) akan iterasi semua item, lalu nge-sum nilai return dari callback.
     *
     * Soal performa:
     * - Kalau $this->items belum diload, akses $cart->total akan menyebabkan relasi items di-load
     *   dari database (1 query tambahan).
     *
     * - Kalau kamu mau hitung total banyak cart sekaligus (misal daftar semua cart user),
     *   lebih efisien eager load dulu:
     *
     *      $carts = Cart::with('items')->get();
     *      foreach ($carts as $c) {
     *          echo $c->total; // tidak nambah query lagi
     *      }
     *
     * Catatan tipe data:
     * - Pastikan kolom price (di CartItem) adalah numeric (int/float), dan quantity int,
     *   supaya operasi $item->price * $item->quantity tidak jadi "string * string".
     *   Kamu bisa enforce ini dengan $casts di model CartItem.
     */
    public function getTotalAttribute()
    {
        return $this->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });
    }

    /**
     * QUERY SCOPE: scopeActive()
     * ---------------------------------------------------------------------------------
     * public function scopeActive($query)
     * {
     *     return $query->where('is_active', true);
     * }
     *
     * Apa itu "local scope"?
     * - Fitur Laravel: method diawali "scope" + NamaStudlyCase akan menjadi query scope khusus.
     * - Cara pakai di luar:
     *      Cart::active()->get();
     *   Alih-alih nulis:
     *      Cart::where('is_active', true)->get();
     *
     * Kenapa enak?
     * - DRY: menghindari duplikat kondisi where yang sama di banyak tempat.
     * - Membaca lebih semantik: "ambil cart aktif saja".
     *
     * Catatan teknis:
     * - Parameter pertama $query adalah instance Illuminate\Database\Eloquent\Builder.
     * - Kamu boleh tambahkan filter lanjutan, misal: ->where('user_id', $userId).
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
