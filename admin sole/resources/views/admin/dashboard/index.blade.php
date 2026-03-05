{{-- ======================================================================================
  FILE    : resources/views/.../manajemen-akun.blade.php  (contoh nama file)
  TUJUAN  : Halaman "Manajemen Akun" → kartu statistik + tabel daftar pengguna.
  TEKNO   : Blade (Laravel View Engine) + Tailwind CSS + jQuery DataTables + Phosphor Icons
  CATATAN : Komentar menjelaskan TAG HTML, ATRIBUT, UTILITAS TAILWIND satu per satu.
====================================================================================== --}}

{{-- ===================== @extends =====================
  Directive Blade: @extends('layouts.admin')
  Fungsi         : Mengatakan view ini mewarisi layout "layouts.admin".
  Efek           : Layout biasanya punya kerangka (header, sidebar, footer) dan slot
                   untuk @section('page') dan @section('content'), serta @stack('scripts').
---------------------------------------------------- --}}
@extends('layouts.admin')

{{-- ===================== @section('page') =====================
  Directive: @section('page') ... @endsection
  Fungsi   : Mengisi section bernama "page" di layout. Umumnya dipakai untuk:
             - <title> tab browser
             - header judul / breadcrumb halaman
  Isi      : "Manajemen Akun"
---------------------------------------------------------- --}}
@section('page')
    Manajemen Akun 
@endsection

{{-- ===================== @section('content') =====================
  Directive: @section('content') ... @endsection
  Fungsi   : Mengisi slot konten utama layout. Semua isi halaman diletakkan di sini.

  ✳ Tentang $stats (DITAMBAHKAN RINCIAN):
    - $stats adalah array/collection yang DIKIRIM dari Controller (mis. DashboardController@index).
    - $stats tidak dibuat di Blade — HARUS disiapkan & dipassing saat return view().
    - Isi $stats (yang dipakai di view ini):
        • $stats['total_users']           → (int) jumlah seluruh user (contoh: User::count()).
        • $stats['total_offline_orders']  → (int) jumlah order bertipe 'offline'.
        • $stats['total_online_orders']   → (int) jumlah order bertipe 'online'.
        • $stats['recent_users']          → (Collection<User>) daftar user terbaru untuk ditabelkan.
    - Contoh di Controller (sekadar ilustrasi asal data):
        $stats = [
            'total_users'           => User::count(),
            'total_offline_orders'  => Order::where('type','offline')->count(),
            'total_online_orders'   => Order::where('type','online')->count(),
            'recent_users'          => User::latest()->take(50)->get(),
        ];
        return view('...manajemen-akun', compact('stats'));
--------------------------------------------------------------- --}}
@section('content')

    {{-- ===================== WRAPPER LUAR =====================
      Tag HTML : <div> → elemen blok untuk membungkus konten.
      class    : p-6
                 - p-6 = padding 1.5rem di semua sisi (Tailwind scale: 6 → 1.5rem).
                 (p = padding all; px = horizontal; py = vertikal; pt/pr/pb/pl = top/right/bottom/left)
      Peran    : Memberi "ruang napas" agar konten tidak menempel ke tepi.
    --------------------------------------------------------- --}}
    <div class="p-6">

        {{-- ===================== GRID: 3 KARTU STATISTIK =====================
          Tag     : <div>
          class   : grid grid-cols-1 md:grid-cols-3 gap-6 mb-6
                    - grid            : mengaktifkan CSS Grid container.
                    - grid-cols-1     : default (mobile) jumlah kolom = 1.
                    - md:grid-cols-3  : pada layar ≥768px (breakpoint md), jumlah kolom = 3.
                    - gap-6           : jarak antar grid item 1.5rem (24px).
                    - mb-6            : margin-bottom 1.5rem.
          Peran   : Menaruh 3 kartu di 1 kolom (mobile) atau 3 kolom (desktop).
        ------------------------------------------------------------------- --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

            {{-- ===================== KARTU 1: TOTAL USER =====================
              Tag     : <div>
              class   : bg-white rounded-lg shadow p-6
                        - bg-white   : latar putih.
                        - rounded-lg : sudut membulat (lebih besar dari rounded biasa).
                        - shadow     : bayangan halus di sekitar card.
                        - p-6        : padding 1.5rem di dalam card.
              Peran   : Menampilkan angka total user (statistik).
            ---------------------------------------------------------------- --}}
            <div class="bg-white rounded-lg shadow p-6">
                {{-- ===== BARIS ISI KARTU (IKON + TEKS) =====
                   Tag   : <div>
                   class : flex items-center
                           - flex          : menjadikan container flexbox (arah default baris/horizontal).
                           - items-center  : vertikal align tengah untuk item flex.
                   Peran : Memposisikan ikon dan teks secara sejajar horizontal.
                ------------------------------------------------------------- --}}
                <div class="flex items-center">

                    {{-- ===== WADAH IKON =====
                       Tag   : <div>
                       class : p-3 rounded-full aspect-square flex-shrink-0 px-4 bg-blue-100 text-blue-600 mr-4
                               - p-3 / px-4     : padding (p = semua sisi; px = kiri-kanan).
                               - rounded-full   : sudut sangat membulat → lingkaran/kapsul.
                               - aspect-square  : rasio aspek 1:1 (butuh Tailwind modern).
                               - flex-shrink-0  : mencegah kotak ini mengecil saat ruang sempit.
                               - bg-blue-100    : latar biru sangat muda.
                               - text-blue-600  : warna teks/ikon biru tegas.
                               - mr-4           : margin-right 1rem (jarak dari teks).
                       Peran : Lingkaran berisi ikon.
                    -------------------------------------------------------- --}}
                    <div class="p-3 rounded-full aspect-square flex-shrink-0 px-4 bg-blue-100 text-blue-600 mr-4">
                        {{-- ===== IKON =====
                           Tag   : <i> → elemen inline (ikon dari Phosphor).
                           class : ph ph-users text-2xl
                                   - ph ph-users : kelas ikon jenis "users".
                                   - text-2xl    : ukuran font besar (berpengaruh ke ukuran ikon).
                           Syarat: Pastikan stylesheet Phosphor di-include (umumnya di layout).
                        --}}
                        <i class="ph ph-users text-2xl"></i>
                    </div>

                    {{-- ===== BLOK TEKS STATISTIK =====
                       Tag : <div>
                       Isi : Label kecil + angka besar (diambil dari controller).
                    --------------------------------- --}}
                    <div>
                        {{-- Tag <p> → paragraf; class:
                           - text-sm      : ukuran teks kecil.
                           - text-gray-500: warna abu (sekunder).
                        --}}
                        <p class="text-sm text-gray-500">Total User</p>

                        {{-- Tag <h3> → heading level 3; class:
                           - text-2xl : ukuran teks besar.
                           - font-bold: ketebalan tebal (bold).
                           Isi       : {{ $stats['total_users'] }} → angka dari controller.
                        --}}
                        <h3 class="text-2xl font-bold">{{ $stats['total_users'] }}</h3>
                    </div>
                </div>
            </div>

            {{-- ===================== KARTU 2: TOTAL ORDER OFFLINE (LINK) =====================
              Tag     : <a> → anchor; bisa diklik.
              href    : route('order.offline') → helper Blade untuk membuat URL dari nama route.
              class   : bg-white rounded-lg shadow p-6 (sama seperti kartu 1).
              Peran   : Jika diklik, menuju halaman daftar order offline.
            ------------------------------------------------------------------------------- --}}
            <a href="{{ route('order.offline') }}" class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    {{-- Wadah ikon: tema hijau --}}
                    <div class="p-3 rounded-full aspect-square flex-shrink-0 px-4 bg-green-100 text-green-600 mr-4">
                        <i class="ph ph-storefront text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Order Offline</p>
                        <h3 class="text-2xl font-bold">{{ $stats['total_offline_orders'] }}</h3>
                    </div>
                </div>
            </a>

            {{-- ===================== KARTU 3: TOTAL ORDER ONLINE (LINK) =====================
              Tag     : <a>
              href    : route('order.online') → ke daftar order online.
              class   : bg-white rounded-lg shadow p-6.
              Peran   : Kartu clickable menuju halaman lain.
            ------------------------------------------------------------------------------ --}}
            <a href ="{{ route('order.online') }}" class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full aspect-square flex-shrink-0 px-4 bg-purple-100 text-purple-600 mr-4">
                        <i class="ph ph-globe-hemisphere-east text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Order Online</p>
                        <h3 class="text-2xl font-bold">{{ $stats['total_online_orders'] }}</h3>
                    </div>
                </div>
            </a>
        </div> {{-- /GRID 3 kartu --}}

        {{-- ===================== CARD TABEL USERS =====================
          Tag   : <div>
          class : bg-white rounded-lg shadow overflow-hidden px-6
                  - bg-white       : latar putih
                  - rounded-lg     : sudut membulat
                  - shadow         : bayangan
                  - overflow-hidden: konten child yang melebihi batas akan terpotong (agar rounded rapi)
                  - px-6           : padding horizontal 1.5rem
          Peran : Pembungkus untuk tabel daftar pengguna.
        ----------------------------------------------------------- --}}
        <div class="bg-white rounded-lg shadow overflow-hidden px-6">

            {{-- ===== HEADER KECIL CARD =====
               Tag   : <div>
               class : py-4 border-b mb-3
                       - py-4    : padding y-axis 1rem.
                       - border-b: garis bawah (separator).
                       - mb-3    : margin bawah 0.75rem.
               Peran : Judul kartu "Users".
            --}}
            <div class="py-4 border-b mb-3">
                {{-- <h3> → heading; class: text-lg (besar sedang), font-semibold (tebal sedang) --}}
                <h3 class="text-lg font-semibold">Users</h3>
            </div>

            {{-- ===== WRAPPER SCROLL HORIZONTAL =====
               Tag   : <div>
               class : overflow-x-auto
                       - memungkinkan scroll horizontal jika tabel lebih lebar dari container (khusus mobile).
            --}}
            <div class="overflow-x-auto">

                {{-- ===================== TABLE =====================
                  Tag   : <table> → elemen semantik tabel.
                  id    : usersTable → untuk selector DataTables jQuery.
                  class : min-w-full divide-y divide-gray-200
                          - min-w-full     : minimal lebar 100% container → tabel melebar penuh.
                          - divide-y       : garis pemisah antar baris (horizontal).
                          - divide-gray-200: warna garis pemisah abu muda.
                  Peran : Menampilkan data user dalam format baris/kolom.
                --------------------------------------------------- --}}
                <table id="usersTable" class="min-w-full divide-y divide-gray-200">

                    {{-- ===================== THEAD (Table Head) =====================
                      Tag   : <thead> → bagian kepala tabel berisi judul kolom (<th>).
                      class : bg-gray-50 → latar abu sangat muda agar beda dengan body.
                    ---------------------------------------------------------------- --}}
                    <thead class="bg-gray-50">
                        {{-- ===== SATU BARIS HEADER =====
                           Tag : <tr> → table row (baris tabel).
                        --}}
                        <tr>
                            {{-- ===== KEPALA KOLOM (TH) =====
                               Tag   : <th> → table header cell; semantik header (tebal dan dapat diakses).
                               class : px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider
                                       - px-6          : padding horizontal 1.5rem (spacing antar kolom).
                                       - py-3          : padding vertikal 0.75rem (tinggi baris header).
                                       - text-left     : teks rata kiri.
                                       - text-xs       : ukuran font kecil.
                                       - font-medium   : ketebalan sedang (antara normal dan bold).
                                       - text-gray-500 : warna abu (sekunder; kontras cukup).
                                       - uppercase     : mengubah huruf jadi kapital semua.
                                       - tracking-wider: jarak antar-karakter (letter-spacing) lebih lebar → gaya header.
                               Isi   : Nama kolom.
                            --}}
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NAMA
                            </th>

                            {{-- Kolom No Telp disembunyikan sementara (komentar Blade).
                               Manfaat: mudah diaktifkan lagi nanti. --}}
                            {{-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No
                                Telp</th> --}}

                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Password</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Action</th>
                        </tr>
                    </thead>

                    {{-- ===================== TBODY (Table Body) =====================
                      Tag   : <tbody> → bagian isi data tabel (baris-baris record).
                      class : bg-white divide-y divide-gray-200
                              - bg-white      : latar putih.
                              - divide-y ...  : garis pemisah antar baris data.
                    ---------------------------------------------------------------- --}}
                    <tbody class="bg-white divide-y divide-gray-200">

                        {{-- ===================== LOOP BLADE: @foreach =====================
                          Sintaks : @foreach ($stats['recent_users'] as $index => $user)
                          Arti    : Ulangi blok <tr> untuk setiap elemen di koleksi $stats['recent_users'].
                          Variabel yang didapat:
                            - $index : angka urut mulai 0 (index array/collection). Kita tampilkan sebagai $index + 1 agar mulai dari 1.
                                      Cocok untuk kolom "No" tanpa bikin counter manual.
                            - $user  : instance App\Models\User pada iterasi saat ini (memiliki properti name, email, dst).
                          Kenapa memakai "as $index => $user"?
                            - Kita butuh DUA hal sekaligus: objek user-nya dan angka urutnya.
                              Dengan pola ini, Blade memberi keduanya tanpa variabel tambahan.
                          Keamanan:
                            - {{ ... }} otomatis di-escape oleh Blade → mencegah HTML/JS injeksi (XSS).
                        ---------------------------------------------------------------- --}}
                        @foreach ($stats['recent_users'] as $index => $user)
                            {{-- ===== SATU BARIS DATA USER =====
                               Tag : <tr>
                            --}}
                            <tr>
                                {{-- ===== SEL DATA (TD) : No =====
                                   Tag   : <td> → table data cell (isi tabel).
                                   class : px-6 py-4 whitespace-nowrap
                                           - px-6            : padding kiri-kanan 1.5rem.
                                           - py-4            : padding atas-bawah 1rem → tinggi baris nyaman.
                                           - whitespace-nowrap: mencegah teks membungkus ke baris baru (tetap satu baris).
                                   Isi   : {{ $index + 1 }} → jadikan nomor urut 1,2,3... (bukan 0,1,2).
                                --}}
                                <td class="px-6 py-4 whitespace-nowrap">{{ $index + 1 }}</td>

                                {{-- ===== TD : Nama =====
                                   {{ $user->name }} → akses properti 'name' dari model User.
                                --}}
                                <td class="px-6 py-4 whitespace-nowrap">{{ $user->name }}</td>

                                {{-- ===== TD : No Telp (DISABLE) ===== --}}
                                {{-- <td class="px-6 py-4 whitespace-nowrap">{{ $user->phone }}</td> --}}

                                {{-- ===== TD : Email ===== --}}
                                <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>

                                {{-- ===== TD : Password (disembunyikan dengan masking) =====
                                   NOTE: Keamanan – jangan pernah menampilkan password asli (di DB tersimpan hash).
                                --}}
                                <td class="px-6 py-4 whitespace-nowrap">**********</td>

                                {{-- ===== TD : Action (Form Hapus) ===== --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{-- ===== FORM DELETE USER =====
                                       Tag     : <form>
                                       action  : route('users.destroy', $user->id)
                                                 → helper route() membuat URL ke route bernama 'users.destroy'
                                                   + parameter {id} = $user->id.
                                       method  : "POST" → form HTML hanya mendukung GET/POST.
                                       @csrf   : Token proteksi CSRF (Cross-Site Request Forgery).
                                       @method('DELETE') : "method spoofing" agar Laravel memperlakukan request sebagai HTTP DELETE
                                                          (cocok dengan Route::delete()).
                                       Peran   : Menghapus user via UserController@destroy.
                                       Tips UX : Tambahkan onsubmit="return confirm('Yakin?')" untuk konfirmasi.
                                    --}}
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')

                                        {{-- ===== BUTTON SUBMIT =====
                                           Tag   : <button type="submit">
                                           class : btn btn-danger (kelas kustom proyek; biasanya warna merah)
                                           Isi   : Ikon trash → <i class="ph ph-trash text-2xl"></i>
                                                   - ph ph-trash : ikon “tempat sampah”.
                                                   - text-2xl    : ukuran ikon besar.
                                        --}}
                                        <button type="submit" class="btn btn-danger">
                                            <i class="ph ph-trash text-2xl"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        {{-- ===================== /LOOP @foreach ===================== --}}

                    </tbody>
                </table> {{-- /TABLE --}}
            </div> {{-- /overflow-x-auto --}}
        </div> {{-- /CARD tabel users --}}
    </div> {{-- /WRAPPER LUAR p-6 --}}
@endsection

{{-- ===================== @push('scripts') =====================
  Directive: @push('scripts') ... @endpush
  Fungsi   : Menitipkan script ke stack "scripts" yang biasanya di-render di akhir <body>
             pada layout melalui @stack('scripts').
  Kegunaan : Memuat resource khusus halaman (jQuery, DataTables) hanya ketika halaman ini dibuka.
------------------------------------------------------------- --}}
@push('scripts')
    {{-- ===== jQuery (dependency DataTables) =====
       <script src="..."> : meload library jQuery dari CDN.
       Versi 3.6.0 sesuai dengan DataTables modern.
    --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    {{-- ===== DataTables CSS & JS (inti) =====
       <link rel="stylesheet" href="..."> : stylesheet DataTables (tampilan tabel, pagination, search box).
       <script src="...">                : script DataTables logika interaktif (sort, search, paginate).
    --}}
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    {{-- ===================== INISIALISASI DATATABLES =====================
      <script>  : blok JS untuk mengaktifkan DataTables pada tabel dengan id #usersTable.
      $(document).ready(...) / $(function(){...}) : memastikan DOM siap sebelum seleksi elemen.
      $('#usersTable').DataTable({...}) : memanggil plugin DataTables.
      Option "language" :
        - Kustomisasi teks UI jadi bahasa Indonesia.
        - previous/next pakai ikon Phosphor (pastikan CSS ikon terload di layout).
    ------------------------------------------------------------------- --}}
    <script>
        $(document).ready(function() {
            $('#usersTable').DataTable({
                "language": {
                    "search": "Cari:",                          // label untuk kotak pencarian
                    "lengthMenu": "Tampilkan _MENU_",           // dropdown jumlah baris
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data", // info paging
                    "paginate": {                               // ikon navigasi halaman
                        "previous": '<i class="ph ph-caret-left"></i>', 
                        "next": '<i class="ph ph-caret-right"></i>'
                    }
                }
            });
        });
    </script>
@endpush
