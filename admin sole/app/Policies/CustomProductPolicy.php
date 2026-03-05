<?php

namespace App\Policies; 
// ← namespace harus sesuai struktur folder (app/Policies). Laravel akan mencari policy di sini
//    saat kita melakukan otorisasi via Gate / $this->authorize() / @can().

use App\Models\CustomProduct; // ← Model yang dilindungi policy ini (produk custom buatan user).
use App\Models\User;          // ← Model user yang sedang login (subjek yang minta izin).
use Illuminate\Auth\Access\Response; 
// ← Kelas helper untuk balikin Response::allow() / Response::deny('alasan').
//    Sekarang method kita return bool, jadi ini belum dipakai — tapi aman dibiarkan.

// =====================================================================================
// CLASS: CustomProductPolicy
// =====================================================================================
// Policy = tempat taruh aturan "siapa boleh melakukan apa" terhadap model tertentu
// (dalam kasus ini: CustomProduct).
//
// Contoh fungsi bisnis:
// - CustomProduct = desain furniture custom buatan user (punya ukuran, material, warna rotan, dsb).
// - User harus bisa: buat desain custom-nya sendiri, lihat punyanya sendiri, update punyanya sendiri.
// - Admin/owner mungkin boleh lihat semua, update status, dsb.
//
// Cara Laravel pakai policy ini:
// 1. Daftarkan di App\Providers\AuthServiceProvider:
//
//    protected $policies = [
//        CustomProduct::class => CustomProductPolicy::class,
//    ];
//
// 2. Lalu di controller:
//    $this->authorize('update', $customProduct);
//    // → otomatis panggil CustomProductPolicy@update($userLogin, $customProduct)
//
// 3. Di Blade:
//    @can('delete', $customProduct)
//        <form>...</form>
//    @endcan
//
// Semua method di bawah menerima:
// - $user → instance User yg lagi login
// - $customProduct → instance CustomProduct (kecuali viewAny/create yg tidak butuh instance spesifik)
//
// Semua method sekarang bertipe return bool → WAJIB return true/false kalau dipakai.
// true  = diizinkan
// false = ditolak (bisa jadi 403 Forbidden saat authorize() dipanggil).
//
// Di sini kita baru nambah komentar dan struktur, belum isi logic final.
// Kamu bisa isi logic real sesuai kebutuhan nanti.
// =====================================================================================
class CustomProductPolicy
{
    /**
     * viewAny(User $user): bool
     * ---------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh melihat DAFTAR SEMUA CustomProduct.
     *
     * Kapan dipanggil?
     * - Biasanya di controller index():
     *      $this->authorize('viewAny', CustomProduct::class);
     *
     * Pola umum bisnis:
     * - Kalau ini panel admin yang bisa lihat semua submission custom, maka mungkin:
     *      return in_array($user->role, ['admin', 'owner']);
     *
     * - Kalau user biasa tidak boleh lihat milik orang lain secara massal, kamu bisa return false.
     *
     * Catatan:
     * - Sekarang body masih kosong (`//`), jadi kalau dipanggil langsung bakal error karena
     *   tidak ada nilai yg dikembalikan padahal function harus return bool.
     * - Jadi method ini *harus* kamu isi nanti sebelum dipakai.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * view(User $user, CustomProduct $customProduct): bool
     * ---------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh melihat SATU CustomProduct tertentu.
     *
     * Kapan dipanggil?
     * - Misal di controller show():
     *      $this->authorize('view', $customProduct);
     *
     * Kenapa penting?
     * - Desain custom biasanya punya detail ukuran, harga per material, preferensi warna, dsb.
     *   Itu sifatnya privat; kita gak mau user A bisa lihat konfigurasi custom user B.
     *
     * Pola logika umum (yang typical banget):
     *
     *   return $user->id === $customProduct->user_id
     *       || in_array($user->role, ['admin', 'owner']);
     *
     * Artinya:
     * - Pemilik boleh lihat produknya sendiri.
     * - Admin / Owner (level tinggi) boleh lihat semua untuk kebutuhan operasional.
     *
     * Tanpa rule ini, ada risiko user lain bisa akses /api/custom-products/{id} dan ngintip barang orang.
     */
    public function view(User $user, CustomProduct $customProduct): bool
    {
        //
    }

    /**
     * create(User $user): bool
     * ---------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh membuat CustomProduct baru.
     *
     * Kapan dipanggil?
     * - Sebelum melakukan store() di controller:
     *      $this->authorize('create', CustomProduct::class);
     *
     * Biasanya:
     * - Semua user yang login boleh membuat custom product request mereka sendiri.
     * - Jadi rule paling wajar:
     *
     *      return $user !== null;
     *
     * - Atau bisa juga batasi role tertentu aja yang boleh submit custom order.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * update(User $user, CustomProduct $customProduct): bool
     * ---------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh mengubah data CustomProduct tertentu.
     *   (misalnya mengubah ukuran, mengganti warna kayu, dsb.)
     *
     * Kapan dipanggil?
     * - Di controller saat update():
     *      $this->authorize('update', $customProduct);
     *
     * Pola paling aman:
     *
     *   return $user->id === $customProduct->user_id
     *       || in_array($user->role, ['admin', 'owner']);
     *
     * Kenapa ini penting?
     * - Tanpa check ini, user lain bisa PATCH/PUT ke customProduct ID orang lain → kebocoran / sabotase.
     */
    public function update(User $user, CustomProduct $customProduct): bool
    {
        //
    }

    /**
     * delete(User $user, CustomProduct $customProduct): bool
     * ---------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh menghapus CustomProduct tertentu.
     *
     * Kapan dipanggil?
     * - Di controller destroy():
     *      $this->authorize('delete', $customProduct);
     *
     * Pertanyaan penting:
     * - Apakah user boleh menghapus desain custom mereka sendiri?
     * - Atau hanya admin yang boleh hapus (supaya histori tetap ada)?
     *
     * Pola umum yang sering dipakai:
     *
     *   return $user->id === $customProduct->user_id
     *       || in_array($user->role, ['admin', 'owner']);
     *
     * Kalau kamu pakai soft delete di model CustomProduct (trait SoftDeletes),
     * maka "delete" di sini artinya soft delete (tandai deleted_at).
     */
    public function delete(User $user, CustomProduct $customProduct): bool
    {
        //
    }

    /**
     * restore(User $user, CustomProduct $customProduct): bool
     * ---------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh me-restore (mengembalikan) CustomProduct yang sebelumnya di-soft-delete.
     *
     * Relevan HANYA jika:
     * - Model CustomProduct pakai SoftDeletes (punya kolom deleted_at).
     *
     * Biasanya hanya pemiliknya sendiri atau admin yang boleh balikin.
     *
     * Contoh rule yang masuk akal:
     *
     *   return $user->id === $customProduct->user_id
     *       || in_array($user->role, ['admin', 'owner']);
     */
    public function restore(User $user, CustomProduct $customProduct): bool
    {
        //
    }

    /**
     * forceDelete(User $user, CustomProduct $customProduct): bool
     * ---------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh melakukan "force delete" = hapus permanen dari DB,
     *   bukan sekadar soft delete.
     *
     * Catatan penting:
     * - Aksi ini biasanya cuma boleh dilakukan oleh role tingkat tinggi (owner / admin),
     *   karena setelah force delete, datanya benar-benar hilang dari database,
     *   tidak bisa di-restore lagi.
     *
     * Pola umum:
     *
     *   return in_array($user->role, ['admin', 'owner']);
     *
     * Kalau aplikasi kamu tidak pernah butuh hapus permanen untuk CustomProduct,
     * method ini mungkin tidak akan pernah dipanggil.
     */
    public function forceDelete(User $user, CustomProduct $customProduct): bool
    {
        //
    }
}
