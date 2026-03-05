{{-- // kode ini untuk: memberitahu Blade bahwa view ini "turunan" dari layout admin utama --}}
{{-- // Tag: @extends (Blade Directive) --}}
{{-- //   - Fungsi: Memakai file layout sebagai kerangka (header/sidebar/footer). --}}
{{-- //   - Atribut: 'layouts.admin' → path view layout di resources/views/layouts/admin.blade.php --}}
@extends('layouts.admin')

{{-- // kode ini untuk: mengisi bagian "page" di layout dengan judul halaman --}}
{{-- // Tag: @section (Blade Directive) --}}
{{-- //   - Fungsi: Mengisi slot/section bernama "page" yang didefinisikan di layout. --}}
{{-- //   - Isi: "Product List" sebagai judul yang nanti dipakai di layout (misal di <title> atau header) --}}
@section('page')
    Product List
@endsection

{{-- // kode ini untuk: mengisi bagian "content" di layout dengan isi utama halaman --}}
@section('content')

    {{-- // =========================  WRAPPER HALAMAN  ========================= --}}
    {{-- // Tag: <div> (HTML), class="p-6" (Tailwind) --}}
    {{-- //   - Fungsi: Pembungkus besar konten halaman agar punya padding (ruang napas). --}}
    {{-- //   - Atribut: class="p-6" → padding 1.5rem di semua sisi. --}}
    <div class="p-6">

        {{-- // =========================  AREA TOMBOL ATAS (KANAN)  ========================= --}}
        {{-- // Tag: <div> --}}
        {{-- //   - Fungsi: Baris yang berisi tombol "Export Excel" & "Tambah" --}}
        {{-- //   - Atribut class: --}}
        {{-- //        flex            → susun horizontal --}}
        {{-- //        justify-end     → posisi item diratakan ke ujung kanan --}}
        {{-- //        items-center    → vertikal center --}}
        {{-- //        mb-4            → margin bawah 1rem --}}
        {{-- //        gap-2           → jarak antar tombol 0.5rem --}}
        <div class="flex justify-end items-center mb-4 gap-2">

            {{-- // =========================  TOMBOL KE HALAMAN TAMBAH  ========================= --}}
            {{-- // Tag: <a> (anchor/tautan) --}}
            {{-- //   - Fungsi: Pindah halaman ke form create produk. --}}
            {{-- //   - Atribut: --}}
            {{-- //       href="{{ route('products.create') }}" → URL ke rute bernama "products.create" (helper Blade). --}}
            {{-- //       class="btn btn-primary flex items-center gap-2"  → gaya tombol + layout ikon & teks --}}
            <a href="{{ route('products.create') }}" class="btn btn-primary flex items-center gap-2">
                {{-- // Ikon plus --}}
                <i class="ph ph-plus"></i>
                {{-- // Label teks tombol --}}
                <span>Tambah</span>
            </a>
        </div>

        {{-- // =========================  CARD PEMBUNGKUS TABEL  ========================= --}}
        {{-- // Tag: <div> --}}
        {{-- //   - Fungsi: Membuat "kartu" putih untuk tabel (rapi & fokus). --}}
        {{-- //   - Atribut class: --}}
        {{-- //       bg-white        → latar belakang putih --}}
        {{-- //       rounded         → sudut membulat --}}
        {{-- //       shadow          → bayangan halus --}}
        {{-- //       overflow-x-auto → jika tabel melebar, bisa scroll mendatar di layar kecil --}}
        {{-- //       p-4             → padding 1rem di dalam card --}}
        <div class="bg-white rounded shadow overflow-x-auto p-4">

            {{-- // =========================  TABEL DATA PRODUK  ========================= --}}
            {{-- // Tag: <table> --}}
            {{-- //   - Fungsi: Menampilkan data produk dalam bentuk tabel. --}}
            {{-- //   - Atribut: --}}
            {{-- //       id="productTable"                 → agar bisa diinisialisasi oleh DataTables (jQuery) --}}
            {{-- //       class="min-w-full divide-y ..."   → kelas utilitas tampilan tabel --}}
            <table id="productTable" class="min-w-full divide-y divide-gray-200">

                {{-- // ========== THEAD: Kepala Tabel ========== --}}
                {{-- // Tag: <thead> --}}
                {{-- //   - Fungsi: Menampung baris judul kolom (header) --}}
                {{-- //   - Atribut class="bg-gray-100" → latar abu agar beda dengan body --}}
                <thead class="bg-gray-100">
                    {{-- // Satu baris header kolom --}}
                    <tr>
                        {{-- // Tag: <th> (table header cell) --}}
                        {{-- //   - Fungsi: Sel judul kolom (biasanya bold + semantik header) --}}
                        {{-- //   - Atribut class: padding, posisi teks, ukuran & warna teks --}}
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">No</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Produk</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Nama Produk</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Deskripsi</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Size</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Harga</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Aksi</th>
                    </tr>
                </thead>

                {{-- // ========== TBODY: Badan Tabel (baris-baris data) ========== --}}
                {{-- // Tag: <tbody> --}}
                {{-- //   - Fungsi: Menampung baris data (hasil looping) --}}
                {{-- //   - Atribut class: warna latar putih & garis pemisah baris --}}
                <tbody class="bg-white divide-y divide-gray-200">
                    {{-- // Blade: @foreach → mengulang setiap item produk --}}
                    {{-- //  $index  : nomor urut (mulai 0) --}}
                    {{-- //  $product: item data produk saat ini --}}
                    @foreach ($products as $index => $product)
                        {{-- // Satu baris data (row) --}}
                        <tr>
                            {{-- // Kolom No: menampilkan nomor urut (index + 1) --}}
                            {{-- // Tag: <td> (table data cell) --}}
                            <td class="px-4 py-2">{{ $index + 1 }}</td>

                            {{-- // Kolom Produk (Gambar): tampilkan gambar atau placeholder --}}
                            <td class="px-4 py-2">
                                @php
                                    // PHP: tentukan path gambar
                                    // - Jika $product->display_image ada, ambil dari storage/public
                                    // - Jika tidak ada, gunakan gambar placeholder bawaan
                                    $img = $product->display_image
                                        ? asset('storage/' . $product->display_image)
                                        : asset('images/placeholder.png');
                                @endphp

                                {{-- // Tag: <img> (gambar) --}}
                                {{-- //   - Atribut: --}}
                                {{-- //        src="{{ $img }}"        → alamat file gambar --}}
                                {{-- //        alt="Product Image"     → teks alternatif (aksesibilitas) --}}
                                {{-- //        class="w-16 h-16 ..."   → ukuran 64px & object-contain agar tidak distorsi --}}
                                <img src="{{ $img }}" alt="Product Image" class="w-16 h-16 object-contain rounded">
                            </td>

                            {{-- // Kolom Nama Produk: judul produk --}}
                            <td class="px-4 py-2">{{ $product->title }}</td>

                            {{-- // Kolom Deskripsi: dipotong dengan ellipsis agar rapi --}}
                            {{-- //   - Atribut class="max-w-sm"   → batasi lebar sel --}}
                            {{-- //   - <div class="truncate">     → CSS untuk potong teks (ellipsis) --}}
                            {{-- //   - title="..."                → tooltip deskripsi penuh saat hover --}}
                            <td class="px-4 py-2 max-w-sm">
                                <div class="truncate" title="{{ $product->description }}">
                                    {{ $product->description }}
                                </div>
                            </td>

                            {{-- // Kolom Size: tampilkan '-' jika null menggunakan null coalescing --}}
                            <td class="px-4 py-2">{{ $product->size ?? '-' }}</td>

                            {{-- // Kolom Harga: format Rupiah pakai number_format --}}
                            {{-- //  number_format($angka, 0, ',', '.') → 1.234.567 --}}
                            <td class="px-4 py-2">Rp {{ number_format($product->price, 0, ',', '.') }}</td>

                            {{-- // Kolom Aksi: tombol Edit & Hapus --}}
                            <td class="px-4 py-2">
                                {{-- // Wrapper aksi: flex agar ikon & tombol sejajar --}}
                                <div class="flex items-center space-x-2">

                                    {{-- // Link Edit --}}
                                    {{-- // Tag: <a> --}}
                                    {{-- //   - href   : route ke halaman edit dengan id produk --}}
                                    {{-- //   - class  : warna teks biru & efek hover --}}
                                    {{-- //   - title  : tooltip "Edit" --}}
                                    {{-- //   - <i>    : ikon pensil --}}
                                    <a href="{{ route('products.edit', $product->id) }}"
                                       class="text-blue-500 hover:text-blue-700" title="Edit">
                                        <i class="ph ph-pencil"></i>
                                    </a>

                                    {{-- // Form Hapus --}}
                                    {{-- // Tag: <form> --}}
                                    {{-- //   - action : route ke destroy (hapus) --}}
                                    {{-- //   - method : POST (HTML hanya GET/POST) --}}
                                    {{-- //   - @method('DELETE') : spoof method agar jadi DELETE --}}
                                    {{-- //   - @csrf : token keamanan Laravel --}}
                                    {{-- //   - onsubmit="return confirm(...)" : popup konfirmasi sebelum hapus --}}
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                          onsubmit="return confirm('Yakin hapus produk ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        {{-- // Tombol submit untuk hapus --}}
                                        {{-- // Tag: <button type="submit"> --}}
                                        {{-- //   - class : warna merah + hover --}}
                                        {{-- //   - title : tooltip "Hapus" --}}
                                        <button type="submit" class="text-red-500 hover:text-red-700" title="Hapus">
                                            <i class="ph ph-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table> {{-- // selesai tabel --}}
        </div> {{-- // selesai card pembungkus tabel --}}
    </div> {{-- // selesai kontainer halaman --}}
@endsection

{{-- // =========================  S T A C K   S C R I P T S  ========================= --}}
{{-- // @push('scripts') → menambahkan resource ke stack "scripts" yang didefinisikan di layout --}}
@push('scripts')

    {{-- // Memuat script ikon Phosphor agar <i class="ph ..."> dapat menampilkan ikon --}}
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    {{-- // Memuat jQuery (wajib sebelum DataTables) --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    {{-- // Memuat CSS & JS DataTables (fitur tabel pintar: sort, search, paginate) --}}
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    {{-- // Memuat DataTables Buttons + JSZip untuk Export Excel (excelHtml5) --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    {{-- // =========================  I N I S I A L I S A S I   D A T A T A B L E S  ========================= --}}
    <script>
        // Jalankan setelah DOM siap (document ready)
        $(function() {

            // Inisialisasi DataTable di tabel dengan id="productTable"
            const dt = $('#productTable').DataTable({

                // language: terjemahan UI DataTables ke Bahasa Indonesia
                language: {
                    search: 'Cari:',                            // label kolom pencarian
                    lengthMenu: 'Tampilkan _MENU_ entri',      // dropdown jumlah baris per halaman
                    info: 'Menampilkan _START_–_END_ dari _TOTAL_ data', // info rangkuman
                    paginate: {
                        previous: '<i class="ph ph-caret-left"></i>', // ikon prev
                        next: '<i class="ph ph-caret-right"></i>'     // ikon next
                    },
                    zeroRecords: 'Data tidak ditemukan',        // saat tidak ada hasil
                    infoEmpty: 'Tidak ada data',               // saat tabel kosong
                    infoFiltered: '(disaring dari _MAX_ total data)' // info filter
                },

                // columnDefs: konfigurasi kolom tertentu
                //  - orderable:false → kolom tidak bisa diurut
                //  - targets: [1, 6] → kolom 1 (gambar) & 6 (aksi) dimatikan sortingnya
                columnDefs: [
                    { orderable: false, targets: [1, 6] }
                ],

                // dom: atur elemen UI DataTables yang ditampilkan
                //  'f' → filter (search box)
                //  'r' → processing
                //  't' → table
                //  'i' → table info
                //  'p' → pagination
                // Catatan: Tidak menaruh 'B' (Buttons) di sini, karena kita pakai tombol custom di header.
                dom: 'frtip',

                // buttons: definisi tombol export “virtual” (tetap didefinisikan agar bisa dipanggil manual)
                buttons: [
                    {
                        extend: 'excelHtml5',                   // jenis tombol: export Excel
                        title: 'Daftar Produk',                 // judul sheet/file
                        exportOptions: { columns: [0,1,2,3,4,5] } // kolom yang diexport (tanpa kolom Aksi = 6)
                    }
                ]
            });

            // Tambahkan placeholder di input pencarian biar lebih informatif
            $('#productTable_filter input').attr('placeholder', 'Cari produk...');

            // Hubungkan tombol custom Export Excel (#btnExcel) dengan tombol virtual DataTables
            $('#btnExcel').on('click', function () {
                dt.button('.buttons-excel').trigger();
            });
        });
    </script>
@endpush
