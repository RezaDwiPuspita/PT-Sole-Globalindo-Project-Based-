<?php

namespace App\Policies; 
// ← namespace harus sama dengan lokasi file (app/Policies).
//    Laravel akan cari policy di namespace ini saat authorize() dipanggil.

use App\Models\Product; // ← Model yang di-protect (produk yang dijual).
use App\Models\User;    // ← User yang sedang login (subjek otorisasi).
use Illuminate\Auth\Access\Response;
// ← (opsional) bisa dipakai kalau kamu mau return Response::allow() / Response::deny('alasan').
//    Di skeleton ini method masih return bool, jadi Response belum dipakai tapi gak apa-apa di-"use".

/**
 * CLASS: ProductPolicy
 * =====================================================================================
 * Policy = aturan izin (authorization rules) untuk aksi terhadap model Product.
 *
 * Ini menjawab pertanyaan seperti:
 *  - Siapa saja yang boleh melihat daftar produk?
 *  - Siapa boleh melihat detail produk tertentu?
 *  - Siapa boleh nambah produk baru?
 *  - Siapa boleh edit/hapus produk?
 *
 * Kapan dipakai?
 * - Di controller kamu bisa panggil:
 *       $this->authorize('update', $product);
 *   => Laravel otomatis akan jalankan ProductPolicy::update($currentUser, $product)
 *
 * - Di Blade kamu bisa:
 *       @can('delete', $product)
 *            <button>Hapus Produk</button>
 *       @endcan
 *
 * Supaya ini aktif, kamu harus daftarkan policy di App\Providers\AuthServiceProvider:
 *
 *   protected $policies = [
 *       Product::class => ProductPolicy::class,
 *   ];
 *
 * Struktur method bawaan:
 * - viewAny()     : izin lihat LIST / INDEX semua product
 * - view()        : izin lihat 1 produk tertentu
 * - create()      : izin bikin produk baru
 * - update()      : izin edit produk
 * - delete()      : izin hapus produk
 * - restore()     : izin restore produk (kalau soft delete)
 * - forceDelete() : izin hapus permanen (hard delete)
 *
 * CATATAN PENTING:
 * Sekarang semua method masih kosong ("//") tapi tanda tangannya return bool.
 * Artinya: kalau dipanggil sekarang, bakal error (karena tidak return apa-apa).
 * Jadi kamu WAJIB isi logic sendiri nanti.
 *
 * Contoh skenario peran (role-based):
 *   - role 'admin' dan 'owner' → boleh create / update / delete / restore / forceDelete.
 *   - role 'staff' → boleh viewAny & view tapi tidak boleh hapus permanen.
 *   - role 'customer' (pembeli biasa) → boleh viewAny & view (browsing katalog),
 *     tapi tidak boleh create/update/delete produk di backend.
 *
 * Biasanya ini world-nya e-commerce:
 *   - semua orang bisa lihat produk, jadi viewAny() & view() = true
 *   - tapi cuma admin/owner boleh nambah/edit/hapus.
 *
 * Kamu bisa pakai pola helper:
 *
 *   protected function isAdmin(User $user): bool
 *   {
 *       return in_array($user->role, ['admin', 'owner']);
 *   }
 *
 * lalu pakai di bawah:
 *   return $this->isAdmin($user);
 *
 * =====================================================================================
 */
class ProductPolicy
{
    /**
     * viewAny(User $user): bool
     * ---------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh melihat "daftar semua produk".
     *
     * Kapan dipanggil:
     * - Biasanya di controller index():
     *       $this->authorize('viewAny', Product::class);
     *
     * Tipikal aturan:
     * - Frontend katalog publik? -> Semua user (bahkan guest) boleh lihat list produk.
     *   Tapi policy ini hanya jalan kalau kamu pakai Gate/authorize yang butuh User,
     *   jadi minimal harus user ter-auth (bukan guest).
     *
     * - Admin panel? -> mungkin hanya admin/owner yang boleh mengakses halaman daftar produk di dashboard admin.
     *
     * Contoh implementasi umum:
     *   return in_array($user->role, ['admin','owner','staff']);
     *
     * Atau kalau kamu mau semua user login boleh melihat list produk:
     *   return true;
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * view(User $user, Product $product): bool
     * ---------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh melihat detail SATU produk tertentu.
     *
     * Kapan dipanggil:
     * - Controller show():
     *       $this->authorize('view', $product);
     *
     * Biasanya:
     * - Katalog produk biasanya public. Jadi secara bisnis hampir selalu boleh dilihat semua user.
     * - Tapi di panel admin, bisa saja kamu pengin batasi kalau produk "draft" belum tayang.
     *
     * Contoh rule simpel (semua user login boleh lihat):
     *   return true;
     *
     * Rule lebih ketat (misal hanya admin bisa lihat produk yang statusnya "hidden"):
     *   if ($product->status === 'hidden') {
     *       return in_array($user->role, ['admin','owner']);
     *   }
     *   return true;
     */
    public function view(User $user, Product $product): bool
    {
        //
    }

    /**
     * create(User $user): bool
     * ---------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh menambah produk baru.
     *
     * Kapan dipanggil:
     * - Controller store():
     *       $this->authorize('create', Product::class);
     *
     * Biasanya:
     * - HANYA admin / owner yang boleh buat produk baru di dashboard.
     * - Customer biasa tidak boleh.
     *
     * Contoh rule umum:
     *   return in_array($user->role, ['admin', 'owner']);
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * update(User $user, Product $product): bool
     * ---------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh mengedit data produk (nama, harga, stok, deskripsi, dsb).
     *
     * Kapan dipanggil:
     * - Controller update():
     *       $this->authorize('update', $product);
     *
     * Biasanya:
     * - Lagi-lagi hanya role tertentu (admin/owner/staff gudang) yang boleh ubah produk.
     * - Customer biasa tidak boleh.
     *
     * Contoh rule umum:
     *   return in_array($user->role, ['admin', 'owner']);
     *
     * Kalau kamu punya konsep "PIC produk" (misal user_id di tabel Product untuk product manager tertentu),
     * kamu bisa juga ijinkan si PIC edit produknya sendiri:
     *
     *   return $product->user_id === $user->id || in_array($user->role, ['admin','owner']);
     */
    public function update(User $user, Product $product): bool
    {
        //
    }

    /**
     * delete(User $user, Product $product): bool
     * ---------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh menghapus produk dari katalog.
     *
     * Kapan dipanggil:
     * - Controller destroy():
     *       $this->authorize('delete', $product);
     *
     * Catatan:
     * - "Hapus" di sini biasanya soft delete (misal set 'is_active' = false atau pakai SoftDeletes).
     * - Menghapus produk berarti produk tidak tampil lagi untuk customer.
     *
     * Rule umum:
     *   return in_array($user->role, ['admin', 'owner']);
     */
    public function delete(User $user, Product $product): bool
    {
        //
    }

    /**
     * restore(User $user, Product $product): bool
     * ---------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh melakukan restore produk yang di-soft-delete.
     *
     * Kapan dipanggil:
     * - Kalau model Product pakai SoftDeletes dan kamu manggil:
     *       $this->authorize('restore', $product);
     *
     * Biasanya:
     * - Hanya admin/owner boleh balikin produk "yang tadinya dihapus".
     *
     * Rule umum:
     *   return in_array($user->role, ['admin', 'owner']);
     */
    public function restore(User $user, Product $product): bool
    {
        //
    }

    /**
     * forceDelete(User $user, Product $product): bool
     * ---------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh menghapus produk secara permanen dari database
     *   (bukan sekadar soft delete).
     *
     * Dampak:
     * - Data produk benar-benar hilang (termasuk histori harga, dsb) → ini high risk.
     * - Biasanya *sangat* dibatasi hanya owner bisnis.
     *
     * Rule umum:
     *   return in_array($user->role, ['owner']);
     *
     * Atau bahkan `return false;` kalau kamu ingin SELALU melarang hard delete demi integritas data.
     */
    public function forceDelete(User $user, Product $product): bool
    {
        //
    }
}
