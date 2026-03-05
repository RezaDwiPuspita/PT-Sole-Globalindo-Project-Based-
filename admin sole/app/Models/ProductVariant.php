<?php

namespace App\Models; // ← namespace harus sesuai struktur folder (app/Models). Composer (PSR-4 autoloading) pakai info ini supaya class ProductVariant bisa ditemukan saat di-"use".

use Illuminate\Database\Eloquent\Factories\HasFactory; // ← trait bawaan Laravel untuk bikin factory (data dummy buat seeding / test otomatis).
use Illuminate\Database\Eloquent\Model;                // ← base class Eloquent ORM (ngasih fitur query builder, relasi antar tabel, accessors, mutators, casting, event model, dll).

/**
 * Class ProductVariant
 * ---------------------------------------------------------------------------------
 * Model ini merepresentasikan "varian" dari sebuah produk.
 *
 * Contoh kasus real:
 *   - Produk utama: Kursi Rotan "Sole"
 *   - Variants:
 *       • Warna: Coklat, Ukuran: Small, Harga: 1.200.000, Stok: 5
 *       • Warna: Natural, Ukuran: Large, Harga: 1.450.000, Stok: 2
 *
 * Biasanya tabel `product_variants` punya kolom seperti:
 *   - id                (primary key)
 *   - product_id        (FK ke tabel products)
 *   - color / warna     (opsional)
 *   - size / ukuran     (opsional)
 *   - price / harga     (bisa beda dari harga default produk induk)
 *   - stock / stok      (jumlah stok khusus varian ini)
 *
 * Catatan konvensi Laravel:
 * - Nama tabel default diasumsikan "product_variants"
 *   (plural snake_case dari nama class "ProductVariant").
 *   Kalau DAO kamu pakai nama tabel lain (misal "variants"), kamu harus override:
 *
 *       protected $table = 'variants';
 *
 * - Primary key default adalah kolom "id", auto-increment bigint.
 *   Kalau PK kamu bukan "id", misal "variant_id" atau pakai uuid string,
 *   kamu perlu atur:
 *
 *       protected $primaryKey = 'variant_id';
 *       public $incrementing = false;
 *       protected $keyType = 'string';
 *
 * - Timestamp:
 *   Secara default Laravel menganggap tabel punya kolom created_at dan updated_at.
 *   Kalau tabel kamu TIDAK punya dua kolom itu, matikan fitur timestamp:
 *
 *       public $timestamps = false;
 */
class ProductVariant extends Model
{
    use HasFactory;
    /**
     * HasFactory:
     * - Menambahkan method static factory() ke model ini.
     * - Dipakai di testing / seeding, contoh:
     *
     *     ProductVariant::factory()->count(10)->create();
     *
     * - Factory-nya biasanya ada di:
     *     database/factories/ProductVariantFactory.php
     */

    /**
     * $guarded
     * ---------------------------------------------------------------------------------
     * protected $guarded = [];
     *
     * Makna:
     * - "Mass assignment" = ngisi banyak kolom sekaligus via array.
     *   Contoh mass assignment:
     *
     *     ProductVariant::create([
     *         'product_id' => 5,
     *         'color'      => 'Natural',
     *         'size'       => 'Large',
     *         'price'      => 1450000,
     *         'stock'      => 2,
     *     ]);
     *
     * - Dengan $guarded = [], berarti TIDAK ADA kolom yang diblokir dari mass assignment.
     *   Alias: semua kolom boleh diisi lewat create()/update([...]).
     *
     * Kelebihan:
     * - Cepat buat prototyping / dev awal (tidak perlu maintain daftar kolom).
     *
     * Risiko:
     * - Kalau kamu langsung lempar $request->all() ke create(), user bisa "nyuntik"
     *   field sensitif/terlarang. Misal kolom internal seperti is_active, internal_notes,
     *   cost_price (harga modal), dsb.
     *
     * Mitigasi:
     * - Pastikan controller melakukan validasi dan hanya mengambil field yang kamu izinkan.
     *   Contoh aman:
     *
     *     $data = $request->validate([
     *         'product_id' => 'required|exists:products,id',
     *         'color'      => 'nullable|string|max:100',
     *         'size'       => 'nullable|string|max:100',
     *         'price'      => 'required|numeric|min:0',
     *         'stock'      => 'required|integer|min:0',
     *     ]);
     *
     *     ProductVariant::create($data);
     *
     * Alternatif yang lebih ketat di level model:
     * - Daripada $guarded = [], kamu bisa tulis whitelist kolom yang boleh diisi:
     *
     *     protected $fillable = [
     *         'product_id',
     *         'color',
     *         'size',
     *         'price',
     *         'stock',
     *     ];
     *
     * - Dengan $fillable, field DI LUAR daftar itu akan diabaikan walaupun ikut dikirim.
     */
    protected $guarded = [];

    /**
     * Cast kolom harga ke decimal untuk presisi
     */
    protected $casts = [
        'price' => 'decimal:2',
        'price_per_10cm' => 'decimal:2',
        'length_price'   => 'decimal:2',
        'width_price'    => 'decimal:2',
        'height_price'   => 'decimal:2',
    ];
}
