<?php

namespace App\Http\Controllers\Api;
// Namespace harus sesuai folder (app/Http/Controllers/Api) agar autoload Composer menemukan class ini.

use App\Http\Controllers\Controller;    // Kelas dasar controller Laravel (middleware(), authorize(), dll)
use App\Models\Cart;                    // Model carts
use App\Models\CartItem;                // Model cart_items
use App\Models\CustomProduct;           // Model custom_products
use App\Models\Product;                 // Model products
use Illuminate\Http\Request;            // Representasi HTTP request (body, query, header)
use Illuminate\Support\Facades\Auth;    // Fasad untuk mengambil user yang sedang login

/**
 * CartController
 * ----------------------------------------------------------------
 * Mengelola keranjang belanja:
 *  - Lihat cart aktif user
 *  - Tambah item (custom / katalog)
 *  - Ubah qty item
 *  - Hapus item
 *
 * Aturan penting:
 *  - Tiap user hanya punya 1 cart aktif (carts.is_active = true)
 *  - Item bisa dua tipe: product_id (katalog) ATAU custom_product_id (custom)
 *  - Semua endpoint di sini diasumsikan memakai guard API (user sudah login)
 */
class CartController extends Controller
{
    /**
     * GET /api/cart
     * ------------------------------------------------------------
     * Ambil cart aktif milik user.
     * Jika belum ada: buat cart baru kosong, kembalikan items=[] total=0.
     */
    public function index()
    {
        // Ambil user yang login dari guard aktif
        $user = Auth::user();

        // Ambil cart aktif + eager load relasi item->product dan item->customProduct
        // (mencegah N+1 query saat frontend membaca detail item)
        $cart = $user->activeCart()
            ->with(['items.product', 'items.customProduct'])
            ->first();

        // Jika belum punya cart aktif → buat baru + balas kosong
        if (!$cart) {
            $cart = $user->carts()->create(['is_active' => true]);

            return response()->json([
                'items' => [],
                'total' => 0,
            ]);
        }

        // Hitung total = sum(price * quantity) semua item
        $total = $cart->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        // Transform items untuk memastikan wood_color dan rattan_color tersedia
        $items = $cart->items->map(function ($item) {
            // Normalisasi warna: jika null, set ke string kosong (bukan null)
            $woodColor = $item->color ?? '';
            $rattanColor = $item->rotan_color ?? '';
            
            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'custom_product_id' => $item->custom_product_id,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'size' => $item->size,
                'length' => $item->length,
                'width' => $item->width,
                'height' => $item->height,
                'bahan' => $item->bahan ?? '',
                'color' => $woodColor, // untuk backward compatibility
                'rotan_color' => $rattanColor, // untuk backward compatibility
                'wood_color' => $woodColor, // alias untuk frontend
                'rattan_color' => $rattanColor, // alias untuk frontend
                'product' => $item->product,
                'custom_product' => $item->customProduct,
            ];
        });

        // Kembalikan payload JSON
        return response()->json([
            'cart_id' => $cart->id,
            'items'   => $items,
            'total'   => $total,
        ]);
    }

    /**
     * POST /api/cart/add
     * ------------------------------------------------------------
     * Tambah item ke cart aktif.
     *
     * Kebijakan penggabungan:
     * - CUSTOM: digabung HANYA jika konfigurasi (material, dimensi, wood_color, rattan_color)
     *           identik. Jika ada perbedaan → buat baris baru.
     * - KATALOG: digabung HANYA jika product_id + seluruh opsi (bahan, dimensi, warna)
     *            identik. Jika berbeda → baris baru.
     */
    public function addToCart(Request $request)
    {
        // Validasi input dasar
        $request->validate([
            'product_id'     => 'nullable|exists:products,id', // wajib valid kalau ada
            'custom_product' => 'nullable|array',              // untuk jalur custom
            'quantity'       => 'required|integer|min:1',      // qty minimal 1

            // opsi (dipakai untuk katalog & disalin ke cart_items demi pencocokan cepat)
            'length'         => 'nullable|numeric',
            'width'          => 'nullable|numeric',
            'height'         => 'nullable|numeric',
            'bahan'          => 'nullable|string|max:255',
            'color'          => 'nullable|string|max:255',
            'rotan_color'    => 'nullable|string|max:255',
        ]);

        // User aktif
        $user = Auth::user();

        // Ambil / buat cart aktif (is_active = true)
        $cart = $user->activeCart()->firstOrCreate(['is_active' => true]);

        // Normalisasi qty
        $qty = (int) $request->quantity;

        /* =========================================================
         * CABANG A: ITEM CUSTOM (request mengandung custom_product)
         * =======================================================*/
        if ($request->has('custom_product') && is_array($request->custom_product)) {

            // Ambil payload custom dari client (sudah divalidasi sebagai array)
            $c = $request->custom_product;

            // Ambil & normalisasi nilai2 konfigurasi
            $material     = $c['material']       ?? null;
            $length       = isset($c['length']) ? (float) $c['length'] : null;
            $width        = isset($c['width'])  ? (float) $c['width']  : null;
            $height       = isset($c['height']) ? (float) $c['height'] : null;
            $woodColor    = $c['wood_color']     ?? null;
            $rattanColor  = $c['rattan_color']   ?? null;
            $materialBase = $c['material_price'] ?? 0; // jika ada harga base material
            $name         = 'Custom ' . ($c['name'] ?? 'Product');

            // Hitung harga per unit custom di server agar konsisten/anti manipulasi
            $unitPrice = CustomProduct::calculatePrice(
                $material, $length, $width, $height, $woodColor, $rattanColor
            );

            // Coba cari item custom dengan konfigurasi IDENTIK di cart_items
            // (tanpa join ke custom_products agar query lebih sederhana)
            $existing = $cart->items()
                ->whereNull('product_id')               // memastikan ini jalur custom
                ->whereNotNull('custom_product_id')
                ->where('bahan',       $material)
                ->where('length',      $length)
                ->where('width',       $width)
                ->where('height',      $height)
                ->where('color',       $woodColor)
                ->where('rotan_color', $rattanColor)
                ->first();

            if ($existing) {
                // Konfigurasi identik → gabungkan qty
                $existing->update([
                    'quantity' => $existing->quantity + $qty,
                ]);

                return response()->json(['message' => 'Item custom digabung (qty ditambahkan)'], 201);
            }

            // Konfigurasi BERBEDA → buat entitas custom baru (snapshot konfigurasi)
            $customProduct = CustomProduct::create([
                'name'               => $name,
                'material'           => $material,
                'material_price'     => $materialBase,
                'wood_color'         => $woodColor,
                'wood_color_price'   => $woodColor ? 80000 : 0,  // contoh biaya tetap
                'rattan_color'       => $rattanColor,
                'rattan_color_price' => $rattanColor ? 50000 : 0, // contoh biaya tetap
                'length'             => $length,
                'width'              => $width,
                'height'             => $height,
                'total_price'        => $unitPrice,
                'user_id'            => $user->id ?? null,        // jika skema menyimpan pemilik desain
            ]);

            // Simpan item ke cart + salin konfigurasi ke kolom cart_items
            $cart->items()->create([
                'custom_product_id' => $customProduct->id,
                'quantity'          => $qty,
                'price'             => $unitPrice,    // harga per unit
                'bahan'             => $material,
                'length'            => $length,
                'width'             => $width,
                'height'            => $height,
                'color'             => $woodColor,
                'rotan_color'       => $rattanColor,
            ]);

            return response()->json(['message' => 'Item custom ditambahkan sebagai baris terpisah'], 201);
        }

        /* =========================================================
         * CABANG B: PRODUK KATALOG (NON-CUSTOM)
         * =======================================================*/
        // Pastikan product_id valid (sudah divalidasi exists)
        // Load relasi yang diperlukan untuk perhitungan harga
        $product = Product::with(['variants', 'woodColors', 'rattanColors'])
            ->findOrFail($request->product_id);

        // Ambil opsi yang mempengaruhi penggabungan baris
        $length      = $request->filled('length') ? (float) $request->length : null;
        $width       = $request->filled('width')  ? (float) $request->width  : null;
        $height      = $request->filled('height') ? (float) $request->height : null;
        $bahan       = $request->bahan       ?? null;
        // PERBAIKAN: Cek juga wood_color dan rattan_color dari request (untuk kompatibilitas)
        $woodColor   = $request->wood_color ?? $request->color ?? null;
        $rattanColor = $request->rattan_color ?? $request->rotan_color ?? null;
        
        // Log untuk debug (bisa dihapus setelah fix)
        \Log::info('Add to cart - Product ID: ' . $product->id, [
            'wood_color' => $woodColor,
            'rattan_color' => $rattanColor,
            'color' => $request->color,
            'rotan_color' => $request->rotan_color,
            'all_request' => $request->all()
        ]);

        // Hitung harga per unit untuk produk katalog
        // PERBAIKAN: Gunakan harga material dari product_variants dan extra_price dari colors pivot
        $unitPrice = $this->calculateProductPrice(
            $product, $bahan, $length, $width, $height, $woodColor, $rattanColor
        );

        // Coba gabung HANYA jika semua opsi identik
        $existing = $cart->items()
            ->where('product_id', $product->id)
            ->where('bahan',       $bahan)
            ->where('length',      $length)
            ->where('width',       $width)
            ->where('height',      $height)
            ->where('color',       $woodColor)
            ->where('rotan_color', $rattanColor)
            ->first();

        if ($existing) {
            $existing->update([
                'quantity' => $existing->quantity + $qty,
            ]);
            return response()->json(['message' => 'Item katalog digabung (qty ditambahkan)'], 201);
        }

        // Opsi beda → buat baris baru
        $cart->items()->create([
            'product_id'  => $product->id,
            'quantity'    => $qty,
            'price'       => $unitPrice,       // harga per unit yang dihitung
            'size'        => $product->size ?? null, // contoh: jika kolom size ada di products
            'length'      => $length,
            'width'       => $width,
            'height'      => $height,
            'bahan'       => $bahan,
            'color'       => $woodColor,
            'rotan_color' => $rattanColor,
        ]);

        return response()->json(['message' => 'Item katalog ditambahkan sebagai baris terpisah'], 201);
    }

    /**
     * PUT /api/cart/{cartItem}
     * ------------------------------------------------------------
     * Update quantity via route model binding (param {cartItem}).
     * Cek kepemilikan dulu agar aman.
     */
    public function updateCartItem(Request $request, CartItem $cartItem)
    {
        // Validasi qty baru
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        // Lindungi dari akses item milik user lain
        if ($cartItem->cart->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Update
        $cartItem->update([
            'quantity' => $request->quantity,
        ]);

        return response()->json(['message' => 'Cart item updated']);
    }

    /**
     * DELETE /api/cart/{cartItem}
     * ------------------------------------------------------------
     * Hapus item via route model binding.
     * Jika cart kosong setelah penghapusan → hapus cart (opsional housekeeping).
     */
    public function removeFromCart(CartItem $cartItem)
    {
        // Hanya pemilik yang boleh hapus
        if ($cartItem->cart->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Hapus item
        $cartItem->delete();

        // Jika cart tidak punya item lagi → hapus cart agar tidak ada "cart yatim"
        if ($cartItem->cart->items()->count() === 0) {
            $cartItem->cart->delete();
        }

        return response()->json(['message' => 'Item removed from cart']);
    }

    /**
     * DELETE /api/cart/item/{itemId}
     * ------------------------------------------------------------
     * Hapus item berdasarkan ID manual (tanpa model binding).
     */
    public function destroy($itemId)
    {
        // Ambil item, 404 jika tidak ada
        $cartItem = CartItem::findOrFail($itemId);

        // Cek kepemilikan
        if ($cartItem->cart->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Hapus
        $cartItem->delete();

        return response()->json(['message' => 'Item removed from cart']);
    }

    /**
     * PATCH /api/cart/item/{itemId}
     * ------------------------------------------------------------
     * Ubah quantity dengan ID manual (tanpa model binding).
     */
    public function updateQty(Request $request, $itemId)
    {
        // Ambil item
        $cartItem = CartItem::findOrFail($itemId);

        // Cek kepemilikan
        if ($cartItem->cart->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Validasi qty baru
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        // Update
        $cartItem->update([
            'quantity' => $request->quantity,
        ]);

        return response()->json(['message' => 'Cart item updated']);
    }

    /**
     * Hitung harga produk katalog berdasarkan material, dimensi, dan warna
     * Menggunakan harga material dari product_variants dan extra_price dari colors pivot
     */
    private function calculateProductPrice($product, $bahan, $length, $width, $height, $woodColor = null, $rattanColor = null)
    {
        // Validasi dimensi
        $length = (float) $length;
        $width  = (float) $width;
        $height = (float) $height;

        if ($length <= 0 || $width <= 0 || $height <= 0) {
            return 0;
        }

        // Ambil harga material dari product_variants
        $variant = $product->variants()
            ->where('type', 'material')
            ->where('name', $bahan)
            ->first();

        if (!$variant) {
            // Fallback ke CustomProduct::calculatePrice jika variant tidak ditemukan
            return CustomProduct::calculatePrice($bahan, $length, $width, $height, $woodColor, $rattanColor);
        }

        // Ambil harga per dimensi dari variant
        // Kolom di product_variants: length_price, width_price, height_price
        $priceLength = (float) ($variant->length_price ?? 0);
        $priceWidth  = (float) ($variant->width_price ?? 0);
        $priceHeight = (float) ($variant->height_price ?? 0);

        // Hitung harga dasar dari dimensi
        $lengthPrice = ($length / 10) * $priceLength;
        $widthPrice  = ($width / 10) * $priceWidth;
        $heightPrice = ($height / 10) * $priceHeight;

        $total = $lengthPrice + $widthPrice + $heightPrice;

        // Tambah biaya warna kayu dari pivot table
        if ($woodColor) {
            $woodColorObj = $product->woodColors()
                ->where('colors.name', $woodColor)
                ->first();
            
            if ($woodColorObj && $woodColorObj->pivot->extra_price) {
                $total += (float) $woodColorObj->pivot->extra_price;
            }
        }

        // Tambah biaya warna rotan dari pivot table
        if ($rattanColor) {
            $rattanColorObj = $product->rattanColors()
                ->where('colors.name', $rattanColor)
                ->first();
            
            if ($rattanColorObj && $rattanColorObj->pivot->extra_price) {
                $total += (float) $rattanColorObj->pivot->extra_price;
            }
        }

        return $total;
    }
}
