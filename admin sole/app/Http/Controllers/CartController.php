<?php

namespace App\Http\Controllers; 
// ← namespace harus sesuai struktur folder file ini (app/Http/Controllers).
//   Ini penting biar Composer autoload tahu "CartController" itu ada di sini
//   saat dipanggil pakai App\Http\Controllers\CartController.

use App\Models\Cart; 
// ← Model Eloquent untuk tabel "carts". Dipakai buat query/CRUD data cart di DB.

use App\Http\Requests\StoreCartRequest;
use App\Http\Requests\UpdateCartRequest;
// ← Form Request classes (Laravel FormRequest).
//    Keduanya biasanya dibuat via artisan: 
//      php artisan make:request StoreCartRequest
//      php artisan make:request UpdateCartRequest
//    Kegunaan FormRequest:
//      - berisi rules() validasi input
//      - bisa authorize() per-user
//    Jadi controller nggak perlu nulis $request->validate(...) manual lagi.
//    Metode store() otomatis cuma jalan kalau validasi di StoreCartRequest lolos.
//    Metode update() otomatis cuma jalan kalau validasi di UpdateCartRequest lolos.

class CartController extends Controller
{
    /**
     * index()
     * ------------------------------------------------------------------
     * "Display a listing of the resource."
     *
     * Konvensi RESTful:
     * - GET /carts
     * - Biasanya untuk menampilkan daftar semua cart (atau cart milik user saat ini).
     *
     * Return tipikal (belum diimplementasi di sini):
     * - Bisa return view('carts.index', [...]) untuk halaman blade (admin panel / dashboard).
     * - Atau bisa return response()->json(...) kalau ini mau dijadikan endpoint API.
     *
     * Catatan:
     * - Saat ini fungsi masih kosong (`//`), jadi belum ada perilaku.
     */
    public function index()
    {
        //
    }

    /**
     * create()
     * ------------------------------------------------------------------
     * "Show the form for creating a new resource."
     *
     * Konvensi RESTful:
     * - GET /carts/create
     * - Biasanya dipakai untuk menampilkan form HTML (Blade) agar user bisa bikin cart baru.
     *
     * Catatan:
     * - Ini style controller untuk aplikasi web tradisional (server-rendered form).
     * - Untuk API biasanya method ini tidak dipakai.
     */
    public function create()
    {
        //
    }

    /**
     * store(StoreCartRequest $request)
     * ------------------------------------------------------------------
     * "Store a newly created resource in storage."
     *
     * Konvensi RESTful:
     * - POST /carts
     * - Tujuan: membuat cart baru berdasarkan data dari form/request.
     *
     * Parameter:
     * - StoreCartRequest $request
     *   • Turunan dari Illuminate\Foundation\Http\FormRequest.
     *   • Berisi rule validasi input, jadi di sini datanya sudah dipastikan valid.
     *   • Akses input yang sudah divalidasi pakai $request->validated().
     *
     * Implementasi tipikal (belum ditulis di sini):
     *   $data = $request->validated();
     *   $cart = Cart::create($data);
     *   return redirect()->route('carts.show', $cart);
     *
     * atau kalau API:
     *   return response()->json($cart, 201);
     */
    public function store(StoreCartRequest $request)
    {
        //
    }

    /**
     * show(Cart $cart)
     * ------------------------------------------------------------------
     * "Display the specified resource."
     *
     * Konvensi RESTful:
     * - GET /carts/{cart}
     *
     * Route Model Binding:
     * - Parameter `Cart $cart` otomatis diisi oleh Laravel
     *   berdasarkan {cart} di URL.
     *   Contoh route:
     *     Route::get('/carts/{cart}', [CartController::class, 'show']);
     *
     *   Jika user akses /carts/12:
     *   - Laravel akan jalankan Cart::findOrFail(12)
     *   - lalu inject hasilnya ke argumen $cart
     *
     * Perilaku tipikal:
     * - return view('carts.show', compact('cart'));
     * - atau return response()->json($cart);
     */
    public function show(Cart $cart)
    {
        //
    }

    /**
     * edit(Cart $cart)
     * ------------------------------------------------------------------
     * "Show the form for editing the specified resource."
     *
     * Konvensi RESTful:
     * - GET /carts/{cart}/edit
     * - Biasanya buat menampilkan form edit cart di Blade view.
     *
     * Route Model Binding:
     * - Sama seperti show(), parameter $cart otomatis di-resolve
     *   dari {cart} di URL.
     *
     * Return tipikal (belum ditulis):
     * - return view('carts.edit', compact('cart'));
     *
     * Catatan:
     * - Lagi-lagi method ini biasanya hanya dipakai untuk aplikasi web berbasis form.
     * - Untuk API, biasanya edit() tidak dipakai (frontend yang handle form).
     */
    public function edit(Cart $cart)
    {
        //
    }

    /**
     * update(UpdateCartRequest $request, Cart $cart)
     * ------------------------------------------------------------------
     * "Update the specified resource in storage."
     *
     * Konvensi RESTful:
     * - PUT/PATCH /carts/{cart}
     *
     * Parameter:
     * - UpdateCartRequest $request
     *   • Sama konsepnya dengan StoreCartRequest tapi khusus untuk update.
     *   • Sudah tervalidasi dan sudah di-authorize sebelum masuk ke sini.
     *
     * - Cart $cart
     *   • Instance cart yang mau di-update, otomatis diambil dari URL oleh route model binding.
     *
     * Implementasi tipikal (belum ditulis):
     *   $data = $request->validated();
     *   $cart->update($data);
     *   return redirect()->route('carts.show', $cart);
     *
     * atau versi API:
     *   return response()->json($cart);
     */
    public function update(UpdateCartRequest $request, Cart $cart)
    {
        //
    }

    /**
     * destroy(Cart $cart)
     * ------------------------------------------------------------------
     * "Remove the specified resource from storage."
     *
     * Konvensi RESTful:
     * - DELETE /carts/{cart}
     *
     * Parameter:
     * - Cart $cart
     *   • Cart yang mau dihapus.
     *   • Lagi-lagi diisi otomatis dari {cart} di route via route model binding.
     *
     * Implementasi tipikal (belum ditulis):
     *   $cart->delete();
     *   return redirect()->route('carts.index');
     *
     * Atau API style:
     *   $cart->delete();
     *   return response()->json(['message' => 'deleted'], 204);
     *
     * Catatan penting keamanan:
     * - Biasanya sebelum hapus perlu cek otorisasi:
     *     $this->authorize('delete', $cart);
     *   Policy CartPolicy::delete() menentukan boleh/tidaknya user hapus cart ini.
     */
    public function destroy(Cart $cart)
    {
        //
    }
}
