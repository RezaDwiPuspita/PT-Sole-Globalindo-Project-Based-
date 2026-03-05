{{-- ======================================================================================
  FILE   : resources/views/auth/login.blade.php (contoh nama)
  TUJUAN : Halaman login admin — form email + password, submit ke route('login')
  TEKNO  : Blade (Laravel), TailwindCSS (utility), kelas kustom: .btn, .btn-primary, .input-field
  CATAT  : Komentar super-rinci menjelaskan tag HTML, atribut, Tailwind utilities, dan Blade directives.
====================================================================================== --}}

@extends('layouts.app')
{{-- @extends :
    - Mengatakan view ini memakai layout "layouts.app".
    - Layout akan menyediakan kerangka dasar (head, body, slot konten, dll).
--}}

@section('content')
{{-- @section('content') :
    - Mengisi section "content" yang didefinisikan di layouts.app (biasanya @yield('content')).
--}}

    {{-- ===================== WRAPPER FULLSCREEN =====================
      Tag    : <div>
      class  : "h-screen w-full flex items-center justify-center container"
        - h-screen    : tinggi = 100vh (setinggi viewport) → memudahkan vertical centering.
        - w-full      : lebar penuh kontainer induk.
        - flex        : aktifkan Flexbox pada container ini.
        - items-center: vertical align center (sumbu silang).
        - justify-center: horizontal align center (sumbu utama).
        - container   : kelas kustom proyek untuk batas lebar + horizontal padding (opsional).
      Peran  : Memposisikan form tepat di tengah layar (tengah horizontal & vertikal).
    -------------------------------------------------------------- --}}
    <div class="h-screen w-full flex items-center justify-center container">

        {{-- ===================== FORM LOGIN =====================
          Tag     : <form>
          action  : route('login') → helper Blade untuk URL ke rute nama "login".
                     (Biasanya disediakan oleh Laravel Auth; menangani autentikasi POST.)
          method  : "POST" → form mengirim data sensitif (password) ⇒ WAJIB POST.
          class   : "max-w-[400px] flex flex-col gap-8 items-center"
                    - max-w-[400px] : batas lebar maksimal 400px (Tailwind arbitrary value).
                    - flex          : jadikan form flex container.
                    - flex-col      : susun anak-anak form secara vertikal (kolom).
                    - gap-8         : jarak antar elemen 2rem (32px).
                    - items-center  : rata tengah horizontal untuk elemen inner (mis. logo).
          Catatan : Name atribut di <input> penting agar request berisi pasangan kunci-nilai.
        ------------------------------------------------------- --}}
        <form action="{{ route('login') }}" method="POST" class="max-w-[400px] flex flex-col gap-8 items-center">

            @csrf
            {{-- @csrf :
                - Blade directive untuk menyisipkan token CSRF tersembunyi:
                  <input type="hidden" name="_token" value="...">
                - Wajib untuk melindungi dari serangan CSRF pada request POST/PUT/PATCH/DELETE.
            --}}

            {{-- ===================== LOGO =====================
              Tag  : <img>
              src  : "assets/logo-sole.png" → path relatif ke public/.
              alt  : "logo" → teks alternatif aksesibilitas (dibaca screen reader / fallback).
              Peran: Branding di bagian atas form.
            ------------------------------------------------ --}}
            <img src="assets/logo-sole.png" alt="logo">

            {{-- ===================== FIELD EMAIL =====================
              Wrapper <div> : "w-full" → elemen anak (label+input) mengambil lebar penuh form.
            --------------------------------------------------------- --}}
            <div class=" w-full">
                {{-- <label> :
                    - for="email"    : terhubung ke input#email → klik label fokus ke input.
                    - class="block text-sm font-medium text-gray-700"
                      * block        : label jadi block-level (baris penuh).
                      * text-sm      : ukuran font kecil.
                      * font-medium  : sedikit lebih tebal.
                      * text-gray-700: warna abu gelap, kontras dg latar putih.
                    Aksesibilitas: label penting untuk penamaan field bagi pembaca layar.
                --}}
                <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>

                {{-- <input> :
                    - class="input-field w-full"
                      * input-field : kelas kustom proyek (biasanya padding, border, radius).
                      * w-full      : lebar penuh kontainer (div).
                    - type="text"   : tipe teks bebas; bisa juga type="email" (akan memberi validasi HTML5).
                    - name="email"  : kunci yang diambil server (request('email')).
                    - id="email"    : id untuk mengaitkan label for="email".
                    - placeholder   : teks petunjuk sementara.
                    Catatan:
                      * Anda dapat mengganti type="email" agar browser validasi format email minimal.
                      * Pertimbangkan autocomplete="username" untuk UX yang lebih baik.
                --}}
                <input class="input-field w-full" type="text" name="email" id="email" placeholder="Email">
            </div>

            {{-- ===================== FIELD PASSWORD =====================
              Wrapper <div> : "w-full" → lebar penuh.
            ------------------------------------------------------------ --}}
            <div class=" w-full">
                {{-- <label> untuk password (atribut for seharusnya menunjuk ke id="password") --}}
                <label for="email" class="block text-sm font-medium text-gray-700">Password</label>
                {{-- NOTE: kecil bug → for="email" di label ini harusnya for="password" agar aksesibilitas benar. --}}

                {{-- <input> password :
                    - class="input-field w-full" : gaya konsisten dengan input email.
                    - type="password"            : menyembunyikan karakter saat diketik.
                    - name="password" / id="password"
                    - placeholder="Password"
                    Saran:
                      * Tambahkan autocomplete="current-password" untuk pengisian otomatis.
                      * Bisa tambahkan minlength atau rule di sisi server (Validator) untuk keamanan.
                --}}
                <input class="input-field w-full" type="password" name="password" id="password" placeholder="Password">
            </div>

            {{-- ===================== BUTTON SUBMIT =====================
              <button> :
                - class="btn btn-primary w-full"
                  * btn / btn-primary : kelas kustom proyek (warna utama, hover, radius).
                  * w-full            : tombol melebar penuh (mudah di-tap di mobile).
                - Isi: "Login" → teks aksi.
              Aksi :
                - Men-submit form ke action (route('login')) dengan POST & token CSRF.
            ---------------------------------------------------------- --}}
            <button class="btn btn-primary w-full">Login</button>
        </form>
    </div>
@endsection
