<?php

namespace App\Models; // ← namespace harus sesuai struktur folder (app/Models). Dipakai autoloader Composer.

use Illuminate\Database\Eloquent\Factories\HasFactory; // ← trait untuk factory (membuat data dummy untuk test/seed).
use Illuminate\Database\Eloquent\Model;                // ← base class Eloquent ORM (query builder, relasi, events, dll).

class OrderItem extends Model
{
    use HasFactory; // ← mengaktifkan OrderItem::factory() untuk keperluan seeding/testing.

    /**
     * $guarded = []
     * - Artinya: TIDAK ada kolom yang diblokir mass assignment.
     * - Dampak : Kamu bisa langsung create/update pakai array berisi field apa pun dari tabel ini.
     * - Risiko : Wajib pastikan input tervalidasi (di Controller/FormRequest) agar kolom sensitif tidak terisi sembarang.
     * - Alternatif aman: pakai $fillable = ['order_id','product_id','custom_product_id','quantity','price','size','material','wood_color','rattan_color','length','width','height'];
     */
    protected $guarded = [];

    /**
     * Relasi: OrderItem → Order (many-to-one / banyak ke satu)
     * - Konsep   : Setiap baris item milik SATU order.
     * - Eloquent : belongsTo(Order::class) berarti:
     *     • FK default di tabel order_items adalah 'order_id' (mengarah ke orders.id).
     *     • Akses: $item->order (satu objek Order).
     * - "return" di sini WAJIB untuk mengembalikan objek relasi (Illuminate\Database\Eloquent\Relations\BelongsTo)
     *   agar Eloquent mendaftarkan relasi ini. Tanpa return, relasi tidak dikenali.
     * - Operator "->" pada PHP: mengakses method/properti pada sebuah OBJEK.
     * - Contoh pakai:
     *     $item = OrderItem::with('order')->first(); // eager load
     *     $no   = $item->order->tracking_number;
     */
    public function order()
    {
        return $this->belongsTo(Order::class); // ← FK: order_items.order_id → orders.id
    }

    /**
     * Relasi: OrderItem → Product (opsional)
     * - Konsep   : Jika item berasal dari katalog produk standar, maka kolom product_id terisi.
     * - Eloquent : belongsTo(Product::class) → FK default 'product_id' pada tabel order_items.
     * - Akses    : $item->product (1 objek Product atau null kalau bukan produk katalog).
     * - Catatan  : Pada desain ini, OrderItem bisa mewakili dua jenis:
     *              1) produk katalog (product_id terisi), atau
     *              2) produk custom (custom_product_id terisi).
     *              Biasanya salah satu saja yang terisi (exclusive).
     * - Contoh:
     *     $items = OrderItem::with('product')->get();
     *     foreach ($items as $i) {
     *         echo optional($i->product)->title; // optional() supaya aman jika null
     *     }
     */
    public function product()
    {
        return $this->belongsTo(Product::class); // ← FK: order_items.product_id → products.id
    }

    /**
     * Relasi: OrderItem → CustomProduct (opsional)
     * - Konsep   : Jika item adalah produk custom, kolom custom_product_id terisi.
     * - Eloquent : belongsTo(CustomProduct::class) → FK default 'custom_product_id' pada order_items.
     * - Akses    : $item->customProduct (1 objek CustomProduct atau null).
     * - Contoh:
     *     $items = OrderItem::with('customProduct')->get();
     *     foreach ($items as $i) {
     *         echo optional($i->customProduct)->material;
     *     }
     */
    public function customProduct()
    {
        return $this->belongsTo(CustomProduct::class); // ← FK: order_items.custom_product_id → custom_products.id
    }
}