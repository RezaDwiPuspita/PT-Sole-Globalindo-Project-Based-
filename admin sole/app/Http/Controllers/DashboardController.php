<?php

namespace App\Http\Controllers;
// ← namespace harus sesuai struktur folder (app/Http/Controllers).
//   Ini penting buat autoload Composer (PSR-4). Jadi kelas ini dikenal sebagai App\Http\Controllers\DashboardController.

use App\Http\Controllers\Controller;
// ← extends Controller bawaan Laravel. Biasanya class Controller punya middleware umum / helper shared.

use App\Models\Order;   // ← Model Eloquent untuk tabel orders
use App\Models\Product; // ← Model Eloquent untuk tabel products
use App\Models\User;    // ← Model Eloquent untuk tabel users
use Carbon\Carbon;      // ← Library tanggal/waktu berbasis OOP yang dipakai Laravel
use Illuminate\Support\Facades\DB; // ← DB facade: dipakai kalau kamu mau query builder mentah/aggregate manual

class DashboardController extends Controller
{
    /**
     * index()
     * ------------------------------------------------------------------
     * Tujuan:
     * - Menyiapkan data statistik & chart untuk dashboard ADMIN (bukan owner).
     * - Render view resources/views/admin/dashboard/index.blade.php
     *
     * Return:
     * - view('admin.dashboard.index', compact(...))
     *   → me-render Blade sekaligus mengirim variabel $stats, $chartData, $topProducts
     *
     * Keterangan data yang dihitung:
     * - total_users               : jumlah semua user
     * - total_offline_orders      : jumlah order dengan type = 'offline'
     * - total_online_orders       : jumlah order dengan type = 'online'
     * - total_received_order      : jumlah order status 'received'
     * - total_process_order       : jumlah order status 'in_progress'
     * - total_done_order          : jumlah order status 'completed'
     * - recent_users              : 5 user terbaru (User::latest() = orderBy created_at desc)
     *
     * - $chartData:
     *   ambil daftar status + total count per status untuk bikin chart "Order Status".
     *   Kemudian setiap status di-map pakai mapStatusOrder() supaya teks status lebih ramah dibaca.
     *   (mapStatusOrder() tampaknya helper global yg mengubah 'received' -> 'Pesanan diterima', dst.)
     *
     * - $topProducts:
     *   melakukan join manual antara order_items dan products lewat Query Builder (DB::table),
     *   lalu SUM(order_items.quantity) per product_id -> TOP 5 produk terlaris.
     *   yang di-select: products.title dan total_sold.
     */
    public function index()
    {
        // ===================== STATISTIK RINGKAS =====================
        $stats = [
            // Hitung semua user. SELECT count(*) FROM users;
            'total_users' => User::count(),

            // Total order offline (order.type = 'offline')
            'total_offline_orders' => Order::where('type', 'offline')->count(),

            // Total order online (order.type = 'online')
            'total_online_orders' => Order::where('type', 'online')->count(),

            // Total status tertentu:
            // status 'received'    = baru diterima
            'total_received_order' => Order::where('status', 'received')->count(),
            // status 'in_progress' = sedang diproses
            'total_process_order' => Order::where('status', 'in_progress')->count(),
            // status 'completed'   = selesai
            'total_done_order' => Order::where('status', 'completed')->count(),

            // Ambil 5 user terbaru (created_at DESC) → buat ditampilkan di dashboard
            'recent_users' => User::latest()->take(5)->get(),
        ];

        // ===================== DATA UNTUK CHART STATUS ORDER =====================
        // Hasil query:
        // [
        //   { status: "received", total: 10 },
        //   { status: "in_progress", total: 4 },
        //   { status: "completed", total: 7 },
        //   ...
        // ]
        //
        // - select('status', DB::raw('count(*) as total')):
        //     SELECT status, COUNT(*) as total FROM orders GROUP BY status ORDER BY status;
        $chartData = Order::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->orderBy('status')
            ->get();

        // map() di Collection akan mengubah setiap item.
        // mapStatusOrder($item->status) -> helper (milik kamu) yg kelihatannya menerjemahkan status internal
        //   jadi label human readable, contoh 'in_progress' -> 'Diproses'.
        $chartData->map(function ($item) {
            $item->status = mapStatusOrder($item->status); // ← penting: ubah field status jadi label tampilan
        });

        // ===================== TOP 5 PRODUK TERLARIS =====================
        // Tujuan: cari produk mana paling banyak dibeli.
        //
        // JOIN order_items.product_id = products.id,
        // SUM(order_items.quantity) AS total_sold,
        // GROUP BY produk -> urutkan DESC → ambil 5 teratas.
        //
        // DB::table() digunakan di sini (query builder level rendah)
        //   alih-alih pakai relasi Eloquent karena kita mau agregasi SUM cepat.
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select(
                'products.title',
                DB::raw('SUM(order_items.quantity) as total_sold')
            )
            ->groupBy('products.id', 'products.title')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // Render Blade "admin.dashboard.index"
        // compact('stats', 'chartData', 'topProducts')
        //   -> ['stats' => $stats, 'chartData' => $chartData, 'topProducts' => $topProducts]
        return view('admin.dashboard.index', compact('stats', 'chartData', 'topProducts'));
    }

    /**
     * dashboard()
     * ------------------------------------------------------------------
     * Tujuan:
     * - Mirip index(), tapi ini jelasnya buat DASHBOARD OWNER
     *   (lihat return view('admin.dashboard.owner', ...)).
     *
     * Perbedaan besar:
     * - Di sini ada perhitungan revenue harian 14 hari terakhir ($revenueData).
     * - Disediakan statistik tambahan seperti revenue total, revenue_change, orders_change, dll.
     * - Mengirim $statsUser terpisah (seperti rekap pesanan untuk tampilan kartu user).
     *
     * Return:
     * - view('admin.dashboard.owner', compact('stats', 'statsUser', 'chartData', 'topProducts'))
     */
    public function dashboard()
    {
        // ===================== REVENUE DATA (14 HARI TERAKHIR) =====================
        // Kita bangun array $revenueData secara manual dengan loop.
        // $i dari 13 → 0 artinya "13 hari yang lalu" sampai "hari ini".
        //
        // KONSEP PENDAPATAN:
        // Pendapatan dihitung berdasarkan kapan order benar-benar menghasilkan uang:
        // - Untuk order OFFLINE: gunakan updated_at saat status menjadi 'completed'
        // - Untuk order ONLINE: gunakan payment_time jika sudah paid, atau updated_at saat completed
        //
        // Untuk setiap hari:
        //   - 'date'   : format('d') → hanya tanggal (01..31)
        //   - 'amount' : sum('total_amount') dari orders yang:
        //                  - status 'completed' (transaksi selesai)
        //                  - di-complete/dibayar pada tanggal itu
        //
        // Hasil akhir $revenueData misalnya:
        // [
        //   ['date' => '12', 'amount' => 1500000],
        //   ['date' => '13', 'amount' => 900000],
        //   ...
        // ]
        // Format nama bulan (singkatan Indonesia)
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        
        $revenueData = [];
        for ($i = 13; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);

            // Pendapatan dihitung berdasarkan kapan order benar-benar menghasilkan uang:
            // 
            // Untuk order OFFLINE: 
            // - Gunakan updated_at saat status menjadi 'completed'
            // - Asumsi: updated_at terakhir = saat status diubah menjadi 'completed'
            $offlineRevenue = Order::where('type', 'offline')
                ->where('status', 'completed')
                ->whereDate('updated_at', $date)
                ->sum('total_amount');

            // Untuk order ONLINE:
            // - Prioritas 1: payment_time (jika sudah dibayar) - ini yang paling akurat
            // - Prioritas 2: updated_at saat status menjadi 'completed' (jika belum ada payment_time)
            $onlineRevenuePaid = Order::where('type', 'online')
                ->where('status', 'completed')
                ->where('payment_status', 'paid')
                ->whereNotNull('payment_time')
                ->whereDate('payment_time', $date)
                ->sum('total_amount');

            // Order online yang completed tapi belum ada payment_time (atau payment_status bukan paid)
            // Gunakan updated_at sebagai fallback
            $onlineRevenueCompleted = Order::where('type', 'online')
                ->where('status', 'completed')
                ->where(function($query) {
                    $query->whereNull('payment_time')
                          ->orWhere('payment_status', '!=', 'paid');
                })
                ->whereDate('updated_at', $date)
                ->sum('total_amount');

            $totalRevenue = $offlineRevenue + $onlineRevenuePaid + $onlineRevenueCompleted;

            // Format tanggal: "02 Jan" atau "15 Feb" (lebih jelas dengan bulan)
            $monthIndex = (int)$date->format('n') - 1; // n = 1-12, jadi kurangi 1 untuk index array

            $revenueData[] = [
                'date' => $date->format('d') . ' ' . $monthNames[$monthIndex], // format: "02 Jan", "15 Feb", dll
                'date_full' => $date->format('Y-m-d'), // untuk sorting/filtering
                'amount' => $totalRevenue
            ];
        }

        // ===================== STATISTIK UTAMA UNTUK OWNER ($stats) =====================
        // Pendapatan total (semua waktu)
        $totalRevenue = Order::where('status', 'completed')->sum('total_amount');
        
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;
        
        // Pendapatan bulan ini (bulan berjalan) - menggunakan logika yang sama dengan chart harian
        // Order OFFLINE: berdasarkan updated_at
        $offlineRevenueThisMonth = Order::where('type', 'offline')
            ->where('status', 'completed')
            ->whereYear('updated_at', $currentYear)
            ->whereMonth('updated_at', $currentMonth)
            ->sum('total_amount');
        
        // Order ONLINE: berdasarkan payment_time (prioritas) atau updated_at
        $onlineRevenueThisMonthPaid = Order::where('type', 'online')
            ->where('status', 'completed')
            ->where('payment_status', 'paid')
            ->whereNotNull('payment_time')
            ->whereYear('payment_time', $currentYear)
            ->whereMonth('payment_time', $currentMonth)
            ->sum('total_amount');
        
        $onlineRevenueThisMonthCompleted = Order::where('type', 'online')
            ->where('status', 'completed')
            ->where(function($query) {
                $query->whereNull('payment_time')
                      ->orWhere('payment_status', '!=', 'paid');
            })
            ->whereYear('updated_at', $currentYear)
            ->whereMonth('updated_at', $currentMonth)
            ->sum('total_amount');
        
        $revenueThisMonth = $offlineRevenueThisMonth + $onlineRevenueThisMonthPaid + $onlineRevenueThisMonthCompleted;
        
        // Pendapatan tahun ini (tahun berjalan) - menggunakan logika yang sama
        $offlineRevenueThisYear = Order::where('type', 'offline')
            ->where('status', 'completed')
            ->whereYear('updated_at', $currentYear)
            ->sum('total_amount');
        
        $onlineRevenueThisYearPaid = Order::where('type', 'online')
            ->where('status', 'completed')
            ->where('payment_status', 'paid')
            ->whereNotNull('payment_time')
            ->whereYear('payment_time', $currentYear)
            ->sum('total_amount');
        
        $onlineRevenueThisYearCompleted = Order::where('type', 'online')
            ->where('status', 'completed')
            ->where(function($query) {
                $query->whereNull('payment_time')
                      ->orWhere('payment_status', '!=', 'paid');
            })
            ->whereYear('updated_at', $currentYear)
            ->sum('total_amount');
        
        $revenueThisYear = $offlineRevenueThisYear + $onlineRevenueThisYearPaid + $onlineRevenueThisYearCompleted;
        
        // ===================== DATA PENDAPATAN PER BULAN (TAHUN INI) =====================
        // Data untuk chart pendapatan per bulan dalam tahun berjalan
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $revenueMonthlyData = [];
        
        for ($month = 1; $month <= 12; $month++) {
            // Pendapatan order OFFLINE bulan ini
            $offlineRevenueMonth = Order::where('type', 'offline')
                ->where('status', 'completed')
                ->whereYear('updated_at', $currentYear)
                ->whereMonth('updated_at', $month)
                ->sum('total_amount');
            
            // Pendapatan order ONLINE bulan ini (berdasarkan payment_time)
            $onlineRevenueMonthPaid = Order::where('type', 'online')
                ->where('status', 'completed')
                ->where('payment_status', 'paid')
                ->whereNotNull('payment_time')
                ->whereYear('payment_time', $currentYear)
                ->whereMonth('payment_time', $month)
                ->sum('total_amount');
            
            // Order online yang completed tapi belum ada payment_time
            $onlineRevenueMonthCompleted = Order::where('type', 'online')
                ->where('status', 'completed')
                ->where(function($query) {
                    $query->whereNull('payment_time')
                          ->orWhere('payment_status', '!=', 'paid');
                })
                ->whereYear('updated_at', $currentYear)
                ->whereMonth('updated_at', $month)
                ->sum('total_amount');
            
            $totalRevenueMonth = $offlineRevenueMonth + $onlineRevenueMonthPaid + $onlineRevenueMonthCompleted;
            
            $revenueMonthlyData[] = [
                'month' => $monthNames[$month - 1],
                'amount' => $totalRevenueMonth
            ];
        }
        
        $stats = [
            'revenue' => $totalRevenue, // Total semua waktu
            'revenue_this_month' => $revenueThisMonth, // Bulan ini
            'orders' => Order::count(), // Total semua order (offline + online)
            'orders_offline' => Order::where('type', 'offline')->count(),
            'orders_online' => Order::where('type', 'online')->count(),
            'products' => Product::count(),
            'users' => User::count(),
            'revenue_data' => $revenueData, // Data harian 14 hari terakhir
            'revenue_monthly_data' => $revenueMonthlyData, // Data bulanan tahun ini
        ];

        // ===================== STATISTIK ORDER STATUS (GABUNGAN OFFLINE + ONLINE) =====================
        $statsUser = [
            'total_users' => User::count(),
            'total_offline_orders' => Order::where('type', 'offline')->count(),
            'total_online_orders' => Order::where('type', 'online')->count(),
            'total_received_order' => Order::where('status', 'received')->count(), // Gabungan offline + online
            'total_process_order' => Order::where('status', 'in_progress')->count(), // Gabungan offline + online
            'total_done_order' => Order::where('status', 'completed')->count(), // Gabungan offline + online
            'total_cancelled_order' => Order::where('status', 'cancelled')->count(), // Gabungan offline + online
            'total_processing_order' => Order::where('status', 'processing')->count(), // Gabungan offline + online
            'recent_users' => User::all(),
        ];

        // ===================== DATA TABEL ORDER STATUS PER TYPE (OFFLINE/ONLINE) =====================
        // Tabel yang menampilkan jumlah order per status dan per type
        $orderStatusTable = [];
        $statuses = ['received', 'processing', 'in_progress', 'sending', 'completed', 'cancelled'];
        
        foreach ($statuses as $status) {
            $orderStatusTable[] = [
                'status' => $status,
                'status_label' => mapStatusOrder($status),
                'offline' => Order::where('type', 'offline')->where('status', $status)->count(),
                'online' => Order::where('type', 'online')->where('status', $status)->count(),
                'total' => Order::where('status', $status)->count(),
            ];
        }

        // ===================== DATA UNTUK CHART STATUS ORDER =====================
        // Sama konsepnya seperti di index():
        $chartData = Order::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->orderBy('status')
            ->get();

        // map status agar tampil human readable di chart (pakai helper mapStatusOrder()).
        $chartData->map(function ($item) {
            $item->status = mapStatusOrder($item->status);
        });

        // ===================== TOP 5 PRODUK TERLARIS (SEMUA) =====================
        // PERBAIKAN: Hanya hitung dari order yang statusnya 'completed'
        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.status', 'completed') // PERBAIKAN: Hanya order yang sudah selesai
            ->whereNotNull('order_items.product_id')
            ->select(
                'products.title',
                DB::raw('SUM(order_items.quantity) as total_sold')
            )
            ->groupBy('products.id', 'products.title')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // ===================== TOP PRODUK TERLARIS OFFLINE =====================
        // PERBAIKAN: Hanya hitung dari order offline yang statusnya 'completed'
        $topProductsOffline = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.type', 'offline')
            ->where('orders.status', 'completed') // PERBAIKAN: Hanya order yang sudah selesai
            ->whereNotNull('order_items.product_id') // Hanya produk yang punya product_id
            ->select(
                'products.title as product_name',
                'order_items.material',
                'order_items.wood_color',
                'order_items.rattan_color',
                'order_items.length',
                'order_items.width',
                'order_items.height',
                DB::raw('SUM(order_items.quantity) as total_sold')
            )
            ->groupBy(
                'products.id',
                'products.title',
                'order_items.material',
                'order_items.wood_color',
                'order_items.rattan_color',
                'order_items.length',
                'order_items.width',
                'order_items.height'
            )
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // Chart data untuk produk terlaris offline (top 5)
        // PERBAIKAN: Hanya hitung dari order offline yang statusnya 'completed'
        $topProductsOfflineChart = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.type', 'offline')
            ->where('orders.status', 'completed') // PERBAIKAN: Hanya order yang sudah selesai
            ->whereNotNull('order_items.product_id')
            ->select(
                'products.title',
                DB::raw('SUM(order_items.quantity) as total_sold')
            )
            ->groupBy('products.id', 'products.title')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // ===================== TOP PRODUK TERLARIS ONLINE =====================
        // PERBAIKAN: Hanya hitung dari order online yang statusnya 'completed'
        $topProductsOnline = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.type', 'online')
            ->where('orders.status', 'completed') // PERBAIKAN: Hanya order yang sudah selesai
            ->whereNotNull('order_items.product_id') // Hanya produk yang punya product_id
            ->select(
                'products.title as product_name',
                'order_items.material',
                'order_items.wood_color',
                'order_items.rattan_color',
                'order_items.length',
                'order_items.width',
                'order_items.height',
                DB::raw('SUM(order_items.quantity) as total_sold')
            )
            ->groupBy(
                'products.id',
                'products.title',
                'order_items.material',
                'order_items.wood_color',
                'order_items.rattan_color',
                'order_items.length',
                'order_items.width',
                'order_items.height'
            )
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // Chart data untuk produk terlaris online (top 5)
        // PERBAIKAN: Hanya hitung dari order online yang statusnya 'completed'
        $topProductsOnlineChart = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.type', 'online')
            ->where('orders.status', 'completed') // PERBAIKAN: Hanya order yang sudah selesai
            ->whereNotNull('order_items.product_id')
            ->select(
                'products.title',
                DB::raw('SUM(order_items.quantity) as total_sold')
            )
            ->groupBy('products.id', 'products.title')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // ===================== RENDER VIEW OWNER =====================
        // Kirim semua variabel ke Blade resources/views/admin/dashboard/owner.blade.php
        return view(
            'admin.dashboard.owner',
            compact(
                'stats', 
                'statsUser', 
                'chartData', 
                'topProducts', 
                'topProductsOffline', 
                'topProductsOnline',
                'topProductsOfflineChart',
                'topProductsOnlineChart',
                'orderStatusTable'
            )
        );
    }
}
