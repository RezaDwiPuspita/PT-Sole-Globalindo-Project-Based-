<?php

namespace App\Models; // ← namespace harus sesuai struktur folder (app/Models). Dipakai autoloader & untuk "use ...".

use Illuminate\Database\Eloquent\Factories\HasFactory; // ← trait untuk membuat factory (seeding/testing).
use Illuminate\Database\Eloquent\Model;                // ← base class ORM Eloquent (menyediakan query builder, relasi, dll).
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Customer;
use App\Models\ShippingCost;

class Order extends Model
{
    use HasFactory; // ← menyertakan method dari trait HasFactory (Order::factory() untuk test/seed).

    /**
     * $guarded = []
     * - Artinya: TIDAK ada kolom yang diblokir dari mass assignment.
     * - Dampak : Boleh langsung create/update pakai array semua field yang ada di tabel.
     * - Hati-hati: Wajib pastikan validasi request di controller/FormRequest sudah ketat
     *   agar kolom sensitif tidak terisi sembarangan.
     * - Alternatif lebih aman: gunakan $fillable = ['user_id','order_date','payment_method','status','type','payment_status','tracking_number','name','phone','address','total_amount', ...];
     */
    protected $guarded = [];

    /**
     * Relasi: Order → User (many-to-one / banyak ke satu)
     * - Konsep   : Setiap order DIMILIKI OLEH satu user.
     * - Eloquent : belongsTo(User::class) berarti:
     *     • FK default di tabel "orders" adalah user_id.
     *     • Akses relasi: $order->user (akan mengembalikan 1 objek User).
     * - "return" di sini WAJIB: kita mengembalikan objek relasi (BelongsTo) agar Eloquent
     *   mengenali definisi relasi ini. Tanpa return, relasi tidak terdaftar.
     * - Operator "->" di PHP: operator untuk mengakses method/properti pada OBJEK (object operator).
     * - Penggunaan:
     *     $order = Order::with('user')->first(); // eager load (menghindari N+1)
     *     $order->user->name;                    // akses properti user terkait
     */
    public function user()
    {
        return $this->belongsTo(User::class); // ← FK: orders.user_id → users.id
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function shippingCost(): HasOne
    {
        return $this->hasOne(ShippingCost::class);
    }

    /**
     * Relasi: Order → OrderItem (one-to-many / satu ke banyak)
     * - Konsep   : Satu order punya banyak baris items (produk atau custom product).
     * - Eloquent : hasMany(OrderItem::class) berarti:
     *     • FK default di tabel "order_items" adalah order_id.
     *     • Akses relasi: $order->items (Collection<Eloquent>).
     * - "return" lagi-lagi mengembalikan objek relasi (HasMany).
     * - Penggunaan:
     *     $order = Order::with('items')->find($id);  // eager load items
     *     foreach ($order->items as $item) { ... }   // iterasi semua item
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class); // ← FK: order_items.order_id → orders.id
    }
    

}
