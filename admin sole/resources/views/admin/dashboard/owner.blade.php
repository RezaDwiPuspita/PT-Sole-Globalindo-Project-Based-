{{-- ======================================================================================
  FILE    : resources/views/.../dashboard.blade.php
  TUJUAN  : Dashboard ringkas: kartu stat, 3 kartu status order user, 3 chart (status, top produk, revenue).
  TEKNO   : Blade + Tailwind CSS + Chart.js + Phosphor Icons
  CATATAN : Struktur HTML tidak diubah; hanya menambah komentar sangat rinci.
====================================================================================== --}}

@extends('layouts.admin')

@section('page')
    Dashboard
@endsection

@section('content')
    <div class="p-6">

        {{-- GRID KARTU STAT --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            {{-- KARTU: REVENUE TOTAL (SEMUA WAKTU) --}}
            <div class="bg-white rounded-lg shadow p-6">
                    <div>
                    <p class="text-sm text-gray-500">Pendapatan Total</p>
                        <h3 class="text-2xl font-bold">Rp{{ number_format($stats['revenue'], 0) }}</h3>
                </div>
                <div class="mt-2 text-xs text-gray-400">
                    Semua waktu
                </div>
                    </div>

            {{-- KARTU: REVENUE BULAN INI --}}
            <div class="bg-white rounded-lg shadow p-6">
                <div>
                    <p class="text-sm text-gray-500">Pendapatan Bulan Ini</p>
                    <h3 class="text-2xl font-bold">Rp{{ number_format($stats['revenue_this_month'], 0) }}</h3>
                </div>
                <div class="mt-2 text-xs text-gray-400">
                    @php
                        $monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                        $currentMonth = $monthNames[date('n') - 1];
                    @endphp
                    {{ $currentMonth }} {{ date('Y') }}
                </div>
            </div>

            {{-- KARTU: ORDERS (GABUNGAN) --}}
            <div class="bg-white rounded-lg shadow p-6">
                    <div>
                        <p class="text-sm text-gray-500">Orders</p>
                        <h3 class="text-2xl font-bold">{{ number_format($stats['orders'], 0) }}</h3>
                    </div>
                <div class="mt-2 text-xs text-gray-400">
                    Total pesanan (Offline + Online)
                </div>
            </div>
                </div>

        {{-- GRID KARTU ORDERS ONLINE & OFFLINE --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            {{-- KARTU: ORDERS OFFLINE --}}
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full aspect-square flex-shrink-0 px-4 bg-green-100 text-green-600 mr-4">
                        <i class="ph ph-storefront text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Order Offline</p>
                        <h3 class="text-2xl font-bold">{{ number_format($stats['orders_offline'], 0) }}</h3>
                    </div>
                </div>
            </div>

            {{-- KARTU: ORDERS ONLINE --}}
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full aspect-square flex-shrink-0 px-4 bg-purple-100 text-purple-600 mr-4">
                        <i class="ph ph-globe text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Order Online</p>
                        <h3 class="text-2xl font-bold">{{ number_format($stats['orders_online'], 0) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- GRID KARTU STATUS ORDER (GABUNGAN OFFLINE + ONLINE) --}}
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">
            {{-- KARTU: TOTAL ORDER DITERIMA --}}
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full aspect-square flex-shrink-0 px-4 bg-blue-100 text-blue-600 mr-4">
                        <i class="ph ph-archive text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Order Diterima</p>
                        <h3 class="text-2xl font-bold">{{ $statsUser['total_received_order'] }}</h3>
                    </div>
                </div>
            </div>

            {{-- KARTU: TOTAL ORDER DIPROSES --}}
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full aspect-square flex-shrink-0 px-4 bg-yellow-100 text-yellow-600 mr-4">
                        <i class="ph ph-gear text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Order Diproses</p>
                        <h3 class="text-2xl font-bold">{{ $statsUser['total_process_order'] }}</h3>
                    </div>
                </div>
            </div>

            {{-- KARTU: TOTAL ORDER SEDANG DIKERJAKAN --}}
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full aspect-square flex-shrink-0 px-4 bg-green-100 text-green-600 mr-4">
                        <i class="ph ph-spinner-gap text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Sedang Dikerjakan</p>
                        <h3 class="text-2xl font-bold">{{ $statsUser['total_processing_order'] }}</h3>
                    </div>
                </div>
            </div>

            {{-- KARTU: TOTAL ORDER SELESAI --}}
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full aspect-square flex-shrink-0 px-4 bg-purple-100 text-purple-600 mr-4">
                        <i class="ph ph-check-circle text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Order Selesai</p>
                        <h3 class="text-2xl font-bold">{{ $statsUser['total_done_order'] }}</h3>
                    </div>
                </div>
            </div>

            {{-- KARTU: TOTAL ORDER DIBATALKAN --}}
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full aspect-square flex-shrink-0 px-4 bg-red-100 text-red-600 mr-4">
                        <i class="ph ph-x-circle text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Order Dibatalkan</p>
                        <h3 class="text-2xl font-bold">{{ $statsUser['total_cancelled_order'] }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- GRID BAGIAN CHART & TABEL STATUS ORDER --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            {{-- CARD: ORDER STATUS CHART --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Order Status</h3>
                <canvas height="300" style="max-height: 300px;" id="orderStatusChart"></canvas>
            </div>

            {{-- TABEL ORDER STATUS PER TYPE (OFFLINE/ONLINE) --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Status Order per Tipe</h3>
                <div class="w-full overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Offline</th>
                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Online</th>
                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($orderStatusTable as $row)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $row['status_label'] }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-center text-gray-900">{{ number_format($row['offline'], 0) }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-center text-gray-900">{{ number_format($row['online'], 0) }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-center font-semibold text-gray-900">{{ number_format($row['total'], 0) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-center text-sm text-gray-500">Belum ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- GRID BAGIAN CHART TOP PRODUK TERLARIS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            {{-- CARD: TOP 5 PRODUK TERLARIS OFFLINE --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Top 5 Produk Terlaris offline</h3>
                <canvas height="300" style="max-height: 300px;" id="topProductsOfflineChart"></canvas>
            </div>

            {{-- CARD: TOP 5 PRODUK TERLARIS ONLINE --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Top 5 Produk Terlaris online</h3>
                <canvas height="300" style="max-height: 300px;" id="topProductsOnlineChart"></canvas>
            </div>
        </div>

        {{-- TABEL PRODUK TERLARIS OFFLINE (FULL WIDTH) --}}
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Produk Terlaris Offline</h3>
            <div class="w-full">
                <table class="w-full divide-y divide-gray-200" id="topProductsOfflineTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Produk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bahan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Warna Kayu</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Warna Rotan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dimensi (cm)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Terjual</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="topProductsOfflineTableBody">
                        @forelse($topProductsOffline as $index => $product)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $product->product_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->material ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->wood_color ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->rattan_color ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($product->length && $product->width && $product->height)
                                        {{ number_format($product->length, 0) }} × {{ number_format($product->width, 0) }} × {{ number_format($product->height, 0) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ number_format($product->total_sold, 0) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- TABEL PRODUK TERLARIS ONLINE (FULL WIDTH) --}}
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Produk Terlaris Online</h3>
            <div class="w-full">
                <table class="w-full divide-y divide-gray-200" id="topProductsOnlineTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Produk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bahan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Warna Kayu</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Warna Rotan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dimensi (cm)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Terjual</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="topProductsOnlineTableBody">
                        @forelse($topProductsOnline as $index => $product)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $product->product_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->material ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->wood_color ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->rattan_color ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($product->length && $product->width && $product->height)
                                        {{ number_format($product->length, 0) }} × {{ number_format($product->width, 0) }} × {{ number_format($product->height, 0) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ number_format($product->total_sold, 0) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- GRID CHART PENDAPATAN --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            {{-- CARD: REVENUE CHART (14 HARI TERAKHIR) --}}
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h3 class="text-lg font-semibold">Pendapatan Harian</h3>
                        <p class="text-xs text-gray-500 mt-1">14 hari terakhir</p>
                    </div>
            </div>

            <div class="h-64">
                <canvas id="revenueChart"></canvas>
            </div>
            </div>

            {{-- CARD: REVENUE CHART (PER BULAN TAHUN INI) --}}
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h3 class="text-lg font-semibold">Pendapatan Per Bulan</h3>
                        <p class="text-xs text-gray-500 mt-1">Tahun {{ date('Y') }}</p>
                    </div>
                </div>

                <div class="h-64">
                    <canvas id="revenueMonthlyChart"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- load Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // REVENUE LINE CHART
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('revenueChart').getContext('2d');

            // Hitung max value dari data untuk menentukan stepSize
            const revenueData = {!! json_encode(array_column($stats['revenue_data'], 'amount')) !!};
            const maxRevenue = Math.max(...revenueData, 0);
            
            // Tentukan stepSize berdasarkan max value (dalam juta, kelipatan 1jt)
            let stepSize = 1000000; // default 1jt
            if (maxRevenue > 0) {
                if (maxRevenue >= 10000000) {
                    stepSize = 2000000; // 2jt untuk data besar
                } else if (maxRevenue >= 5000000) {
                    stepSize = 1000000; // 1jt untuk data sedang-besar
                } else if (maxRevenue >= 1000000) {
                    stepSize = 1000000; // 1jt untuk data sedang
                } else {
                    stepSize = 1000000; // 1jt untuk data kecil (tetap 1jt agar konsisten)
                }
            }

            // Tentukan suggestedMax dengan padding yang lebih baik (kelipatan 1jt)
            let suggestedMax = 10000000; // default 10jt
            if (maxRevenue > 0) {
                // Bulatkan ke atas ke kelipatan 1jt
                suggestedMax = Math.ceil(maxRevenue * 1.2 / 1000000) * 1000000;
                // Minimum 1jt
                if (suggestedMax < 1000000) {
                    suggestedMax = 1000000;
                }
            }

            const revenueChart = new Chart(ctx, {
                type: 'line',
                data: {
                    // daftar tanggal dari backend
                    labels: {!! json_encode(array_column($stats['revenue_data'], 'date')) !!},
                    datasets: [{
                        label: 'Pendapatan',
                        // nominal per tanggal dari backend
                        data: revenueData,
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#3B82F6',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const value = context.parsed.y;
                                    const juta = (value / 1000000).toFixed(2).replace('.', ',');
                                    return 'Pendapatan: Rp ' + juta + ' juta';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            suggestedMax: suggestedMax,
                            grid: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                stepSize: stepSize,
                                callback: function(value) {
                                    // Format: semua nilai dalam juta dengan koma (1,0 juta, 2,0 juta, dll)
                                    const juta = value / 1000000;
                                    return 'Rp ' + juta.toFixed(1).replace('.', ',') + ' juta';
                                }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: {
                                maxRotation: 45,
                                minRotation: 45
                            }
                        }
                    }
                }
            });

            // REVENUE MONTHLY CHART (PER BULAN TAHUN INI)
            const ctxMonthly = document.getElementById('revenueMonthlyChart').getContext('2d');
            const revenueMonthlyChart = new Chart(ctxMonthly, {
                type: 'bar',
                data: {
                    labels: {!! json_encode(array_column($stats['revenue_monthly_data'], 'month')) !!},
                    datasets: [{
                        label: 'Pendapatan',
                        data: {!! json_encode(array_column($stats['revenue_monthly_data'], 'amount')) !!},
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 2,
                        borderRadius: 4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                callback: function(value) {
                                    // Format: semua nilai dalam juta dengan koma (1,0 juta, 2,0 juta, dll)
                                    const juta = value / 1000000;
                                    return 'Rp ' + juta.toFixed(1).replace('.', ',') + ' juta';
                                }
                            }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        });
    </script>

    {{-- redundant kedua kalinya load Chart.js, tapi ini bukan penyebab error sintaks --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        /**
         * STATUS & TOP PRODUCT CHARTS
         *
         * Data yang dikirim dari controller ke Blade:
         *
         * - $chartData:
         *   contoh bentuk PHP (collection):
         *   [
         *     { status: "received",   total: 10 },
         *     { status: "processing", total:  5 },
         *     { status: "completed",  total:  3 },
         *   ]
         *
         * - $topProducts:
         *   [
         *     { title: "Kursi Rotan A", total_sold: 42 },
         *     { title: "Meja Kayu B",   total_sold: 37 },
         *     ...
         *   ]
         *
         * Catatan penting:
         *   Di bawah kita pakai directive Blade untuk mengubah data PHP (Collection/array)
         *   menjadi literal array JavaScript yang valid. Jangan taruh directive itu
         *   di dalam komentar lagi, karena Blade tetap akan parsing.
         */

        // label & data status pesanan
        const orderStatusLabels = @json($chartData->pluck('status'));
        const orderStatusCounts = @json($chartData->pluck('total'));

        // Data produk terlaris offline
        const topProductsOfflineLabels = @json($topProductsOfflineChart->pluck('title'));
        const topProductsOfflineCounts = @json($topProductsOfflineChart->pluck('total_sold'));
        const topProductsOfflineData = @json($topProductsOffline);
        
        // Data produk terlaris online
        const topProductsOnlineLabels = @json($topProductsOnlineChart->pluck('title'));
        const topProductsOnlineCounts = @json($topProductsOnlineChart->pluck('total_sold'));
        const topProductsOnlineData = @json($topProductsOnline);

        // Doughnut chart: status pesanan
        const ctxOrder = document.getElementById('orderStatusChart').getContext('2d');
        new Chart(ctxOrder, {
            type: 'doughnut',
            data: {
                labels: orderStatusLabels,
                datasets: [{
                    label: 'Jumlah',
                    data: orderStatusCounts,
                    backgroundColor: ['#60A5FA', '#FBBF24', '#34D399', '#F87171', '#A78BFA'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // Bar chart: Top produk terlaris OFFLINE
        const ctxProductsOffline = document.getElementById('topProductsOfflineChart').getContext('2d');
        const topProductsOfflineChart = new Chart(ctxProductsOffline, {
            type: 'bar',
            data: {
                labels: topProductsOfflineLabels,
                datasets: [{
                    label: 'Terjual',
                    data: topProductsOfflineCounts,
                    backgroundColor: '#10B981'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                },
                onClick: (event, elements) => {
                    if (elements.length > 0) {
                        const clickedIndex = elements[0].index;
                        const clickedProductName = topProductsOfflineLabels[clickedIndex];
                        filterTableByProduct('offline', clickedProductName);
                    }
                }
            }
        });

        // Bar chart: Top produk terlaris ONLINE
        const ctxProductsOnline = document.getElementById('topProductsOnlineChart').getContext('2d');
        const topProductsOnlineChart = new Chart(ctxProductsOnline, {
            type: 'bar',
            data: {
                labels: topProductsOnlineLabels,
                datasets: [{
                    label: 'Terjual',
                    data: topProductsOnlineCounts,
                    backgroundColor: '#3B82F6'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                },
                onClick: (event, elements) => {
                    if (elements.length > 0) {
                        const clickedIndex = elements[0].index;
                        const clickedProductName = topProductsOnlineLabels[clickedIndex];
                        filterTableByProduct('online', clickedProductName);
                    }
                }
            }
        });

        // =================================================================
        //  FUNGSI UPDATE TABEL BERDASARKAN CHART (REALTIME)
        // =================================================================
        function updateTableFromChart(type) {
            const tbodyId = type === 'offline' ? 'topProductsOfflineTableBody' : 'topProductsOnlineTableBody';
            const tbody = document.getElementById(tbodyId);
            if (!tbody) return;

            const data = type === 'offline' ? topProductsOfflineData : topProductsOnlineData;
            
            tbody.innerHTML = '';
            
            if (!data || data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada data</td></tr>';
                return;
            }

            data.forEach((product, index) => {
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50';
                
                const dimensi = (product.length && product.width && product.height) 
                    ? `${parseInt(product.length)} × ${parseInt(product.width)} × ${parseInt(product.height)}`
                    : '-';

                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${index + 1}</td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">${product.product_name || '-'}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${product.material || '-'}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${product.wood_color || '-'}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${product.rattan_color || '-'}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${dimensi}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">${parseInt(product.total_sold).toLocaleString('id-ID')}</td>
                `;
                
                tbody.appendChild(row);
            });
        }

        // Filter tabel berdasarkan nama produk (saat chart diklik)
        function filterTableByProduct(type, productName) {
            const tbodyId = type === 'offline' ? 'topProductsOfflineTableBody' : 'topProductsOnlineTableBody';
            const tbody = document.getElementById(tbodyId);
            if (!tbody) return;

            const data = type === 'offline' ? topProductsOfflineData : topProductsOnlineData;
            const filtered = data.filter(p => p.product_name === productName);
            
            tbody.innerHTML = '';
            
            if (filtered.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data untuk produk ini</td></tr>';
                return;
            }

            filtered.forEach((product, index) => {
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50 bg-blue-50';
                
                const dimensi = (product.length && product.width && product.height) 
                    ? `${parseInt(product.length)} × ${parseInt(product.width)} × ${parseInt(product.height)}`
                    : '-';

                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${index + 1}</td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">${product.product_name || '-'}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${product.material || '-'}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${product.wood_color || '-'}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${product.rattan_color || '-'}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${dimensi}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">${parseInt(product.total_sold).toLocaleString('id-ID')}</td>
                `;
                
                tbody.appendChild(row);
            });
        }

        // Update tabel saat halaman dimuat dan sinkron dengan chart
        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi tabel offline dan online
            updateTableFromChart('offline');
            updateTableFromChart('online');
            
            // Auto-refresh halaman setiap 60 detik untuk update data realtime
            setInterval(function() {
                location.reload();
            }, 60000); // Refresh setiap 1 menit
        });
    </script>
@endpush
