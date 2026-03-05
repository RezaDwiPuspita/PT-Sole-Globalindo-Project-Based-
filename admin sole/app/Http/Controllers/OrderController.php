<?php

namespace App\Http\Controllers;
// ← namespace harus cocok dengan struktur folder (app/Http/Controllers).
//   Ini penting buat autoload PSR-4 Composer. Jadi kelas ini dikenal sebagai App\Http\Controllers\OrderController.

use App\Http\Controllers\Controller;
// ← extends Controller dasar Laravel (bisa punya middleware umum dsb).

use App\Models\Order;      // ← Model Eloquent utk tabel orders
use App\Models\OrderItem;  // ← Model Eloquent utk tabel order_items
use App\Models\Product;    // ← Model produk (dipakai ambil size / daftar produk)
use App\Models\User;       // ← Model user (dipakai pilih customer)
use App\Models\Color;      // ← Model warna (ambil extra_price untuk kayu & rotan)
use App\Models\City;       // ← Model kota untuk geocoding
use App\Models\Customer;
use App\Models\ShippingCost;
use App\Models\ShippingConfig;
use App\Services\ShippingOriginService;
use Illuminate\Http\Request;          // ← Representasi HTTP request yg masuk (body, query, files, dll)
use Illuminate\Support\Facades\Auth;  // ← Untuk autentikasi user
use Illuminate\Support\Facades\DB;    // ← Facade database (digunakan buat transaction begin/commit/rollBack)
use Illuminate\Support\Facades\Http;  // ← Untuk geocoding API
use Carbon\Carbon;                     // ← Untuk manipulasi tanggal

class OrderController extends Controller
{
    public function __construct(protected ShippingOriginService $originService)
    {
    }
    /**
     * index()
     * ------------------------------------------------------------------
     * Tujuan:
     * - Menampilkan daftar semua ORDER ONLINE di halaman admin.
     *
     * Mekanisme:
     * - Order::with(['user','items'])
     *      ->with(...)   : eager loading relasi user dan items milik tiap order
     *                      supaya tidak kena masalah N+1 query saat looping di Blade.
     * - latest()         : ORDER BY created_at DESC
     * - where('type','online')
     * - where('status','!=','in_cart')  : sembunyikan order yang masih "belum jadi", hanya keranjang.
     *
     * Return:
     * - return view('admin.order.order-online', compact('orders'))
     *   mengirim variabel $orders ke Blade.
     */
    public function index()
    {
        $orders = Order::with(['user', 'items'])
            ->latest()
            ->where('type', 'online')
            ->where('status', '!=', 'in_cart')
            ->get();

        return view('admin.order.order-online', compact('orders'));
    }

    /**
     * indexOffline()
     * ------------------------------------------------------------------
     * Tujuan:
     * - Sama seperti index(), tapi untuk ORDER OFFLINE.
     *
     * Catatan:
     * - Offline order = pesanan yang dibuat manual oleh admin / toko fisik.
     */
    public function indexOffline()
    {
        $orders = Order::with(['user', 'items'])
            ->latest()
            ->where('type', 'offline')
            ->where('status', '!=', 'in_cart')
            ->get();

        return view('admin.order.order-offline', compact('orders'));
    }

    /**
     * listAll()
     * ------------------------------------------------------------------
     * Tujuan:
     * - Menampilkan daftar semua pesanan (online & offline) dalam satu list
     * - Dengan filter per bulan dan minggu
     *
     * Parameter:
     * - month: bulan yang dipilih (format: Y-m, contoh: 2024-11)
     * - week: minggu ke berapa (1-4)
     *
     * Return:
     * - view('admin.order.list-pesanan', compact('orders', 'selectedMonth', 'selectedWeek', 'monthName', 'weekRange'))
     */
    public function listAll(Request $request)
    {
        // Hanya admin yang bisa akses, bukan owner
        if (Auth::user()->role === 'owner') {
            abort(403, 'Unauthorized access.');
        }

        // Ambil parameter dari request, jika tidak ada ambil dari session, jika tidak ada lagi gunakan default
        $selectedMonth = $request->input('month', session('orders_list_month', date('Y-m')));
        $selectedWeek = $request->input('week', session('orders_list_week', 1));

        // Simpan ke session agar persist saat pindah halaman
        session(['orders_list_month' => $selectedMonth]);
        session(['orders_list_week' => $selectedWeek]);

        // Parse bulan yang dipilih
        $yearMonth = explode('-', $selectedMonth);
        $year = (int)$yearMonth[0];
        $month = (int)$yearMonth[1];

        // Hitung tanggal awal dan akhir minggu yang dipilih
        $firstDayOfMonth = Carbon::create($year, $month, 1);
        $lastDayOfMonth = $firstDayOfMonth->copy()->endOfMonth();
        
        // Hitung minggu ke berapa dalam bulan
        // Minggu 1: hari 1-7, Minggu 2: hari 8-14, Minggu 3: hari 15-21, Minggu 4: hari 22-akhir bulan
        $dayStart = (($selectedWeek - 1) * 7) + 1;
        $dayEnd = min($dayStart + 6, $lastDayOfMonth->day);
        
        $weekStart = Carbon::create($year, $month, $dayStart)->startOfDay();
        $weekEnd = Carbon::create($year, $month, $dayEnd)->endOfDay();

        // Query order dengan filter tanggal (berdasarkan order_date atau created_at)
        $orders = Order::with(['user', 'items'])
            ->where(function($query) use ($weekStart, $weekEnd) {
                $query->whereBetween('order_date', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')])
                      ->orWhereBetween(DB::raw('DATE(created_at)'), [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')]);
            })
            ->where('status', '!=', 'in_cart')
            ->latest()
            ->get();

        // Nama bulan dalam bahasa Indonesia
        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $monthName = $monthNames[$month] ?? '';

        // Format range tanggal minggu
        $weekRange = $weekStart->format('d') . ' - ' . $weekEnd->format('d') . ' ' . $monthName . ' ' . $year;

        // ===================== RINGKASAN PRODUK (UNTUK TABEL) =====================
        // Ambil semua order items dari orders yang sudah difilter
        $orderItems = OrderItem::whereHas('order', function($query) use ($weekStart, $weekEnd) {
                $query->where(function($q) use ($weekStart, $weekEnd) {
                    $q->whereBetween('order_date', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')])
                      ->orWhereBetween(DB::raw('DATE(orders.created_at)'), [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')]);
                })
                ->where('status', '!=', 'in_cart');
            })
            ->with(['product', 'customProduct', 'order'])
            ->get();

        // Kelompokkan dan jumlahkan quantity berdasarkan kombinasi produk
        $productSummary = [];
        foreach ($orderItems as $item) {
            // Ambil nama produk
            $productName = 'Custom Product';
            if ($item->product_id && $item->product) {
                $productName = $item->product->title;
            } elseif ($item->custom_product_id && $item->customProduct) {
                $productName = $item->customProduct->name ?? 'Custom Product';
            }

            // Ambil material
            $material = $item->material ?? '-';
            if (empty($material) || $material === '-') {
                if ($item->product && $item->product->default_bahan) {
                    $material = $item->product->default_bahan;
                } elseif ($item->custom_product_id && $item->customProduct && $item->customProduct->material) {
                    $material = $item->customProduct->material;
                } else {
                    $material = '-';
                }
            }

            // Ambil warna kayu
            $woodColor = $item->wood_color ?? '-';

            // Ambil warna rotan
            $rattanColor = $item->rattan_color ?? '-';

            // Format dimensi
            $dimensions = '-';
            if ($item->length && $item->width && $item->height) {
                $dimensions = $item->length . ' x ' . $item->width . ' x ' . $item->height;
            } elseif ($item->product) {
                if ($item->product->default_length && $item->product->default_width && $item->product->default_height) {
                    $dimensions = $item->product->default_length . ' x ' . $item->product->default_width . ' x ' . $item->product->default_height;
                }
            }

            // Buat key unik untuk grouping
            $key = md5($productName . '|' . $material . '|' . $woodColor . '|' . $rattanColor . '|' . $dimensions);

            if (!isset($productSummary[$key])) {
                $productSummary[$key] = [
                    'product_name' => $productName,
                    'material' => $material,
                    'wood_color' => $woodColor,
                    'rattan_color' => $rattanColor,
                    'dimensions' => $dimensions,
                    'quantity' => 0
                ];
            }

            $productSummary[$key]['quantity'] += $item->quantity;
        }

        // Convert ke array dan sort by quantity descending
        $productSummary = collect($productSummary)->sortByDesc('quantity')->values()->all();

        return view('admin.order.list-pesanan', compact('orders', 'selectedMonth', 'selectedWeek', 'monthName', 'weekRange', 'year', 'productSummary'));
    }

    /**
     * create()
     * ------------------------------------------------------------------
     * Tujuan:
     * - Menampilkan form pembuatan order OFFLINE (input manual dari admin).
     *
     * Data yang dikirim ke view:
     * - $customers : semua user dengan role 'customer', untuk dipilih sebagai pemilik order.
     * - $products  : semua product yang bisa dipilih sebagai item order.
     *
     * Return:
     * - view('admin.order.create-offline', compact('customers','products'))
     */
    public function create()
    {
        $customers = User::where('role', 'customer')->get();
        
        // Load produk dengan relasi warna dan variants (untuk ditampilkan di dropdown saat produk dipilih)
        $products = Product::with(['woodColors', 'rattanColors', 'variants'])->get();
        
        // Master warna (untuk fallback atau custom product tanpa produk)
        $woodColors = Color::where('type', 'wood')->get();
        $rattanColors = Color::where('type', 'rattan')->get();

        // Siapkan data produk untuk JavaScript (hindari nested closures di Blade)
        // PERBAIKAN: Tambahkan data variants (material) untuk dropdown bahan dan perhitungan harga
        $productsData = $products->map(function($product) {
            return [
                'id' => $product->id,
                'title' => $product->title,
                'price' => $product->price,
                'wood_colors' => $product->woodColors->map(function($c) {
                    return [
                        'name' => $c->name,
                        'extra_price' => $c->pivot->extra_price ?? 0
                    ];
                })->values()->toArray(),
                'rattan_colors' => $product->rattanColors->map(function($c) {
                    return [
                        'name' => $c->name,
                        'extra_price' => $c->pivot->extra_price ?? 0
                    ];
                })->values()->toArray(),
                'default_bahan' => $product->default_bahan,
                'default_length' => $product->default_length,
                'default_width' => $product->default_width,
                'default_height' => $product->default_height,
                // Tambahkan data variants (material) untuk dropdown bahan dan perhitungan harga
                'variants' => $product->variants()
                    ->where('type', 'material')
                    ->get()
                    ->map(function($variant) {
                        return [
                            'name' => $variant->name,
                            'length_price' => (float) ($variant->length_price ?? 0),
                            'width_price' => (float) ($variant->width_price ?? 0),
                            'height_price' => (float) ($variant->height_price ?? 0),
                        ];
                    })->values()->toArray(),
            ];
        })->values()->toArray();

        return view('admin.order.create-offline', compact('customers', 'products', 'woodColors', 'rattanColors', 'productsData'));
    }

    /**
     * store(Request $request)
     * ------------------------------------------------------------------
     * Tujuan:
     * - Menyimpan order offline baru beserta item-itemnya.
     * - Biasanya dipanggil dari form create() di atas.
     *
     * Flow umum:
     *   1. Mulai DB::beginTransaction() → supaya kalau ada error di tengah,
     *      semua insert dibatalkan (rollback).
     *   2. Validasi request.
     *   3. (opsional) create user baru jika user_id tidak dipilih (kode kamu masih di-comment).
     *   4. Hitung totalAmount dari item.
     *   5. Insert Order.
     *   6. Insert OrderItem untuk masing-masing item.
     *   7. Commit transaction → simpan permanen.
     *
     * on success:
     *   redirect()->route('order.offline')->with('success', ...)
     *
     * on failure:
     *   rollback dan kembali ke form dengan pesan error.
     */
    public function store(Request $request)
    {
        // Mulai transaksi manual.
        DB::beginTransaction();

        try {
            // ===================== VALIDASI INPUT =====================
            // Struktur payload yang diharapkan:
            // - user_id                     : (opsional) user existing
            // - new_customer[name|phone|address]: data customer baru jika user_id kosong
            // - payment_method              : 'cash'|'transfer'|'credit_card'
            // - items[]                     : daftar item (minimal 1)
            //      - quantity, price
            //      - product_id (jika type=product)
            //      - material/length/width/height/... (jika type=custom)
            $validated = $request->validate([
                'user_id' => 'nullable|exists:users,id',

                // field new_customer.* diwajibkan HANYA JIKA user_id tidak diisi
                'new_customer.name' => 'required_if:user_id,null',
                'new_customer.phone' => 'required_if:user_id,null',
                'new_customer.address' => 'required_if:user_id,null',

                'payment_method' => 'required|in:cash,transfer,credit_card',

                // province dan city dipakai HANYA untuk perhitungan ongkir (tidak disimpan ke DB)
                'province' => 'required_if:city_id,null|string',
                'city' => 'required_if:city_id,null|string',
                'city_id' => 'nullable|exists:cities,id',
                
                'items' => 'required|array|min:1',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0',

                // Untuk item bertipe "product"
                'items.*.product_id' => 'nullable|required_if:items.*.type,product|exists:products,id',

                // Untuk item bertipe "custom"
                'items.*.material' => 'required_if:items.*.type,custom',

                'items.*.length' => 'nullable|numeric|min:0',
                'items.*.width' => 'nullable|numeric|min:0',
                'items.*.height' => 'nullable|numeric|min:0',

                'items.*.wood_color' => 'nullable',
                'items.*.rattan_color' => 'nullable',
            ]);

            // ===================== HITUNG TOTAL AMOUNT =====================
            // totalAmount = Σ (unitPrice(item) * quantity) + shipping(province, items)
            // Catatan penting:
            // - Server MENGABAIKAN price yang dikirim client untuk keamanan; kita hitung ulang di sini.
            // - Shipping dihitung ephemeral (berdasarkan province + volume), TIDAK DISIMPAN.
            $items = $validated['items'];

            $computedItems = [];
            $subtotal = 0.0;

            foreach ($items as $it) {
                $qty = (int)($it['quantity'] ?? 1);

                // Branch 1: produk katalog (punya product_id)
                if (!empty($it['product_id'])) {
                    // Load product dengan relasi yang diperlukan untuk perhitungan harga
                    $product = Product::with(['variants', 'woodColors', 'rattanColors'])
                        ->find($it['product_id']);
                    $unit = $product ? (float)$product->price : 0.0; // fallback 0 kalau produk tidak ditemukan
                    
                    // Jika ada kustomisasi (material, warna, dimensi), hitung ulang harga dengan extra price
                    $material     = $it['material']    ?? null;
                    $length       = (float)($it['length'] ?? 0);
                    $width        = (float)($it['width']  ?? 0);
                    $height       = (float)($it['height'] ?? 0);
                    $woodColor    = $it['wood_color']    ?? null;
                    $rattanColor  = $it['rattan_color']  ?? null;
                    
                    // Jika ada kustomisasi, hitung harga custom
                    if ($material || ($length > 0 && $width > 0 && $height > 0)) {
                        $customPrice = $this->computeCustomUnitPrice(
                            $material, $length, $width, $height, $woodColor, $rattanColor, $product?->id
                        );
                        $unit = $customPrice; // Gunakan harga custom jika ada kustomisasi
                    }
                    
                    $subtotal += $unit * $qty;

                    // simpan data untuk pembuatan OrderItem (dengan kustomisasi jika ada)
                    $computedItems[] = [
                        'type'         => 'product',
                        'product_id'   => $product?->id,
                        'quantity'     => $qty,
                        'unit_price'   => $unit,
                        'size'         => $product?->size ?? 'Custom',
                        // Simpan kustomisasi jika ada
                        'material'     => $material,
                        'wood_color'   => $woodColor,
                        'rattan_color' => $rattanColor,
                        'length'       => $length > 0 ? $length : null,
                        'width'        => $width > 0 ? $width : null,
                        'height'       => $height > 0 ? $height : null,
                    ];
                } else {
                    // Branch 2: custom furniture
                    $material     = $it['material']    ?? null;
                    $length       = (float)($it['length'] ?? 0);
                    $width        = (float)($it['width']  ?? 0);
                    $height       = (float)($it['height'] ?? 0);
                    $woodColor    = $it['wood_color']    ?? null;
                    $rattanColor  = $it['rattan_color']  ?? null;

                    $unit = $this->computeCustomUnitPrice(
                        $material, $length, $width, $height, $woodColor, $rattanColor, null
                    );

                    $subtotal += $unit * $qty;

                    $computedItems[] = [
                        'type'         => 'custom',
                        'product_id'   => null,
                        'quantity'     => $qty,
                        'unit_price'   => $unit,
                        'size'         => ($length > 0 && $width > 0 && $height > 0)
                            ? "{$length}x{$width}x{$height}" : 'Custom',
                        'material'     => $material,
                        'wood_color'   => $woodColor,
                        'rattan_color' => $rattanColor,
                        'length'       => $length,
                        'width'        => $width,
                        'height'       => $height,
                    ];
                }
            }

            // Hitung ongkir menggunakan logika yang sama dengan API ShippingController
            $city = $this->resolveCityForShipping(
                $validated['city_id'] ?? null,
                $validated['province'],
                $validated['city'] ?? ''
            );

            $shippingDetails = $this->calculateShippingFromItems($city, $computedItems);

            $shipping = $shippingDetails['price'];
            $totalAmount = $subtotal + $shipping;

            // ===================== BUAT ORDER BARU =====================
            $customer = Customer::create([
                'name'     => $validated['new_customer']['name'],
                'phone'    => $validated['new_customer']['phone'],
                'address'  => $validated['new_customer']['address'],
                'city_id'  => $city?->id,
                'type'     => 'offline',
            ]);

            $order = Order::create([
                'order_date'      => now(),
                'payment_method'  => $validated['payment_method'],
                'status'          => 'received',   // pesanan offline default = diterima
                'type'            => 'offline',
                'payment_status'  => 'paid',       // offline diasumsikan sudah bayar
                'payment_time'    => now(),
                'total_amount'    => $totalAmount,
                'total_ongkir'    => $shipping,
                'name'            => $validated['new_customer']['name'],
                'phone'           => $validated['new_customer']['phone'],
                'address'         => $validated['new_customer']['address'],
                'customer_id'     => $customer->id,
                'tracking_number' => str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT), // random resi 8 digit
            ]);

            $this->persistShippingCost($order, $shippingDetails);

            // ===================== SIMPAN ITEM-ITEM ORDER =====================
            foreach ($computedItems as $ci) {
                $orderItemData = [
                    'order_id' => $order->id,
                    'quantity' => $ci['quantity'],
                    'price'    => $ci['unit_price'], // simpan HARGA SATUAN, total_amount hitung dari * qty
                    'size'     => $ci['size'] ?? 'Custom',
                ];

                if ($ci['type'] === 'product') {
                    $orderItemData['product_id']   = $ci['product_id'];
                    // Simpan kustomisasi jika ada (untuk produk yang dikustomisasi)
                    $orderItemData['material']     = $ci['material'] ?? null;
                    $orderItemData['wood_color']   = $ci['wood_color'] ?? null;
                    $orderItemData['rattan_color'] = $ci['rattan_color'] ?? null;
                    $orderItemData['length']       = $ci['length'] ?? null;
                    $orderItemData['width']        = $ci['width'] ?? null;
                    $orderItemData['height']       = $ci['height'] ?? null;
                } else {
                    // Data custom furniture (custom material/warna/ukuran)
                    $orderItemData['material']     = $ci['material'];
                    $orderItemData['wood_color']   = $ci['wood_color'];
                    $orderItemData['rattan_color'] = $ci['rattan_color'];
                    $orderItemData['length']       = $ci['length'];
                    $orderItemData['width']        = $ci['width'];
                    $orderItemData['height']       = $ci['height'];
                    $orderItemData['product_id']   = null;
                }

                OrderItem::create($orderItemData);
            }

            DB::commit();

            return redirect()->route('order.offline')
                ->with('success', 'Order offline berhasil dibuat');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', 'Gagal membuat order: ' . $e->getMessage());
        }
    }

    /**
     * show($id)
     * ------------------------------------------------------------------
     * Tujuan:
     * - Menampilkan detail satu order ke halaman admin.
     */
    public function show($id)
    {
        $order = Order::with(['user', 'items.product', 'items.customProduct'])
            ->where('id', $id)
            ->firstOrFail();

        $order->load(['user', 'items.product', 'items.customProduct']);

        return view('admin.order.show', compact('order'));
    }

    /**
     * edit(Order $order)
     * ------------------------------------------------------------------
     * Tujuan:
     * - Tampilkan halaman edit order (biasanya offline order atau admin override).
     */
    public function edit(Order $order)
    {
        $order->load(['user', 'items.product', 'items.customProduct']);
        $products = Product::all();

        return view('admin.orders.edit', compact('order', 'products'));
    }

    /**
     * update(Request $request, Order $order)
     * ------------------------------------------------------------------
     * Tujuan:
     * - Update data sebuah order (status, payment status, item2 dalam order).
     */
    public function update(Request $request, Order $order)
    {
        DB::beginTransaction();

        try {
            // ===================== VALIDASI REQUEST =====================
            $validated = $request->validate([
                'payment_method'  => 'required|in:cash,transfer,credit_card',
                'type'            => 'required|in:online,offline',
                'payment_status'  => 'nullable|in:waiting_payment,paid',
                'status'          => 'required|in:in_cart,processing,received,in_progress,completed,cancelled',

                // province opsional saat update; jika diberikan, dipakai untuk hitung ulang ongkir
                'province'        => 'nullable|string',

                'items'           => 'sometimes|array|min:1',
                'items.*.id'      => 'sometimes|exists:order_items,id',
                'items.*.type'    => 'sometimes|in:product,custom',
                'items.*.quantity'=> 'sometimes|integer|min:1',
                'items.*.price'   => 'sometimes|numeric|min:0',

                'items.*.product_id'   => 'sometimes|exists:products,id',
                'items.*.material'     => 'sometimes',

                'items.*.length'       => 'sometimes|numeric|min:0',
                'items.*.width'        => 'sometimes|numeric|min:0',
                'items.*.height'       => 'sometimes|numeric|min:0',

                'items.*.wood_color'   => 'nullable',
                'items.*.rattan_color' => 'nullable',
                'city_id'              => 'nullable|exists:cities,id',
            ]);

            // ===================== UPDATE INFO DASAR ORDER =====================
            $order->update([
                'payment_method' => $validated['payment_method'],
                'type'           => $validated['type'],
                'payment_status' => $validated['payment_status'] ?? $order->payment_status,
                'status'         => $validated['status'],
                'payment_time'   => (
                    ($validated['payment_status'] ?? $order->payment_status) === 'paid'
                    && $order->payment_status !== 'paid'
                )
                    ? now()
                    : $order->payment_time,
            ]);

            // ===================== HANDLE ITEMS (JIKA DIKIRIM) =====================
            if (isset($validated['items'])) {
                $currentItemIds = $order->items->pluck('id')->toArray();
                $updatedItemIds = [];

                foreach ($validated['items'] as $item) {
                    $itemData = [
                        'quantity' => $item['quantity'],
                    ];

                    if (isset($item['product_id']) && !empty($item['product_id'])) {
                        $product = Product::find($item['product_id']);
                        $unit = $product ? (float)$product->price : 0.0;
                        
                        // Jika ada kustomisasi, hitung ulang harga
                        $material     = $item['material'] ?? null;
                        $length       = (float)($item['length'] ?? 0);
                        $width        = (float)($item['width']  ?? 0);
                        $height       = (float)($item['height'] ?? 0);
                        $woodColor    = $item['wood_color'] ?? null;
                        $rattanColor  = $item['rattan_color'] ?? null;
                        
                        // Jika ada kustomisasi, hitung harga custom
                        if ($material || ($length > 0 && $width > 0 && $height > 0)) {
                            $customPrice = $this->computeCustomUnitPrice(
                                $material, $length, $width, $height, $woodColor, $rattanColor, $product?->id
                            );
                            $unit = $customPrice;
                        }

                        $itemData['product_id']   = $item['product_id'];
                        $itemData['price']        = $unit; // unit price
                    $itemData['size']         = $product->size ?? 'Custom';
                        $itemData['custom_product_id'] = null;

                        // Simpan kustomisasi jika ada
                        $itemData['material']     = $material;
                        $itemData['wood_color']   = $woodColor;
                        $itemData['rattan_color'] = $rattanColor;
                        $itemData['length']       = $length > 0 ? $length : null;
                        $itemData['width']        = $width > 0 ? $width : null;
                        $itemData['height']       = $height > 0 ? $height : null;
                    } else {
                        // item custom
                        $material     = $item['material'] ?? null;
                        $length       = (float)($item['length'] ?? 0);
                        $width        = (float)($item['width']  ?? 0);
                        $height       = (float)($item['height'] ?? 0);
                        $woodColor    = $item['wood_color'] ?? null;
                        $rattanColor  = $item['rattan_color'] ?? null;

                        $unit = $this->computeCustomUnitPrice(
                            $material, $length, $width, $height, $woodColor, $rattanColor
                        );

                        $itemData['material']     = $material;
                        $itemData['wood_color']   = $woodColor;
                        $itemData['rattan_color'] = $rattanColor;
                        $itemData['length']       = $length;
                        $itemData['width']        = $width;
                        $itemData['height']       = $height;
                        $itemData['product_id']   = null;
                        $itemData['price']        = $unit; // unit price
                        $itemData['size']         = ($length > 0 && $width > 0 && $height > 0)
                            ? "{$length}x{$width}x{$height}" : 'Custom';
                    }

                    if (isset($item['id'])) {
                        OrderItem::where('id', $item['id'])
                            ->where('order_id', $order->id)
                            ->update($itemData);

                        $updatedItemIds[] = $item['id'];
                    } else {
                        $itemData['order_id'] = $order->id;
                        $newItem = OrderItem::create($itemData);
                        $updatedItemIds[] = $newItem->id;
                    }
                }

                $itemsToDelete = array_diff($currentItemIds, $updatedItemIds);
                if (!empty($itemsToDelete)) {
                    OrderItem::whereIn('id', $itemsToDelete)
                        ->where('order_id', $order->id)
                        ->delete();
                }

                // Recalculate total_amount setelah update item
                $order->refresh();

                $subtotal = $order->items->sum(function ($it) {
                    return (float)$it->price * (int)$it->quantity;
                });

                $shippingDetails = $this->emptyShippingDetails();
                $shipping = 0.0;
                if (!empty($validated['province']) || !empty($validated['city_id'])) {
                    $itemsForShip = $order->items->map(function ($it) {
                        return [
                            'type'         => $it->product_id ? 'product' : 'custom',
                            'product_id'   => $it->product_id,
                            'quantity'     => (int)$it->quantity,
                            'unit_price'   => (float)$it->price,
                            'material'     => $it->material,
                            'wood_color'   => $it->wood_color,
                            'rattan_color' => $it->rattan_color,
                            'length'       => (float)$it->length,
                            'width'        => (float)$it->width,
                            'height'       => (float)$it->height,
                        ];
                    })->all();

                    $city = $this->resolveCityForShipping(
                        $validated['city_id'] ?? null,
                        $validated['province'],
                        $validated['city'] ?? ''
                    );

                    $shippingDetails = $this->calculateShippingFromItems($city, $itemsForShip);
                    $shipping = $shippingDetails['price'];
                    $this->persistShippingCost($order, $shippingDetails);
                }

                $order->update([
                    'total_amount' => $subtotal + $shipping,
                    'total_ongkir' => $shipping,
                ]);
            }

            DB::commit();

            return redirect()->route('order.online')
                ->with('success', 'Order berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', 'Gagal memperbarui order: ' . $e->getMessage());
        }
    }

    /**
     * updateStatus(Request $request, $id)
     * ------------------------------------------------------------------
     * Tujuan:
     * - Endpoint cepat hanya untuk update status dan/atau payment_status.
     */
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'status'          => 'required|in:in_cart,processing,received,in_progress,completed,cancelled',
            'payment_status'  => 'nullable|in:waiting_payment,paid'
        ]);

        $updateData = [
            'status'         => $request->status,
            'payment_status' => $request->payment_status ?? $order->payment_status,
        ];

        if ($request->payment_status === 'paid' && $order->payment_status !== 'paid') {
            $updateData['payment_time'] = now();
        }

        $order->update($updateData);

        return redirect()->back()->with('success', 'Status order berhasil diperbarui');
    }

    /**
     * destroy($id)
     * ------------------------------------------------------------------
     * Tujuan:
     * - Hapus 1 order dari DB.
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        
        $order->delete();

        return redirect()->back()->with('success', 'Order berhasil dihapus');
    }

    // ===================== Tambahan: utilitas harga & ongkir (ephemeral) =====================

    // Hitung harga satuan produk custom (server-trust).
    // Tambahan harga warna diambil dari tabel colors sesuai input admin.
    private function computeCustomUnitPrice(
        ?string $material,
        float $length,
        float $width,
        float $height,
        ?string $woodColor,
        ?string $rattanColor,
        ?int $productId = null
    ): float {
        // Kalau material atau dimensi belum lengkap → 0
        if (!$material || $length <= 0 || $width <= 0 || $height <= 0) {
            return 0.0;
        }

        // PERBAIKAN: Gunakan harga material dari product_variants jika ada productId
        $priceLength = 0;
        $priceWidth = 0;
        $priceHeight = 0;

        if ($productId) {
            $product = Product::with(['variants', 'woodColors', 'rattanColors'])->find($productId);
            if ($product) {
                // Ambil harga material dari variants
                $variant = $product->variants()
                    ->where('type', 'material')
                    ->where('name', $material)
                    ->first();

                if ($variant) {
                    $priceLength = (float) ($variant->length_price ?? 0);
                    $priceWidth  = (float) ($variant->width_price ?? 0);
                    $priceHeight = (float) ($variant->height_price ?? 0);
                }
            }
        }

        // Fallback ke hardcoded jika variant tidak ditemukan
        if ($priceLength === 0 && $priceWidth === 0 && $priceHeight === 0) {
            $materialPrices = [
                'Kayu Jati' => [
                    'length' => 14000,
                    'width'  => 14000,
                    'height' => 14000,
                ],
                'Kayu Jati & Rotan' => [
                    'length' => 20000,
                    'width'  => 20000,
                    'height' => 20000,
                ],
            ];

            $base = $materialPrices[$material] ?? null;
            if (!$base) {
                return 0.0;
            }

            $priceLength = $base['length'];
            $priceWidth  = $base['width'];
            $priceHeight = $base['height'];
        }

        // 2) Harga dari dimensi (tanpa warna)
        $price = ($length / 10) * $priceLength
               + ($width  / 10) * $priceWidth
               + ($height / 10) * $priceHeight;

        // 3) Tambahan harga warna kayu dari pivot table (jika ada productId)
        if ($woodColor && $productId) {
            if (!isset($product)) {
                $product = Product::with(['woodColors'])->find($productId);
            }
            if ($product) {
                $color = $product->woodColors()->where('colors.name', $woodColor)->first();
                if ($color && $color->pivot->extra_price) {
                    $price += (float) $color->pivot->extra_price;
                }
            }
        }

        // 4) Tambahan harga warna rotan dari pivot table (jika ada productId)
        if ($rattanColor && $productId) {
            if (!isset($product)) {
                $product = Product::with(['rattanColors'])->find($productId);
            }
            if ($product) {
                $color = $product->rattanColors()->where('colors.name', $rattanColor)->first();
                if ($color && $color->pivot->extra_price) {
                    $price += (float) $color->pivot->extra_price;
                }
            }
        }

        // Bulatkan ke integer agar konsisten dengan frontend (Math.round)
        return (float) round($price);
    }

    // Hitung ongkir menggunakan logika yang sama dengan API ShippingController (Haversine + volumetrik)
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
        // Ambil volume divisor dari database, fallback ke config
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

        // Tarif per kg (tier) - ambil dari database dan dapatkan rate object untuk relasi
        $rateConfig = ShippingConfig::getRateByWeight($beratVolume);
        $tarifPerKg = $rateConfig ? (float) $rateConfig->tarif_per_kg : 0;
        // Tarif per km - ambil dari database, fallback ke config
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
            'total_volume_cm3' => round($totalVolumeCm3, 2), // total_volume (dari volume_weight)
            'berat_volume' => round($beratVolume, 2), // berat_volume (calculated: total_volume / divisor)
            'total_items' => $totalItems,
            'item_summary' => $itemSummary,
            'city_id' => $city->id,
            // Relasi ke origin dan config
            'shipping_origin_id' => $origin?->id,
            'shipping_config_rate_id' => $rateConfig?->id,
            // Biaya yang dihitung (tarif_per_km tidak disimpan, diambil dari config saat runtime)
            'biaya_jarak' => round($biayaJarak, 2), // calculated: distance × tarif_per_km
            'biaya_berat' => round($biayaBerat, 2), // calculated: berat_volume × tarif_per_kg
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

    // Helper: resolve city untuk shipping (mirip dengan ShippingController)
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
            // Jika geocoding gagal, return null
            report($e);
        }

        return null;
    }

    // Helper: Haversine formula (sama dengan ShippingController)
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
                'total_volume_cm3'  => $details['total_volume_cm3'], // total_volume (dari volume_weight)
                'berat_volume'      => $details['berat_volume'] ?? 0, // berat_volume (calculated)
                'total_items'       => $details['total_items'],
                'item_summary'      => $details['item_summary'],
                'total_summary'     => $this->formatTotalSummary($details),
                'city_id'           => $details['city_id'] ?? null,
                // Relasi ke origin dan config
                'shipping_origin_id' => $details['shipping_origin_id'] ?? null,
                'shipping_config_rate_id' => $details['shipping_config_rate_id'] ?? null,
                // Biaya yang dihitung (tarif_per_km tidak disimpan, diambil dari config saat runtime)
                'biaya_jarak'       => $details['biaya_jarak'] ?? 0, // calculated: distance × tarif_per_km
                'biaya_berat'       => $details['biaya_berat'] ?? 0, // calculated: berat_volume × tarif_per_kg
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
