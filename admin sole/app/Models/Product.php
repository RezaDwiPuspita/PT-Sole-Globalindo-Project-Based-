<?php

namespace App\Models; // ← namespace harus sesuai struktur folder (app/Models). Dipakai Composer autoload biar class ini bisa ditemukan otomatis.

use Illuminate\Database\Eloquent\Factories\HasFactory; // ← trait untuk bikin factory (seeding/testing): Product::factory().
use Illuminate\Database\Eloquent\Model;                // ← base class Eloquent ORM (CRUD, relasi antar tabel, casts, events, dll).
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory; 
    // HasFactory:
    // - nambahin method factory() ke model ini.
    // - dipakai saat testing / seeding database dengan data dummy.
    //   contoh: Product::factory()->count(10)->create();

    /**
     * $guarded = []
     * ---------------------------------------------------------------------------------
     * - Artinya: tidak ada kolom yang diblokir untuk "mass assignment".
     *   Mass assignment = create()/update() pakai array langsung.
     *   Contoh:
     *     Product::create([
     *         'title' => 'Meja Rotan',
     *         'harga' => 1500000,
     *         'stok'  => 3,
     *     ]);
     *
     * - Karena $guarded = [], SEMUA kolom boleh diisi seperti itu.
     * - Ini praktis banget saat dev awal, tapi punya risiko security:
     *   kalau ada field sensitif (misal: is_admin, is_active, dsb.) bisa ikut terisi tanpa filter.
     *
     * - Alternatif yang lebih aman:
     *     protected $fillable = ['title','nama','harga','stok','size','gambar', ...];
     *   => dengan $fillable kita whitelist field mana yang boleh di-mass assign.
     *
     * - Kesimpulan:
     *   $guarded = [] oke selama:
     *     1) input user sudah divalidasi di controller / FormRequest,
     *     2) tabel products tidak punya kolom sensitif yang tidak boleh diedit user biasa.
     */
    protected $guarded = [];

    /**
     * Relasi: Product → ProductVariant (One-to-Many)
     * ---------------------------------------------------------------------------------
     * - Makna bisnis:
     *   Satu produk utama bisa punya banyak variasi. 
     *   Misalnya:
     *     Product: "Kursi Rotan"
     *     Variants:
     *       - warna: Coklat, ukuran: Small, stok: 12
     *       - warna: Natural, ukuran: Medium, stok: 4
     *
     * - Eloquent:
     *   return $this->hasMany(ProductVariant::class);
     *
     *   Ini berarti:
     *   - Laravel menganggap tabel `product_variants` punya kolom `product_id`
     *     yg jadi foreign key ke tabel `products.id`.
     *
     * - Cara akses di code:
     *   $product = Product::with('variants')->find($id);
     *   foreach ($product->variants as $variant) {
     *       echo $variant->color;
     *   }
     *
     * - Kenapa pakai return?
     *   Karena method relasi WAJIB me-return instance relasi,
     *   dalam hal ini Illuminate\Database\Eloquent\Relations\HasMany.
     *
     * - Catatan performa:
     *   Kalau kamu loop banyak Product dan tiap iterasi akses ->variants TANPA eager loading,
     *   kamu akan kena N+1 query problem.
     *   Solusi: pakai with('variants') seperti contoh di atas.
     */
    public function variants()
    {
        return $this->hasMany(ProductVariant::class); // FK default: product_variants.product_id → products.id
    }

    /**
     * Relasi: Product → CartItem (One-to-Many)
     * ---------------------------------------------------------------------------------
     * - Makna bisnis:
     *   Produk ini bisa muncul di banyak keranjang belanja (cart_items).
     *   Contoh:
     *     CartItem:
     *       cart_id = 5
     *       product_id = 3  // -> menunjuk ke produk ini
     *       quantity = 2
     *
     * - Eloquent:
     *   return $this->hasMany(CartItem::class);
     *
     *   Artinya:
     *   - Tabel `cart_items` harus punya kolom `product_id`
     *     yang mengacu ke `products.id`.
     *
     * - Cara akses:
     *   $product = Product::with('cartItems')->find($id);
     *   $diKeranjang = $product->cartItems->sum('quantity'); // total kuantitas di semua keranjang
     *
     * - Catatan performa:
     *   Sama seperti relasi lain, kalau ambil banyak Product lalu akses ->cartItems
     *   satu per satu tanpa eager load, bakal kena N+1.
     *   Solusi: gunakan Product::with('cartItems')->get().
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class); // FK default: cart_items.product_id → products.id
    }

    /**
     * Relasi: Product → OrderItem (One-to-Many)
     * ---------------------------------------------------------------------------------
     * - Makna bisnis:
     *   Produk ini bisa muncul di banyak pesanan (order_items).
     *   Contoh:
     *     OrderItem:
     *       order_id = 88
     *       product_id = 5  // -> menunjuk ke produk ini
     *       quantity = 3
     *
     * - Eloquent:
     *   return $this->hasMany(OrderItem::class);
     *
     *   Artinya:
     *   - Tabel `order_items` harus punya kolom `product_id`
     *     yang mengacu ke `products.id`.
     *
     * - Cara akses:
     *   $product  = Product::with('orderItems')->find($id);
     *   $terjual  = $product->orderItems->sum('quantity'); // total kuantitas terjual
     *   $pendapatan = $product->orderItems->sum(fn ($row) => $row->price * $row->quantity);
     *
     * - Kenapa ini berguna?
     *   Ini bikin analitik gampang banget:
     *   - Hitung total terjual per product.
     *   - Hitung omset per product.
     *
     * - Catatan performa:
     *   Sama seperti relasi lain, kalau ambil banyak Product lalu akses ->orderItems
     *   satu per satu tanpa eager load, bakal kena N+1.
     *   Solusi: gunakan Product::with('orderItems')->get().
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class); // FK default: order_items.product_id → products.id
    }

        /**
     * Relasi: Product → Color (Many-to-Many lewat color_product)
     * ---------------------------------------------------------------------------------
     * - Satu produk bisa punya banyak warna (kayu dan rotan).
     * - Satu warna (misal "Natural Jati") bisa dipakai banyak produk.
     * - Data tambahan di pivot:
     *     extra_price → tambahan harga untuk warna ini di produk ini
     *     is_default  → penanda warna default di pilihan user
     */
    public function colors(): BelongsToMany
    {
        return $this->belongsToMany(Color::class)
            ->withPivot('extra_price', 'is_default')
            ->withTimestamps();
    }

    /**
     * Helper: hanya warna KAYU (type = 'wood')
     */
    public function woodColors(): BelongsToMany
    {
        return $this->colors()->where('colors.type', 'wood');
    }

    /**
     * Helper: hanya warna ROTAN (type = 'rattan')
     */
    public function rattanColors(): BelongsToMany
    {
        return $this->colors()->where('colors.type', 'rattan');
    }

}
