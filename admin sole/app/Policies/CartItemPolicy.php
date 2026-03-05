<?php

namespace App\Policies; 
// ← namespace harus sesuai struktur folder (app/Policies). 
//    Laravel akan cari policy di namespace ini saat melakukan authorization via Gate/Policy.

use App\Models\CartItem; // ← Model yang ingin kita lindungi hak aksesnya.
use App\Models\User;     // ← Model user yang sedang login (subjek yang minta akses).
use Illuminate\Auth\Access\Response; 
// ← (Opsional) dipakai kalau kamu mau balikin Response allow()/deny() dengan message,
//    bukan cuma true/false. Saat ini belum dipakai di kode, tapi use-nya sudah disiapkan.

/**
 * Class CartItemPolicy
 * ------------------------------------------------------------------------------------------------
 * Policy = tempat kita mendefinisikan "siapa boleh melakukan aksi apa" terhadap suatu model.
 *
 * Di Laravel:
 * - Policy biasa diregister di AuthServiceProvider, misal:
 *
 *     protected $policies = [
 *         CartItem::class => CartItemPolicy::class,
 *     ];
 *
 * - Setelah diregister, kamu bisa pakai:
 *     $this->authorize('update', $cartItem);
 *     Gate::authorize('delete', $cartItem);
 *
 * - Atau di Blade:
 *     @can('delete', $cartItem)
 *         <button>Hapus</button>
 *     @endcan
 *
 * Intinya:
 * - Setiap method di policy ini akan dipanggil oleh Laravel untuk memutuskan 
 *   apakah $user boleh melakukan aksi tertentu terhadap $cartItem.
 *
 * Konvensi nama method di policy ↔ aksi bawaan Laravel:
 *   - viewAny()     → boleh lihat daftar CartItem?
 *   - view()        → boleh lihat CartItem tertentu?
 *   - create()      → boleh menambahkan item ke cart?
 *   - update()      → boleh mengubah item (misal ubah quantity)?
 *   - delete()      → boleh menghapus item dari cart?
 *   - restore()     → boleh restore item yang soft-deleted? (kalau pakai SoftDeletes)
 *   - forceDelete() → boleh hapus permanen? (hard delete)
 *
 * Return type:
 * - Semua method saat ini return bool.
 *   - true  = diizinkan
 *   - false = ditolak (akan memicu 403 Forbidden kalau dipakai via $this->authorize())
 *
 * Kamu juga bisa ganti return bool dengan `Response` agar bisa ada pesan khusus:
 *
 *   return $user->id === $cartItem->cart->user_id
 *       ? Response::allow()
 *       : Response::deny('Ini bukan cart kamu.');
 *
 * Tapi di template default, Laravel generate return bool.
 */
class CartItemPolicy
{
    /**
     * viewAny(User $user): bool
     * ------------------------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh "melihat daftar semua CartItem".
     *
     * Ini biasanya dipakai di:
     *   $this->authorize('viewAny', CartItem::class);
     *
     * Contoh implementasi yang masuk akal:
     * - Hanya admin/owner yang boleh lihat semua cart item user lain
     *   (misal halaman admin untuk audit keranjang semua user).
     * - User biasa tidak boleh lihat cart item milik ORANG LAIN,
     *   tapi untuk dirinya sendiri, kita biasanya pakai query terfilter, bukan policy ini.
     *
     * Contoh isi valid:
     *   return $user->role === 'admin' || $user->role === 'owner';
     *
     * Sekarang masih kosong (//), artinya belum ditentukan → kalau dipanggil akan error 
     * karena tidak ada nilai bool yang dikembalikan.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * view(User $user, CartItem $cartItem): bool
     * ------------------------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh melihat SATU CartItem tertentu.
     *
     * Ini dipakai saat:
     *   $this->authorize('view', $cartItem);
     *
     * Pola paling umum:
     * - Hanya pemilik cart yang boleh lihat item tersebut.
     * - Admin boleh lihat semua.
     *
     * Contoh implementasi yang sering dipakai:
     *   return $user->id === $cartItem->cart->user_id
     *       || in_array($user->role, ['admin','owner']);
     *
     * Catatan:
     * - $cartItem->cart->user_id mengacu ke pemilik keranjang.
     */
    public function view(User $user, CartItem $cartItem): bool
    {
        //
    }

    /**
     * create(User $user): bool
     * ------------------------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh menambahkan CartItem baru (add to cart).
     *
     * Ini dipanggil saat:
     *   $this->authorize('create', CartItem::class);
     *
     * Kasus umum:
     * - User yang login boleh menambahkan item ke keranjangnya.
     * - Guest (tidak login) tidak boleh → return false.
     *
     * Contoh implementasi sederhana:
     *   return $user !== null;
     *
     * atau kalau sistem butuh role khusus:
     *   return in_array($user->role, ['customer','admin','owner']);
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * update(User $user, CartItem $cartItem): bool
     * ------------------------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh UPDATE CartItem spesifik.
     *   Misal: ubah quantity item keranjang.
     *
     * Dipanggil saat:
     *   $this->authorize('update', $cartItem);
     *
     * Logika umum:
     * - Hanya pemilik keranjang boleh update isinya.
     *
     * Contoh implementasi:
     *   return $user->id === $cartItem->cart->user_id;
     *
     * Kenapa penting?
     * - Ini mencegah user A mengirim request PATCH/PUT ke item cart user B (nyolong akses).
     */
    public function update(User $user, CartItem $cartItem): bool
    {
        //
    }

    /**
     * delete(User $user, CartItem $cartItem): bool
     * ------------------------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh menghapus item dari cart (remove item).
     *
     * Dipanggil saat:
     *   $this->authorize('delete', $cartItem);
     *
     * Pola umum (mirip update):
     * - Pemilik cart boleh hapus.
     * - Admin mungkin juga boleh hapus (misal untuk moderation).
     *
     * Contoh implementasi:
     *   return $user->id === $cartItem->cart->user_id
     *       || in_array($user->role, ['admin','owner']);
     */
    public function delete(User $user, CartItem $cartItem): bool
    {
        //
    }

    /**
     * restore(User $user, CartItem $cartItem): bool
     * ------------------------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh "restore" CartItem yang sudah dihapus secara soft delete.
     *
     * Catatan:
     * - Ini hanya relevan kalau model CartItem pakai trait SoftDeletes.
     *   (yaitu ada kolom deleted_at dan pakai use SoftDeletes;).
     *
     * Dipanggil saat:
     *   $this->authorize('restore', $cartItem);
     *
     * Contoh implementasi tipikal:
     *   return $user->id === $cartItem->cart->user_id;
     */
    public function restore(User $user, CartItem $cartItem): bool
    {
        //
    }

    /**
     * forceDelete(User $user, CartItem $cartItem): bool
     * ------------------------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh menghapus CartItem secara permanen dari database
     *   (bukan sekadar soft delete).
     *
     * Dipanggil saat:
     *   $this->authorize('forceDelete', $cartItem);
     *
     * Biasanya:
     * - Hanya admin/owner yang boleh force delete.
     *
     * Contoh:
     *   return in_array($user->role, ['admin','owner']);
     *
     * Jika kamu tidak pakai soft deletes, kadang method ini jarang dipakai.
     */
    public function forceDelete(User $user, CartItem $cartItem): bool
    {
        //
    }
}
