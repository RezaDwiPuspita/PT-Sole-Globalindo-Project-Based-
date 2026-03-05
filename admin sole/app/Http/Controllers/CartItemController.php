<?php

namespace App\Http\Controllers;
// ← namespace harus cocok dengan struktur folder file ini: app/Http/Controllers
//   Supaya class ini bisa di-autoload Composer sebagai App\Http\Controllers\CartItemController.

use App\Models\CartItem;
// ← Model Eloquent untuk tabel `cart_items`. Dipakai buat query / create / update / delete CartItem.

use App\Http\Requests\StoreCartItemRequest;
use App\Http\Requests\UpdateCartItemRequest;
// ← Form Request classes khusus untuk validasi & otorisasi input.
//    - StoreCartItemRequest dipakai saat tambah item baru ke cart (store).
//    - UpdateCartItemRequest dipakai saat mengubah item yang sudah ada (update).
//    Keduanya biasanya dibuat pakai artisan:
//      php artisan make:request StoreCartItemRequest
//      php artisan make:request UpdateCartItemRequest
//
//    FormRequest punya dua fungsi penting:
//    1. rules()     -> aturan validasi field
//    2. authorize() -> izin user boleh jalanin action ini atau nggak
//
//    Jadi controller tidak perlu melakukan $request->validate() manual, 
//    karena kalau request tidak lolos validasi/otorisasi, Laravel langsung balikin error response
//    SEBELUM masuk ke method store()/update().

class CartItemController extends Controller
{
    /**
     * index()
     * ------------------------------------------------------------------
     * REST: GET /cart-items
     *
     * Tujuan umum:
     * - Menampilkan daftar semua CartItem.
     *   - Untuk aplikasi admin: bisa jadi daftar semua item yang ada di semua cart.
     *   - Untuk aplikasi user biasa: biasanya tidak dipakai langsung, 
     *     karena user biasanya lihat cart melalui user->cart, bukan semua CartItem global.
     *
     * Return tipikal (belum diimplementasi di sini):
     * - return view('cart-items.index', [...]);
     *   atau
     * - return response()->json($items);
     *
     * Keamanan:
     * - Biasanya kamu akan batasi hanya admin yang boleh lihat seluruh CartItem.
     *   Itu bisa dilakukan dengan:
     *      $this->authorize('viewAny', CartItem::class);
     *   asalkan CartItemPolicy::viewAny() sudah didefinisikan.
     */
    public function index()
    {
        //
    }

    /**
     * create()
     * ------------------------------------------------------------------
     * REST: GET /cart-items/create
     *
     * Tujuan umum:
     * - Menampilkan form (Blade view) untuk membuat CartItem baru secara manual.
     *
     * Catatan:
     * - Ini gaya controller untuk aplikasi web tradisional (server-rendered).
     * - Biasanya tidak dipakai dalam API JSON.
     * - Di e-commerce biasa, user tidak "mengisi form create cart item" manual di halaman terpisah;
     *   mereka klik "Add to Cart" → langsung kena store().
     *
     * Return tipikal (belum dibuat di sini):
     * - return view('cart-items.create');
     */
    public function create()
    {
        //
    }

    /**
     * store(StoreCartItemRequest $request)
     * ------------------------------------------------------------------
     * REST: POST /cart-items
     *
     * Tujuan umum:
     * - Menyimpan CartItem baru ke database (misal saat user menambahkan produk ke keranjang).
     *
     * Parameter:
     * - StoreCartItemRequest $request
     *   • Ini turunan FormRequest.
     *   • Jika authorize() = false di dalamnya → auto 403 sebelum sampai sini.
     *   • Jika rules() gagal → auto 422 validation error sebelum sampai sini.
     *   • Jadi di sini kita bisa berasumsi data valid dan user authorized.
     *
     * Flow tipikal (yang BELUM ditulis di kode kamu, tapi idealnya nanti seperti ini):
     *
     *   // ambil data tervalidasi
     *   $data = $request->validated();
     *
     *   // buat CartItem baru
     *   $cartItem = CartItem::create($data);
     *
     *   // balikin respon
     *   return redirect()->route('cart-items.show', $cartItem);
     *
     * atau kalau API JSON:
     *
     *   return response()->json($cartItem, 201); // 201 Created
     *
     * Security note:
     * - Biasanya kamu tidak mau user bikin CartItem untuk cart milik user lain.
     *   Pastikan di StoreCartItemRequest::authorize() kamu cek kepemilikan cart_id.
     */
    public function store(StoreCartItemRequest $request)
    {
        //
    }

    /**
     * show(CartItem $cartItem)
     * ------------------------------------------------------------------
     * REST: GET /cart-items/{cartItem}
     *
     * Route Model Binding:
     * - Parameter {cartItem} di route otomatis di-convert Laravel menjadi instance CartItem.
     *   Contoh: /cart-items/15 → Laravel jalankan CartItem::findOrFail(15) 
     *   dan inject hasilnya ke argumen $cartItem.
     *
     * Tujuan umum:
     * - Menampilkan detail satu cart item.
     *   Contoh kalau dipakai admin: lihat item ini punya cart siapa, produk apa, qty berapa.
     *
     * Return tipikal:
     * - return view('cart-items.show', compact('cartItem'));
     *   atau
     * - return response()->json($cartItem);
     *
     * Keamanan:
     * - Jangan lupa cek kepemilikan item.
     *   Misal: $this->authorize('view', $cartItem);
     *   yang implementasinya di CartItemPolicy::view()
     */
    public function show(CartItem $cartItem)
    {
        //
    }

    /**
     * edit(CartItem $cartItem)
     * ------------------------------------------------------------------
     * REST: GET /cart-items/{cartItem}/edit
     *
     * Tujuan umum:
     * - Menampilkan form edit (Blade view) untuk mengubah CartItem tertentu.
     *   (misal mengubah kuantitas).
     *
     * Parameter:
     * - $cartItem hasil route model binding seperti di show().
     *
     * Catatan:
     * - Biasanya untuk aplikasi web berbasis form.
     * - Di API modern, frontend akan handle UI form sendiri dan langsung call update() 
     *   dengan AJAX → edit() kadang tidak dipakai sama sekali.
     *
     * Return tipikal:
     * - return view('cart-items.edit', compact('cartItem'));
     */
    public function edit(CartItem $cartItem)
    {
        //
    }

    /**
     * update(UpdateCartItemRequest $request, CartItem $cartItem)
     * ------------------------------------------------------------------
     * REST: PUT/PATCH /cart-items/{cartItem}
     *
     * Tujuan umum:
     * - Mengubah data CartItem yang sudah ada. Contoh paling umum: update quantity.
     *
     * Parameter:
     * - UpdateCartItemRequest $request
     *   • Sama konsepnya dengan StoreCartItemRequest tapi untuk update.
     *   • Sudah divalidasi & authorize sebelum eksekusi method ini.
     *
     * - CartItem $cartItem
     *   • CartItem spesifik yang mau diubah, otomatis diambil via route model binding.
     *
     * Flow tipikal yang BELUM ditulis di kode kamu tapi biasanya seperti ini:
     *
     *   $data = $request->validated();  // ambil data yang lolos validasi
     *
     *   // pastikan user tidak bisa ubah item milik cart orang lain:
     *   // $this->authorize('update', $cartItem);
     *
     *   $cartItem->update($data);       // mass assignment -> pastikan $fillable / $guarded aman di model
     *
     *   return response()->json([
     *       'message'   => 'Cart item updated',
     *       'cart_item' => $cartItem,
     *   ]);
     *
     * atau kalau full Blade/web:
     *   return redirect()->route('cart-items.show', $cartItem);
     */
    public function update(UpdateCartItemRequest $request, CartItem $cartItem)
    {
        //
    }

    /**
     * destroy(CartItem $cartItem)
     * ------------------------------------------------------------------
     * REST: DELETE /cart-items/{cartItem}
     *
     * Tujuan umum:
     * - Menghapus satu CartItem dari database/cart.
     *   Contoh paling sering: user klik "hapus produk ini dari keranjang".
     *
     * Parameter:
     * - CartItem $cartItem
     *   • Item yang akan dihapus. Di-inject via route model binding.
     *
     * Flow tipikal (belum ditulis di kode kamu tapi biasanya seperti ini):
     *
     *   // Cek otorisasi → hanya pemilik cart boleh hapus
     *   // $this->authorize('delete', $cartItem);
     *
     *   $cartItem->delete();
     *
     *   // Kalau ini controller web:
     *   // return redirect()->route('cart-items.index')
     *   //        ->with('success','Item removed from cart');
     *
     *   // Kalau API JSON:
     *   // return response()->json(['message' => 'Item removed'], 200);
     *
     * Security notes:
     * - WAJIB pastikan user gak bisa hapus cart item user lain.
     *   Itu bisa dicek di CartItemPolicy::delete() atau manual if ($cartItem->cart->user_id !== Auth::id()) ...
     */
    public function destroy(CartItem $cartItem)
    {
        //
    }
}
