{{-- // kode ini untuk: memakai layout admin utama (header, sidebar, footer, stack scripts, dsb.) --}}
@extends('layouts.admin') {{-- @extends: mewarisi kerangka dari resources/views/layouts/admin.blade.php --}}

{{-- // kode ini untuk: mengisi section "page" di layout (biasanya dipakai untuk <title> / breadcrumb) --}}
@section('page')
    Order List
@endsection

{{-- // kode ini untuk: mengisi section "content" di layout dengan isi utama halaman --}}
@section('content')
    {{-- 
        // WRAPPER HALAMAN
        // <div class="p-6"> : <div> = container blok
        //   - p-6 : padding semua sisi 1.5rem (Tailwind spacing scale: 6 = 1.5rem)
        //   Tujuan: beri "ruang napas" supaya konten tidak mepet tepi.
    --}}
    <div class="p-6">

        {{-- 
            // BARIS ATAS: HEADING + GROUP TOMBOL KANAN
            // class="flex justify-between items-center mb-4"
            //   - flex            : aktifkan Flexbox (anak disusun horizontal)
            //   - justify-between : ruang sisa dibagi di tengah → kiri & kanan menempel tepi (space-between)
            //   - items-center    : vertikal rata tengah
            //   - mb-4            : margin-bottom 1rem
        --}}
        <div class="flex justify-between items-center mb-4">

            {{-- 
                // HEADING HALAMAN
                // Tag: <h2> → heading level 2 (semantik SEO/aksesibilitas)
                // class="text-2xl font-semibold"
                //   - text-2xl      : ukuran font besar
                //   - font-semibold : ketebalan semi-bold
                // Teks: "Daftar Order Offline" → judul halaman agar user tahu konteks.
            --}}
            <h2 class="text-2xl font-semibold">Daftar Order Offline</h2>

            {{-- 
                // KELOMPOK TOMBOL KANAN ATAS
                // class="form-group flex items-center justify-end flex-row gap-2"
                //   - flex / flex-row : susun mendatar
                //   - items-center    : rata tengah vertikal
                //   - justify-end     : dorong grup ke kanan
                //   - gap-2           : jarak antar anak 0.5rem
            --}}
            <div class="form-group flex items-center justify-end flex-row gap-2">

                {{-- 
                    // TOMBOL EXPORT EXCEL
                    // <button> : elemen tombol interaktif
                    // id="btnExcel"   : dipakai di JS untuk trigger DataTables export
                    // type="button"   : tombol biasa (bukan submit form)
                    // class="btn btn-secondary flex items-center gap-2"
                    //   - btn / btn-secondary : kelas kustom proyek (gaya tombol abu)
                    //   - flex items-center    : ikon + teks sejajar, vertikal rata tengah
                    //   - gap-2                : jarak 0.5rem antara ikon & label
                --}}
                <button id="btnExcel" type="button" class="btn btn-secondary flex items-center gap-2">
                    {{-- 
                        // IKON DOWNLOAD
                        // <i> : tag inline untuk ikon
                        // class="ph ph-download-simple" : Phosphor Icons (butuh CSS di @push('scripts'))
                    --}}
                    <i class="ph ph-download-simple"></i>

                    {{-- 
                        // LABEL TOMBOL
                        // <span> : elemen inline sederhana untuk teks
                    --}}
                    <span>Export Excel</span>
                </button>

                {{-- 
                    // TOMBOL "TAMBAH" → KE HALAMAN CREATE ORDER OFFLINE
                    // <a href="..."> : anchor (tautan)
                    // route('order.offline.create') : helper Blade → generate URL dari nama route
                    // class="btn btn-primary flex items-center gap-2" : tombol biru + ikon + label
                --}}
                <a href="{{ route('order.offline.create') }}" class="btn btn-primary flex items-center gap-2">
                    {{-- // Ikon plus dari Phosphor Icons --}}
                    <i class="ph ph-plus"></i>
                    {{-- // Label teks tombol --}}
                    <span>Tambah</span>
                </a>
            </div>
        </div>

        {{-- 
            // CARD PEMBUNGKUS TABEL
            // <div class="bg-white rounded shadow overflow-x-auto p-4">
            //   - bg-white        : latar putih
            //   - rounded         : sudut membulat
            //   - shadow          : bayangan halus
            //   - overflow-x-auto : memungkinkan scroll horizontal (penting untuk tabel di mobile)
            //   - p-4             : padding 1rem di dalam card
        --}}
        <div class="bg-white rounded shadow overflow-x-auto p-4">

            {{-- 
                // TABEL DATA ORDER OFFLINE
                // <table id="orderTable" ...>
                //   - id="orderTable" : selector untuk inisialisasi DataTables via jQuery
                // class="min-w-full divide-y divide-gray-200"
                //   - min-w-full      : lebar minimal 100% (biar mengisi kontainer)
                //   - divide-y        : garis pembatas antar baris (horizontal)
                //   - divide-gray-200 : warna garis abu muda
            --}}
            <table id="orderTable" class="min-w-full divide-y divide-gray-200">

                {{-- 
                    // THEAD: KEPALA TABEL (JUDUL KOLOM)
                    // <thead> : bagian header tabel
                    // class="bg-gray-100" : latar abu tipis
                --}}
                <thead class="bg-gray-100">
                    <tr> {{-- <tr> : table row / baris tabel --}}
                        {{-- 
                            // <th> : table header cell (tebal, semantis)
                            // class="px-4 py-2 text-left text-sm font-medium text-gray-500"
                            //   - px-4 : padding sumbu X (kiri-kanan) 1rem
                            //   - py-2 : padding sumbu Y (atas-bawah) 0.5rem
                            //   - text-left : rata kiri
                            //   - text-sm : ukuran teks kecil
                            //   - font-medium : ketebalan sedang
                            //   - text-gray-500 : warna abu
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
                    // TBODY: ISI DATA TABEL (DI-LOOP DARI $orders)
                    // class="bg-white divide-y divide-gray-200" : latar putih + garis antar baris
                --}}
                <tbody class="bg-white divide-y divide-gray-200">
                    {{-- 
                        // @foreach ($orders as $index => $order)
                        //   - @foreach : directive Blade untuk perulangan koleksi
                        //   - $orders  : koleksi/array Eloquent Order yang dikirim dari Controller
                        //   - $index   : nomor indeks (dimulai dari 0) → kita tampilkan +1 agar mulai dari 1
                        //   - $order   : item/record Order pada iterasi saat ini (punya field: name, phone, status, dll)
                        // Kapan pakai foreach? Saat harus men-generate elemen berulang dari data dinamis.
                    --}}
                    @foreach ($orders as $index => $order)
                        <tr> {{-- satu baris data order --}}
                            {{-- 
                                // KOLOM: NOMOR URUT
                                // {{ $index + 1 }} : +1 supaya tampil 1,2,3... (bukan 0,1,2...)
                            --}}
                            <td class="px-4 py-2">{{ $index + 1 }}</td>

                            {{-- 
                                // KOLOM: ORDER ID
                                // Tampilkan format "ORD-00001"
                                // str_pad($order->tracking_number, 5, '0', STR_PAD_LEFT)
                                //   - str_pad : fungsi PHP untuk menambahkan karakter '0' di kiri hingga panjang 5
                                //   - contoh: 12 → "00012"
                            --}}
                            <td class="px-4 py-2">ORD-{{ str_pad($order->tracking_number, 5, '0', STR_PAD_LEFT) }}</td>

                            {{-- 
                                // KOLOM: IDENTITAS PELANGGAN (nama & telepon)
                                // <div> dalam <td> agar bisa styling terpisah
                                //   - font-medium : tebal sedang untuk nama
                                //   - text-sm text-gray-500 : teks kecil & abu untuk telepon
                            --}}
                            <td class="px-4 py-2">
                                <div class="font-medium">{{ $order->name }}</div>
                                <div class="text-sm text-gray-500">{{ $order->phone }}</div>
                            </td>

                            {{-- 
                                // KOLOM: TANGGAL ORDER
                                // {{ $order->order_date }} : langsung tampilkan tanggal sesuai format penyimpanan
                            --}}
                            <td class="px-4 py-2">{{ $order->order_date }}</td>

                            {{-- 
                                // KOLOM: TOTAL DALAM FORMAT RUPIAH
                                // number_format($order->total_amount, 0, ',', '.')
                                //   - 0        : tanpa desimal
                                //   - ','      : tanda desimal (konvensi Indonesia)
                                //   - '.'      : pemisah ribuan
                                // hasil: 1234567 → "1.234.567"
                            --}}
                            <td class="px-4 py-2">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>

                            {{-- 
                                // KOLOM: STATUS ORDER (BADGE BERWARNA)
                                // @php ... @endphp : blok PHP dalam Blade untuk mapping status → kelas warna
                                // $statusClasses : associative array "status" => "kelas Tailwind"
                                // Nanti dipakai pada <span> sebagai badge.
                            --}}
                            <td class="px-4 py-2">
                                @php
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
                                {{-- 
                                    // Badge status:
                                    // <span class="px-2 py-1 text-xs rounded-full ...">
                                    //   - px-2 : padding horizontal 0.5rem
                                    //   - py-1 : padding vertikal 0.25rem
                                    //   - text-xs : ukuran huruf sangat kecil (cocok untuk badge)
                                    //   - rounded-full : kapsul/oval
                                    // class dinamis: {{ $statusClasses[$order->status] ?? 'bg-gray-100' }}
                                    //   - jika status tak ditemukan di mapping → fallback 'bg-gray-100'
                                    // {{ mapStatusOrder($order->status) }} :
                                    //   - helper function milik aplikasi untuk mengubah kode status (received, processing)
                                    //     menjadi label human readable (mis. "Diterima", "Diproses").
                                --}}
                                <span class="px-2 py-1 text-xs rounded-full {{ $statusClasses[$order->status] ?? 'bg-gray-100' }}">
                                    {{ mapStatusOrder($order->status) }}
                                </span>
                            </td>

                            {{-- 
                                // KOLOM: STATUS PEMBAYARAN
                                // Percabangan Blade @if → tampilkan badge jika payment_status ada.
                            --}}
                            <td class="px-4 py-2">
                                @if ($order->payment_status)
                                    @php
                                        $paymentClasses = [
                                            'waiting_payment' => 'bg-yellow-100 text-yellow-800',
                                            'paid'            => 'bg-green-100 text-green-800',
                                        ];
                                    @endphp

                                    {{-- 
                                        // Badge status pembayaran:
                                        //   waiting_payment → "Menunggu Pembayaran"
                                        //   paid            → "Dibayar"
                                        // class: px-2/py-1/text-xs/rounded-full seperti badge status di atas.
                                    --}}
                                    <span class="px-2 py-1 text-xs rounded-full {{ $paymentClasses[$order->payment_status] ?? 'bg-gray-100' }}">
                                        {{ $order->payment_status === 'waiting_payment' ? 'Menunggu Pembayaran' : 'Dibayar' }}
                                    </span>

                                    {{-- // Jika ada waktu pembayaran, tampilkan kecil di bawahnya --}}
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
                                // KOLOM: AKSI (Detail, Edit, Hapus)
                                // <div class="flex items-center space-x-2">
                                //   - flex          : ikon/link sejajar
                                //   - items-center  : vertikal rata tengah
                                //   - space-x-2     : jarak horizontal antar anak 0.5rem
                            --}}
                            <td class="px-4 py-2">
                                <div class="flex items-center space-x-2">

                                    {{-- 
                                        // LINK: DETAIL ORDER
                                        // route('orders.show', $order->id) → URL ke halaman show (detail) order ini
                                        // class="text-blue-500 hover:text-blue-700" : warna teks biru + efek hover
                                        // title="Detail" : tooltip saat hover
                                        // <i class="ph ph-eye"></i> : ikon "lihat"
                                    --}}
                                    <a href="{{ route('orders.show', $order->id) }}" class="text-blue-500 hover:text-blue-700" title="Detail">
                                        <i class="ph ph-eye"></i>
                                    </a>

                                    {{-- 
                                        // LINK: EDIT ORDER
                                        // route('orders.edit', $order->id) : menuju halaman edit order
                                        // class kuning: text-yellow-500 + hover
                                    --}}
                                    <a href="{{ route('orders.edit', $order->id) }}" class="text-yellow-500 hover:text-yellow-700" title="Edit">
                                        <i class="ph ph-pencil"></i>
                                    </a>

                                    {{-- 
                                        // FORM: HAPUS ORDER
                                        // <form method="POST"> : HTML hanya dukung GET/POST
                                        // @csrf               : token keamanan
                                        // @method('DELETE')   : spoofing agar Laravel mengenali sebagai DELETE
                                        // onsubmit="return confirm(...)" : konfirmasi sebelum eksekusi
                                        // Tombol: ikon "trash"
                                    --}}
                                    <form action="{{ route('orders.destroy', $order->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700"
                                            onclick="return confirm('Are you sure to delete this order?')" title="Delete order">
                                            <i class="ph ph-trash"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table> {{-- // SELESAI TABEL --}}
        </div> {{-- // SELESAI CARD PEMBUNGKUS TABEL --}}
    </div> {{-- // SELESAI WRAPPER HALAMAN --}}
@endsection

{{-- 
    // PUSH SCRIPTS
    // @push('scripts') : menambahkan resource (CSS/JS) ke stack "scripts" yang akan di-render di layout
--}}
@push('scripts')
    {{-- 
        // SCRIPT PHOSPHOR ICONS
        // Perlu supaya <i class="ph ..."> menampilkan ikon. Dimuat via CDN agar cepat dipakai.
    --}}
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    {{-- 
        // jQuery & DataTables (inti)
        // Urutan penting:
        //   1) jQuery   → dependensi DataTables
        //   2) CSS DT   → styling tabel (search box, pagination)
        //   3) JS DT    → logika interaktif (sort, search, paginate)
    --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    {{-- 
        // DataTables Buttons (untuk export) + JSZip (untuk Excel)
        // Buttons: plugin DT yang menambah tombol "Excel/CSV/PDF"
        // JSZip  : dipakai oleh buttons.html5 untuk membangkitkan file .xlsx di klien (browser)
    --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    {{-- 
        // INISIALISASI DATATABLES
        // $(function(){ ... }) : jQuery document-ready (jalan setelah DOM siap)
    --}}
    <script>
        $(function () {
            {{-- 
                // Buat DataTable untuk #orderTable
                // Simpan ke variabel "table" supaya bisa akses API (mis. trigger tombol excel)
            --}}
            const table = $('#orderTable').DataTable({
                {{-- 
                    // order: set urutan default saat tabel terbuat
                    // [[3, 'desc']] : urutkan kolom indeks 3 (Tanggal) desc → terbaru di atas
                --}}
                order: [[3, 'desc']],

                {{-- 
                    // language: terjemahan UI DataTables (biar lokal/IDN)
                    //   - search     : label untuk kotak pencarian
                    //   - lengthMenu : label dropdown jumlah baris
                    //   - info       : teks info paging
                    //   - paginate   : ikon prev/next (pakai Phosphor)
                --}}
                language: {
                    search: 'Cari:',
                    lengthMenu: 'Tampilkan _MENU_',
                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ order',
                    paginate: {
                        previous: '<i class="ph ph-caret-left"></i>',
                        next: '<i class="ph ph-caret-right"></i>'
                    }
                },

                {{-- 
                    // columnDefs: atur perilaku kolom tertentu
                    // { orderable: false, targets: [7] } : kolom ke-8 (index 7) = "Aksi" tidak bisa di-sort
                    // karena berisi tombol (eye/pencil/trash) bukan data.
                --}}
                columnDefs: [
                    { orderable: false, targets: [7] }
                ],

                {{-- 
                    // dom: tentukan komponen UI apa yang muncul dan urutannya
                    // 'f' : filter (search box)
                    // 'r' : processing state
                    // 't' : table
                    // 'i' : table info
                    // 'p' : pagination
                    // (Tidak menyertakan 'B' agar toolbar tombol export tidak muncul di atas tabel;
                    //  kita akan trigger tombol export via JS kustom)
                --}}
                dom: 'frtip',

                {{-- 
                    // buttons: definisi tombol "virtual" untuk export
                    // extend: 'excelHtml5' → export ke .xlsx menggunakan HTML5 + JSZip
                    // title: judul sheet/file (opsional)
                    // exportOptions: columns: ':visible:not(:last-child)' → hanya kolom tampak, kecuali kolom terakhir (Aksi)
                --}}
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Daftar Order Offline',
                        exportOptions: { columns: ':visible:not(:last-child)' }
                    }
                ]
            });
            
            {{-- 
                // TOMBOL CUSTOM EXPORT EXCEL
                // #btnExcel diklik → panggil tombol "virtual" DataTables tipe excel
                // table.button('.buttons-excel').trigger();
                //   - .button(selector) : ambil instance tombol
                //   - .trigger()        : jalankan aksi tombol (generate download .xlsx)
            --}}
            $('#btnExcel').on('click', function () {
                table.button('.buttons-excel').trigger();
            });
        });
    </script>
@endpush
