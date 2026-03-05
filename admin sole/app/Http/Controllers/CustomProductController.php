<?php

namespace App\Http\Controllers;
// ← namespace harus cocok dengan struktur folder file ini (app/Http/Controllers)
//   supaya autoload Composer bisa menemukan kelas ini sebagai App\Http\Controllers\CustomProductController.

use App\Models\CustomProduct;
// ← Model Eloquent untuk tabel custom_products (produk custom buatan user).
//   Dipakai buat query database: ambil data, simpan data baru, update, hapus.

use App\Http\Requests\StoreCustomProductRequest;
use App\Http\Requests\UpdateCustomProductRequest;
// ← Form Request khusus Laravel untuk validasi & otorisasi.
//    - StoreCustomProductRequest: dipakai di method store() (saat bikin produk custom baru).
//    - UpdateCustomProductRequest: dipakai di method update() (saat edit produk custom).
//
//    Keduanya biasanya dibuat pakai artisan:
//      php artisan make:request StoreCustomProductRequest
//      php artisan make:request UpdateCustomProductRequest
//
//    Pentingnya FormRequest:
//    - Validasi input tinggal didefinisikan di rules() masing-masing request class.
//    - authorize() di dalam FormRequest bisa dipakai buat cek hak akses user.
//    - Kalau authorize() = false → Laravel auto balikin 403 Forbidden sebelum masuk ke controller.
//    - Kalau rules() gagal → Laravel auto balikin 422 Unprocessable Entity sebelum masuk ke controller.
//    Artinya: di dalam controller kita sudah dapat data yang aman/divalidasi.

class CustomProductController extends Controller
{
    /**
     * index()
     * ------------------------------------------------------------------
     * REST: GET /custom-products
     *
     * Tujuan umum:
     * - Menampilkan daftar semua CustomProduct.
     *
     * Ini biasanya digunakan di panel admin:
     * - Admin ingin melihat semua pesanan custom yang pernah dibuat user.
     *
     * Di aplikasi user biasa:
     * - Bisa dipakai untuk "riwayat desain custom" milik user itu sendiri.
     *
     * Implementasi yang biasanya ditulis di sini (belum ada di kode kamu):
     *
     *   // (opsional) batasi hanya custom product milik user:
     *   // $this->authorize('viewAny', CustomProduct::class);
     *
     *   $customProducts = CustomProduct::where('user_id', auth()->id())->get();
     *
     *   // Jika aplikasi web Blade:
     *   // return view('custom-products.index', compact('customProducts'));
     *
     *   // Jika API JSON:
     *   // return response()->json($customProducts);
     */
    public function index()
    {
        //
    }

    /**
     * create()
     * ------------------------------------------------------------------
     * REST: GET /custom-products/create
     *
     * Tujuan umum:
     * - Menampilkan halaman/form untuk membuat CustomProduct baru.
     *
     * Ini pola khas controller web tradisional Laravel:
     * - User buka form → submit → masuk ke store().
     *
     * Untuk API murni (SPA/mobile), method ini kadang tidak dipakai,
     * karena form dibuat di frontend, bukan Blade.
     *
     * Return tipikal (kalau pakai Blade):
     *   return view('custom-products.create');
     */
    public function create()
    {
        //
    }

    /**
     * store(StoreCustomProductRequest $request)
     * ------------------------------------------------------------------
     * REST: POST /custom-products
     *
     * Tujuan:
     * - Menyimpan produk custom baru (desain custom dari user) ke database.
     *
     * Parameter:
     * - StoreCustomProductRequest $request
     *   • turunan FormRequest
     *   • sudah divalidasi & sudah diautorisasi sebelum method ini jalan.
     *   • $request->validated() akan berisi array field yang lulus rules().
     *
     * Flow normal (yang BELUM kamu isi tapi *umumnya* seperti ini):
     *
     *   // 1. Ambil data tervalidasi
     *   $data = $request->validated();
     *
     *   // 2. Set user_id agar custom product ini terasosiasi dengan user yang login
     *   $data['user_id'] = auth()->id();
     *
     *   // 3. (opsional) hitung harga akhir menggunakan logic bisnis
     *   //    misalnya dengan CustomProduct::calculatePrice(...)
     *   //    lalu masukkan ke $data['total_price']
     *
     *   // 4. Simpan di database
     *   $customProduct = CustomProduct::create($data);
     *
     *   // 5. Response
     *   //    - Jika web:
     *   //      return redirect()->route('custom-products.show', $customProduct)
     *   //           ->with('success', 'Custom product created!');
     *   //
     *   //    - Jika API JSON:
     *   //      return response()->json($customProduct, 201); // 201 Created
     *
     * Catatan keamanan:
     * - Karena $guarded = [] di model CustomProduct kamu, mass assignment bisa isi kolom apa pun.
     *   Wajib pastikan StoreCustomProductRequest NGE-LIMIT field apa yang boleh dikirim user.
     */
    public function store(StoreCustomProductRequest $request)
    {
        //
    }

    /**
     * show(CustomProduct $customProduct)
     * ------------------------------------------------------------------
     * REST: GET /custom-products/{customProduct}
     *
     * Route Model Binding:
     * - Parameter {customProduct} di route akan otomatis di-resolve
     *   jadi instance CustomProduct dari database.
     *   Contoh URL: /custom-products/7
     *   Laravel otomatis jalankan: CustomProduct::findOrFail(7)
     *   lalu inject ke argumen $customProduct.
     *
     * Tujuan umum:
     * - Menampilkan detail 1 produk custom.
     *   Bisa dipakai untuk:
     *     • Admin melihat detail request custom user.
     *     • User melihat desain custom miliknya sendiri.
     *
     * Keamanan:
     * - Harus dicek bahwa user hanya boleh lihat miliknya sendiri.
     *   Biasanya:
     *     $this->authorize('view', $customProduct);
     *   → logic ada di CustomProductPolicy::view()
     *
     * Return tipikal:
     *   // web (Blade):
     *   // return view('custom-products.show', compact('customProduct'));
     *
     *   // API JSON:
     *   // return response()->json($customProduct);
     */
    public function show(CustomProduct $customProduct)
    {
        //
    }

    /**
     * edit(CustomProduct $customProduct)
     * ------------------------------------------------------------------
     * REST: GET /custom-products/{customProduct}/edit
     *
     * Tujuan umum:
     * - Menampilkan form edit di Blade untuk mengubah data custom product.
     *
     * Pola ini khas Laravel web tradisional (bukan API JSON).
     *
     * Constraint keamanan:
     * - User hanya boleh edit custom product miliknya.
     *   Bisa lewat policy:
     *     $this->authorize('update', $customProduct);
     *
     * Return tipikal (kalau pakai Blade):
     *   return view('custom-products.edit', compact('customProduct'));
     */
    public function edit(CustomProduct $customProduct)
    {
        //
    }

    /**
     * update(UpdateCustomProductRequest $request, CustomProduct $customProduct)
     * ------------------------------------------------------------------
     * REST: PUT/PATCH /custom-products/{customProduct}
     *
     * Tujuan:
     * - Update data dari satu CustomProduct yang sudah ada.
     *
     * Parameter:
     * - UpdateCustomProductRequest $request
     *   • turunan FormRequest.
     *   • sudah lolos authorize() dan rules() sebelum masuk sini.
     *   • gunakan $request->validated() untuk ambil input aman.
     *
     * - CustomProduct $customProduct
     *   • instance yang mau diedit (di-resolve otomatis via route model binding).
     *
     * Flow umum (BELUM kamu implementasi di kode, tapi idealnya seperti ini):
     *
     *   // cek hak akses (opsional kalau authorize() FormRequest belum cover ini)
     *   // $this->authorize('update', $customProduct);
     *
     *   $data = $request->validated();
     *
     *   // (opsional) Recalculate harga total kalau dimensi/material berubah
     *   // $data['total_price'] = CustomProduct::calculatePrice(...);
     *
     *   $customProduct->update($data);
     *
     *   // Kalau web:
     *   // return redirect()->route('custom-products.show', $customProduct)
     *   //        ->with('success', 'Custom product updated!');
     *
     *   // Kalau API:
     *   // return response()->json([
     *   //     'message' => 'Custom product updated',
     *   //     'data'    => $customProduct,
     *   // ]);
     *
     * Security note:
     * - Jangan izinkan user update CustomProduct milik orang lain → harus di-handle via policy.
     */
    public function update(UpdateCustomProductRequest $request, CustomProduct $customProduct)
    {
        //
    }

    /**
     * destroy(CustomProduct $customProduct)
     * ------------------------------------------------------------------
     * REST: DELETE /custom-products/{customProduct}
     *
     * Tujuan umum:
     * - Menghapus satu CustomProduct dari database.
     *
     * Route Model Binding:
     * - $customProduct otomatis diisi dengan record berdasarkan {customProduct} di URL.
     *
     * Keamanan PENTING:
     * - Jangan biarkan user menghapus custom product milik user lain.
     *   Biasanya:
     *     $this->authorize('delete', $customProduct);
     *   yang logic-nya kamu definisikan di CustomProductPolicy::delete().
     *
     * Flow umum yang biasanya ditulis:
     *
     *   // $this->authorize('delete', $customProduct);
     *   $customProduct->delete();
     *
     *   // Web:
     *   // return redirect()->route('custom-products.index')
     *   //        ->with('success', 'Custom product deleted');
     *
     *   // API:
     *   // return response()->json(['message' => 'Deleted'], 200);
     *
     */
    public function destroy(CustomProduct $customProduct)
    {
        //
    }
}
