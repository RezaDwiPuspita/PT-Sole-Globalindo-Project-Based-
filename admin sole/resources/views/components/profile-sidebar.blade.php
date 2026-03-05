{{-- ======================================================================================
  BLOK   : Sidebar Profil + Menu (Informasi Akun, Pesananku, Logout)
  TUJUAN : Menampilkan sapaan, nama user login, navigasi profil, order, dan tombol logout.
  TEKNO  : Blade (Laravel), Tailwind CSS, helper Auth, Route, Str, @csrf.
  CATATAN: Komentar super-rinci di baris terkait (tag, atribut Tailwind, helper, dan best-practice).
====================================================================================== --}}

{{-- ===================== JUDUL HALAMAN =====================
  <h1>                  : heading utama halaman (level 1).
  class="col-span-12"   : jika berada dalam grid 12 kolom, ini mengambil penuh 12 kolom (lebar penuh).
  text-2xl              : ukuran font besar (≈ 1.5rem).
  mb-5                  : margin-bottom 1.25rem → jarak ke elemen di bawah.
---------------------------------------------------------- --}}
<h1 class="col-span-12 text-2xl mb-5">Profil</h1>

{{-- ===================== KOLOM SIDEBAR (LEBAR 3/12) =====================
  <div class="col-span-3"> : di layout grid 12 kolom, ambil 3 kolom (¼ lebar).
----------------------------------------------------------------------- --}}
<div class="col-span-3">

  {{-- ===================== KARTU SIDEBAR =====================
    rounded-sm          : sudut membulat kecil.
    border              : garis tepi tipis.
    border-slate-200    : warna garis abu kebiruan terang.
    p-4                 : padding 1rem ke semua sisi isi.
    mb-6                : jarak bawah 1.5rem dari elemen berikutnya.
  --------------------------------------------------------- --}}
  <div class="rounded-sm border border-slate-200 p-4 mb-6">

    {{-- ====== HEADER KARTU: Sapaan & Nama User ======
      flex flex-col     : tata letak vertikal (anak ditumpuk ke bawah).
      pb-3              : padding-bottom 0.75rem (beri ruang bawah).
      border-b          : garis bawah sebagai pemisah header & body.
      border-slate-200  : warna garis bawah.
    ------------------------------------------------ --}}
    <div class="flex flex-col pb-3 border-b border-slate-200">
      {{-- "Halo 👋" : sapaan sederhana --}}
      <div class="">Halo 👋</div>

      {{-- Nama user login:
         {{ Auth::user()->nama }} :
           - Auth::user() → helper Laravel untuk mengambil instance user yang sedang login.
           - ->nama        → ambil properti kolom "nama" dari model User.
         font-bold        : teks tebal.
         text-xl          : ukuran font ≈ 1.25rem.
      --}}
      <div class="font-bold text-xl">{{ Auth::user()->nama }}</div>
    </div>

    {{-- ====== BODY KARTU: Menu Navigasi ======
      py-3              : padding vertikal 0.75rem (atas & bawah).
      flex flex-col     : tata letak vertikal.
      gap-6             : jarak antar item 1.5rem → longgar & rapi.
    ------------------------------------------------ --}}
    <div class="py-3 flex flex-col gap-6">

      {{-- ===================== MENU: Informasi Akun =====================
        <a href="/profile"> : tautan ke halaman profil. (Saran: gunakan route() agar tidak hardcode)
        class="flex gap-2 items-center {{ ... ? 'font-semibold' : '' }}"
          - flex            : susun ikon & teks secara horizontal.
          - gap-2           : jarak 0.5rem antara ikon & teks.
          - items-center    : vertical-align tengah untuk ikon & teks.
          - {{ ternary }}   : Blade ternary untuk class dinamis.
              Str::contains(Route::currentRouteName(), 'profile')
                • Route::currentRouteName() → nama rute aktif saat ini (string).
                • Str::contains(haystack, 'needle') → cek apakah nama rute mengandung kata 'profile'.
                  - Str adalah helper string Laravel (Illuminate\Support\Str).
                • Jika TRUE → tambahkan kelas 'font-semibold' (menebalkan sebagai indikator menu aktif).
                • Jika FALSE → tidak menambahkan apapun (string kosong).
      ----------------------------------------------------------------- --}}
      <a
        href="/profile"
        class="flex gap-2 items-center {{ Str::contains(Route::currentRouteName(), 'profile') ? 'font-semibold' : '' }}"
      >
        {{-- Ikon profil (Phosphor Icons). Pastikan CSS ikon dimuat di layout. --}}
        <i class="ph ph-user"></i>
        <div>Informasi Akun</div>
      </a>

      {{-- ===================== MENU: Pesananku =====================
        Mirip dengan di atas, namun mengecek kata 'order' pada nama rute aktif.
        Saran: gunakan route('orders.index') atau rute bernama lain untuk robust (tidak hardcode '/order').
      ---------------------------------------------------------------- --}}
      <a
        href="/order"
        class="flex gap-2 items-center {{ Str::contains(Route::currentRouteName(), 'order') ? 'font-semibold' : '' }}"
      >
        <i class="ph ph-shopping-cart-simple"></i>
        <div>Pesananku</div>
      </a>

      {{-- ===================== FORM: Logout =====================
        <form action="{{ route('logout') }}" method="POST">
          - route('logout') : gunakan rute resmi logout Laravel (biasanya disediakan oleh Auth scaffolding).
          - method="POST"   : logout sebaiknya via POST untuk keamanan (mencegah CSRF pada GET).
        @csrf :
          - Wajib. Menyertakan token CSRF agar permintaan POST diterima Laravel.
        @method('POST') :
          - Tidak diperlukan karena method HTML sudah POST. (Spoofing @method biasa dipakai untuk PUT/PATCH/DELETE.)
      ---------------------------------------------------------------- --}}
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        @method('POST') {{-- NOTE: ini redundant; boleh dihapus. method form sudah POST. --}}

        {{-- Tombol logout:
           class="flex gap-2 items-center"
             - flex         : ikon & teks horizontal.
             - gap-2        : jarak 0.5rem.
             - items-center : rata tengah vertikal.
        --}}
        <button class="flex gap-2 items-center">
          <i class="ph ph-sign-out"></i>
          <div>Logout</div>
        </button>
      </form>

    </div>
  </div>
</div>

