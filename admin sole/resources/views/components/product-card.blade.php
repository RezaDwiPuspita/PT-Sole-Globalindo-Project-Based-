{{-- ======================================================================================
  KOMPONEN : Kartu Produk (link ke halaman detail)
  TUJUAN   : Menampilkan gambar, kategori, nama, dan harga produk.
  TEKNO    : Blade (Laravel), Tailwind CSS, helper asset(), number_format(), relasi Eloquent.
  CATATAN  : Komentar super-rinci di baris terkait (tag, atribut, utilitas Tailwind, Blade/ PHP).
====================================================================================== --}}

{{-- <a> = anchor/tautan yang membungkus seluruh kartu → seluruh kartu menjadi area klik --}}
{{-- href="/shop/{{ $product->id }}" :
     - Hardcode path "/shop/{id}" menuju halaman detail produk; {{ $product->id }} = id dari model Product.
   class="item-cardoverflow-hidden" :
     - Kemungkinan TYPO: seharusnya ada SPASI → "item-card overflow-hidden".
     - item-card       : kelas kustom (opsional, berasal dari CSS proyek).
     - overflow-hidden : konten yang meluber dari kartu akan terpotong (mis. sudut gambar mengikuti radius kartu).
   REKOMENDASI:
     - tambahkan "block" agar <a> bertindak seperti blok penuh: class="item-card overflow-hidden block".
--}}
<a href="/shop/{{ $product->id }}" class="item-card overflow-hidden block">

  {{-- ==================== GAMBAR PRODUK ====================
     Tag <img> menampilkan gambar produk.
     class :
       rounded-sm          → sudut membulat kecil (small).
       bg-primary-100/10   → latar fallback warna primary sangat tipis (10% opacity) saat gambar belum termuat.
       h-64                → tinggi tetap 16rem (256px).
       w-full              → lebar penuh 100% dari parent (menjaga rasio kartu).
       object-cover        → gambar tetap menutup area, crop jika rasio berbeda (tanpa distorsi).
     src="..." :
       - Ternary dengan PHP (dalam Blade) untuk menentukan URL sumber gambar:
         strpos($product->gambar, 'http') === 0 ? $product->gambar : asset('storage/images/' . $product->gambar)
         Penjelasan fungsi:
           • strpos($string, 'http') === 0 → cek apakah $product->gambar diawali "http" (URL absolut, mis. CDN).
             - strpos mengembalikan posisi substring; "=== 0" berarti tepat di awal string.
           • Jika YA → gunakan $product->gambar langsung (URL eksternal).
           • Jika TIDAK → gunakan asset('storage/images/' . $product->gambar)
             - asset()  → helper Laravel untuk membentuk URL publik (berbasis APP_URL) ke file di public path.
             - 'storage/images/...' → asumsi file sudah di-serve via "php artisan storage:link".
     alt="" :
       - Teks alternatif untuk aksesibilitas/SEO. Sebaiknya isi deskriptif, contoh: alt="{{ $product->nama }}".
     OPSI TAMBAHAN:
       - Tambahkan loading="lazy" untuk penundaan muat gambar (perf).
  --}}
  <img
    class="rounded-sm bg-primary-100/10 h-64 w-full object-cover"
    src="{{ strpos($product->gambar, 'http') === 0 ? $product->gambar : asset('storage/images/' . $product->gambar) }}"
    alt="{{ $product->nama }}"
    loading="lazy"
  >

  {{-- ==================== BLOK TEKS (DETAIL RINGKAS) ====================
     class="flex flex-col" :
       - flex       → aktifkan Flexbox pada kontainer teks.
       - flex-col   → arah sumbu utama vertikal (anak ditumpuk ke bawah).
  --}}
  <div class="flex flex-col">

    {{-- KATEGORI PRODUK
       <h3> → semantik heading (penting untuk struktur dokumen & SEO; di dalam kartu tidak wajib H3, tapi ok).
       class="text-sm mt-2 font-bold" :
         - text-sm   → ukuran teks kecil.
         - mt-2      → margin-top 0.5rem (supaya ada jarak dari gambar).
         - font-bold → ketebalan font tebal.
       {{ $product->category->nama }} :
         - Akses relasi Eloquent "category" dari model Product (pastikan relasi didefinisikan di model Product).
         - Ambil kolom "nama" pada model Category terkait.
    --}}
    <h3 class="text-sm mt-2 font-bold">{{ $product->category->nama }}</h3>

    {{-- NAMA PRODUK
       class="" dikosongkan → mewarisi default; bisa tambahkan "truncate" bila ingin potong teks panjang:
       contoh: class="truncate" untuk memotong nama terlalu panjang menjadi satu baris dengan ellipsis.
       {{ $product->nama }} menampilkan nama/judul produk.
    --}}
    <div class="">{{ $product->nama }}</div>

    {{-- HARGA PRODUK (FORMAT RUPIAH)
       class="text-sm" → ukuran kecil agar hierarki visual jelas (kategori & nama lebih menonjol).
       Isi: "Rp{{ number_format($product->harga, 0, ',', '.') }}"
         - number_format(nilai, 0, ',', '.') :
             • 0          → tanpa desimal.
             • ','        → separator desimal (Indonesia).
             • '.'        → separator ribuan (Indonesia).
         - Contoh output: Rp1.250.000
       CATATAN:
         - Jika harga bertipe integer (satuan rupiah), format ini sudah sesuai.
         - Jika bertipe float, pastikan pembulatan sesuai kebijakan (mis. round).
    --}}
    <div class="text-sm">Rp{{ number_format($product->harga, 0, ',', '.') }}</div>

  </div>
</a>
