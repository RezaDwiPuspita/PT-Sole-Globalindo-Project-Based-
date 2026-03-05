<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    /*
    |-----------------------------------------------------------------------------
    | MASS ASSIGNMENT
    |-----------------------------------------------------------------------------
    | Catatan: pakai $fillable supaya lebih aman. Kalau mau cepat, $guarded = []
    | tetap bisa, tapi pastikan controller sudah validasi ketat.
    */
    protected $fillable = [
        'cart_id',
        'product_id',
        'custom_product_id',
        'price',
        'quantity',
        'size',
        'length',
        'width',
        'height',
        'bahan',
        'color',
        'rotan_color',
    ];

    /*
    |-----------------------------------------------------------------------------
    | CASTS (pastikan operasi matematika & komparasi dimensi konsisten)
    |-----------------------------------------------------------------------------
    | - price: float
    | - quantity: int
    | - length/width/height: float → memudahkan perbandingan & hitung total
    */
    protected $casts = [
        'price'    => 'float',
        'quantity' => 'int',
        'length'   => 'float',
        'width'    => 'float',
        'height'   => 'float',
    ];

    /*
    |-----------------------------------------------------------------------------
    | RELATIONS
    |-----------------------------------------------------------------------------
    */
    public function cart()
    {
        // FK default: cart_items.cart_id → carts.id
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        // FK default: cart_items.product_id → products.id
        return $this->belongsTo(Product::class);
    }

    public function customProduct()
    {
        // FK default: cart_items.custom_product_id → custom_products.id
        return $this->belongsTo(CustomProduct::class);
    }

    /*
    |-----------------------------------------------------------------------------
    | ACCESSOR: subtotal (price * quantity)
    |-----------------------------------------------------------------------------
    */
    public function getSubtotalAttribute()
    {
        return ($this->price ?? 0) * ($this->quantity ?? 0);
    }

    /*
    |-----------------------------------------------------------------------------
    | ACCESSOR: wood_color (alias untuk color)
    |-----------------------------------------------------------------------------
    | Frontend menggunakan wood_color, tapi di database kolomnya adalah color
    */
    public function getWoodColorAttribute()
    {
        return $this->color;
    }

    /*
    |-----------------------------------------------------------------------------
    | ACCESSOR: rattan_color (alias untuk rotan_color)
    |-----------------------------------------------------------------------------
    | Frontend menggunakan rattan_color, tapi di database kolomnya adalah rotan_color
    | (sebenarnya sudah sama, tapi untuk konsistensi tetap ditambahkan)
    */
    public function getRattanColorAttribute()
    {
        return $this->rotan_color;
    }

    /*
    |-----------------------------------------------------------------------------
    | LOCAL SCOPES: bantu controller “mencari item sejenis”
    |-----------------------------------------------------------------------------
    | scopeMatchConfig(): dipakai untuk mencocokkan MERGE KEY keranjang.
    | - Untuk produk katalog: product_id + atribut konfigurasi.
    | - Untuk custom: boleh juga dipakai kalau kamu simpan snapshot konfig di cart_items.
    |
    | Catatan:
    | - Nilai null & string kosong sering bikin pencarian beda. Kita normalisasi di mutator di bawah.
    */
    public function scopeMatchConfig($query, array $k)
    {
        return $query
            ->when(isset($k['product_id']), fn($q) => $q->where('product_id', $k['product_id']))
            ->when(array_key_exists('bahan', $k),        fn($q) => $q->where('bahan',        $k['bahan']))
            ->when(array_key_exists('length', $k),       fn($q) => $q->where('length',       (float) $k['length']))
            ->when(array_key_exists('width', $k),        fn($q) => $q->where('width',        (float) $k['width']))
            ->when(array_key_exists('height', $k),       fn($q) => $q->where('height',       (float) $k['height']))
            ->when(array_key_exists('color', $k),        fn($q) => $q->where('color',        $k['color']))
            ->when(array_key_exists('rotan_color', $k),  fn($q) => $q->where('rotan_color',  $k['rotan_color']));
    }

    /*
    |-----------------------------------------------------------------------------
    | HELPER: buildMergeKey() → kunci penggabungan item katalog
    |-----------------------------------------------------------------------------
    | Dipanggil di controller saat mau cek “sudah ada belum item dengan konfigurasi sama?”
    */
    public static function buildMergeKey(
        ?int $productId,
        ?string $bahan,
        $length,
        $width,
        $height,
        ?string $color,
        ?string $rotanColor
    ): array {
        return [
            'product_id'  => $productId,
            'bahan'       => self::normalizeEmpty($bahan),
            // supaya stabil, kita bulatkan 2 desimal (harus sesuai tipe kolom di DB)
            'length'      => is_null($length) ? null : round((float) $length, 2),
            'width'       => is_null($width)  ? null : round((float) $width, 2),
            'height'      => is_null($height) ? null : round((float) $height, 2),
            'color'       => self::normalizeEmpty($color),
            'rotan_color' => self::normalizeEmpty($rotanColor),
        ];
    }

    /*
    |-----------------------------------------------------------------------------
    | MUTATORS SEDERHANA: normalisasi string kosong → null
    |-----------------------------------------------------------------------------
    | Tujuan: query pencocokan tidak gagal hanya karena "" vs NULL.
    */
    public function setBahanAttribute($value)      { $this->attributes['bahan']       = self::normalizeEmpty($value); }
    public function setColorAttribute($value)      { $this->attributes['color']       = self::normalizeEmpty($value); }
    public function setRotanColorAttribute($value) { $this->attributes['rotan_color'] = self::normalizeEmpty($value); }

    /*
    |-----------------------------------------------------------------------------
    | UTIL: normalizeEmpty()
    |-----------------------------------------------------------------------------
    */
    protected static function normalizeEmpty($v)
    {
        // Trim spasi; jika "" → jadikan NULL
        if (is_string($v)) {
            $v = trim($v);
            return $v === '' ? null : $v;
        }
        return $v;
    }
}
