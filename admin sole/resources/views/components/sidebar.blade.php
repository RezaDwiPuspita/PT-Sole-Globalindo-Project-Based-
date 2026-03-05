{{-- ======================================================================================
  FILE   : resources/views/.../partials/sidebar.blade.php (contoh)
  TUJUAN : Sidebar navigasi Admin (logo, menu Home, Dashboard Owner, Sales, Order, Logout)
  TEKNO  : Blade (Laravel), Tailwind CSS, helper Auth, helper route(), komponen ikon Phosphor.
  CATAT  : Komentar super-rinci di baris terkait (tag, atribut Tailwind, helper, variabel).
====================================================================================== --}}

@php
    // ================================== VAR PRE-COMPUTE ==================================
    // $salesOpen dan $orderOpen digunakan untuk menentukan apakah submenu "Sales" dan "Order"
    // ditampilkan (expanded) atau disembunyikan (collapsed) saat halaman dimuat.
    //
    // isNavbarActive([...]) → (helper kustom proyek, BUKAN fungsi bawaan Laravel)
    //   - Biasanya menerima satu string nama route atau array beberapa nama/fragmen route,
    //     mengembalikan true jika route saat ini cocok dengan salah satu input.
    //   - Implementasi umum: membandingkan Route::currentRouteName() dengan daftar yang diberikan,
    //     atau menggunakan Route::is('prefix.*').
    //   - Di sini kita kirim array ['product'] dan ['order.offline', 'order.online'] → artinya jika route aktif
    //     adalah "order.offline" atau "order.online", maka submenu Order dibuka.
    //   - Untuk Sales, kita pakai ['product'] agar aktif saat di halaman products.index atau products.create.
    //   - Catatan: Jangan pakai ['order'] saja karena akan match dengan "orders.list" juga.
    //   - $orderActive: untuk menentukan apakah button "Order" harus berwarna kuning (hanya saat submenu aktif)
    $salesOpen = isNavbarActive(['product']);
    $orderOpen = isNavbarActive(['order.offline', 'order.online']);
    $orderActive = isNavbarActive(['order.offline', 'order.online']); // Button Order berwarna kuning hanya saat submenu aktif
@endphp

{{-- ===================== ASIDE WRAPPER =====================
  <aside>            : elemen semantik untuk konten samping (sidebar).
  class:
    w-64             : lebar tetap 16rem (256px).
    min-h-screen     : tinggi minimal = tinggi viewport (100vh), agar sidebar sepanjang layar.
    bg-white         : latar putih (tema terang).
    p-4              : padding 1rem di semua sisi.
    shadow-md        : bayangan medium di sekitar container (elevasi).
---------------------------------------------------------- --}}
<aside class="w-64 min-h-screen bg-white p-4 shadow-md">

  {{-- ===================== LOGO WRAPPER =====================
    text-center       : konten di tengah secara horizontal (teks/inline level).
    mb-6              : margin-bottom 1.5rem (jarak ke menu).
  -------------------------------------------------------- --}}
  <div class="text-center mb-6">
    {{-- <img> logo:
         src="{{ asset('assets/logo-sole.png') }}"
           - asset()        : helper Laravel membentuk URL absolut ke file publik (public/assets/...)
         alt="logo"         : teks alternatif (aksesibilitas).
         class="w-auto"     : lebar otomatis mengikuti gambar, tinggi mengikuti proporsi. --}}
    <img src="{{ asset('assets/logo-sole.png') }}" alt="logo" class="w-auto">
  </div>

  {{-- ===================== LIST NAVIGASI =====================
    <ul>               : daftar item navigasi.
    class="space-y-1"  : jarak vertikal 0.25rem antar <li>.
  -------------------------------------------------------- --}}
  <ul class="space-y-1">

    {{-- ===================== ITEM: Home =====================
      <li> pembungkus item list.
    ------------------------------------------------------ --}}
    <li>
      {{-- <a> sebagai link menu:
           href="{{ route('admin.index') }}"
             - route('admin.index') : helper ke rute bernama 'admin.index' (stabil dibanding hardcode URL).
           class="flex items-center px-4 py-3 rounded-lg transition {{ ... }}"
             - flex              : isi (ikon + label) disusun horizontal.
             - items-center      : vertical-align tengah untuk ikon & teks.
             - px-4 / py-3       : padding x=1rem, y=0.75rem (klik area nyaman).
             - rounded-lg        : sudut membulat cukup besar.
             - transition        : enable transisi halus pada hover.
             - ekspresi ternary  : jika isNavbarActive('admin.index') true → beri 'bg-yellow-300 font-semibold'
                                   (menandakan menu aktif); else → 'hover:bg-yellow-100' (efek hover saja). --}}
      <a href="{{ route('admin.index') }}"
         class="flex items-center px-4 py-3 rounded-lg transition
               {{ isNavbarActive('admin.index') ? 'bg-yellow-300 font-semibold' : 'hover:bg-yellow-100' }}">
        {{-- Ikon rumah (Phosphor). Pastikan CSS ikon di-include di layout. --}}
        <i class="ph ph-house"></i>
        {{-- <span class="ml-3"> :
             ml-3 → margin-left 0.75rem untuk memberi jarak dari ikon. --}}
        <span class="ml-3">Home</span>
      </a>
    </li>

    {{-- ===================== KONDISI ROLE OWNER =====================
      @if (Auth::user()->role === 'owner')
        - Tampilkan menu "Dashboard" hanya untuk user dengan role 'owner'.
      Auth::user()      : ambil user login saat ini.
      ->role            : ambil kolom role dari Model User.
    -------------------------------------------------------------- --}}
    @if (Auth::user()->role === 'owner')
      <li>
        <a href="{{ route('dashboard.owner') }}"
           class="flex items-center px-4 py-3 rounded-lg transition
               {{ isNavbarActive('dashboard.owner') ? 'bg-yellow-300 font-semibold' : 'hover:bg-yellow-100' }}">
          <i class="ph ph-grid-four"></i>
          <span class="ml-3">Dashboard</span>
        </a>
      </li>
    @endif

    {{-- ===================== GROUP: Sales (collapsible) =====================
      Pola:
        - <button> sebagai trigger expand/collapse submenu.
        - <ul id="salesMenu"> sebagai isi submenu.
        - $salesOpen menentukan state awal (expanded/hidden) saat render server-side.
        - onclick="toggleMenu('salesMenu')" → handler JS untuk toggle class 'hidden'.
        - $salesActive: untuk menentukan apakah button "Sales" harus berwarna kuning (hanya saat submenu aktif)
    ---------------------------------------------------------------------- --}}
    @php
        $salesActive = isNavbarActive(['product']); // Button Sales berwarna kuning hanya saat submenu aktif
    @endphp
    <li>
      {{-- <button> trigger:
           class="w-full flex items-center justify-between px-4 py-3 rounded-lg transition {{ ... }}"
             - w-full          : lebar penuh (menutupi baris).
             - justify-between : kiri berisi label; kanan berisi ikon caret.
             - Ternary $salesActive:
                 true  → 'bg-yellow-300 font-semibold' (submenu sedang aktif).
                 false → 'hover:bg-yellow-100' (submenu tidak aktif; tampilkan hover saja). --}}
      <button onclick="toggleMenu('salesMenu')"
              class="w-full flex items-center justify-between px-4 py-3 rounded-lg transition
                    {{ $salesActive ? 'bg-yellow-300 font-semibold' : 'hover:bg-yellow-100' }}">
        <div class="flex items-center">
          <i class="ph ph-lock-key"></i>
          <span class="ml-3">Sales</span>
        </div>
        {{-- Ikon caret dinamis:
             class="ph {{ $salesOpen ? 'ph-caret-up' : 'ph-caret-down' }}"
             - Jika terbuka → caret-up (menghadap ke atas)
             - Jika tertutup → caret-down (menghadap ke bawah) --}}
        <i class="ph {{ $salesOpen ? 'ph-caret-up' : 'ph-caret-down' }}"></i>
      </button>

      {{-- Submenu Sales:
         <ul id="salesMenu"
           - id : HARUS unik karena dipakai toggleMenu('salesMenu').
           - class:
               pl-10    : padding-left 2.5rem (indentasi submenu).
               mt-1     : margin-top kecil.
               space-y-3: jarak antar <li> submenu 0.75rem.
               py-3     : padding atas bawah 0.75rem.
               text-sm  : ukuran teks kecil.
               text-gray-800 : warna teks abu gelap.
               {{ $salesOpen ? '' : 'hidden' }} : jika $salesOpen false → tambahkan 'hidden' agar tersembunyi. --}}
      <ul id="salesMenu" class="pl-10 mt-1 space-y-3 py-3 text-sm text-gray-800 {{ $salesOpen ? '' : 'hidden' }}">
        <li>
          {{-- Link submenu: Product List
             class dinamis:
               - aktif → 'font-semibold text-black'
               - non-aktif → 'hover:text-black' --}}
          <a href="{{ route('products.index') }}"
             class="{{ isNavbarActive('products.index') ? 'font-semibold text-black' : 'hover:text-black' }}">
            Product List
          </a>
        </li>
        <li>
          {{-- Link submenu: Tambah Product --}}
          <a href="{{ route('products.create') }}"
             class="{{ isNavbarActive('products.create') ? 'font-semibold text-black' : 'hover:text-black' }}">
            Tambah Product
          </a>
        </li>
      </ul>
    </li>

    {{-- ===================== GROUP: Order (collapsible) ===================== --}}
    <li>
      <button onclick="toggleMenu('orderMenu')"
              class="w-full flex items-center justify-between px-4 py-3 rounded-lg transition
                    {{ $orderActive ? 'bg-yellow-300 font-semibold' : 'hover:bg-yellow-100' }}">
        <div class="flex items-center">
          <i class="ph ph-lock-key"></i>
          <span class="ml-3">Order</span>
        </div>
        <i class="ph {{ $orderOpen ? 'ph-caret-up' : 'ph-caret-down' }}"></i>
      </button>

      <ul id="orderMenu"
          class="pl-10 mt-1 space-y-3 py-3 text-sm text-gray-800 {{ $orderOpen ? '' : 'hidden' }}">
        <li>
          <a href="{{ route('order.offline') }}"
             class="{{ isNavbarActive('order.offline') ? 'font-semibold text-black' : 'hover:text-black' }}">
            Order Offline
          </a>
        </li>
        <li>
          <a href="{{ route('order.online') }}"
             class="{{ isNavbarActive('order.online') ? 'font-semibold text-black' : 'hover:text-black' }}">
            Order Online
          </a>
        </li>
      </ul>
    </li>

    {{-- ===================== ITEM: List Pesanan =====================
      Menu untuk menampilkan semua pesanan (online & offline) dalam satu list
      dengan filter per minggu.
      Hanya ditampilkan untuk admin (bukan owner).
    ------------------------------------------------------ --}}
    @if (Auth::user()->role !== 'owner')
    <li>
      <a href="{{ route('orders.list') }}"
         class="flex items-center px-4 py-3 rounded-lg transition
               {{ isNavbarActive('orders.list') ? 'bg-yellow-300 font-semibold' : 'hover:bg-yellow-100' }}">
        <i class="ph ph-list-bullets"></i>
        <span class="ml-3">List Pesanan</span>
      </a>
    </li>
    @endif

    {{-- ===================== ITEM: Logout (Form POST) =====================
      Menggunakan form POST agar aman (CSRF protected). Jangan gunakan GET untuk logout.
      mt-10           : margin-top besar (spasi dari menu sebelumnya).
    ------------------------------------------------------------------- --}}
    <li class="mt-10">
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        {{-- <button> full width agar area klik luas.
             hover:bg-red-50 : highlight merah samar saat hover.
             text-red-600    : warna teks merah (konsisten dengan aksi keluar/hapus). --}}
        <button type="submit"
                class="w-full flex items-center px-4 py-3 rounded-lg hover:bg-red-50 text-red-600">
          <i class="ph ph-sign-out"></i>
          <span class="ml-3">Logout</span>
        </button>
      </form>
    </li>

  </ul>
</aside>

{{-- ===================== SCRIPT: Toggle Submenu =====================
  @push('scripts')       : menaruh JS ke stack "scripts" yang dirender di layout (biasanya sebelum </body>).
  Fungsi toggleMenu(id)  : find element by id, lalu toggle kelas 'hidden':
    - hidden (Tailwind)  : display: none;
    - classList.toggle() : jika kelas ada → dihapus (jadi tampil), jika tidak ada → ditambahkan (disembunyikan).
  CATATAN:
    * Ini client-side toggle sederhana — state tidak persist saat reload. Untuk persist,
      gunakan server-side ($salesOpen/$orderOpen) SEPERTI yang sudah dilakukan saat render awal.
------------------------------------------------------------------ --}}
@push('scripts')
  <script>
    function toggleMenu(id) {
      const menu = document.getElementById(id); // ambil element submenu berdasarkan id unik
      menu.classList.toggle('hidden');          // nyalakan/matikan kelas 'hidden' (show/hide)
    }
  </script>
@endpush
