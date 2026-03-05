<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\City;
use App\Models\ShippingCost;
use App\Models\ShippingConfig;
use App\Services\ShippingOriginService;
use Illuminate\Http\Request;              // <- tipe objek request HTTP (input dari client)
use Illuminate\Support\Facades\Auth;     // <- facade untuk ambil user yg sedang login (auth:api)
use Illuminate\Support\Facades\Storage;  // <- untuk operasi file storage (upload bukti pembayaran)
use Illuminate\Support\Facades\Http;     // <- untuk geocoding

class OrderController extends Controller
{
    public function __construct(protected ShippingOriginService $originService)
    {
    }

    public function checkout(Request $request)
    {
        // ===================== VALIDASI INPUT =====================
        // $request adalah instance Illuminate\Http\Request yang membawa semua input dari client (JSON/form-data).
        // ->validate([...]) akan otomatis:
        //   - cek aturan (required/in/nullable/dll)
        //   - jika gagal: kirim respons 422 berisi error
        $request->validate([
            'payment_method' => 'required|in:cash,transfer,credit_card', // hanya boleh salah satu
            'type' => 'required|in:online,offline',                      // tipe order menentukan alur status
            'address' => 'nullable|string|max:255',                      // alamat opsional
            'phone' => 'nullable|string|max:255',                        // telepon opsional
            'ongkir' => 'nullable|numeric|min:0',                        // ongkir opsional, min 0
            // PERBAIKAN: Tambahkan validasi untuk data alamat untuk perhitungan ongkir
            'province' => 'nullable|string',                             // provinsi untuk perhitungan ongkir
            'city' => 'nullable|string',                                 // kota/kabupaten untuk perhitungan ongkir
            'city_id' => 'nullable|exists:cities,id',                    // city_id jika ada
        ]);

        // Ambil user yang sedang login dari guard aktif (auth:api sesuai middleware di route/api)
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Address disimpan di order, tidak perlu update user

        // Ambil cart aktif milik user beserta relasi 'items' (eager loading).
        // NOTE: butuh method relasi di model User:
        //   public function activeCart(){ return $this->hasOne(Cart::class)->where('is_active', true); }
        // Jika belum ada, panggilan ini akan error (BadMethodCallException).
        $cart = $user->activeCart()->with('items')->first();

        // Jika tidak ada cart atau cart kosong → tidak bisa checkout
        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        // ===================== BUAT ORDER BARU =====================
        // Logika penentuan status awal:
        //   - type = 'online'  → status order 'received' (terima order), payment_status 'waiting_payment'
        //   - type = 'offline' → status order 'processing' (langsung diproses), payment_status null
        // Tracking number: angka 8 digit acak (dipadding) sebagai contoh.
        
        // Hitung subtotal dari cart items
        $subtotal = $cart->items->sum(function ($item) {
            // $item adalah CartItem; ambil harga satuan * qty
            return $item->price * $item->quantity;
        });
        
        // Ambil ongkir dari request (atau default 0)
        $ongkir = $request->ongkir ?? 0;
        
        $order = Order::create([
            'user_id' => $user->id,
            'order_date' => now(),                              // timestamp saat ini (Carbon instance)
            'payment_method' => $request->payment_method,       // cash/transfer/credit_card
            'status' => $request->type === 'online' ? 'received' : 'processing',
            'type' => $request->type,                           // online/offline (ikut input)
            'payment_status' => $request->type === 'online' ? 'waiting_payment' : null,
            'tracking_number' => str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT), // contoh generator resi
            'name' => $user->name,                              // salin nama user saat ini (snapshot)
            'phone' => $request->phone,                         // nomor telp checkout (boleh beda dari profil)
            'address' => $request->address,                     // alamat kirim (boleh null utk offline)
            'total_ongkir' => $ongkir,                          // PERBAIKAN: Simpan total ongkir secara terpisah
            'total_amount' => $subtotal + $ongkir,              // total = subtotal + ongkir
        ]);

        // ===================== PINDAHKAN ITEM CART → ORDER ITEMS =====================
        foreach ($cart->items as $cartItem) {
            // Siapkan data dasar order item
            $orderItemData = [
                'order_id' => $order->id,       // relasi balik ke order
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->price,    // harga final per item yang sudah dihitung di cart
            ];

            if ($cartItem->product_id) {
                // Item berasal dari produk katalog (bukan custom)
                $orderItemData['product_id'] = $cartItem->product_id;
                $orderItemData['size'] = $cartItem->size ?? null;
                
                // Simpan kustomisasi jika ada (dari cart_item)
                $orderItemData['material'] = $cartItem->bahan ?? null;
                $orderItemData['wood_color'] = $cartItem->color ?? null;
                $orderItemData['rattan_color'] = $cartItem->rotan_color ?? null;
                $orderItemData['length'] = $cartItem->length ?? null;
                $orderItemData['width'] = $cartItem->width ?? null;
                $orderItemData['height'] = $cartItem->height ?? null;
            } else {
                // Item custom: ambil detail dari relasi customProduct
                $orderItemData['custom_product_id'] = $cartItem->custom_product_id;
                if ($cartItem->customProduct) {
                    $orderItemData['material'] = $cartItem->customProduct->material;
                    $orderItemData['wood_color'] = $cartItem->customProduct->wood_color;
                    $orderItemData['rattan_color'] = $cartItem->customProduct->rattan_color;
                    $orderItemData['length'] = $cartItem->customProduct->length;
                    $orderItemData['width'] = $cartItem->customProduct->width;
                    $orderItemData['height'] = $cartItem->customProduct->height;
                } else {
                    // Fallback: ambil dari cart_item langsung jika customProduct tidak ada
                    $orderItemData['material'] = $cartItem->bahan ?? null;
                    $orderItemData['wood_color'] = $cartItem->color ?? null;
                    $orderItemData['rattan_color'] = $cartItem->rotan_color ?? null;
                    $orderItemData['length'] = $cartItem->length ?? null;
                    $orderItemData['width'] = $cartItem->width ?? null;
                    $orderItemData['height'] = $cartItem->height ?? null;
                }
            }

            // Simpan OrderItem ke DB
            OrderItem::create($orderItemData);
        }

        // ===================== HITUNG DAN SIMPAN DETAIL ONGKIR =====================
        // PERBAIKAN: Hitung detail ongkir dan simpan ke shipping_costs (sama seperti order offline)
        // Hitung detail ongkir jika ada alamat lengkap, tidak bergantung pada $ongkir > 0
        if ($request->province || $request->city || $request->city_id) {
            // Resolve city untuk perhitungan ongkir
            $city = $this->resolveCityForShipping(
                $request->city_id,
                $request->province ?? '',
                $request->city ?? ''
            );

            // Siapkan data items untuk perhitungan ongkir
            $itemsForShipping = $cart->items->map(function ($cartItem) {
                return [
                    'length' => (float) ($cartItem->length ?? 0),
                    'width' => (float) ($cartItem->width ?? 0),
                    'height' => (float) ($cartItem->height ?? 0),
                    'quantity' => (int) $cartItem->quantity,
                ];
            })->filter(function ($item) {
                // Hanya item yang punya dimensi lengkap
                return $item['length'] > 0 && $item['width'] > 0 && $item['height'] > 0;
            })->values()->all();

            if ($city && !empty($itemsForShipping)) {
                // Hitung detail ongkir jika city ditemukan dan ada items
                $shippingDetails = $this->calculateShippingFromItems($city, $itemsForShipping);
                
                // Update total_ongkir dengan hasil perhitungan (jika ada)
                if ($shippingDetails['price'] > 0) {
                    $order->update([
                        'total_ongkir' => $shippingDetails['price'],
                        'total_amount' => $subtotal + $shippingDetails['price']
                    ]);
                }
                
                // PERBAIKAN: Simpan detail ke shipping_costs (termasuk distance_km dan biaya_jarak)
                // bahkan jika price = 0, untuk audit trail
                $this->persistShippingCost($order, $shippingDetails);
            } elseif (!empty($itemsForShipping)) {
                // Jika city tidak ditemukan tapi ada items, simpan dengan nilai 0
                // Ini memastikan shipping_costs selalu terisi untuk audit trail
                $emptyDetails = $this->emptyShippingDetails();
                $emptyDetails['city_id'] = null;
                $this->persistShippingCost($order, $emptyDetails);
            }
        }

        // ===================== NONAKTIFKAN CART SETELAH CHECKOUT =====================
        // Agar tidak digunakan lagi pada transaksi berikutnya
        $cart->update(['is_active' => false]);

        // Respons sukses 201 (Created) + payload order
        return response()->json([
            'message' => 'Order created successfully',
            'order' => $order
        ], 201);
    }

    public function uploadPaymentProof(Request $request, Order $order)
    {
        // ===================== OTORISASI PEMILIK ORDER =====================
        // Pastikan order milik user yang sedang login (Auth::id()).
        if ($order->user_id !== Auth::id()) {
             return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Hanya order online yang butuh bukti pembayaran
        if ($order->type !== 'online') {
            return response()->json(['message' => 'Only online orders require payment proof'], 400);
        }

        // Validasi file 'proof' harus image jpeg/png/jpg max 2MB
        $request->validate([
            'proof' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // ===================== UPLOAD FILE KE STORAGE =====================
        // store('payment_proofs', 'public'):
        //   - folder: storage/app/public/payment_proofs
        //   - disk: 'public' = sesuai config/filesystems.php
        // NOTE: pastikan sudah 'php artisan storage:link' agar bisa diakses via /storage/...
        $path = $request->file('proof')->store('payment_proofs', 'public');

        // Simpan path file & set payment_status kembali 'waiting_payment' (nanti admin verifikasi → set 'paid')
        $order->update([
            'payment_proof' => $path,
            'payment_status' => 'waiting_payment' // Status akan diubah admin setelah verifikasi manual
        ]);

        return response()->json(['message' => 'Payment proof uploaded successfully']);
    }

    public function orderHistory()
    {
        // Ambil semua order milik user login, urut terbaru, dan eager-load relasi items.product & items.customProduct
        // ->with([...]) mencegah N+1 query saat mengakses relasi pada koleksi.
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $orders = $user->orders()
            ->with(['items.product', 'items.customProduct'])
            ->latest()
            ->get();
        return response()->json($orders);
    }

    public function unpaidOrders()
    {
        // Ambil order yang:
        //   - payment_status = 'waiting_payment'
        //   - type = 'online'
        //   - milik user yang login
        //   - beserta relasi items.product/customProduct
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $orders = $user->orders()
            ->with(['items.product', 'items.customProduct'])
            ->latest()
            ->where('payment_status', 'waiting_payment')
            ->where('type', 'online')
            ->get();

        return response()->json($orders);
    }

    public function orderDetail($orderId)
    {
        // Cari order berdasarkan tracking_number ATAU id
        // ->with([...]) untuk load relasi
        // ->firstOrFail() akan melempar 404 jika tidak ditemukan
        $order = Order::with(['items.product', 'items.customProduct'])
            ->where('tracking_number', $orderId)
            ->orWhere('id', $orderId)
            ->firstOrFail();

        // Cegah akses order milik user lain
        if ($order->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // (opsional) pastikan relasi terload (sudah with, ini redundant tapi aman)
        $order->load(['items.product', 'items.customProduct']);

        return response()->json($order);
    }

    // ===================== METHOD UNTUK PERHITUNGAN ONGKIR =====================
    // Method ini sama dengan yang ada di OrderController (untuk konsistensi)

    private function calculateShippingFromItems(?City $city, array $items): array
    {
        if (!$city || $city->lat === null || $city->lng === null) {
            return $this->emptyShippingDetails();
        }

        $originData = $this->originService->getOriginCoordinates();
        $originLat = $originData['lat'];
        $originLng = $originData['lng'];
        $origin = $originData['origin'] ?? null;

        // Hitung jarak dengan Haversine
        $distanceKm = $this->haversine(
            (float) $originLat,
            (float) $originLng,
            (float) $city->lat,
            (float) $city->lng
        );

        // Hitung total volume dan berat volumetrik
        $divisor = ShippingConfig::getVolumeDivisor();
        $totalVolumeCm3 = 0.0;
        $totalItems = 0;
        $itemSummary = [];
        
        foreach ($items as $it) {
            $l = (float)($it['length'] ?? 0);
            $w = (float)($it['width']  ?? 0);
            $h = (float)($it['height'] ?? 0);
            $qty = (int)($it['quantity'] ?? 1);
        
            if ($l > 0 && $w > 0 && $h > 0) {
                $totalVolumeCm3 += ($l * $w * $h) * max(1, $qty);
                $totalItems += max(1, $qty);
                $itemSummary[] = [
                    'length_cm' => $l,
                    'width_cm' => $w,
                    'height_cm' => $h,
                    'qty' => max(1, $qty),
                ];
            }
        }

        // Hitung berat volume (total_volume / divisor)
        $beratVolume = $totalVolumeCm3 / $divisor;

        // Tarif per kg (tier) - ambil dari database
        $rateConfig = ShippingConfig::getRateByWeight($beratVolume);
        $tarifPerKg = $rateConfig ? (float) $rateConfig->tarif_per_kg : 0;
        // Tarif per km - ambil dari database
        $tarifPerKm = ShippingConfig::getTarifPerKm();

        if ($tarifPerKm <= 0 || $tarifPerKg <= 0) {
            return $this->emptyShippingDetails();
        }

        // Hitung biaya
        $biayaBerat = $beratVolume * $tarifPerKg;
        $biayaJarak = max(0, $distanceKm) * $tarifPerKm;

        $price = round($biayaBerat + $biayaJarak);
        return [
            'price' => $price,
            'distance_km' => round($distanceKm, 2),
            'total_volume_cm3' => round($totalVolumeCm3, 2),
            'berat_volume' => round($beratVolume, 2),
            'total_items' => $totalItems,
            'item_summary' => $itemSummary,
            'city_id' => $city->id,
            'shipping_origin_id' => $origin?->id,
            'shipping_config_rate_id' => $rateConfig?->id,
            'biaya_jarak' => round($biayaJarak, 2),
            'biaya_berat' => round($biayaBerat, 2),
        ];
    }

    private function emptyShippingDetails(): array
    {
        return [
            'price' => 0.0,
            'distance_km' => 0.0,
            'total_volume_cm3' => 0.0,
            'berat_volume' => 0.0,
            'total_items' => 0,
            'item_summary' => [],
            'city_id' => null,
            'biaya_jarak' => 0.0,
            'biaya_berat' => 0.0,
        ];
    }

    private function resolveCityForShipping(?int $cityId, string $province, string $cityName): ?City
    {
        if ($cityId !== null) {
            return City::find($cityId);
        }

        $cityName = trim($cityName);
        if ($cityName === '') {
            return null;
        }

        // Cari di database dulu
        $city = City::where('province', $province)
            ->where('kabupaten', 'LIKE', '%' . $cityName . '%')
            ->first();

        if ($city) {
            return $city;
        }

        // Jika tidak ditemukan, coba geocoding API
        $query = trim($cityName . ', ' . $province . ', Indonesia');
        $baseUrl = config('shipping.geocoding.base_url', 'https://nominatim.openstreetmap.org/search');

        try {
            $response = Http::withHeaders([
                'User-Agent' => config('app.name', 'Laravel') . ' Shipping Geocoder',
            ])->get($baseUrl, [
                'q'              => $query,
                'format'         => 'json',
                'addressdetails' => 1,
                'limit'          => 1,
                'countrycodes'   => 'id',
            ]);

            if ($response->ok()) {
                $results = $response->json();
                if (is_array($results) && count($results) > 0) {
                    $first = $results[0];
                    if (!empty($first['lat']) && !empty($first['lon'])) {
                        $lat = (float) $first['lat'];
                        $lng = (float) $first['lon'];

                        // Validasi koordinat dalam Indonesia
                        if ($lat >= -11 && $lat <= 6 && $lng >= 95 && $lng <= 141) {
                            // Simpan ke database untuk penggunaan selanjutnya
                            return City::create([
                                'kabupaten' => $cityName,
                                'province' => $province,
                                'lat'      => $lat,
                                'lng'      => $lng,
                            ]);
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return null;
    }

    private function haversine($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2 +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    private function persistShippingCost(Order $order, array $details): void
    {
        ShippingCost::updateOrCreate(
            ['order_id' => $order->id],
            [
                'total_ongkir'      => $details['price'],
                'distance_km'       => $details['distance_km'],
                'total_volume_cm3'  => $details['total_volume_cm3'],
                'berat_volume'      => $details['berat_volume'] ?? 0,
                'total_items'       => $details['total_items'],
                'item_summary'      => $details['item_summary'],
                'total_summary'     => $this->formatTotalSummary($details),
                'city_id'           => $details['city_id'] ?? null,
                'shipping_origin_id' => $details['shipping_origin_id'] ?? null,
                'shipping_config_rate_id' => $details['shipping_config_rate_id'] ?? null,
                'biaya_jarak'       => $details['biaya_jarak'] ?? 0,
                'biaya_berat'       => $details['biaya_berat'] ?? 0,
            ]
        );
    }

    private function formatTotalSummary(array $details): string
    {
        $parts = [];

        if ($details['total_items'] > 0) {
            $parts[] = "Items: {$details['total_items']}";
        }

        if (isset($details['total_volume_cm3']) && $details['total_volume_cm3'] > 0) {
            $parts[] = "Total Volume: {$details['total_volume_cm3']} cm³";
        }

        if (isset($details['berat_volume']) && $details['berat_volume'] > 0) {
            $parts[] = "Berat Volume: {$details['berat_volume']} kg";
        }

        if (!empty($parts)) {
            return implode(' · ', $parts);
        }

        return 'Tidak ada volume';
    }
}
