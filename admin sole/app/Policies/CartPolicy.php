<?php

namespace App\Policies; 
// ← namespace harus cocok dengan struktur folder (app/Policies).
//    Laravel pakai namespace ini saat melakukan autorisasi via Gate / Policy.

use App\Models\Cart;  // ← Model yang mau kita lindungi hak aksesnya (keranjang belanja / shopping cart).
use App\Models\User;  // ← User yang saat ini login dan sedang minta izin melakukan aksi.
use Illuminate\Auth\Access\Response; 
// ← Opsional. Kalau ingin return Response::allow() / Response::deny('pesan'), ini dipakai.
//    Di kode default kita pakai bool, jadi belum dipakai, tapi use-nya aman dibiarkan.

/**
 * Class CartPolicy
 * ------------------------------------------------------------------------------------------------
 * Policy = tempat kita mendefinisikan aturan "siapa boleh melakukan aksi apa" terhadap suatu model.
 *
 * Kaitannya dengan Laravel:
 * - CartPolicy biasanya diregister di App\Providers\AuthServiceProvider:
 *
 *     protected $policies = [
 *         Cart::class => CartPolicy::class,
 *     ];
 *
 * - Setelah diregister, kamu bisa:
 *     $this->authorize('update', $cart);      // di controller
 *     Gate::authorize('delete', $cart);       // manual
 *
 * - Di Blade:
 *     @can('update', $cart)
 *         <button>Edit Keranjang</button>
 *     @endcan
 *
 * - Masing-masing method di policy ini mewakili "action" (view, update, delete, dst).
 *   Laravel akan otomatis panggil method yg sesuai saat kamu memanggil authorize()/@can.
 *
 * Signature method:
 * - Hampir semua method menerima parameter ($user, $cart)
 *   - $user → instance App\Models\User untuk user yang lagi login
 *   - $cart → instance App\Models\Cart yang dicek kepemilikannya
 *
 * Return type:
 * - Semua method sekarang di-declare `: bool` (harus kembalikan true/false).
 *   true  = boleh
 *   false = tolak (akan jadi 403 Forbidden kalau dipanggil via $this->authorize()).
 *
 * Kamu juga BOLEH (opsional) ganti return bool dengan Response, misalnya:
 *
 *   return $user->id === $cart->user_id
 *       ? Response::allow()
 *       : Response::deny('Ini bukan keranjang milik Anda.');
 *
 * Tapi kalau kamu sudah menuliskan type return `: bool`, jangan kembalikan Response (akan type error).
 */
class CartPolicy
{
    /**
     * viewAny(User $user): bool
     * ------------------------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh melihat "daftar semua Cart".
     *
     * Pemakaian tipikal di controller index():
     *   $this->authorize('viewAny', Cart::class);
     *
     * Kapan ini relevan?
     * - Biasanya untuk halaman admin / owner bisnis yang ingin melihat SEMUA keranjang user.
     * - User biasa hampir tidak pernah dikasih akses ke semua cart milik orang lain,
     *   jadi kalau ini cart publik admin-only, logikanya bisa:
     *
     *   return in_array($user->role, ['admin', 'owner']);
     *
     * Catatan:
     * - Sekarang masih kosong (`//`) jadi belum ada nilai return.
     *   Artinya kalau method ini dipanggil, bakal error karena tidak mengembalikan bool.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * view(User $user, Cart $cart): bool
     * ------------------------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh melihat SATU Cart tertentu.
     *
     * Pemakaian tipikal:
     *   $this->authorize('view', $cart);
     *
     * Logika umum yang wajar:
     * - Hanya pemilik cart itu sendiri yang boleh lihat isi cart-nya.
     * - Admin/owner juga boleh (misal untuk customer support).
     *
     * Contoh implementasi praktis:
     *
     *   return $user->id === $cart->user_id
     *       || in_array($user->role, ['admin', 'owner']);
     *
     * Kenapa cek $cart->user_id?
     * - Karena tabel carts biasanya punya kolom user_id sebagai foreign key kepemilikan.
     *
     * Penting:
     * - Ini yang mencegah user A melihat cart milik user B (data sensitif).
     */
    public function view(User $user, Cart $cart): bool
    {
        //
    }

    /**
     * create(User $user): bool
     * ------------------------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh membuat Cart baru.
     *
     * Dipanggil saat:
     *   $this->authorize('create', Cart::class);
     *
     * Use case:
     * - Biasanya SEMUA user yg login boleh punya / bikin cart (apalagi kalau "guest checkout" tidak dipakai).
     * - Atau, kalau bisnis membatasi, kamu bisa bikin aturan khusus.
     *
     * Implementasi tipikal:
     *   return $user !== null;
     *
     * Artinya: selama dia login (punya instance User valid), dia boleh create cart.
     *
     * Kenapa penting?
     * - Bisa kamu pakai kalau ada flow "mulai transaksi baru", "buat keranjang belanja offline", dsb.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * update(User $user, Cart $cart): bool
     * ------------------------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh UPDATE cart tertentu.
     *   Contoh aksi update:
     *     - tambah item
     *     - ubah kuantitas item
     *     - ganti ongkir (kalau itu simpan di cart)
     *
     * Ini dipakai sebelum memodifikasi cart, misalnya:
     *   $this->authorize('update', $cart);
     *
     * Logika umum yang benar:
     * - HANYA pemilik cart yang boleh ngedit cart tersebut.
     *
     * Contoh implementasi realistis:
     *
     *   return $user->id === $cart->user_id;
     *
     * Bisa juga kamu izinkan admin:
     *
     *   return $user->id === $cart->user_id
     *       || in_array($user->role, ['admin', 'owner']);
     *
     * Alasan keamanan:
     * - Ini mencegah user A ngirim PATCH/PUT yang mengedit cart user B (nyolong akses).
     */
    public function update(User $user, Cart $cart): bool
    {
        //
    }

    /**
     * delete(User $user, Cart $cart): bool
     * ------------------------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh menghapus cart tertentu.
     *
     * Dipanggil saat:
     *   $this->authorize('delete', $cart);
     *
     * Skenario umum:
     * - User boleh "mengosongkan / menghapus" cart miliknya sendiri.
     * - Admin support bisa menghapus cart tertentu untuk bersih-bersih data, dsb.
     *
     * Contoh implementasi:
     *
     *   return $user->id === $cart->user_id
     *       || in_array($user->role, ['admin', 'owner']);
     *
     * Catatan:
     * - Kadang ada kolom is_active di carts untuk menandai cart aktif/nonaktif.
     *   Alih-alih hard delete row-nya, kita cuma set is_active = false.
     *   Dalam kasus itu, "delete" policy ini bisa berarti "boleh nonaktifkan".
     */
    public function delete(User $user, Cart $cart): bool
    {
        //
    }

    /**
     * restore(User $user, Cart $cart): bool
     * ------------------------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh me-"restore" cart yang sebelumnya dihapus
     *   *secara soft delete*.
     *
     * Catatan penting:
     * - Ini hanya relevan kalau model Cart pakai SoftDeletes (yaitu kolom deleted_at + trait SoftDeletes).
     * - Kalau Cart kamu TIDAK pakai soft delete, method ini jarang dipakai.
     *
     * Implementasi umum:
     *
     *   return $user->id === $cart->user_id
     *       || in_array($user->role, ['admin', 'owner']);
     *
     * Jika soft delete tidak digunakan sama sekali, kadang method ini dibiarkan tidak dipakai,
     * tapi karena policy bawaan artisan:make:policy generate method ini, tetap ada template-nya.
     */
    public function restore(User $user, Cart $cart): bool
    {
        //
    }

    /**
     * forceDelete(User $user, Cart $cart): bool
     * ------------------------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh menghapus Cart secara permanen dari database
     *   (bukan soft delete, tapi benar-benar destroy/hard delete).
     *
     * Dipanggil saat:
     *   $this->authorize('forceDelete', $cart);
     *
     * Biasanya:
     * - Cuma admin/owner sistem yang boleh hard delete,
     *   supaya user biasa gak bisa benar-benar nge-wipe jejak datanya.
     *
     * Contoh implementasi:
     *
     *   return in_array($user->role, ['admin', 'owner']);
     *
     * Kalau aplikasi kamu tidak pakai konsep force delete sama sekali, method ini bisa dibiarkan,
     * tapi pastikan jangan dipanggil dari controller.
     */
    public function forceDelete(User $user, Cart $cart): bool
    {
        //
    }
}
