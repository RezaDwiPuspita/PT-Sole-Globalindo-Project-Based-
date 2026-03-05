{{-- ======================================================================================
  FILE   : resources/views/components/status-pill.blade.php (contoh)
  TUJUAN : Menampilkan "pill/badge" status dengan warna berbeda sesuai nilai $status.
  TEKNO  : Blade @switch/@case/@break/@default, Tailwind (px/py/rounded/bg/text).
  CATAT  : Komentar super-rinci tepat di baris terkait (tag, class, Blade directive).
====================================================================================== --}}

{{-- 
  <div class="text-center">
    - <div>           : elemen blok pembungkus.
    - text-center     : utilitas Tailwind → text-align: center; (semua teks di dalamnya rata tengah).
--}}
<div class="text-center">

    {{-- 
      @switch($status)
      - Blade directive untuk membuat "percabangan" multi-cabang (serupa switch-case di PHP).
      - $status        : variabel yang dikirim dari parent view/komponen berisi string status pesanan,
                         contoh: 'Menunggu konfirmasi', 'Belum Bayar', 'Diproses', dll.
      Alur kerja:
        * @switch memeriksa nilai $status sekali,
        * kemudian mencocokkannya dengan @case di bawah.
        * Jika cocok, blok @case tersebut dijalankan sampai @break.
        * Jika tidak ada yang cocok, @default (jika ada) dieksekusi.
    --}}
    @switch($status)

        {{-- ===================== CASE: "Menunggu konfirmasi" =====================
           @case('Menunggu konfirmasi') : blok ini dipilih bila $status persis 'Menunggu konfirmasi'
           <div class="px-2 py-1 rounded-lg bg-yellow-100 text-yellow-600">{{ $status }}</div>
             - px-2            : padding-left & padding-right 0.5rem (8px) → ruang horizontal.
             - py-1            : padding-top & padding-bottom 0.25rem (4px) → ruang vertikal.
             - rounded-lg      : sudut membulat agak besar → bentuk "pill" yang lembut.
             - bg-yellow-100   : background kuning sangat muda (indikasi status warning).
             - text-yellow-600 : warna teks kuning lebih tua agar kontras.
             - {{ $status }}   : menampilkan teks status yang sama persis. 
        ---------------------------------------------------------------------- --}}
        @case('Menunggu konfirmasi')
            <div class="px-2 py-1 rounded-lg bg-yellow-100 text-yellow-600">{{ $status }}</div>
        @break {{-- @break : menghentikan eksekusi switch setelah case ini dieksekusi --}}

        {{-- ===================== CASE: "Belum Bayar" =====================
           - Warna oranye: sering dipakai untuk "pending payment".
        ---------------------------------------------------------------- --}}
        @case('Belum Bayar')
            <div class="px-2 py-1 rounded-lg bg-orange-100 text-orange-600">{{ $status }}</div>
        @break

        {{-- ===================== CASE: "Diproses" =====================
           - Biru: progres/aktifitas sedang berjalan.
        ------------------------------------------------------------- --}}
        @case('Diproses')
            <div class="px-2 py-1 rounded-lg bg-blue-100 text-blue-600">{{ $status }}</div>
        @break

        {{-- ===================== CASE: "Dikirim" =====================
           - Indigo: berbeda dari biru, memberi identitas status pengiriman.
        ------------------------------------------------------------- --}}
        @case('Dikirim')
            <div class="px-2 py-1 rounded-lg bg-indigo-100 text-indigo-600">{{ $status }}</div>
        @break

        {{-- ===================== CASE: "Ditolak" =====================
           - Merah: menandakan error/penolakan/cancel.
        ----------------------------------------------------------- --}}
        @case('Ditolak')
            <div class="px-2 py-1 rounded-lg bg-red-100 text-red-600">{{ $status }}</div>
        @break

        {{-- ===================== CASE BERTUMPUK: "Selesai" / "Pesanan diterima" =====================
           - Dua @case berturut-turut → keduanya jatuh ke blok tampilan yang sama (hijau = sukses).
           - Pola ini setara dengan "OR": jika $status 'Selesai' ATAU 'Pesanan diterima', pakai gaya hijau.
        ------------------------------------------------------------------------------------------------ --}}
        @case('Selesai')
        @case('Pesanan diterima')
            <div class="px-2 py-1 rounded-lg bg-green-100 text-green-600">{{ $status }}</div>
        @break

        {{-- ===================== DEFAULT =====================
           @default : jika tidak ada @case yang cocok, tidak menampilkan apapun (kosong).
           Catatan:
             - Anda bisa menambahkan tampilan default, misalnya badge abu-abu:
               <div class="px-2 py-1 rounded-lg bg-slate-100 text-slate-600">{{ $status }}</div>
             - Di sini dibiarkan kosong sesuai kode asli.
        ----------------------------------------------------- --}}
        @default
    @endswitch
</div>
