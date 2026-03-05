<?php

namespace App\Policies;
// ← namespace harus sesuai struktur folder (app/Policies). Laravel akan mencari policy di folder ini
//    ketika kita gunakan Gate/@can/$this->authorize() untuk model tertentu.

use App\Models\OrderItem; // ← Model yang ingin kita lindungi aksesnya (baris item dalam pesanan).
use App\Models\User;      // ← Model user yang sedang login (subjek otorisasi).
use Illuminate\Auth\Access\Response;
// ← Response bisa dipakai kalau kamu mau balikin Response::allow() / Response::deny('alasan').
//    Saat ini method-method kita bertipe bool, jadi Response belum terpakai. Aman dibiarkan.

/**
 * CLASS: OrderItemPolicy
 * =====================================================================================
 * Policy ini mendeskripsikan aturan "siapa boleh ngapain" terhadap OrderItem.
 *
 * Kenapa penting?
 * - OrderItem biasanya berisi data detail pesanan:
 *   • produk apa yang dibeli,
 *   • berapa qty,
 *   • berapa harga,
 *   • ukuran / kustomisasi.
 *
 * Data ini sifatnya sensitif (misal, nominal belanja user lain).
 * Jadi jangan sampai user A bisa lihat / edit OrderItem milik user B.
 *
 * Cara pasang policy ini:
 *
 * 1. Daftarkan di App\Providers\AuthServiceProvider:
 *
 *    protected $policies = [
 *        OrderItem::class => OrderItemPolicy::class,
 *    ];
 *
 * 2. Pakai di controller:
 *
 *    $this->authorize('view', $orderItem);
 *    // Akan panggil OrderItemPolicy::view($currentUser, $orderItem)
 *
 * 3. Pakai di Blade:
 *
 *    @can('delete', $orderItem)
 *        <button>Hapus</button>
 *    @endcan
 *
 * Masing-masing method di bawah akan mengembalikan boolean:
 * - true  = diizinkan
 * - false = ditolak
 *
 * Default skeleton dari Laravel masih kosong (`//`). Itu artinya kalau kamu panggil
 * policy ini saat kosong, bakal error karena function wajib return bool tapi tidak ada return.
 *
 * Jadi nanti kamu HARUS mengisi logikamu sendiri.
 * =====================================================================================
 */
class OrderItemPolicy
{
    /**
     * viewAny(User $user): bool
     * ---------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh melihat DAFTAR OrderItem (bukan item spesifik).
     *
     * Kapan dipanggil?
     * - Biasanya di controller index(), misalnya:
     *     $this->authorize('viewAny', OrderItem::class);
     *
     * Gaya aturan yang sering dipakai:
     * - Hanya admin/owner boleh lihat semua order item dari semua orang
     *   (misalnya di halaman admin panel).
     * - Pelanggan biasa TIDAK BOLEH melihat `semua` order item global,
     *   karena itu akan bocorin belanjaan orang lain.
     *
     * Implementasi khas:
     *   return in_array($user->role, ['admin', 'owner']);
     *
     * Kalau kamu tidak pernah pakai index() publik untuk OrderItem, kamu bahkan
     * bisa langsung `return false;`.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * view(User $user, OrderItem $orderItem): bool
     * ---------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah $user boleh melihat OrderItem tertentu.
     *
     * Kapan dipanggil?
     * - Misal controller show() butuh menampilkan detail item pesanan:
     *     $this->authorize('view', $orderItem);
     *
     * Kenapa penting:
     * - Setiap OrderItem terkait ke satu Order utama.
     *   Biasanya relasi Eloquent-nya di model OrderItem:
     *      public function order() { return $this->belongsTo(Order::class); }
     *
     *   Dan model Order punya:
     *      public function user() { return $this->belongsTo(User::class); }
     *
     * Jadi pemilik item pesanan = $orderItem->order->user_id
     *
     * Pola rule yang umum:
     *
     *   return $user->id === $orderItem->order->user_id
     *       || in_array($user->role, ['admin', 'owner']);
     *
     * Artinya:
     * - Pemilik pesanan boleh lihat itemnya,
     * - Admin/owner boleh juga, buat keperluan operasional.
     */
    public function view(User $user, OrderItem $orderItem): bool
    {
        //
    }

    /**
     * create(User $user): bool
     * ---------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh membuat OrderItem baru.
     *
     * Catatan penting:
     * - Dalam flow normal e-commerce, user TIDAK menambah OrderItem langsung.
     *   OrderItem biasanya dibuat otomatis saat checkout (copy dari cart).
     * - Biasanya yang boleh "create" OrderItem manual adalah sistem internal
     *   atau admin yang lagi input order offline.
     *
     * Jadi aturan yang sering dipakai:
     *
     *   return in_array($user->role, ['admin', 'owner']);
     *
     * atau bahkan `return false;` kalau kamu tidak ingin user memanggil endpoint create OrderItem secara langsung.
     *
     * Digunakan di controller misal:
     *     $this->authorize('create', OrderItem::class);
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * update(User $user, OrderItem $orderItem): bool
     * ---------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh mengubah OrderItem tertentu.
     *   Contoh perubahan: ubah qty, ubah ukuran custom, ubah harga item.
     *
     * Kapan dipanggil?
     * - Di controller update():
     *     $this->authorize('update', $orderItem);
     *
     * Fakta bisnis:
     * - Secara umum, setelah order dibuat, customer tidak boleh seenaknya ubah harga atau qty.
     * - Perubahan qty/harga biasanya kewenangan admin (misal koreksi stok).
     *
     * Rule yang lazim:
     *
     *   return in_array($user->role, ['admin', 'owner']);
     *
     * atau jika kamu izinkan edit selama status pesanan masih "draft" / "in_cart":
     *
     *   return $user->id === $orderItem->order->user_id
     *       && $orderItem->order->status === 'in_cart';
     */
    public function update(User $user, OrderItem $orderItem): bool
    {
        //
    }

    /**
     * delete(User $user, OrderItem $orderItem): bool
     * ---------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh menghapus OrderItem tertentu.
     *
     * Kapan dipanggil?
     * - Di controller destroy():
     *     $this->authorize('delete', $orderItem);
     *
     * Pertanyaan bisnis:
     * - Bolehkah user membatalkan sebagian item di order setelah checkout?
     * - Atau hanya admin yang boleh menghapus item demi koreksi invoice?
     *
     * Contoh rule ketat (hanya admin/owner):
     *
     *   return in_array($user->role, ['admin', 'owner']);
     *
     * Atau rule fleksibel (pemilik boleh hapus selama order belum diproses):
     *
     *   return $user->id === $orderItem->order->user_id
     *       && in_array($orderItem->order->status, ['in_cart','received']);
     */
    public function delete(User $user, OrderItem $orderItem): bool
    {
        //
    }

    /**
     * restore(User $user, OrderItem $orderItem): bool
     * ---------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh melakukan restore pada OrderItem yang di-soft-delete.
     *
     * Catatan:
     * - Ini hanya relevan kalau model OrderItem pakai SoftDeletes (punya kolom deleted_at).
     * - Biasanya hanya admin / owner yang boleh melakukan restore (balikin item yang terhapus).
     *
     * Contoh rule umum:
     *
     *   return in_array($user->role, ['admin', 'owner']);
     */
    public function restore(User $user, OrderItem $orderItem): bool
    {
        //
    }

    /**
     * forceDelete(User $user, OrderItem $orderItem): bool
     * ---------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh "force delete", yaitu hapus permanen dari database
     *   (bukan sekadar soft delete).
     *
     * Resiko:
     * - Ini final, tidak bisa di-restore.
     *
     * Biasanya:
     * - Hanya super-admin / owner yang boleh melakukan aksi ini.
     *
     * Contoh rule:
     *
     *   return in_array($user->role, ['owner']);
     */
    public function forceDelete(User $user, OrderItem $orderItem): bool
    {
        //
    }
}
