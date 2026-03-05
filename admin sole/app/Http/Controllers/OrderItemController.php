<?php

namespace App\Http\Controllers;
// ← namespace harus sesuai struktur folder (app/Http/Controllers).
//   Ini penting biar Composer (autoload PSR-4) tahu "alamat" kelas ini.

use App\Models\OrderItem;                     // ← Model Eloquent untuk tabel order_items
use App\Http\Requests\StoreOrderItemRequest;  // ← Form Request khusus validasi untuk create (store)
use App\Http\Requests\UpdateOrderItemRequest; // ← Form Request khusus validasi untuk update
// Catatan: StoreOrderItemRequest & UpdateOrderItemRequest adalah class yang biasanya dibuat via
//   php artisan make:request StoreOrderItemRequest
// yang isinya rules() validasi, authorize(), dll.
// Jadi controller ini bisa langsung anggap input sudah bersih.

class OrderItemController extends Controller
{
    /**
     * index()
     * ------------------------------------------------------------------
     * TUJUAN:
     * - Menampilkan daftar OrderItem (semua / milik order tertentu).
     *
     * STATUS SAAT INI:
     * - Masih kosong (//).
     *
     * CARA PAKAI NORMALNYA:
     * - Biasanya kita ambil data:
     *     $items = OrderItem::with('order','product','customProduct')->get();
     *   lalu:
     *     return view('admin.order-items.index', compact('items'));
     *
     * RETURN YANG DIHARAPKAN:
     * - View Blade untuk listing, atau bisa juga JSON kalau ini API.
     */
    public function index()
    {
        //
    }

    /**
     * create()
     * ------------------------------------------------------------------
     * TUJUAN:
     * - Menampilkan form untuk membuat OrderItem baru (di panel admin).
     * - Biasanya hanya relevan kalau admin bisa nambah baris item secara manual
     *   ke sebuah order (misal: upsell / custom add).
     *
     * STATUS SAAT INI:
     * - Masih kosong.
     *
     * CARA PAKAI NORMALNYA:
     * - Ambil daftar produk untuk dropdown:
     *     $products = Product::all();
     *     return view('admin.order-items.create', compact('products'));
     */
    public function create()
    {
        //
    }

    /**
     * store(StoreOrderItemRequest $request)
     * ------------------------------------------------------------------
     * TUJUAN:
     * - Simpan data OrderItem baru ke database.
     *
     * PARAM:
     * - $request: tipe StoreOrderItemRequest (bukan Request biasa).
     *   Kenapa beda?
     *   - StoreOrderItemRequest biasanya punya:
     *        public function rules() { ... }
     *        public function authorize() { ... }
     *     Jadi, pada titik ini $request sudah tervalidasi otomatis oleh Laravel.
     *
     * FLOW UMUM YANG NANTI BISA DIISI:
     *   $data = $request->validated();       // ambil data yang sudah diverifikasi rules()
     *   OrderItem::create($data);            // buat item baru
     *   return redirect()->back()->with('success','Item ditambahkan');
     *
     * RETURN YANG DIHARAPKAN:
     * - Redirect (untuk admin panel)
     * - atau JSON (kalau controller dipakai API)
     */
    public function store(StoreOrderItemRequest $request)
    {
        //
    }

    /**
     * show(OrderItem $orderItem)
     * ------------------------------------------------------------------
     * TUJUAN:
     * - Menampilkan detail satu OrderItem tertentu (by id).
     *
     * ROUTE MODEL BINDING:
     * - Parameter method adalah `OrderItem $orderItem` (bukan $id).
     * - Laravel otomatis akan cari row OrderItem berdasarkan {orderItem} di URL route.
     *   Contoh route: GET /order-items/{orderItem}
     *   Kalau {orderItem} = 15 → Laravel akan inject OrderItem::findOrFail(15) ke argumen ini.
     *
     * FLOW UMUM YANG NANTI BISA DIISI:
     *   $orderItem->load(['order','product','customProduct']);
     *   return view('admin.order-items.show', compact('orderItem'));
     *
     * RETURN YANG DIHARAPKAN:
     * - Halaman detail item (qty, harga, ukuran custom, warna, dsb).
     * - atau JSON detail item untuk API.
     */
    public function show(OrderItem $orderItem)
    {
        //
    }

    /**
     * edit(OrderItem $orderItem)
     * ------------------------------------------------------------------
     * TUJUAN:
     * - Menampilkan form edit untuk 1 OrderItem.
     *
     * ROUTE MODEL BINDING LAGI:
     * - Sama seperti show(), Laravel inject otomatis instance OrderItem sesuai ID di URL.
     *
     * FLOW NORMAL:
     *   $orderItem->load('product','customProduct');
     *   $products = Product::all();
     *   return view('admin.order-items.edit', compact('orderItem','products'));
     *
     * RETURN:
     * - View Blade form edit (admin bisa ubah quantity, harga, warna custom, dll).
     */
    public function edit(OrderItem $orderItem)
    {
        //
    }

    /**
     * update(UpdateOrderItemRequest $request, OrderItem $orderItem)
     * ------------------------------------------------------------------
     * TUJUAN:
     * - Menyimpan perubahan (edit) terhadap sebuah OrderItem yang sudah ada.
     *
     * PARAM:
     * - $request   : tipe UpdateOrderItemRequest → sudah tervalidasi otomatis.
     * - $orderItem : instance OrderItem yang mau diubah (via route model binding).
     *
     * FLOW UMUM YANG NANTI BISA DIISI:
     *   $data = $request->validated();
     *   $orderItem->update($data);
     *   return redirect()->back()->with('success','Item berhasil diupdate');
     *
     * CATATAN BISNIS:
     * - Saat ubah item (qty/harga), biasanya total_amount di parent Order
     *   juga harus direcalculate. Jadi sering ada step tambahan:
     *     $order = $orderItem->order;
     *     $order->update(['total_amount' => ... hitung ulang ...]);
     *
     * RETURN:
     * - Redirect, atau JSON result kalau API.
     */
    public function update(UpdateOrderItemRequest $request, OrderItem $orderItem)
    {
        //
    }

    /**
     * destroy(OrderItem $orderItem)
     * ------------------------------------------------------------------
     * TUJUAN:
     * - Menghapus 1 baris OrderItem dari suatu order.
     *
     * PARAM:
     * - $orderItem : di-inject otomatis via route model binding.
     *
     * FLOW NORMAL:
     *   $orderItem->delete();s
     */
    public function destroy(OrderItem $orderItem)
    {
        //
    }
}
