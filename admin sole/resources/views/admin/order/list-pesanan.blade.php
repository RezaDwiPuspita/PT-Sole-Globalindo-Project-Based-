@extends('layouts.admin')

@php
    // peta status -> kelas Tailwind utk badge
    $statusClasses = [
        'in_cart'     => 'bg-gray-100 text-gray-800',
        'processing'  => 'bg-yellow-100 text-yellow-800',
        'received'    => 'bg-blue-100 text-blue-800',
        'in_progress' => 'bg-indigo-100 text-indigo-800',
        'sending'     => 'bg-indigo-100 text-indigo-800',
        'completed'   => 'bg-green-100 text-green-800',
        'cancelled'   => 'bg-red-100 text-red-800',
    ];
@endphp

@section('page')
    List Pesanan
@endsection

@section('content')
    <div class="p-6">

        {{-- FILTER BULAN DAN MINGGU --}}
        <div class="bg-white rounded shadow p-4 mb-6">
            <form method="GET" action="{{ route('orders.list') }}" class="flex flex-wrap items-end gap-4">
                {{-- FILTER BULAN --}}
                <div class="flex-1 min-w-[200px]">
                    <label for="month" class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih Bulan
                    </label>
                    <select name="month" id="month" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                        @php
                            // Generate opsi bulan (12 bulan terakhir + bulan saat ini)
                            $currentDate = \Carbon\Carbon::now();
                            $monthNames = [
                                'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
                                'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
                                'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
                                'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
                            ];
                            for ($i = 12; $i >= 0; $i--) {
                                $date = $currentDate->copy()->subMonths($i);
                                $value = $date->format('Y-m');
                                $monthName = $monthNames[$date->format('F')] ?? $date->format('F');
                                $displayLabel = $monthName . ' ' . $date->format('Y');
                        @endphp
                        <option value="{{ $value }}" {{ $selectedMonth == $value ? 'selected' : '' }}>
                            {{ $displayLabel }}
                        </option>
                        @php
                            }
                        @endphp
                    </select>
                </div>

                {{-- FILTER MINGGU --}}
                <div class="flex-1 min-w-[150px]">
                    <label for="week" class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih Minggu
                    </label>
                    <select name="week" id="week" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                        <option value="1" {{ $selectedWeek == 1 ? 'selected' : '' }}>Minggu ke-1</option>
                        <option value="2" {{ $selectedWeek == 2 ? 'selected' : '' }}>Minggu ke-2</option>
                        <option value="3" {{ $selectedWeek == 3 ? 'selected' : '' }}>Minggu ke-3</option>
                        <option value="4" {{ $selectedWeek == 4 ? 'selected' : '' }}>Minggu ke-4</option>
                    </select>
                </div>

                {{-- TOMBOL FILTER --}}
                <div>
                    <button type="submit" 
                            class="px-6 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition flex items-center gap-2">
                        <i class="ph ph-magnifying-glass"></i>
                        <span>Filter</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- JUDUL DINAMIS --}}
        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6 rounded">
            <h3 class="text-lg font-semibold text-gray-800">
                List Pesanan Bulan {{ $monthName }} {{ $year }}
            </h3>
            <p class="text-sm text-gray-600 mt-1">
                Periode: {{ $weekRange }}
            </p>
        </div>

        {{-- PESAN JIKA TIDAK ADA PESANAN --}}
        @if(!isset($productSummary) || count($productSummary) == 0)
            <div class="flex flex-col items-center justify-center py-12 px-4">
                {{-- GAMBAR SHOPPING CART KOSONG --}}
                <div class="mb-4">
                    <svg class="w-20 h-20 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                {{-- TEKS --}}
                <div class="text-center">
                    <p class="text-base text-gray-600 font-medium">
                        Tidak ada pesanan yang masuk di minggu ini
                    </p>
                </div>
            </div>
        @else
            {{-- TOMBOL EXPORT EXCEL (DI BAWAH KOTAK JUDUL) --}}
            <div class="flex justify-end items-center mb-4">
                <button id="btnExcelProduct" type="button" class="btn btn-secondary flex items-center gap-2">
                    <i class="ph ph-download-simple"></i>
                    <span>Export Excel</span>
                </button>
            </div>

            {{-- TABEL RINGKASAN PRODUK --}}
            <div class="bg-white rounded shadow overflow-x-auto p-4 mb-6">
                <table id="productSummaryTable" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">NO</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">NAMA PRODUK</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">BAHAN</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">WARNA KAYU</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">WARNA ROTAN</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">DIMENSI (CM)</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">QTY</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($productSummary as $index => $product)
                            <tr>
                                <td class="px-4 py-2">{{ $index + 1 }}</td>
                                <td class="px-4 py-2 font-medium">{{ $product['product_name'] }}</td>
                                <td class="px-4 py-2">{{ $product['material'] }}</td>
                                <td class="px-4 py-2">{{ $product['wood_color'] }}</td>
                                <td class="px-4 py-2">{{ $product['rattan_color'] }}</td>
                                <td class="px-4 py-2">{{ $product['dimensions'] }}</td>
                                <td class="px-4 py-2 font-semibold">{{ $product['quantity'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

    <script>
        $(document).ready(function () {
            // Inisialisasi DataTables untuk tabel ringkasan produk
            @if(isset($productSummary) && count($productSummary) > 0)
            const productTable = $('#productSummaryTable').DataTable({
                order: [[6, 'desc']], // Sort by QTY (kolom 6) descending
                language: {
                    search: 'Cari:',
                    lengthMenu: 'Tampilkan _MENU_',
                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ pesanan',
                    paginate: {
                        previous: '<i class="ph ph-caret-left"></i>',
                        next: '<i class="ph ph-caret-right"></i>'
                    }
                },
                dom: 'rtip', // Hapus 'f' (filter/search) karena sudah diganti dengan export excel
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Ringkasan Produk - ' + '{{ $monthName }} {{ $year }}',
                        exportOptions: { columns: ':visible' }
                    }
                ]
            });

            // Hubungkan tombol export excel dengan DataTables button
            $('#btnExcelProduct').on('click', function () {
                productTable.button('.buttons-excel').trigger();
            });
            @endif
        });
    </script>
@endpush

