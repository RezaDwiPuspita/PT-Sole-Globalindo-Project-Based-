<!DOCTYPE html>
{{-- ======================================================================================
  FILE   : resources/views/layouts/admin.blade.php (contoh nama)
  TUJUAN : Layout utama Admin (kerangka HTML: <head>, sidebar, header, @yield content, scripts).
  TEKNO  : Blade (Laravel), TailwindCSS, Flowbite, DataTables, Toastify, SweetAlert2, Vite.
  CATAT  : Komentar super-rinci pada baris terkait: tag/atribut/utility/Blade directive/JS.
====================================================================================== --}}

@php
    // ===================== KONFIG LOCALE CARBON =====================
    // \Carbon\Carbon::setLocale('id')
    //  - Mengatur locale Carbon ke bahasa Indonesia.
    //  - Dampak: metode format "terjemahan" (mis. translatedFormat()) menampilkan nama bulan/dll dalam bahasa Indonesia.
    \Carbon\Carbon::setLocale('id');
@endphp

{{-- 
  <html lang="{{ str_replace('_','-', app()->getLocale()) }}">
  - Elemen root HTML.
  - lang="..."     : atribut bahasa halaman. app()->getLocale() biasanya "id" / "en_US".
  - str_replace('_','-', ...) : HTML standar memakai dash (id-ID), bukan underscore (id_ID).
--}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    {{-- ===================== METADATA DASAR ===================== --}}
    <meta charset="utf-8"> {{-- Encoding karakter UTF-8 (wajib modern web). --}}
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- 
      viewport: lebar = lebar device; initial-scale=1 → skala awal 100%
      Penting untuk responsive design di mobile.
    --}}

    {{-- 
      <title>@yield('title', 'Sole Globalindo | Admin Website')</title>
      - @yield('title', 'default') : slot judul halaman; jika child tidak mengisi @section('title'),
        maka pakai default "Sole Globalindo | Admin Website".
    --}}
    <title>@yield('title', 'Sole Globalindo | Admin Website')</title>

    {{-- ===================== FAVICON & ICON FONTS ===================== --}}
    <link rel="shortcut icon" href="assets/logo-sole.png" type="image/x-icon">
    {{-- favicon: ikon kecil di tab browser. --}}

    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    {{-- Boxicons: set ikon berbasis font. Digunakan jika ada <i class="bx ..."> --}}

    {{-- ===================== FONTS ===================== --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    {{-- preconnect: optimisasi DNS/TLS ke host fonts agar loading lebih cepat. --}}
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    {{-- Memuat font "Figtree" weight 400/600 dari Bunny Fonts. --}}

    {{-- ===================== UI LIBS (CSS) ===================== --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    {{-- Flowbite: komponen berbasis Tailwind (modal, dropdown, dsb.). --}}

    @vite('resources/css/app.css')
    {{-- @vite : directive Laravel untuk memuat aset yang dibundel oleh Vite (Tailwind config, dll.).
       Pastikan Vite dikonfigurasi (vite.config.js) & dev/build telah dijalankan. --}}

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    {{-- Toastify CSS: untuk notifikasi toast ringan. --}}

    <link href="https://cdn.datatables.net/v/dt/dt-2.0.2/b-3.0.1/b-html5-3.0.1/b-print-3.0.1/datatables.min.css"
        rel="stylesheet">
    {{-- DataTables bundle CSS v2 + Buttons (HTML5 export/print). --}}

    {{-- Alternatif CSS DataTables (DISABLED): dibiarkan sebagai referensi.
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.2/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.1/css/buttons.dataTables.min.css.css"> 
    --}}

    {{-- ===================== UI LIBS (JS HEAD) ===================== --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- SweetAlert2: pop-up konfirmasi/alert modern. --}}

    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    {{-- Phosphor Icons (CSS/JS): <i class="ph ph-..."></i> untuk ikon outline. --}}

    {{-- ===================== OVERRIDE THEME (SIMPLE) ===================== --}}
    <style>
        * {
            /* prefers-color-scheme: 'light' !important
               Catatan: properti CSS yang benar adalah 'color-scheme' atau media query prefers-color-scheme.
               Baris ini "tidak standar". Jika tujuan memaksa mode terang, pertimbangkan:
               :root { color-scheme: light; } atau logika kelas "dark" di HTML. */
            prefers-color-scheme: 'light' !important
        }
    </style>
</head>

{{-- 
  BLOK REDIRECT (KOMENTAR)
  - Contoh logika auth/guard. Dikomentari agar tidak mengeksekusi redirect saat rendering layout.
  - Jika diperlukan, pindahkan ke Controller atau middleware agar lebih tepat.
--}}
{{-- 
@php
    if (Auth::check() && Auth::user()->is_admin && Auth::guard('company')->check()) {
        return redirect()->route('home');
    } else {
        return redirect()->route('home');
    }
@endphp 
--}}

{{-- 
  <body class="relative flex">
  - relative : posisi relatif (membuat child absolute sebagai referensi).
  - flex     : menjadikan body flex container (sidebar + konten utama sejajar).
--}}
<body class="relative min-h-screen flex flex-row">

    {{-- ===================== SIDEBAR =====================
       @include('components.sidebar')
       - Menyisipkan partial Blade (sidebar navigasi).
       - Komponen ini biasanya berisi <aside> dan list menu.
    --}}
     @include('components.sidebar')

    {{-- ===================== KONTEN UTAMA (KANAN) ===================== --}}
    <div class="flex-grow flex-1">
        {{-- 
          <div class="sticky top-0"> : membuat header menempel di atas saat scroll.
          - sticky    : posisi lengket mengikuti scroll.
          - top-0     : jarak dari atas 0.
        --}}
        <div class="sticky top-0">
            {{-- Header/topbar komponen terpisah --}}
            @include('components.header')
        </div>

        {{-- 
          <main class="py-4 px-6 bg-bg min-h-screen">
          - py-4 : padding vertical 1rem (16px).
          - px-6 : padding horizontal 1.5rem (24px).
          - bg-bg: kelas kustom (mungkin warna latar konten).
          - min-h-screen: tinggi minimum setara tinggi viewport (agar footer tidak floating).
        --}}
        <main class="py-4 px-6 bg-bg min-h-screen">

            {{-- Judul Halaman: diisi oleh child view melalui @section('page') --}}
            <h1 class="font-bold text-lg pb-4 border-b border-slate-200">@yield('page')</h1>

            <div class="mt-4">
                {{-- 
                  Flash message sukses dari session:
                  - session('success') : jika tersedia (biasanya setelah create/update/delete berhasil).
                  - Ditampilkan kotak hijau sederhana (redundan dengan Toastify, tapi baik untuk FOUC/no-JS).
                --}}
                @if (session('success'))
                    <div class="bg-green-200 mt-5 p-3 rounded-md col-span-12 mb-5 text-green-800">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Slot konten utama: child view akan mengisi @section('content') --}}
                @yield('content')
            </div>
        </main>
    </div>

    {{-- ===================== SCRIPT: TOGGLE SIDEBAR / LAYOUT =====================
       - Mengelola class "expanded" pada <nav> dan <main> untuk melebarkan/menyempitkan sidebar.
       - Menyimpan preferensi user di localStorage agar persist after reload.
    --}}
    <script>
        const links = document.querySelectorAll("ul li a");  // (opsional) referensi link menu
        const nav = document.querySelector("nav");           // asumsi: sidebar di-markup sebagai <nav>
        const main = document.querySelector("main");         // konten utama

        const menuIcon = document.querySelector("nav svg");  // tombol ikon untuk toggle sidebar

        // Ambil preferensi awal dari localStorage
        const isNavExpanded = localStorage.getItem("isNavExpanded") === "true";

        if (nav && main && isNavExpanded) {
            nav.classList.remove("expanded");
            main.classList.add("expanded");
        }

        if (menuIcon && nav && main) {
        // Klik ikon menu → toggle kelas & simpan state
            menuIcon.addEventListener("click", () => {
            nav.classList.toggle("expanded");
            main.classList.toggle("expanded");

            const isNavExpandedNow = !nav.classList.contains("expanded");
            localStorage.setItem("isNavExpanded", isNavExpandedNow.toString());
            });
        }
    </script>

    {{-- ===================== SCRIPT LIBRARIES (FOOTER) ===================== --}}
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    {{-- jQuery: diperlukan oleh beberapa plugin (DataTables v2 butuh jQuery kompat). --}}

    {{-- Alternatif DataTables JS (DISABLED) --}}
    {{-- <script src="https://cdn.datatables.net/2.0.2/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.1/js/buttons.print.min.js"></script> --}}
    {{-- <script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwindcss.min.js"></script> --}}

    {{-- Tailwind CSS sudah dimuat via @vite('resources/css/app.css') di bagian head --}}
    {{-- Tidak perlu CDN karena sudah dikonfigurasi dengan Vite dan PostCSS --}}

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    {{-- Toastify JS: menampilkan toast notifikasi. --}}

    <script src="https://cdn.datatables.net/v/dt/dt-2.0.2/b-3.0.1/b-html5-3.0.1/b-print-3.0.1/datatables.min.js"></script>
    {{-- DataTables bundle JS v2 + Buttons. --}}

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    {{-- JSZip: dependency export Excel (DataTables Buttons HTML5). --}}

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    {{-- pdfmake + vfs_fonts: dependency export PDF (DataTables Buttons). --}}

    <script src="https://cdn.datatables.net/buttons/3.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.1/js/buttons.colVis.min.js"></script>
    {{-- Buttons HTML5 (CSV/Excel/PDF) & Column visibility toggle. --}}

    {{-- ===================== SWEETALERT: KONFIRMASI HAPUS (GLOBAL) ===================== --}}
    <script>
        // Buat instance Swal khusus dengan class tombol custom (menggunakan kelas utility proyek).
        const customSwal = Swal.mixin({
            customClass: {
                confirmButton: "btn !bg-primary-700 text-white ml-3",
                cancelButton: "btn border !border-primary-700 text-primary-700"
            },
            buttonsStyling: false,  // kita styling manual via class di atas
            reverseButtons: true,   // tukar posisi (Cancel kiri, OK kanan) → UX lokal sering prefer ini
        });

        // Handler global untuk tombol dengan class .delete-btn
        $(".delete-btn").on("click", function(e) {
            const btn = $(this); // simpan referensi tombol (agar bisa .parent().submit())
            e.preventDefault();  // cegah submit langsung

            customSwal.fire({
                title: "Apakah Anda Yakin?",
                text: "Perubahan yang terjadi setelah ini tidak bisa dikembalikan",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, yakin!",
                cancelButtonText: "Tidak, kembali!",
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit form induk dari tombol delete (asumsi tombol ada di dalam <form>)
                    btn.parent().submit()
                }
            })
        })
    </script>

    {{-- ===================== STACK SCRIPTS DARI CHILD VIEW =====================
       @stack('scripts') : tempat child view "menitipkan" JS via @push('scripts') agar dimuat di akhir.
    --}}
    @stack('scripts')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    {{-- Flowbite JS: mengaktifkan komponen Flowbite (modal, dropdown, collapse). --}}

    {{-- ===================== FUNGSI KONFIRMASI HAPUS (VARIAN LAIN) =====================
       - Fungsi ini mencari form berdasarkan data-item-id dan menampilkan SweetAlert.
       - Catatan: Anda sudah punya handler .delete-btn di atas. Pastikan tidak duplikatif.
    --}}
    <script>
        function confirmDelete() {
            const itemId = event.target.getAttribute('data-item-id'); // ambil id dari atribut tombol
            const form = document.querySelector(`.delete-form[data-item-id="${itemId}"]`); // form target

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Ketika sudah dihapus, item di sini tidak bisa dikembalikan',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus barang!',
                cancelButtonText: 'Tidak, batalkan',
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>

    {{-- ===================== TOASTIFY: FLASH MESSAGE (SUCCESS/ERROR) =====================
       - Menampilkan notifikasi toast jika ada session('success') atau session('error').
       - 'gravity' top + 'position' right: toast di kanan atas.
       - style.background: warna brand untuk sukses / merah untuk error.
    --}}
    @if (session('success'))
        <script>
            Toastify({
                text: "{{ session('success') }}",
                duration: 3000,
                newWindow: true,
                close: true,
                gravity: "top",
                position: "right",
                stopOnFocus: true,
                style: {
                    background: "#96c93d", // hijau
                },
            }).showToast();
        </script>
    @endif

    @if (session('error'))
        <script>
            Toastify({
                text: "{{ session('error') }}",
                duration: 3000,
                newWindow: true,
                close: true,
                gravity: "top",
                position: "right",
                stopOnFocus: true,
                style: {
                    background: "#e74c3c", // merah
                },
            }).showToast();
        </script>
    @endif

    @if ($errors->any())
        {{-- Loop semua pesan error validasi & tampilkan masing-masing dalam toast merah --}}
        <script>
            @foreach ($errors->all() as $error)
                Toastify({
                    text: "{{ $error }}",
                    duration: 3000,
                    newWindow: true,
                    close: true,
                    gravity: "top",
                    position: "right",
                    stopOnFocus: true,
                    style: {
                        background: "#e74c3c",
                    },
                }).showToast();
            @endforeach
        </script>
    @endif
</body>

</html>
