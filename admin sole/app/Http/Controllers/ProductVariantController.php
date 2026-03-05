<?php

namespace App\Http\Controllers;
// ← namespace harus sesuai struktur folder (app/Http/Controllers).
//   Ini penting supaya autoload PSR-4 Composer bisa menemukan controller ini
//   saat kita pakai di routes, misal Route::resource('variants', ProductVariantController::class);

use App\Models\ProductVariant;                  // ← Model Eloquent untuk tabel product_variants
use App\Http\Requests\StoreProductVariantRequest;  // ← Form Request custom untuk validasi saat create (store)
use App\Http\Requests\UpdateProductVariantRequest; // ← Form Request custom untuk validasi saat update
                                                   //    (FormRequest = class khusus yang berisi rules() & authorize())

class ProductVariantController extends Controller
{
    /**
     * index()
     * ------------------------------------------------------------------
     * ROUTE TYPICAL (resource): GET /product-variants
     *
     * TUJUAN:
     * - Menampilkan daftar semua ProductVariant (misalnya di halaman admin).
     *
     * NOTE:
     * - Sekarang masih kosong (//).
     * - Implementasi umum:
     *      $variants = ProductVariant::with('product')->get();
     *      return view('admin.variants.index', compact('variants'));
     *
     * RETURN:
     * - Biasanya view Blade (untuk panel admin).
     * - Bisa juga JSON kalau ini mau dipakai API.
     */
    public function index()
    {
        //
    }

    /**
     * create()
     * ------------------------------------------------------------------
     * ROUTE TYPICAL (resource): GET /product-variants/create
     *
     * TUJUAN:
     * - Menampilkan form "buat varian baru".
     * - Biasanya form ini butuh data produk induknya,
     *   contoh dropdown pilih Product mana.
     *
     * NOTE:
     * - Masih kosong.
     * - Contoh implementasi:
     *      $products = Product::all();
     *      return view('admin.variants.create', compact('products'));
     *
     * RETURN:
     * - View Blade yang berisi form input type/name/price dst.
     */
    public function create()
    {
        //
    }

    /**
     * store()
     * ------------------------------------------------------------------
     * ROUTE TYPICAL (resource): POST /product-variants
     *
     * PARAMETER:
     * - StoreProductVariantRequest $request
     *      -> Ini adalah FormRequest kustom.
     *      -> Berbeda dari Request biasa, FormRequest punya:
     *           - rules()  : definisi validasi
     *           - authorize(): cek otorisasi user
     *      -> Jadi di sini kamu tidak perlu panggil $request->validate() lagi,
     *         karena FormRequest sudah valid sebelum masuk ke method ini.
     *
     * TUJUAN:
     * - Menyimpan varian baru ke database.
     * - Biasanya field yang diambil:
     *      product_id, type, name, price
     *
     * NOTE:
     * - Masih kosong. Implementasi tipikal:
     *
     *      $variant = ProductVariant::create([
     *          'product_id' => $request->product_id,
     *          'type'       => $request->type,       // ex: 'material', 'wood_color', 'rattan_color'
     *          'name'       => $request->name,       // ex: 'Walnut Brown'
     *          'price'      => $request->price,      // biaya tambahan
     *      ]);
     *
     *      return redirect()
     *          ->route('variants.index')
     *          ->with('success', 'Variant created!');
     *
     * RETURN:
     * - Biasanya redirect dengan flash message sukses.
     * - Bisa juga return JSON kalau controller ini mau dipakai API.
     */
    public function store(StoreProductVariantRequest $request)
    {
        //
    }

    /**
     * show()
     * ------------------------------------------------------------------
     * ROUTE TYPICAL (resource): GET /product-variants/{productVariant}
     *
     * PARAMETER:
     * - ProductVariant $productVariant
     *      -> Route Model Binding otomatis:
     *         {productVariant} di URL akan di-resolve oleh Laravel jadi record ProductVariant
     *         berdasarkan primary key (biasanya kolom id).
     *
     * TUJUAN:
     * - Menampilkan detail satu varian tertentu.
     *
     * NOTE:
     * - Masih kosong.
     * - Implementasi umum:
     *      return view('admin.variants.show', compact('productVariant'));
     *
     * RETURN:
     * - Biasanya view detail varian
     * - Alternatif untuk API: return response()->json($productVariant);
     */
    public function show(ProductVariant $productVariant)
    {
        //
    }

    /**
     * edit()
     * ------------------------------------------------------------------
     * ROUTE TYPICAL (resource): GET /product-variants/{productVariant}/edit
     *
     * PARAMETER:
     * - ProductVariant $productVariant
     *      -> Ini lagi-lagi hasil route model binding otomatis dari Laravel.
     *
     * TUJUAN:
     * - Menampilkan form edit varian.
     *   Misalnya admin ingin ubah harga tambahan, nama varian, dll.
     *
     * NOTE:
     * - Masih kosong.
     * - Implementasi umum:
     *      return view('admin.variants.edit', compact('productVariant'));
     *
     * RETURN:
     * - View Blade berisi form <input> yang sudah terisi nilai lama.
     */
    public function edit(ProductVariant $productVariant)
    {
        //
    }

    /**
     * update()
     * ------------------------------------------------------------------
     * ROUTE TYPICAL (resource): PUT/PATCH /product-variants/{productVariant}
     *
     * PARAMETER:
     * - UpdateProductVariantRequest $request
     *      -> FormRequest kustom untuk validasi update.
     * - ProductVariant $productVariant
     *      -> Data varian lama yang mau diubah.
     *
     * TUJUAN:
     * - Memperbarui data varian di database.
     *
     * NOTE:
     * - Masih kosong.
     * - Implementasi umum:
     *
     *      $productVariant->update([
     *          'type'  => $request->type,
     *          'name'  => $request->name,
     *          'price' => $request->price,
     *      ]);
     *
     *      return redirect()
     *          ->route('variants.index')
     *          ->with('success', 'Variant updated!');
     *
     * RETURN:
     * - Biasanya redirect dengan flash message.
     * - Bisa juga return JSON jika ini API.
     */
    public function update(UpdateProductVariantRequest $request, ProductVariant $productVariant)
    {
        //
    }

    /**
     * destroy()
     * ------------------------------------------------------------------
     * ROUTE TYPICAL (resource): DELETE /product-variants/{productVariant}
     *
     * PARAMETER:
     * - ProductVariant $productVariant
     *      -> Varian yang mau dihapus. Sudah otomatis di-bind dari URL.
     *
     * TUJUAN:
     * - Menghapus varian dari DB.
     */
    public function destroy(ProductVariant $productVariant)
    {
        //
    }
}
