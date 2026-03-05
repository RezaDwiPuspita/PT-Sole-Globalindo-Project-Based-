<?php

namespace App\Policies; 
// ← namespace harus sesuai letak file (app/Policies). Laravel pakai ini buat auto-resolve policy.

use App\Models\ProductVariant; // ← Model yang mau kita proteksi (varian produk spesifik).
use App\Models\User;           // ← User yang lagi login / subjek yang minta akses.
use Illuminate\Auth\Access\Response;
// ← Kelas helper kalau nanti mau return Response::allow() / Response::deny('alasan').
//    Sekarang method masih return bool, jadi Response belum dipakai (aman aja tetep di-import).

/**
 * CLASS: ProductVariantPolicy
 * =====================================================================================
 * Policy = kumpulan aturan "siapa boleh ngapain" terhadap model ProductVariant.
 *
 * ProductVariant itu biasanya baris yang mewakili kombinasi atribut produk,
 * misalnya:
 *    - Kursi Rotan "Sole", ukuran L, warna Natural, harga 1.450.000
 *    - Kursi Rotan "Sole", ukuran S, warna Hitam,    harga 1.200.000
 *
 * Kenapa policy khusus untuk varian?
 *    - Kadang ada role yang boleh ubah info stok/harga varian tapi TIDAK boleh ubah
 *      deskripsi produk utama. Misal: staff gudang bisa update stok varian,
 *      tapi tidak boleh edit nama produk besar.
 *
 * Bagaimana policy dipanggil?
 *    - Di controller, contoh:
 *          $this->authorize('update', $variant);
 *      Ini akan memanggil ProductVariantPolicy::update($currentUser, $variant)
 *
 *    - Di Blade:
 *          @can('delete', $variant)
 *              <button>Hapus Varian</button>
 *          @endcan
 *
 * Supaya Laravel tahu: daftarkan di App\Providers\AuthServiceProvider
 *
 *    protected $policies = [
 *        ProductVariant::class => ProductVariantPolicy::class,
 *    ];
 *
 * Tentang method-method di bawah:
 *  - viewAny(User $user)                   → boleh lihat daftar semua varian?
 *  - view(User $user, ProductVariant $pv)  → boleh lihat detail varian tertentu?
 *  - create(User $user)                    → boleh nambah varian baru?
 *  - update(User $user, ProductVariant $pv)→ boleh edit varian ini?
 *  - delete(User $user, ProductVariant $pv)→ boleh soft-delete varian?
 *  - restore(User $user, ProductVariant $pv)→ boleh restore varian soft-deleted?
 *  - forceDelete(User $user, ProductVariant $pv)→ boleh hapus permanen dari DB?
 *
 * Penting:
 * Saat ini semua method masih kosong ("//"). Karena method harus return bool,
 * kalau kamu panggil authorize() sekarang bakal error (no return).
 * Jadi nanti kamu harus isi logiknya, misal pakai role.
 *
 * Contoh pola role yang umum di dashboard admin:
 *    - 'owner' dan 'admin' boleh semuanya
 *    - 'staff' boleh viewAny, view, update stok
 *    - 'customer' (pembeli biasa) hanya boleh view (kalau memang varian terlihat publik)
 *
 * Kamu bisa bikin helper private untuk DRY:
 *
 *    protected function isAdminLike(User $user): bool
 *    {
 *        return in_array($user->role, ['owner', 'admin']);
 *    }
 *
 * lalu panggil di setiap method:
 *    return $this->isAdminLike($user);
 *
 * =====================================================================================
 */
class ProductVariantPolicy
{
    /**
     * viewAny(User $user): bool
     * ---------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh melihat LIST / INDEX semua varian produk.
     *
     * Kapan dipanggil:
     * - Biasanya di controller index():
     *       $this->authorize('viewAny', ProductVariant::class);
     *
     * Contoh aturan yang sering dipakai:
     * - Hanya staff internal (admin/owner/staff gudang) yang boleh buka halaman
     *   manajemen varian di dashboard.
     *
     * Implementasi tipikal (nanti kamu isi):
     *   return in_array($user->role, ['owner','admin','staff']);
     *
     * Kalau kamu ingin SEMUA user login bisa lihat varian (misal buat katalog publik),
     * bisa aja dibuat:
     *   return true;
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * view(User $user, ProductVariant $productVariant): bool
     * ---------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh melihat detail SATU varian tertentu.
     *
     * Kapan dipanggil:
     * - Controller show():
     *       $this->authorize('view', $variant);
     *
     * Biasanya:
     * - Untuk katalog publik: semua user (bahkan customer) boleh lihat.
     * - Untuk admin panel internal: boleh jadi hanya admin/owner.
     *
     * Contoh rule yang aman secara umum:
     *   return true;
     *
     * Atau rule ketat admin-only:
     *   return in_array($user->role, ['owner','admin']);
     *
     * Kamu juga bisa buat aturan: kalau varian status-nya "draft" / "hidden",
     * hanya admin/owner yang boleh lihat.
     *
     *    if ($productVariant->status === 'hidden') {
     *        return in_array($user->role, ['owner','admin']);
     *    }
     *    return true;
     */
    public function view(User $user, ProductVariant $productVariant): bool
    {
        //
    }

    /**
     * create(User $user): bool
     * ---------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh MENAMBAHKAN varian produk baru.
     *
     * Kapan dipanggil:
     * - Controller store():
     *       $this->authorize('create', ProductVariant::class);
     *
     * Biasanya:
     * - Hanya level 'owner' / 'admin' (atau 'staff gudang' tertentu) yang boleh nambah varian baru
     *   karena menambah varian = menambah kombinasi SKU/stok baru.
     *
     * Contoh rule umum:
     *   return in_array($user->role, ['owner','admin']);
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * update(User $user, ProductVariant $productVariant): bool
     * ---------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh MENGEDIT varian tertentu.
     *   (misalnya ubah harga khusus varian, ubah stok varian).
     *
     * Kapan dipanggil:
     * - Controller update():
     *       $this->authorize('update', $variant);
     *
     * Pola umum di bisnis:
     * - admin / owner boleh apa saja
     * - staff gudang boleh update stok saja (tapi tidak boleh ubah harga),
     *   jadi kadang kamu perlu cek jenis field yang diubah sebelum approve.
     *
     * Versi simpel:
     *   return in_array($user->role, ['owner','admin']);
     *
     * Versi lebih granular (pseudo):
     *   if ($user->role === 'staff') {
     *       // izinkan hanya kalau yang diubah cuma kolom 'stock'
     *   }
     *   if (in_array($user->role, ['owner','admin'])) {
     *       return true;
     *   }
     *   return false;
     */
    public function update(User $user, ProductVariant $productVariant): bool
    {
        //
    }

    /**
     * delete(User $user, ProductVariant $productVariant): bool
     * ---------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh MENGHAPUS varian produk ini.
     *
     * Kapan dipanggil:
     * - Controller destroy():
     *       $this->authorize('delete', $variant);
     *
     * Catatan:
     * - "delete" di policy biasanya dianggap soft delete / menandai tidak aktif.
     * - Menghapus varian berarti SKU itu tidak muncul lagi buat dijual.
     *
     * Rule umum:
     *   return in_array($user->role, ['owner','admin']);
     *
     * Bisa juga kamu larang hapus kalau varian ini masih punya order aktif,
     * misalnya:
     *   if ($productVariant->orderItems()->exists()) {
     *       return false;
     *   }
     *   return in_array($user->role, ['owner','admin']);
     */
    public function delete(User $user, ProductVariant $productVariant): bool
    {
        //
    }

    /**
     * restore(User $user, ProductVariant $productVariant): bool
     * ---------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh "mengembalikan" varian dari soft delete.
     *
     * Kapan dipanggil:
     * - Kalau model ProductVariant pakai SoftDeletes dan kamu panggil:
     *       $this->authorize('restore', $variant);
     *
     * Biasanya:
     * - Hanya owner/admin yang boleh restore.
     *
     * Rule umum:
     *   return in_array($user->role, ['owner','admin']);
     */
    public function restore(User $user, ProductVariant $productVariant): bool
    {
        //
    }

    /**
     * forceDelete(User $user, ProductVariant $productVariant): bool
     * ---------------------------------------------------------------------------------
     * Tujuan:
     * - Menentukan apakah user boleh MENGHAPUS PERMANEN data varian ini dari database.
     *   (bukan sekadar soft delete).
     *
     * Dampak:
     * - Ini high risk karena stok/harga histori SKU itu hilang total.
     * - Biasanya hanya 'owner' yang boleh.
     * - Bahkan kadang bisnis memutuskan: forceDelete SELALU DILARANG → return false;
     *
     * Rule umum:
     *   return in_array($user->role, ['owner']);
     *
     * Atau lebih ketat:
     *   return false;
     */
    public function forceDelete(User $user, ProductVariant $productVariant): bool
    {
        //
    }
}
