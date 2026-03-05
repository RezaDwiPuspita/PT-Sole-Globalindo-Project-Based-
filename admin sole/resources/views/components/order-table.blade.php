{{-- ======================================================================================
  FILE    : (potongan tabel order + modal detail)
  TUJUAN  : Menampilkan daftar transaksi beserta aksi & modal detail tiap baris.
  TEKNO   : Blade (Laravel), Tailwind CSS, (opsional) Flowbite/Alpine untuk modal (data-modal-*)
  CATATAN : Komentar menjelaskan TAG, ATRIBUT, UTILITAS Tailwind, Blade directive, dan catatan penting.
====================================================================================== --}}

<div>
    {{-- ===================== TABEL DAFTAR ORDER =====================
      Tag   : <table>
      id    : "order-table" → selector untuk JS (DataTables/skrip custom).
      class :
        order-table        → kelas kustom proyek (opsional).
        table-auto         → kolom me-lebar sesuai konten (Tailwind). (bandingkan: table-fixed).
        w-full             → lebar 100% kontainer.
        bg-white           → latar putih.
        !mt-4              → margin-top: 1rem; tanda "!" memaksa override jika ada style lain.
    ---------------------------------------------------------------- --}}
    <table id="order-table" class="order-table table-auto w-full bg-white !mt-4">

        {{-- ===================== THEAD (Kepala Tabel) =====================
          class :
            text-xs         → ukuran font kecil untuk header.
            uppercase       → huruf kapital semua (gaya header tabel).
            text-slate-400  → warna teks abu ke-slate (lebih soft).
            bg-slate-50     → latar abu sangat muda (membedakan header & body).
            rounded-sm      → radius kecil; efek lebih ke kontainer jika dibungkus.
        ---------------------------------------------------------------- --}}
        <thead class="text-xs uppercase text-slate-400 bg-slate-50 rounded-sm">
            <tr>
                {{-- Setiap <th> dibungkus <div> agar teks mudah dipusatkan dengan utility class --}}
                <th class="p-2"> {{-- p-2 = padding 0.5rem; beri ruang agar tidak dempet --}}
                    <div class="font-semibold text-center">No</div> {{-- font-semibold = semi-bold; text-center = rata tengah --}}
                </th>
                <th class="p-2">
                    <div class="font-semibold text-center">Id Transaksi</div>
                </th>
                <th class="p-2">
                    <div class="font-semibold text-center">Tahun</div> {{-- CATATAN: label "Tahun" --}}
                </th>
                <th class="p-2">
                    <div class="font-semibold text-center">Bulan</div> {{-- CATATAN: label "Bulan" --}}
                </th>
                <th class="p-2">
                    <div class="font-semibold text-center">Tanggal Transaksi</div>
                </th>
                <th class="p-2">
                    <div class="font-semibold text-center">Pengguna</div>
                </th>
                <th class="p-2">
                    <div class="font-semibold text-center">Produk</div>
                </th>
                <th class="p-2">
                    <div class="font-semibold text-center">Resi</div>
                </th>
                <th class="p-2">
                    <div class="font-semibold text-center">Bukti Pembayaran</div>
                </th>
                <th class="p-2">
                    <div class="font-semibold text-center">Total Harga</div>
                </th>

                {{-- ===================== KOLOM STATUS OPSIONAL =====================
                  Blade: @if (!isset($noStatus))
                  Arti  : Hanya tampilkan kolom Status jika variabel $noStatus TIDAK diset.
                          Ini berguna saat tabel dipakai ulang di halaman yang berbeda.
                ------------------------------------------------------------------- --}}
                @if (!isset($noStatus))
                    <th class="p-2">
                        <div class="font-semibold text-center">Status</div>
                    </th>
                @endif

                <th class="p-2">
                    <div class="font-semibold text-center">Aksi</div>
                </th>
            </tr>
        </thead>

        {{-- ===================== TBODY (Isi Data) =====================
          class :
            text-sm                 → ukuran font isi data.
            divide-y divide-slate-100 → garis pemisah horizontal antar baris warna abu sangat muda.
        ---------------------------------------------------------------- --}}
        <tbody class="text-sm divide-y divide-slate-100">

            {{-- ===================== KETIKA DATA KOSONG =====================
              Kondisi: $items->count() === 0
              - colspan="8" : jumlah kolom digabung untuk satu sel pesan.
                *CATATAN*: Pastikan colspan sesuai jumlah kolom TABEL aktual.
                Saat ini tabel punya 11/12 kolom (tergantung $noStatus). Jika "Belum ada data"
                ingin span seluruh kolom, sesuaikan: colspan="{{ isset($noStatus) ? 11 : 12 }}"
            ---------------------------------------------------------------- --}}
            @if ($items->count() === 0)
                <tr>
                    <td colspan="{{ isset($noStatus) ? 11 : 12 }}" class="p-4 text-center text-slate-400">
                        Belum ada data
                    </td>
                </tr>
            @endif

            {{-- ===================== LOOP DATA ORDER =====================
              Blade: @foreach ($items as $key => $item)
              - $items : koleksi Eloquent/array berisi data transaksi.
              - $key   : index numerik (dimulai 0) → dipakai untuk nomor urut & id unik modal.
              - $item  : satu record transaksi (punya relasi ke user & carts).
            ---------------------------------------------------------------- --}}
            @foreach ($items as $key => $item)
                <tr>
                    {{-- ========== KOLOM NO (NOMOR URUT) ==========
                      $key + 1 → agar mulai dari 1, bukan 0.
                      !text-center : "!" memaksa style rata tengah jika ada style lain konflik.
                    ------------------------------------------------ --}}
                    <td class="p-2 !text-center">{{ $key + 1 }}</td>

                    {{-- ========== KOLOM ID TRANSAKSI (BUTTON MODAL) ==========
                      Button ini membuka modal detail berdasarkan atribut data-modal-target/toggle.
                      class:
                        underline                → garis bawah (indikasi link).
                        text-blue-800            → warna biru.
                        !text-center flex items-center justify-center → pusatkan konten.
                      Teks: "KDKP1892000{{ $item->id }}" → prefix + id transaksi.
                    ----------------------------------------------------------- --}}
                    <td class="p-2">
                        <!-- Modal toggle -->
                        <button
                            data-modal-target="modal-detail-{{ $key }}"  {{-- target id modal di bawah --}}
                            data-modal-toggle="modal-detail-{{ $key }}"  {{-- plugin (mis. Flowbite) baca ini untuk show/hide --}}
                            class="underline text-blue-800 !text-center flex items-center justify-center"
                            type="button">
                            KDKP1892000{{ $item->id }}
                        </button>
                    </td>

                    {{-- ========== KOLOM TAHUN & BULAN ==========
                      *CATATAN PENTING*: Saat ini label THEAD: "Tahun" dulu baru "Bulan".
                      Tetapi di data di bawah:
                        - Kolom pertama menampilkan 'F' (nama BULAN)
                        - Kolom kedua menampilkan 'Y' (TAHUN)
                      Jadi TERBALIK. Perbaiki urutannya agar sesuai:
                        - Untuk "Tahun" pakai 'Y'
                        - Untuk "Bulan" pakai 'F'
                    ----------------------------------------------------------- --}}
                    <td class="p-2 text-center">
                        {{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('Y') }} {{-- Tahun (Y) --}}
                    </td>
                    <td class="p-2 text-center">
                        {{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('F') }} {{-- Bulan (F, nama bulan) --}}
                    </td>

                    {{-- ========== KOLOM TANGGAL TRANSAKSI ==========
                      Menampilkan timestamp asli. Bisa diformat:
                      → translatedFormat('d M Y H:i') untuk tampilan lokal.
                    ------------------------------------------------- --}}
                    <td class="p-2 !text-center">{{ $item->created_at }}</td>

                    {{-- ========== KOLOM PENGGUNA (EMAIL) ==========
                      $item->user->email → akses relasi user dari transaksi.
                    ------------------------------------------------ --}}
                    <td class="p-2 text-center">{{ $item->user->email }}</td>

                    {{-- ========== KOLOM PRODUK (LIST PRODUK DALAM SATU ORDER) ==========
                      text-left      → rata kiri.
                      text-nowrap    → cegah wrapping; tambahkan <br> manual per item.
                      Loop: $orderCarts[$item->id] diasumsikan berisi Cart milik order ini.
                      Tampilkan nama produk + jumlah.
                    ------------------------------------------------------------- --}}
                    <td class="p-2 text-left text-nowrap">
                        @foreach ($orderCarts[$item->id] as $cart)
                            <p>{{ $cart->product->nama }}, {{ $cart->jumlah }}</p>
                            <br>
                        @endforeach
                    </td>

                    {{-- ========== KOLOM RESI ==========
                      Null coalescing display: jika resi null → tampilkan '-' .
                    ------------------------------------- --}}
                    <td class="p-2 text-center">{{ $item->resi ?? '-' }}</td>

                    {{-- ========== KOLOM BUKTI PEMBAYARAN ==========
                      Jika ada file: jadikan link ke storage dengan target="_blank" + rel keamanan.
                      Jika tidak ada: tampil '-'.
                      asset('storage/images/...') → pastikan disk 'public' & symlink "storage:link" aktif.
                    -------------------------------------------------------------- --}}
                    <td class="p-2 text-center">
                        <a
                            {{ $item->bukti_pembayaran ? "target='_blank' rel='noopener noreferrer'" : '' }}
                            class="text-blue-500"
                            href="{{ $item->bukti_pembayaran ? asset('storage/images/' . $item->bukti_pembayaran) : '#' }}">
                            {{ $item->bukti_pembayaran ? 'Lihat' : '-' }}
                        </a>
                    </td>

                    {{-- ========== KOLOM TOTAL HARGA ==========
                      number_format(angka, 0, ',', '.') → format rupiah: ribuan titik, tanpa desimal.
                      Menggunakan harga + ongkos_kirim.
                    ------------------------------------------------ --}}
                    <td class="p-2 text-center">
                        Rp{{ number_format($item->harga + $item->ongkos_kirim, 0, ',', '.') }}
                    </td>

                    {{-- ========== KOLOM STATUS (DITAMPILKAN JIKA $noStatus TIDAK DISSET) ==========
                      @component('components.status-pill', ['status' => $item->status]) → include komponen badge status.
                    -------------------------------------------------------------------------------- --}}
                    @if (!isset($noStatus))
                        <td class="p-2 text-center">
                            @component('components.status-pill', ['status' => $item->status])
                            @endcomponent
                        </td>
                    @endif

                    {{-- ========== KOLOM AKSI ==========
                      Berisi: tombol konfirmasi/ditolak (saat "Menunggu konfirmasi"),
                              tombol input resi (saat "Diproses"),
                              tombol download (jika $noStatus diset → tampilan khusus, misal di halaman pembayaran).
                    ----------------------------------- --}}
                    <td>
                        <div class="flex gap-2 items-center justify-center">

                            {{-- ===== AKSI: KONFIRMASI / TOLAK (Saat status "Menunggu konfirmasi") =====
                               Form POST ke route('admin.order.update', $item->id) dengan @csrf.
                               Hidden input "status" mengirim status baru.
                            --}}
                            @if ($item->status === 'Menunggu konfirmasi')
                                <form method="POST" action="{{ route('admin.order.update', $item->id) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="Diproses">
                                    <button class="btn !bg-green-500 py-1 text-white text-sm rounded-md">
                                        Konfirmasi
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.order.update', $item->id) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="Ditolak">
                                    <button class="delete-btn" title="Tolak pesanan">
                                        <i class="ph ph-x text-xl text-red-700"></i>
                                    </button>
                                </form>
                            @endif

                            {{-- ===== AKSI: INPUT RESI (Saat status "Diproses") =====
                               Tombol membuka modal input resi (data-modal-target/toggle).
                            --}}
                            @if ($item->status === 'Diproses')
                                <!-- Modal toggle -->
                                <button
                                    data-modal-target="inputresi-modal-{{ $key }}"
                                    data-modal-toggle="inputresi-modal-{{ $key }}"
                                    class="btn btn-primary !bg-primary-500 py-1 text-sm rounded-md"
                                    type="button">
                                    Input Resi
                                </button>

                                {{-- ===================== MODAL INPUT RESI =====================
                                  id    : "inputresi-modal-{{ $key }}" (unik per baris).
                                  class :
                                    hidden               → tersembunyi default.
                                    overflow-y-auto/x-hidden → scroll Y jika konten tinggi.
                                    fixed top-0 right-0 left-0 → overlay menutupi layar.
                                    z-50                → di atas konten lain (butuh layer cukup tinggi).
                                    justify-center items-center → pusatkan konten (jika pakai flex/JS helper).
                                    w-full md:inset-0   → lebar penuh; di md: inset 0 (menempel tepi).
                                    h-[calc(100%-1rem)] max-h-full → tinggi modal responsif.
                                  *Catatan*: Atribut data-modal-* biasanya dipakai Flowbite. Pastikan skripnya dimuat.
                                ---------------------------------------------------------------- --}}
                                <div
                                    id="inputresi-modal-{{ $key }}"
                                    tabindex="-1"
                                    aria-hidden="true"
                                    class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                                    <div class="relative p-4 w-full max-w-2xl max-h-full">
                                        <!-- Modal content -->
                                        <div class="relative bg-white rounded-lg shadow">

                                            <!-- Modal header -->
                                            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                                                <h3 class="text-xl font-semibold text-gray-900">Input Resi</h3>
                                                {{-- Tombol close (ikon X) – pastikan data-modal-hide sesuai id modal --}}
                                                <button
                                                    type="button"
                                                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center"
                                                    data-modal-hide="inputresi-modal-{{ $key }}">
                                                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                                    </svg>
                                                    <span class="sr-only">Close modal</span>
                                                </button>
                                            </div>

                                            <!-- Modal body -->
                                            <div class="p-4 md:p-5 space-y-4">
                                                <form method="POST" action="{{ route('admin.order.update', $item->id) }}">
                                                    @csrf
                                                    <input type="hidden" name="status" value="Dikirim"> {{-- Ubah status ke "Dikirim" --}}
                                                    <input
                                                        type="text"
                                                        name="resi"
                                                        class="input-field mb-4 w-full"
                                                        placeholder="Tuliskan resi pengiriman disini"
                                                        required> {{-- required: wajib diisi --}}
                                                    <button class="btn btn-primary !bg-primary-500">
                                                        Tambahkan resi
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- ===== AKSI: DOWNLOAD (Mode tanpa kolom status) =====
                               Ditampilkan jika $noStatus diset (misal di halaman riwayat pembayaran).
                            --}}
                            @if (isset($noStatus))
                                <a
                                    href="/payment/{{ $item->id }}/print"
                                    class="text-white rounded-md bg-green-500 flex items-center justify-center py-2 mt-4 w-full gap-2"
                                    title="Unduh bukti pembayaran">
                                    <i class="ph ph-download"></i>
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>

{{-- ===================== KUMPULAN MODAL DETAIL (SATU PER BARIS) =====================
  Ditaruh terpisah dari tabel agar struktur DOM rapi. Dipanggil oleh tombol "Id Transaksi".
------------------------------------------------------------------ --}}
<div>
    @foreach ($items as $key => $item)
        <!-- Main modal detail pesanan -->
        <div
            id="modal-detail-{{ $key }}"        {{-- id harus sama dengan data-modal-target/toggle --}}
            tabindex="-1"
            aria-hidden="true"
            class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-2xl max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                        <h3 class="text-xl font-semibold text-gray-900">Detail Pesanan</h3>

                        {{-- BUGFIX: di kode asli tertulis "<butto n ...>" (typo).
                           Pastikan ini <button> dan data-modal-hide mengarah ke "modal-detail-{{ $key }}" --}}
                        <button
                            type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center"
                            data-modal-hide="modal-detail-{{ $key }}">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>

                    <!-- Modal body -->
                    <div class="p-4 md:p-5 space-y-4">
                        {{-- Nama penerima (dari relasi user) --}}
                        <div>
                            <div class="text-gray-500 text-opacity-60">Nama</div>
                            <div>{{ $item->user->nama }}</div>
                        </div>

                        {{-- Alamat pengiriman --}}
                        <div>
                            <div class="text-gray-500 text-opacity-60">Alamat</div>
                            <div>{{ $item->user->alamat }}</div>
                        </div>

                        {{-- Daftar produk pada order ini --}}
                        <div>
                            <div class="text-gray-500 text-opacity-60">Detail Produk Dipesan</div>
                            @foreach ($orderCarts[$item->id] as $cart)
                                <p>{{ $cart->product->nama }} x {{ $cart->jumlah }}</p>
                            @endforeach
                        </div>
                    </div> {{-- /modal body --}}
                </div> {{-- /modal content --}}
            </div> {{-- /wrapper ukuran modal --}}
        </div>
    @endforeach
</div>
