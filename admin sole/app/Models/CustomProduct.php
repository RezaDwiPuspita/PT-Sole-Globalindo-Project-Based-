<?php

namespace App\Models; // ← namespace harus selaras dengan struktur folder (app/Models). Dipakai autoloader & untuk 'use'.

use Illuminate\Database\Eloquent\Factories\HasFactory; // ← trait untuk factory (seeding/testing dengan model factories)
use Illuminate\Database\Eloquent\Model;                // ← base class Eloquent model (ORM Laravel)

class CustomProduct extends Model
{
    use HasFactory; // ← “menyuntikkan” method/fitur dari trait HasFactory ke class ini (memungkinkan CustomProduct::factory()).

    /**
     * $guarded = []
     * - Arti: tidak ada kolom yang diblok mass-assignment (create([...]) / update([...])).
     * - Pro: praktis saat mengisi banyak field sekaligus.
     * - Kontra: rawan jika validasi request tidak ketat (bisa tanpa sengaja mengisi kolom sensitif).
     * - Alternatif lebih aman: gunakan $fillable = ['name','material','length','width','height','wood_color','rattan_color','total_price', ...];
     */
    protected $guarded = [];

    /**
     * Relasi: CustomProduct → User (many-to-one / banyak-ke-satu).
     * - Konsep: satu custom product dimiliki oleh satu user.
     * - Eloquent default foreign key: user_id pada tabel custom_products.
     * - belongsTo(User::class):
     *    • 'belongsTo' = relasi anak → induk (FK ada di tabel model ini).
     *    • Argumen User::class → string nama kelas penuh (FQCN) "App\Models\User" (pakai ::class agar aman saat refactor).
     * - return ... :
     *    • wajib mengembalikan objek relasi (Illuminate\Database\Eloquent\Relations\BelongsTo) agar Eloquent tahu definisinya.
     * - Pemakaian:
     *    • $custom->user            (lazy load jika belum di-eager-load)
     *    • CustomProduct::with('user')->get()  (eager load untuk hindari N+1 query)
     * - Operator '->' di PHP: memanggil method/properti pada OBJEK (object operator).
     */
    public function user()
    {
        return $this->belongsTo(User::class); // ← return di sini penting; tanpa return Eloquent tak mengenali relasi ini.
    }

    /**
     * Relasi: CustomProduct → OrderItem (one-to-many / satu-ke-banyak).
     * - Konsep: satu custom product bisa muncul di banyak baris order_items (misal di beberapa pesanan).
     * - hasMany(OrderItem::class):
     *    • 'hasMany' = relasi induk → banyak anak (FK default order_items.custom_product_id).
     * - Akses:
     *    • $custom->orderItems        (Collection)
     *    • CustomProduct::with('orderItems')->find($id)
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class); // ← return objek relasi ‘HasMany’ ke Eloquent.
    }

    /**
     * Relasi: CustomProduct → CartItem (one-to-many).
     * - Konsep: custom product juga bisa tampil di beberapa cart item.
     * - hasMany(CartItem::class) → FK default cart_items.custom_product_id.
     * - Akses:
     *    • $custom->cartItems (Collection)
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class); // ← Eloquent tahu harus mencari cart_items.custom_product_id = $this->id
    }

    /**
     * Metode statis: calculatePrice(...)
     * Tujuan  :
     *   - Menghitung total harga produk custom berdasarkan:
     *     • material   → menentukan tarif per “unit” dimensi,
     *     • dimensi    → length/width/height (cm), dihitung (dimensi/10) * tarif,
     *     • woodColor  → biaya tetap tambahan (opsional),
     *     • rattanColor→ biaya tetap tambahan (opsional).
     *
     * Parameter:
     *   $material     (string)       : 'Kayu Jati' | 'Kayu Jati & Rotan' (harus ada di $materialPrices).
     *   $length       (number)       : panjang (cm).
     *   $width        (number)       : lebar (cm).
     *   $height       (number)       : tinggi (cm).
     *   $woodColor    (string|null)  : warna kayu opsional; key harus dikenali di $woodColorPrices jika diisi.
     *   $rattanColor  (string|null)  : warna rotan opsional; key harus dikenali di $rattanColorPrices jika diisi.
     *
     * Return:
     *   (number) total harga (integer/float).
     *
     * Catatan:
     * - “static function” artinya method dipanggil lewat kelas (CustomProduct::calculatePrice(...)) tanpa perlu instance.
     * - Validasi berat dilakukan di Controller/FormRequest. Di sini hanya ada guard ringan agar aman dari input tidak valid.
     * - Jika skema tarif sering berubah, idealnya dipindah ke config() agar mudah disetel tanpa redeploy (lihat catatan bawah).
     */
    public static function calculatePrice($material, $length, $width, $height, $woodColor = null, $rattanColor = null)
    {
        // Tarif per material (per 10 cm).
        $materialPrices = [
            'Kayu Jati' => [
                'length' => 14000, // biaya per (length/10)
                'width'  => 14000, // biaya per (width/10)
                'height' => 14000, // biaya per (height/10)
            ],
            'Kayu Jati & Rotan' => [
                'length' => 20000,
                'width'  => 20000,
                'height' => 20000,
            ],
        ];

        // Biaya tambahan fixed untuk warna kayu
        $woodColorPrices = [
            'Natural Jati' => 60000,
            'Walnut Brown' => 80000,
            'Coklat Salak' => 90000,
        ];

        // Biaya tambahan fixed untuk warna rotan
        $rattanColorPrices = [
            'Merah'  => 50000,
            'Putih'  => 50000,
            'Coklat' => 50000,
            'Hitam'  => 50000,
        ];

        // ---------- Guard/validasi ringan (power-off default tetap aman) ----------
        if (! isset($materialPrices[$material])) {
            // Jika material tak dikenali, bisa juga lempar exception sesuai kebijakan aplikasi.
            // throw new \InvalidArgumentException("Material tidak dikenal: {$material}");
            return 0; // fallback aman
        }

        // Pastikan dimensi angka & > 0 (hindari negatif/NaN).
        $length = (float) $length;
        $width  = (float) $width;
        $height = (float) $height;

        if ($length <= 0 || $width <= 0 || $height <= 0) {
            return 0; // fallback aman (bisa juga dilempar exception)
        }
        // --------------------------------------------------------------------------

        // Hitung komponen harga dimensi.
        // Rumus: (dimensi / 10) * tarif_per_unit
        $lengthPrice = ($length / 10) * $materialPrices[$material]['length'];
        $widthPrice  = ($width  / 10) * $materialPrices[$material]['width'];
        $heightPrice = ($height / 10) * $materialPrices[$material]['height'];

        // Total dasar dari dimensi
        $total = $lengthPrice + $widthPrice + $heightPrice;

        // Tambah biaya warna kayu (jika ada & valid)
        if ($woodColor && isset($woodColorPrices[$woodColor])) {
            $total += $woodColorPrices[$woodColor];
        }

        // Tambah biaya warna rotan (jika ada & valid)
        if ($rattanColor && isset($rattanColorPrices[$rattanColor])) {
            $total += $rattanColorPrices[$rattanColor];
        }

        return $total; // ← “return” mengembalikan angka ke pemanggil (Controller/Service yang membutuhkan total harga)
    }
}
