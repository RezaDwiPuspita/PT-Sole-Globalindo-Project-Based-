{{-- // kode ini untuk: memakai layout admin utama (kerangka: header, sidebar, footer, stack scripts, dsb.) --}}
{{-- // Tag: @extends (Blade Directive)
    // Fungsi  : Memastikan view ini “menumpang” ke layout 'layouts.admin' di resources/views/layouts/admin.blade.php
    // Dampak  : Semua @section('page') dan @section('content') di bawah akan “disuntikkan” ke slot milik layout.
--}}
@extends('layouts.admin')

@php
    // peta status -> kelas Tailwind utk badge (dibuat global di file ini agar selalu tersedia)
    $statusClasses = [
        'in_cart'     => 'bg-gray-100 text-gray-800',   // abu: masih di keranjang
        'processing'  => 'bg-yellow-100 text-yellow-800',// kuning: diproses
        'received'    => 'bg-blue-100 text-blue-800',    // biru: diterima
        'in_progress' => 'bg-indigo-100 text-indigo-800',// nila: sedang dikerjakan
        'sending'     => 'bg-indigo-100 text-indigo-800',// nila: sedang dikirim
        'completed'   => 'bg-green-100 text-green-800',  // hijau: selesai
        'cancelled'   => 'bg-red-100 text-red-800',      // merah: dibatalkan
    ];
@endphp

{{-- // kode ini untuk: mengisi judul halaman di layout (biasanya dipakai di <title> atau header layout) --}}
{{-- // Tag: @section (Blade Directive)
    // Nama    : 'page' → harus sama dengan nama section yang didefinisikan di layouts.admin
    // Isi     : "Order List" → akan dipakai layout untuk menampilkan judul halaman.
--}}
@section('page')
    Order List
@endsection

{{-- // kode ini untuk: konten utama halaman (bagian yang tampil di area body layout) --}}
{{-- // Tag: @section (Blade Directive) dengan nama 'content' → slot konten utama di layouts.admin --}}
@section('content')
    {{-- 
        // WRAPPER KONTEN HALAMAN
        // Tag: <div>
        //   - class="p-6"
        //      p-6 (Tailwind)      : padding 1.5rem di semua sisi → memberi “ruang napas” pada isi halaman.
    --}}
    <div class="p-6">

        {{-- 
            // BARIS ATAS: HEADING + TOMBOL EXPORT (POSISI KANAN)
            // Tag: <div>
            //   - class="flex justify-between items-center mb-4"
            //      flex               : susun horizontal
            //      justify-between    : space-between; heading di kiri, tombol di kanan
            //      items-center       : vertikal rata tengah
            //      mb-4               : beri jarak dengan konten di bawah
        --}}

        <div class="flex justify-between items-center mb-4">
            {{-- 
                // HEADING HALAMAN
                // Tag: <h2>
                //   - class="text-2xl font-semibold"
                //      text-2xl     : ukuran font besar (≈24px)
                //      font-semibold: ketebalan semi-bold (di antara medium & bold).
                // Teks: "Daftar Order Online" → judul halaman agar user tahu konteks.
            --}}
            <h2 class="text-2xl font-semibold">Daftar Order Online</h2>

            {{-- 
                // GROUP TOMBOL DI KANAN (HANYA EXPORT EXCEL)
                // Tag: <div>
                //   - class="form-group flex items-center justify-end flex-row"
                //      form-group       : kelas  untuk grup form/tombol
                //      flex             : tata letak horizontal
                //      items-center     : vertikal rata tengah
                //      justify-end      : merapat ke kanan (meskipun parent sudah between, ini menjaga kerapian)
                //      flex-row         : eksplisit baris horizontal (default)
            --}}
            <div class="form-group flex items-center justify-end flex-row">
                {{-- 
                    // TOMBOL EXPORT EXCEL (CUSTOM TRIGGER UNTUK DATATABLES)
                    // Tag: <button>
                    //   - id="btnExcel"          : ID unik agar gampang ditangkap oleh JavaScript (event listener)
                    //   - type="button"          : tombol biasa (tidak mengirim form)
                    //   - class="btn btn-secondary flex items-center gap-2"
                    //       btn / btn-secondary  : kelas tombol kustom dari tema (warna sekunder).
                    //       flex                 : ikon & teks sejajar horizontal
                    //       items-center         : vertikal rata tengah (untuk ikon & teks)
                    //       gap-2                : jarak di antara ikon & teks (0.5rem)
                --}}
                <button id="btnExcel" type="button" class="btn btn-secondary flex items-center gap-2">
                    {{-- 
                        // IKON (PHOSPHOR ICONS)
                        // Tag: <i> (inline)
                        //   - class="ph ph-download-simple" : memilih ikon "download-simple"
                        // Catatan: style ikon akan muncul karena kita memuat CSS Phosphor lewat CDN di @push('scripts')
                    --}}
                    <i class="ph ph-download-simple"></i>

                    {{-- 
                        // LABEL TOMBOL
                        // Tag: <span> (inline)
                        //   - Alasan pakai <span>: elemen inline yang tidak memulai baris baru, cocok untuk label singkat.
                    --}}
                    <span>Export Excel</span>
                </button>
            </div>
        </div>

        {{-- 
            // CARD PEMBUNGKUS TABEL
            // Tag: <div>
            //   - class="bg-white rounded shadow overflow-x-auto p-4"
            //      bg-white            : latar putih
            //      rounded             : sudut membulat
            //      shadow              : bayangan halus 
            //      overflow-x-auto     : jika tabel lebar, bisa scroll horizontal di layar kecil
            //      p-4                 : padding 1rem di dalam card
        --}}
        <div class="bg-white rounded shadow overflow-x-auto p-4">

            {{-- 
                // TABEL DATA ORDER ONaLINE
                // Tag: <table>
                //   - id="orderTable"                  : ID dipakai untuk inisialisasi DataTables di JavaScript
                //   - class="min-w-full divide-y divide-gray-200"
                //      min-w-full          : lebar minimal 100% agar tabel memenuhi kontainer
                //      divide-y            : garis pemisah antar baris header/body
                //      divide-gray-200     : warna garis abu muda
            --}}
            <table id="orderTable" class="min-w-full divide-y divide-gray-200">

                {{-- 
                    // THEAD (TABLE HEADER): bagian kepala tabel yang berisi judul kolom
                    // Tag: <thead>
                    //   - class="bg-gray-100" : latar abu muda agar kontras dengan body
                --}}
                <thead class="bg-gray-100">
                    <tr>
                        {{-- 
                            // TH: Cell header kolom (biasanya otomatis bold & dipakai screen reader untuk semantik tabel)
                            // class="px-4 py-2 text-left text-sm font-medium text-gray-500"
                            //   px-4 / py-2      : padding horizontal/vertikal
                            //   text-left        : rata kiri
                            //   text-sm          : ukuran teks kecil
                            //   font-medium      : ketebalan sedang
                            //   text-gray-500    : abu-abu agar tidak terlalu kontras
                        --}}
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">No</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Order ID</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Pelanggan</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Tanggal</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Total</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Status</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Pembayaran</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Aksi</th>
                    </tr>
                </thead>

                {{-- 
                    // TBODY (TABLE BODY): isi baris-baris data
                    // Tag: <tbody>
                    //   - class="bg-white divide-y divide-gray-200"
                    //     bg-white         : latar putih
                    //     divide-y         : garis pemisah antar baris
                    //     divide-gray-200  : warna garis abu muda
                --}}
                <tbody class="bg-white divide-y divide-gray-200">
                    {{-- 
                        // LOOP DATA
                        // @foreach ($orders as $index => $order)
                        // - $orders  : dikirim dari Controller (koleksi Eloquent).
                           - $index   : index loop (0,1,2,...) berguna untuk nomor urut tampil.
                           - $order   : 1 record Order
                    --}}
                    @foreach ($orders as $index => $order)
                        <tr>
                            {{-- 
                                // KOLOM: NOMOR URUT
                                // {{ $index + 1 }} → +1 agar tampil mulai dari 1 (bukan 0)
                            --}}
                            <td class="px-4 py-2">{{ $index + 1 }}</td>

                            {{-- 
                                // KOLOM: ORDER ID DENGAN ZERO-PADDING
                                // Format: ORD-00001 (prefix + zero padding)
                                // str_pad($order->tracking_number, 5, '0', STR_PAD_LEFT)
                                //   str_pad         : fungsi PHP untuk "menambah" karakter ke kiri/kanan
                                //   5               : total panjang string setelah dipadding
                                //   '0'             : karakter padding
                                //   STR_PAD_LEFT    : padding di kiri (contoh "1" → "00001").
                            --}}
                            <td class="px-4 py-2">ORD-{{ str_pad($order->tracking_number, 5, '0', STR_PAD_LEFT) }}</td>

                            {{-- 
                                // KOLOM: IDENTITAS PELANGGAN (NAMA & NO HP)
                                // $order->user      : relasi ke model User 
                                // name / phone   : properti dari User.
                                // <div class="font-medium"> : nama bold sedang
                                // <div class="text-sm text-gray-500"> : no HP kecil dan redup
                            --}}
                            <td class="px-4 py-2">
                                <div class="font-medium">{{ $order->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $order->phone ?? ($order->customer->phone ?? '-') }}</div>
                            </td>

                            {{-- 
                                // KOLOM: TANGGAL ORDER
                                // {{ $order->order_date }} : tampilkan apa adanya (format sesuai yang disimpan)
                            --}}
                            <td class="px-4 py-2">{{ $order->order_date }}</td>

                            {{-- 
                                // KOLOM: TOTAL DALAM FORMAT RUPIAH
                                // number_format($order->total_amount, 0, ',', '.')
                                //   0      : tanpa desimal
                                //   ','    : separator desimal (ID)
                                //   '.'    : separator ribuan (ID)
                                //  tanpa desimal, koma sebagai pemisah desimal, titik sebagai pemisah ribuan → gaya Indonesia.
                                  - contoh: 1250000 
                            --}}
                            <td class="px-4 py-2">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>

                            {{-- 
                                // KOLOM: STATUS ORDER (BADGE BERWARNA)
                                // @php ... @endphp : blok PHP di Blade untuk siapkan kelas warna berdasarkan status.
                                // {{ mapStatusOrder($order->status) }} : helper untuk menampilkan label status 
                            --}}
                            <td class="px-4 py-2">

                                @php
                                    // ADDED: guard supaya variabel selalu ada meski definisi atas terlewat karena urutan render
                                    if (!isset($statusClasses)) {
                                        $statusClasses = [
                                            'in_cart'     => 'bg-gray-100 text-gray-800',
                                            'processing'  => 'bg-yellow-100 text-yellow-800',
                                            'received'    => 'bg-blue-100 text-blue-800',
                                            'in_progress' => 'bg-indigo-100 text-indigo-800',
                                            'sending'     => 'bg-indigo-100 text-indigo-800',
                                            'completed'   => 'bg-green-100 text-green-800',
                                            'cancelled'   => 'bg-red-100 text-red-800',
                                        ];
                                    }
                                @endphp

                                {{-- 
                                    // BADGE STATUS
                                    // class dinamis: {{ $statusClasses[$order->status] ?? 'bg-gray-100' }}
                                    - $statusClasses[...] : ambil kelas Tailwind dari peta.
                                    - Operator null coalesce '??':
                                        {{ $statusClasses[$order->status] ?? 'bg-gray-100' }}
                                        → Jika key tidak ada, pakai default bg abu.
                                    - px-2 : padding horizontal 0.5rem (8px).
                                    - py-1 : padding vertikal 0.25rem (4px).
                                    - text-xs : font kecil.
                                    - rounded-full : kapsul/oval badge.
                                    //   - jika status tidak ada di peta → gunakan default 'bg-gray-100'
                                --}}
                                <span class="px-2 py-1 text-xs rounded-full {{ $statusClasses[$order->status] ?? 'bg-gray-100' }}">
                                    {{ mapStatusOrder($order->status) }}
                                </span>
                            </td>

                            {{-- 
                                // KOLOM: STATUS PEMBAYARAN
                                // Tampilkan badge + waktu bayar jika tersedia.
                                // @if ($order->payment_status) : cek apakah ada status pembayaran.
                                - Cek dulu apakah status pembayaran ada (beberapa order offline bisa saja null).
                                - $paymentClasses : peta status → warna badge.
                                - Conditional text: ternary → waiting_payment ? 'Menunggu Pembayaran' : 'Dibayar'
                                - Tampilkan payment_time jika tersedia.
                            --}}
                            <td class="px-4 py-2">
                                @if ($order->payment_status)
                                    @php
                                        // peta status pembayaran -> kelas warna
                                        $paymentClasses = [
                                            'waiting_payment' => 'bg-yellow-100 text-yellow-800',
                                            'paid'            => 'bg-green-100 text-green-800',
                                        ];
                                    @endphp

                                    {{-- 
                                        // BADGE PEMBAYARAN
                                        // Teks:
                                        //   waiting_payment → “Menunggu Pembayaran”
                                        //   paid            → “Dibayar”
                                        // class dinamis diambil dari $paymentClasses
                                    --}}
                                    <span class="px-2 py-1 text-xs rounded-full {{ $paymentClasses[$order->payment_status] ?? 'bg-gray-100' }}">
                                        {{ $order->payment_status === 'waiting_payment' ? 'Menunggu Pembayaran' : 'Dibayar' }}
                                    </span>

                                    {{-- 
                                        // WAKTU PEMBAYARAN (OPSIONAL)
                                        // @if ($order->payment_time) : tampilkan hanya jika ada timestamp-nya
                                    --}}
                                    @if ($order->payment_time)
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $order->payment_time }}
                                        </div>
                                    @endif
                                @else
                                    {{-- // Jika belum ada status pembayaran sama sekali --}}
                                    <span class="text-xs text-gray-500">-</span>
                                @endif
                            </td>

                            {{-- 
                                // KOLOM: AKSI (DETAIL, EDIT, HAPUS, MARK COMPLETED, CANCEL)
                                // Dibungkus flex agar ikon-ikon rapi sejajar.
                            --}}
                            <td class="px-4 py-2">
                                <div class="flex items-center space-x-2">

                                    {{-- 
                                        // LINK: DETAIL ORDER
                                        // route('orders.show', $order->id) : ke halaman show detail order
                                        // title="Detail" : tooltip judul
                                        // Ikon: <i class="ph ph-eye"></i> (ikon “lihat”)
                                    --}}
                                    <a href="{{ route('orders.show', $order->id) }}"
                                       class="text-blue-500 hover:text-blue-700" title="Detail">
                                        <i class="ph ph-eye"></i>
                                    </a>

                                    {{-- 
                                        // LINK: EDIT ORDER
                                        // href="route('orders.edit', $order->id)" : ke halaman edit
                                        // title="Edit" : tooltip
                                        // Ikon "pencil"
                                    --}}
                                    <a href="{{ route('orders.edit', $order->id) }}"
                                       class="text-yellow-500 hover:text-yellow-700" title="Edit">
                                        <i class="ph ph-pencil"></i>
                                    </a>

                                    {{-- 
                                        // FORM: HAPUS ORDER
                                        // method="POST"  : HTML tidak mendukung DELETE langsung
                                        // @csrf          : token keamanan
                                        // @method('DELETE'): spoof method agar dianggap DELETE oleh Laravel
                                        // onsubmit="return confirm(...)" : konfirmasi sebelum eksekusi
                                        // Tombol: <button type="submit"> dengan ikon "trash"
                                    --}}
                                    <form action="{{ route('orders.destroy', $order->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700"
                                            onclick="return confirm('Are you sure to delete this order?')"
                                            title="Delete order">
                                            <i class="ph ph-trash"></i>
                                        </button>
                                    </form>
                            
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table> {{-- // SELESAI TABEL --}}
        </div> {{-- // SELESAI CARD PEMBUNGKUS --}}
    </div> {{-- // SELESAI WRAPPER HALAMAN --}}
@endsection

{{-- 
    // STACK SCRIPTS
    // @push('scripts') : menambahkan resource CSS/JS ke stack “scripts” yang biasanya dirender di akhir <body> oleh layout.
--}}
@push('scripts')
    {{-- 
        // SCRIPT PHOSPHOR ICONS
        // Diperlukan agar tag <i class="ph ..."></i> benar-benar menampilkan ikon.
        // Dimuat via CDN (praktis untuk prototyping).
    --}}
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    {{-- 
        // jQuery (WAJIB sebelum DataTables JS)
        // DataTables CSS (gaya tabel dengan search, paginate, dsb.)
        // DataTables JS (fitur tabel pintar)
    --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    {{-- 
        // DataTables Buttons + JSZip (untuk export ke Excel)
        // Buttons CSS/JS: menambah kemampuan tombol "excelHtml5"
        // JSZip: dependensi agar bisa membentuk file .xlsx di sisi klien (browser)
    --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

    {{-- 
        // INISIALISASI DATATABLES
        // $(document).ready(function(){ ... })
        //   - jQuery “document ready”: menjalankan kode setelah DOM siap, agar selector #orderTable & #btnExcel sudah ada.
    --}}
    <script>
        $(document).ready(function () {
            {{-- 
                // 1) Inisialisasi DataTables pada tabel #orderTable
                //    Simpan instance ke variabel 'table' agar bisa panggil API DataTables (misal: table.button(...).trigger())
            --}}
            const table = $('#orderTable').DataTable({
                {{-- 
                    // order: set urutan default saat tabel pertama kali terbuat
                    // [[3, 'desc']] : kolom indeks 3 (Tanggal) descending → data terbaru muncul di atas
                --}}
                order: [[3, 'desc']],

                {{-- 
                    // language: terjemahan string bawaan DataTables ke bahasa Indonesia
                    // search, lengthMenu, info, paginate: label-label yang muncul di UI DataTables
                --}}
                language: {
                    search: 'Cari:',                         // label input pencarian
                    lengthMenu: 'Tampilkan _MENU_',          // label jumlah baris/halaman
                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ order', // info paging
                    paginate: {
                        previous: '<i class="ph ph-caret-left"></i>',
                        next: '<i class="ph ph-caret-right"></i>'
                    }
                },


                {{-- 
                    // columnDefs: konfigurasi perilaku kolom tertentu
                    // { orderable: false, targets: [7] }
                    //   - Nonaktifkan sorting untuk kolom "Aksi" (index 7) karena berisi tombol, bukan data yang relevan diurutkan.
                --}}
                columnDefs: [
                    { orderable: false, targets: [7] }
                ],

                {{-- 
                    // dom: layout komponen DataTables yang ditampilkan
                    // 'f' : filter (search box)
                    // 'r' : processing (status loading)
                    // 't' : table
                    // 'i' : table info (Menampilkan X–Y dari Z data)
                    // 'p' : pagination (navigasi hal.)
                    // Tidak menaruh 'B' (Buttons) di sini agar toolbar bawaan tombol tidak muncul
                --}}
                dom: 'frtip',

                {{-- 
                    // buttons: definisi tombol "virtual" (tidak ditampilkan langsung karena 'B' tidak ada di dom)
                    // extend : 'excelHtml5' → jenis tombol export Excel
                    // title  : judul dokumen/worksheet hasil export
                    // exportOptions: { columns: ':visible:not(:last-child)' }
                    //   - Export semua kolom visible KECUALI kolom terakhir (Aksi), karena kolom aksi tidak berguna di Excel.
                --}}
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Daftar Order Online',
                        exportOptions: { columns: ':visible:not(:last-child)' }
                    }
                ]
            });

            {{-- 
                // 2) Hubungkan tombol custom "#btnExcel" dengan tombol virtual DataTables
                //    Ketika #btnExcel di-klik → panggil API: table.button('.buttons-excel').trigger()
                //    .button('.buttons-excel')  : memilih tombol yang tipenya "excel"
                //    .trigger()                 : jalankan aksi tombol (membuka dialog download Excel)
            --}}
            $('#btnExcel').on('click', function () {
                table.button('.buttons-excel').trigger();
            });
        });
    </script>
@endpush
