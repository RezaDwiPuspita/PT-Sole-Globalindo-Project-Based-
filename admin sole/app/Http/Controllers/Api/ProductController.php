<?php

namespace App\Http\Controllers\Api;  // ← namespace harus cocok dengan lokasi file (app/Http/Controllers/Api)
//    Ini penting supaya Composer autoload bisa menemukan class ini ketika dipanggil.

use App\Http\Controllers\Controller;   // ← base controller dari Laravel
//    Controller ini biasanya punya helper bawaan (authorize(), middleware(), dsb).
//    Dengan extend Controller, ProductController ikut mewarisi kemampuan itu.

use App\Models\Product;                // ← Model Eloquent "Product" (tabel products)
//    Dipakai untuk query data produk dari database.

use Illuminate\Http\Request;           // ← Representasi HTTP request masuk (inputan user, query string, header, dsb).
//    Di controller ini, Request belum dipakai langsung,
//    tapi biasanya disiapkan kalau nanti mau tambah filter, search, pagination, dsb.

/**
 * ProductController (API)
 * ------------------------------------------------------------------
 * Controller ini menyediakan endpoint API publik (JSON) terkait produk.
 *
 * Endpoint yang dicakup di sini:
 *
 * 1. index() → GET /api/products
 *    - Ambil semua produk + semua variasi (variants) setiap produk.
 *
 * 2. show($product) → GET /api/products/{product}
 *    - Ambil satu produk berdasarkan ID (route model binding),
 *      lalu ikutkan data variants milik produk itu.
 *
 * Catatan penting:
 * - Respon memakai response()->json(...), jadi cocok dipakai oleh frontend mobile/SPA.
 * - Kita pakai eager loading relasi 'variants' buat hindari masalah N+1 query.
 *   (N+1 query = performa jelek karena query DB berulang2 per row.)
 */
class ProductController extends Controller
{
    /**
     * GET /api/products
     * ------------------------------------------------------------------
     * Tujuan:
     * - Mengembalikan daftar SEMUA produk dalam bentuk JSON,
     *   termasuk relasi 'variants' masing-masing product.
     *
     * Detail teknis:
     * - Product::with('variants')->get()
     *      • with('variants'):
     *          - "Eager load" relasi.
     *          - Laravel akan lakukan JOIN terpisah di depan (2 query besar),
     *            bukan 1 query per product saat di-loop. Ini jauh lebih efisien.
     *      • ->get():
     *          - Eksekusi query dan mengembalikan Collection<Product>.
     *
     * - $products akan jadi collection berisi banyak Product Eloquent Model.
     *   Masing-masing Product sudah punya properti $product->variants (Collection<ProductVariant>).
     *
     * - response()->json($products):
     *      • Mengubah Collection model Eloquent menjadi JSON.
     *      • Laravel otomatis serialize atribut model dan relasinya.
     *      • Status HTTP default = 200 OK.
     *
     * Return:
     * - HTTP 200 + body JSON array berisi daftar produk.
     */
    public function index()
    {
        // Ambil semua produk beserta relasi "variants"-nya dari DB
        // Hanya ambil variants dengan type "material" untuk frontend
        $products = Product::with([
            'variants' => function($query) {
                $query->where('type', 'material');
            },
            'woodColors',
            'rattanColors'
        ])->get();

        // Format response sesuai kebutuhan frontend
        return response()->json($products->map(function($product) {
            return [
                'id' => $product->id,
                'title' => $product->title,
                'price' => $product->price,
                'size' => $product->size,
                'description' => $product->description,
                'display_image' => $product->display_image,
                'default_bahan' => $product->default_bahan,
                'default_color' => $product->default_color,
                'default_rotan_color' => $product->default_rotan_color,
                'default_length' => $product->default_length,
                'default_width' => $product->default_width,
                'default_height' => $product->default_height,
                'variants' => $product->variants->map(function($variant) {
                    return [
                        'type' => $variant->type,
                        'name' => $variant->name,
                        'price_per_10cm' => $variant->price_per_10cm,
                        'length_price' => $variant->length_price,
                        'width_price' => $variant->width_price,
                        'height_price' => $variant->height_price,
                    ];
                }),
                'wood_colors' => $product->woodColors->map(function($color) {
                    return [
                        'name' => $color->name,
                        'pivot' => [
                            'extra_price' => $color->pivot->extra_price ?? 0,
                            'is_default' => $color->pivot->is_default ?? false,
                        ]
                    ];
                }),
                'rattan_colors' => $product->rattanColors->map(function($color) {
                    return [
                        'name' => $color->name,
                        'pivot' => [
                            'extra_price' => $color->pivot->extra_price ?? 0,
                            'is_default' => $color->pivot->is_default ?? false,
                        ]
                    ];
                }),
            ];
        }));
    }

    /**
     * GET /api/products/{product}
     * ------------------------------------------------------------------
     * Tujuan:
     * - Mengembalikan detail SATU produk (berdasarkan ID yang dikirim di URL),
     *   beserta daftar variants miliknya.
     *
     * Parameter:
     * - Product $product
     *   • Ini disebut "Route Model Binding".
     *   • Artinya: jika route kamu didefinisikan seperti
     *       Route::get('/api/products/{product}', [ProductController::class, 'show']);
     *     maka {product} di URL (misal /api/products/7) akan otomatis dicari di DB (SELECT * FROM products WHERE id=7).
     *   • Jika tidak ketemu → Laravel otomatis akan return 404 Not Found.
     *
     * Kenapa masih perlu $product->load('variants')?
     * - Karena route model binding hanya ambil row products.
     * - Relasi variants belum di-load.
     * - load('variants') akan melakukan eager load RELASI UNTUK SATU INSTANCE yang sudah ada.
     *   (Beda dengan with('variants') yang dipakai di query builder sebelum get()).
     *
     * Return:
     * - HTTP 200 + JSON untuk 1 product, lengkap dengan field variants.
     */
    public function show(Product $product)
    {
        // Muat relasi 'variants' untuk produk ini agar $product->variants langsung siap dipakai
        // Hanya ambil variants dengan type "material" untuk frontend
        $product->load([
            'variants' => function($query) {
                $query->where('type', 'material');
            },
            'woodColors',
            'rattanColors'
        ]);

        // Format response sesuai kebutuhan frontend
        return response()->json([
            'id' => $product->id,
            'title' => $product->title,
            'price' => $product->price,
            'size' => $product->size,
            'description' => $product->description,
            'display_image' => $product->display_image,
            
            // Default values
            'default_bahan' => $product->default_bahan,
            'default_color' => $product->default_color,
            'default_rotan_color' => $product->default_rotan_color,
            'default_length' => $product->default_length,
            'default_width' => $product->default_width,
            'default_height' => $product->default_height,
            
            // Variants dengan harga material
            'variants' => $product->variants->map(function($variant) {
                return [
                    'type' => $variant->type,
                    'name' => $variant->name,
                    'price_per_10cm' => $variant->price_per_10cm,
                    'length_price' => $variant->length_price,
                    'width_price' => $variant->width_price,
                    'height_price' => $variant->height_price,
                ];
            }),
            
            // Warna kayu dengan pivot
            'wood_colors' => $product->woodColors->map(function($color) {
                return [
                    'name' => $color->name,
                    'pivot' => [
                        'extra_price' => $color->pivot->extra_price ?? 0,
                        'is_default' => $color->pivot->is_default ?? false,
                    ]
                ];
            }),
            
            // Warna rotan dengan pivot
            'rattan_colors' => $product->rattanColors->map(function($color) {
                return [
                    'name' => $color->name,
                    'pivot' => [
                        'extra_price' => $color->pivot->extra_price ?? 0,
                        'is_default' => $color->pivot->is_default ?? false,
                    ]
                ];
            }),
        ]);
    }
}
