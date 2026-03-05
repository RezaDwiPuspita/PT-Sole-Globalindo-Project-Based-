<?php

namespace App\Policies;
// ← namespace harus sesuai struktur folder (app/Policies). Laravel akan cari policy di sini
//    ketika kamu pakai $this->authorize() / Gate / @can() untuk model Order.

use App\Models\Order; // ← Model yang diawasi policy ini (pesanan / transaksi).
use App\Models\User;  // ← User yang sedang login (subjek otorisasi).
use Illuminate\Auth\Access\Response;
// ← (opsional) dipakai untuk return Response::allow() / Response::deny('alasan').
//    Sekarang method return bool, jadi Response belum kepake. Aman dibiarkan.

/**
 * CLASS: OrderPolicy
 * =====================================================================================
 * Policy = "aturan izin".
 *
 * Policy ini dipakai untuk menjawab pertanyaan seperti:
 * - Bolehkah user X melihat order Y?
 * - Bolehkah user X meng-update order Y?
 * - Bolehkah user X menghapus order Y?
 *
 * Kenapa penting?
 * - Model Order berisi data transaksi (alamat kirim, nomor HP, total harga, status bayar).
 *   Itu data privat.
 * - Jangan sampai user A bisa mengintip/mengubah Order milik user B.
 *
 * Cara mengaktifkan policy:
 * 1. Daftar di App\Providers\AuthServiceProvider:
 *
 *    protected $policies = [
 *        Order::class => OrderPolicy::class,
 *    ];
 *
 * 2. Panggil di controller:
 *
 *    $this->authorize('view', $order);
 *    // -> ini akan otomatis menjalankan OrderPolicy::view($currentUser, $order)
 *
 * 3. Panggil di Blade:
 *
 *    @can('update', $order)
 *        <button>Edit pesanan</button>
 *    @endcan
 *
 * Penting:
 * - Skeleton bawaan Laravel masih kosong (cuma `//`). Tapi signature method sudah return bool.
 *   Artinya: kalau kamu biarkan kosong lalu dipanggil, ini akan error (no return value for bool).
 *   Jadi kamu WAJIB mendefinisikan logikamu nanti.
 *
 * Pola rule umum untuk e-commerce:
 * - Pelanggan hanya boleh akses pesanannya sendiri.
 * - Admin/owner boleh akses semua order.
 *
 * Contoh simple rule reusable:
 *
 *   protected function isAdmin(User $user): bool
 *   {
 *       return in_array($user->role, ['admin', 'owner']);
 *   }
 *
 *   protected function ownsOrder(User $user, Order $order): bool
 *   {
 *       return $order->user_id === $user->id;
 *   }
 *
 *   // lalu di tiap method:
 *   return $this->isAdmin($user) || $this->ownsOrder($user, $order);
 *
 * =====================================================================================
 */
class OrderPolicy
{
    /**
     * viewAny(User $user): bool
     * ---------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh melihat "daftar semua Order".
     *   Biasanya dipakai di halaman admin -> daftar pesanan semua pelanggan.
     *
     * Dipanggil oleh:
     * - Controller index(), misal:
     *     $this->authorize('viewAny', Order::class);
     *
     * Aturan umum:
     * - Admin/owner boleh, karena mereka perlu dashboard penjualan.
     * - Pelanggan biasa TIDAK boleh lihat semua pesanan (itu bocorin pesanan orang lain).
     *
     * Implementasi khas:
     *     return in_array($user->role, ['admin', 'owner']);
     *
     * Kalau kamu tidak punya halaman "list semua order" (kecuali admin),
     * kamu bisa langsung `return false;` di production untuk user biasa.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * view(User $user, Order $order): bool
     * ---------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh melihat 1 pesanan tertentu.
     *   Misal detail pesanan: alamat, status pembayaran, item yang dibeli, dsb.
     *
     * Dipanggil oleh:
     * - Controller show(), misal:
     *     $this->authorize('view', $order);
     *
     * Kenapa penting:
     * - Ini mencegah user A lihat pesanan user B.
     *
     * Rule e-commerce yang sehat:
     * - User boleh view kalau:
     *     1. Dia pemilik order tersebut
     *        (artinya $order->user_id == $user->id), ATAU
     *     2. Dia admin/owner (punya hak memonitor semua order).
     *
     * Secara logika:
     *
     *     return $order->user_id === $user->id
     *         || in_array($user->role, ['admin', 'owner']);
     *
     * NOTE:
     * - Pastikan kolom user_id memang ada di tabel orders dan diisi dengan ID user pemilik.
     */
    public function view(User $user, Order $order): bool
    {
        //
    }

    /**
     * create(User $user): bool
     * ---------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh membuat order baru.
     *
     * Dipanggil oleh:
     * - Controller store(), misal:
     *     $this->authorize('create', Order::class);
     *
     * Analisis bisnis:
     * - Biasanya SEMUA user yang login boleh checkout dan bikin order (pesanan sendiri).
     * - Bahkan kadang guest checkout juga diizinkan (tanpa policy).
     *
     * Rule simple:
     *     return true;
     *
     * Atau kalau kamu mau batasi hanya role tertentu (misal sistem B2B), kamu bisa cek role di sini.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * update(User $user, Order $order): bool
     * ---------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh mengupdate 1 order tertentu.
     *   Contoh update:
     *   - Ubah alamat pengiriman
     *   - Ubah status pesanan
     *   - Upload bukti bayar
     *
     * Dipanggil oleh:
     * - Controller update(), misal:
     *     $this->authorize('update', $order);
     *
     * Kenapa ini tricky:
     * - Pelanggan boleh mengubah ALAMAT HANYA SELAMA order belum dikirim.
     * - Pelanggan TIDAK boleh ubah status jadi "Selesai" sendiri.
     * - Admin boleh ubah status (Diproses, Dikirim, Diterima, Dibatalkan, dll).
     *
     * Pola umum:
     *
     *     // izinkan kalau dia pemilik order DAN order masih tahap awal
     *     $isOwnerAndEditable =
     *         $order->user_id === $user->id &&
     *         in_array($order->status, ['received', 'processing', 'in_cart', 'waiting_payment']);
     *
     *     // atau kalau dia admin/owner
     *     $isAdmin = in_array($user->role, ['admin', 'owner']);
     *
     *     return $isOwnerAndEditable || $isAdmin;
     *
     * Kamu bebas definisikan sendiri daftar status mana yang boleh diubah oleh customer.
     */
    public function update(User $user, Order $order): bool
    {
        //
    }

    /**
     * delete(User $user, Order $order): bool
     * ---------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh "menghapus" (cancel / delete) order tertentu.
     *
     * Dipanggil oleh:
     * - Controller destroy(), misal:
     *     $this->authorize('delete', $order);
     *
     * Perlu bedain dua hal:
     * - "Cancel pesanan" (set status jadi dibatalkan), biasanya boleh untuk pemilik kalau masih awal.
     * - "Delete hard dari DB" (bener-bener hilang), biasanya cuma boleh admin/owner.
     *
     * Pola umum:
     *
     *     // Customer boleh batalkan pesanan sendiri kalau statusnya belum diproses
     *     $canCustomerCancel =
     *         $order->user_id === $user->id &&
     *         in_array($order->status, ['received', 'waiting_payment']);
     *
     *     // Admin boleh delete kapan aja (misal order dummy / test)
     *     $isAdmin = in_array($user->role, ['admin', 'owner']);
     *
     *     return $canCustomerCancel || $isAdmin;
     */
    public function delete(User $user, Order $order): bool
    {
        //
    }

    /**
     * restore(User $user, Order $order): bool
     * ---------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh melakukan restore pada order yang di-soft-delete.
     *
     * Hanya relevan jika model Order pakai SoftDeletes (use Illuminate\Database\Eloquent\SoftDeletes;).
     * Kalau tidak pakai SoftDeletes, kemungkinan besar method ini gak akan kepake sama sekali.
     *
     * Biasanya:
     * - Hanya admin/owner yang boleh balikin order yang sebelumnya dihapus.
     *
     * Implementasi khas:
     *     return in_array($user->role, ['admin', 'owner']);
     */
    public function restore(User $user, Order $order): bool
    {
        //
    }

    /**
     * forceDelete(User $user, Order $order): bool
     * ---------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh "force delete" / hapus permanen dari database.
     *   Ini berbeda dari soft delete.
     *
     * Risiko tinggi:
     * - Data order hilang total (bukti transaksi, bukti pembayaran, histori pelanggan).
     *   Ini biasanya benar-benar cuma boleh dilakukan oleh superadmin/owner
     *   dan bahkan kadang DILARANG (demi histori akuntansi).
     *
     * Aturan umum:
     *
     *     return in_array($user->role, ['owner']);
     *
     * Karena biasanya hanya pemilik bisnis boleh menghapus catatan transaksi permanen.
     */
    public function forceDelete(User $user, Order $order): bool
    {
        //
    }
}
