<!doctype html>
{{-- ======================================================================================
  FILE   : resources/views/layouts/auth.blade.php (contoh nama)
  TUJUAN : Layout sederhana untuk halaman auth (login/register/forgot) atau halaman polos.
  TEKNO  : Blade (Laravel), Tailwind via Vite, Toastify, Boxicons, Phosphor Icons.
  CATAT  : Komentar super-rinci per baris/atribut/tag/direktif.
====================================================================================== --}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  {{-- 
    <html lang="...">
    - Tag root dokumen HTML.
    - lang="{{ str_replace('_','-', app()->getLocale()) }}":
        * app()->getLocale() mengembalikan locale aplikasi (mis. "id" atau "en_US").
        * HTML BCP47 lebih lazim memakai tanda minus (en-US), jadi kita ganti underscore -> minus.
        * Membantu pembaca layar & mesin pencari memahami bahasa halaman. 
  --}}

<head>
    {{-- ===================== META DASAR ===================== --}}
    <meta charset="utf-8">
    {{-- charset="utf-8": set encoding karakter universal agar huruf Latin/non-Latin tampil benar. --}}

    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- viewport:
        - width=device-width : lebar viewport mengikuti lebar device → responsif.
        - initial-scale=1    : skala awal 100%, tidak di-zoom secara default. --}}

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- 
      META CSRF:
      - Laravel menyertakan token CSRF pada setiap form POST/PUT/PATCH/DELETE.
      - Beberapa script (mis. AJAX) membaca token ini dari <meta> untuk disertakan di header (X-CSRF-TOKEN).
      - csrf_token() menghasilkan string unik per session.
      - Keamanan: mencegah Cross-Site Request Forgery. 
    --}}

    <title>Sole Globalindo | Admin Website</title>
    {{-- <title> : judul tab browser. Bisa juga dibuat dinamis via @yield('title') jika perlu. --}}

    {{-- ===================== ASSETS: ICONS & FONTS ===================== --}}
    <link rel="shortcut icon" href="assets/logo-sole.png" type="image/x-icon">
    {{-- favicon: ikon kecil pada tab/bookmark. --}}

    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    {{-- Boxicons: set ikon berbasis font (pakai <i class="bx bx-..."></i>). --}}

    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    {{-- Webfont "Figtree" (400 normal, 600 semibold) dari Bunny Fonts. display=swap: fallback font dipakai sementara. --}}

    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    {{-- Phosphor Icons (web build): gunakan <i class="ph ph-..."></i> untuk ikon ringan, beragam gaya. --}}

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    {{-- Toastify CSS: gaya untuk toast notification (notifikasi ringan mengambang). --}}

    {{-- ===================== TAILWIND VIA VITE ===================== --}}
    @vite('resources/css/app.css')
    {{-- 
      @vite: directive Laravel untuk memuat berkas yang dibundel oleh Vite (dev server/build).
      - 'resources/css/app.css' biasanya memuat Tailwind + CSS kustom Anda.
      - Pastikan konfigurasi Vite dan Tailwind sudah benar.
    --}}
</head>

<body>
    {{-- 
      <div id="app">:
      - Konvensi umum proyek Laravel + Vue/Alpine/Inertia: node root untuk mengaitkan JS SPA/komponen.
      - Meski di sini belum ada JS mount, id ini aman dibiarkan sebagai kontainer utama.
    --}}
    <div id="app">
        {{-- ===================== AREA KONTEN DINAMIS ===================== --}}
        <main class="">
            {{-- 
              @yield('content'):
              - Slot yang akan diisi oleh child view (mis. halaman login/register).
              - Class "" (kosong) → Anda bisa tambahkan utilitas Tailwind jika butuh spacing/latar.
            --}}
            @yield('content')
        </main>

        {{-- ===================== JS LIBRARIES FOOTER ===================== --}}
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
        {{-- Toastify JS: API window.Toastify({ ... }).showToast() untuk menampilkan notifikasi. --}}

        @stack('scripts')
        {{-- 
          @stack('scripts'):
          - Tempat file/kode JS tambahan dari child view "dititipkan" via @push('scripts').
          - Memastikan script spesifik-halaman di-load setelah dependensi utama & DOM siap.
        --}}

        {{-- ===================== FLASH: SUCCESS ===================== --}}
        @if (session('success'))
            {{-- 
              session('success'):
              - Diset oleh controller setelah aksi (create/update/delete) berhasil.
              - Jika ada, tampilkan toast hijau di kanan atas selama 3 detik.
            --}}
            <script>
                Toastify({
                    text: "{{ session('success') }}",   // teks notifikasi dari server
                    duration: 3000,                     // 3 detik
                    newWindow: true,                    // link di toast (jika ada) buka tab baru
                    close: true,                        // tampilkan tombol X untuk menutup
                    gravity: "top",                     // posisi vertikal (top/bottom)
                    position: "right",                  // posisi horizontal (left/center/right)
                    stopOnFocus: true,                  // pause saat fokus hover
                    style: {
                        background: "#96c93d",          // hijau (sukses)
                    },
                }).showToast();
            </script>
        @endif

        {{-- ===================== FLASH: ERROR (VALIDATION) ===================== --}}
        @if ($errors->any())
            {{-- 
              $errors->any(): true jika ada error validasi dari Laravel Validator.
              $errors->all(): array semua pesan error (string).
              Kita loop & tampilkan tiap error sebagai toast merah.
            --}}
            <script>
                @foreach ($errors->all() as $error)
                    Toastify({
                        text: "{{ $error }}",           // satu pesan error
                        duration: 3000,
                        newWindow: true,
                        close: true,
                        gravity: "top",
                        position: "right",
                        stopOnFocus: true,
                        style: {
                            background: "#e74c3c",      // merah (error)
                        },
                    }).showToast();
                @endforeach
            </script>
        @endif
    </div>
</body>

</html>
