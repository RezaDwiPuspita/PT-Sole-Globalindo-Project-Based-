{{-- ======================================================================================
  FILE    : (potongan header user)
  TUJUAN  : Menampilkan header sederhana dengan nama & role user yang login.
  TEKNO   : Blade (Laravel), Auth facade, Tailwind CSS utilities, Blade switch directive.
  CATATAN : Komentar menjelaskan TAG/ATRIBUT/UTILITAS Tailwind dan directive Blade satu-per-satu.
====================================================================================== --}}

{{-- ===================== AMBIL USER YANG SEDANG LOGIN =====================
  @php $u = Auth::user(); @endphp
  - @php ... @endphp : Menjalankan kode PHP di dalam Blade.
  - Auth::user()     : Mengambil instance model User yang sedang autentikasi (guard default).
  - $u               : Variabel pendek untuk memudahkan akses (alias user).
  *CATATAN*: Jika halaman bisa diakses tanpa login, sebaiknya cek null:
             $u = Auth::user(); if (!$u) { ... tampilkan guest ... }
------------------------------------------------------------------------ --}}
@php $u = Auth::user(); @endphp

{{-- ===================== HEADER UTAMA =====================
  Tag   : <header>
  class :
    bg-white            → latar belakang putih (light mode).
    px-10               → padding kiri-kanan 2.5rem (40px).
    py-4                → padding atas-bawah 1rem (16px).
    flex                → aktifkan Flexbox supaya anak tersusun horizontal.
    items-center        → vertikal align tengah untuk anak Flex.
    justify-between     → spasi antara kiri (logo/kosong) & kanan (profil) dibuat berjauhan.
    border-b            → garis border di sisi bawah.
    border-slate-200    → warna border abu-slate muda.
--------------------------------------------------------- --}}
<header class="bg-white px-10 py-4 flex items-center justify-between border-b border-slate-200">

  {{-- ===================== KOLOM KIRI HEADER =====================
    Tag   : <div>
    Isi   : Kosong (placeholder). Umum dipakai untuk logo / breadcrumb / tombol.
    *Kenapa ada?* Agar "justify-between" bekerja: kiri kosong, kanan profil.
  ------------------------------------------------------------- --}}
  <div></div>

  {{-- ===================== KOLOM KANAN HEADER (PROFIL) =====================
    Tag   : <div>
    class :
      flex            → susun avatar + teks profil secara horizontal.
      items-center    → vertikal rata tengah.
      gap-3           → jarak antar anak 0.75rem (12px).
    Isi   : Avatar bulat + blok teks (nama & role).
  ------------------------------------------------------------- --}}
  <div class="flex items-center gap-3">

    {{-- ===================== AVATAR PLACEHOLDER =====================
      Tag   : <div>
      class :
        rounded-full   → membuat bentuk bulat sempurna.
        h-10 w-10      → tinggi & lebar 2.5rem (40px).
        bg-slate-300   → warna latar abu (placeholder).
      *Catatan*: Bisa diganti <img src="..." alt="Nama User" class="h-10 w-10 rounded-full"> untuk foto asli.
    -------------------------------------------------------------- --}}
    <div class="rounded-full h-10 w-10 bg-slate-300"></div>

    {{-- ===================== BLOK TEKS PROFIL =====================
      Tag   : <div>
      class : leading-tight → line-height lebih rapat (tight) agar nama & role saling dekat.
    ------------------------------------------------------------- --}}
    <div class="leading-tight">

      {{-- ===================== BARIS NAMA USER =====================
        Tag   : <div>
        class : font-bold → teks tebal (bold) untuk menonjolkan nama.
        Isi   : Ditentukan dengan Blade @switch berdasarkan role:
                - owner → hardcode "Agus Setiawan"
                - admin → hardcode "Dyah Sri Rahayu"
                - default → nama asli dari database: {{ $u->name }}
        *Catatan penting*:
          - @switch/@case/@break/@default/@endswitch → directive kontrol alur di Blade (mirip switch-case PHP).
          - Hardcode nama berdasarkan role hanya cocok untuk demo. Jika ada banyak user owner/admin,
            sebaiknya tetap tampilkan $u->name agar dinamis.
      ----------------------------------------------------------- --}}
      <div class="font-bold">
        @switch($u->role) {{-- mulai switch: periksa nilai $u->role --}}
          @case('owner')  {{-- jika role "owner" --}}
            Agus Setiawan {{-- tampilkan nama statis ini --}}
            @break        {{-- hentikan eksekusi switch di sini --}}
          @case('admin')  {{-- jika role "admin" --}}
            Dyah Sri Rahayu {{-- tampilkan nama statis ini --}}
            @break        {{-- hentikan eksekusi switch di sini --}}
          @default        {{-- fallback untuk role lain (misal "customer", "staff", dll) --}}
            {{ $u->name }} {{-- tampilkan nama aktual dari user yang login --}}
        @endswitch         {{-- akhiri switch --}}
      </div>

      {{-- ===================== BARIS ROLE USER =====================
        Tag   : <div>
        class :
          text-sm        → ukuran teks kecil, agar hierarki visual: nama > role.
          text-gray-500  → warna abu untuk kesan sekunder.
          capitalize     → huruf pertama setiap kata jadi kapital (contoh: "admin" → "Admin").
        Isi   : {{ $u->role }} → string peran user dari database.
        *Catatan*: Pastikan $u->role berisi nilai yang diharapkan (ex: 'owner', 'admin', 'customer').
      ------------------------------------------------------------ --}}
      <div class="text-sm text-gray-500 capitalize">{{ $u->role }}</div>
    </div>
  </div>
</header>
