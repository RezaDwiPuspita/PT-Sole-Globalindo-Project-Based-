<!-- 
  HEADER UTAMA
  Tag     : <header>
  class   : 
    sticky         → posisi "lengket": elemen akan menempel di atas viewport saat di-scroll.
    top-0          → posisi menempel tepat di atas (0px dari atas).
    bg-white       → background putih (untuk tema terang).
    dark:bg-[#182235] → saat dark mode aktif, pakai warna latar hex #182235.
    border-b       → garis border di sisi bawah (bottom border).
    border-slate-200      → warna border abu-slate terang untuk light mode.
    dark:border-slate-700 → warna border lebih gelap saat dark mode.
    z-30           → z-index 30, supaya header berada di atas konten lain (mis. sidebar, body).
-->
<header class="sticky top-0 bg-white dark:bg-[#182235] border-b border-slate-200 dark:border-slate-700 z-30">

  <!-- 
    WRAPPER PADDING HORIZONTAL RESPONSIF
    class:
      px-4   → padding kiri-kanan 1rem (16px) di semua ukuran.
      sm:px-6 → saat ≥ sm (640px), padding kiri-kanan 1.5rem (24px).
      lg:px-8 → saat ≥ lg (1024px), padding kiri-kanan 2rem (32px).
    Tujuan : memberi ruang kiri/kanan yang proporsional di berbagai lebar layar.
  -->
  <div class="px-4 sm:px-6 lg:px-8">

    <!-- 
      BARIS FLEX KONTEN HEADER
      class:
        flex                      → aktifkan Flexbox untuk susun horizontal.
        items-center              → semua anak diratakan tegak Tengah (cross-axis center).
        justify-between           → elemen pertama di sisi kiri, elemen kedua di sisi kanan.
        h-16                      → tinggi tetap 4rem (64px) untuk header.
        -mb-px                    → margin-bottom negatif 1px, menyatu visual dengan border bawah (rapikan 1px gap).
    -->
    <div class="flex items-center justify-between h-16 -mb-px">

      <!-- KIRI: Area untuk tombol hamburger (pembuka sidebar di layar kecil) -->
      <div class="flex">
        <!-- 
          TOMBOL HAMBURGER (muncul di mobile)
          Tag     : <button>
          class   :
            text-slate-500        → warna ikon/teks abu-slate menengah.
            hover:text-slate-600  → sedikit lebih gelap saat hover.
            lg:hidden             → disembunyikan saat layar ≥ lg (1024px), karena di desktop sidebar biasanya sudah terlihat.
          
          Alpine.js:
            @click.stop="sidebarOpen = !sidebarOpen"
              - @click            → directive Alpine untuk event click.
              - .stop             → modifier untuk menghentikan event bubbling (mencegah propagasi ke parent).
              - "sidebarOpen = !sidebarOpen" → toggle boolean reactive state 'sidebarOpen'.
            aria-controls="sidebar"
              - Atribut ARIA: mengindikasikan tombol ini mengontrol elemen dengan id "sidebar".
            :aria-expanded="sidebarOpen"
              - Binding Alpine (":") untuk atribut aria-expanded → true/false mengikuti state 'sidebarOpen'.
              - Membantu aksesibilitas: screen reader tahu apakah sidebar sedang terbuka.
        -->
        <button
          class="text-slate-500 hover:text-slate-600 lg:hidden"
          @click.stop="sidebarOpen = !sidebarOpen"
          aria-controls="sidebar"
          :aria-expanded="sidebarOpen"
        >
          <!-- sr-only: teks untuk screen reader, tidak terlihat secara visual -->
          <span class="sr-only">Open sidebar</span>

          <!-- 
            IKON "HAMBURGER" → 3 garis horizontal
            SVG:
              class="w-6 h-6 fill-current" → ukuran 24x24, isi (fill) mengikuti currentColor (yaitu color dari teks).
              viewBox="0 0 24 24"          → area gambar 24x24.
            <rect> → kotak; kita gambar 3 buah bar setebal 2px pada y=5, 11, 17.
          -->
          <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <rect x="4" y="5"  width="16" height="2" />
            <rect x="4" y="11" width="16" height="2" />
            <rect x="4" y="17" width="16" height="2" />
          </svg>
        </button>
      </div>

      <!-- KANAN: Kumpulan kontrol (tema, notifikasi, profil, dsb.) -->
      <!-- 
        class:
          flex             → susun horizontal.
          items-center     → vertical middle align.
          space-x-3        → jarak horisontal antar item 0.75rem (12px).
      -->
      <div class="flex items-center space-x-3">

        <!-- 
          (Opsional) TOMBOL SEARCH / MODAL SEARCH
          Komponen Blade: <x-modal-search />
          → Dikomentari (tidak aktif). Jika diaktifkan, biasanya membuka modal pencarian.
        -->
        {{-- <x-modal-search /> --}}

        <!-- 
          (Opsional) NOTIFIKASI
          Komponen Blade: <x-dropdown-notifications align="right" />
          → Dikomentari. Jika diaktifkan, menampilkan dropdown notifikasi, align ke kanan.
        -->
        {{-- <x-dropdown-notifications align="right" /> --}}

        <!-- 
          (Opsional) HELP / INFO
          Komponen Blade: <x-dropdown-help align="right" />
          → Dikomentari. Jika diaktifkan, menampilkan dropdown bantuan, align ke kanan.
        -->
        {{-- <x-dropdown-help align="right" /> --}}

        <!-- 
          TOGGLE DARK MODE
          Komponen Blade: <x-theme-toggle />
          - Biasanya mengubah class 'dark' di <html> atau <body> memakai Alpine/JS.
          - Tailwind akan men-trigger varian 'dark:' (mis. dark:bg-[#182235]) saat mode gelap aktif.
        -->
        <x-theme-toggle />

        <!-- PEMBATAS VERTIKAL -->
        <!-- 
          <hr>        → garis lurus pembatas.
          class:
            w-px      → lebar 1px (jadi membuat garis tipis vertikal).
            h-6       → tinggi 1.5rem (24px).
            bg-slate-200          → warna garis untuk light mode.
            dark:bg-slate-700     → warna garis saat dark mode.
            border-none           → buang border default hr agar tampilannya murni pakai background.
        -->
        <hr class="w-px h-6 bg-slate-200 dark:bg-slate-700 border-none" />

        <!-- 
          DROPDOWN PROFIL PENGGUNA
          Komponen Blade: <x-dropdown-profile align="right" />
          - Biasanya menampilkan avatar/nama user & menu: Profile, Settings, Logout.
          - align="right" → dropdown membuka ke kanan atau posisi di-justify ke kanan.
        -->
        <x-dropdown-profile align="right" />

      </div> <!-- /Right controls -->

    </div> <!-- /flex baris header -->
  </div> <!-- /padding wrapper -->
</header>
