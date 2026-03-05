{{-- ======================================================================================
  FILE    : resources/views/.../order-offline-create.blade.php (contoh)
  TUJUAN  : Form tambah Order Offline (data pelanggan + item pesanan + ringkasan harga)
  TEKNO   : Blade, Tailwind utility + kelas tombol kustom (btn btn-primary/secondary), jQuery
  CATATAN : Komentar menjelaskan TAG, ATRIBUT, UTILITAS TAILWIND (px/py/mt/mb/flex/grid, dll),
            Blade directive (@extends/@section/@csrf/@foreach/@if), serta JS (event, selector).
====================================================================================== --}}

{{-- ===================== @extends =====================
  Directive Blade: @extends('layouts.admin')
  Fungsi         : View ini mewarisi layout "layouts.admin" (header, sidebar, footer, @stack('scripts')).
---------------------------------------------------- --}}
@extends('layouts.admin')

{{-- ===================== @section('page') =====================
  Fungsi : Mengisi section "page" pada layout (sering dipakai untuk <title> / breadcrumb).
  Isi    : "Tambah"
----------------------------------------------------------- --}}
@section('page')
    Tambah
@endsection

{{-- ===================== @section('content') =====================
  Fungsi : Mengisi konten utama halaman di slot layout.
--------------------------------------------------------------- --}}
@section('content')

    {{-- ===================== WRAPPER KONTAINER =====================
      Tag   : <div>
      class : container
              - "container" (kelas kustom/proyek) → pembungkus lebar tetap + padding horizontal.
              Peran : Membuat isi halaman rapi & punya padding standar tema.
    ------------------------------------------------------------- --}}
    <div class="container">

        {{-- ===================== TAMPILKAN ERROR VALIDASI (opsional) =====================
          @if ($errors->all()) : Blade if → kondisi server-side; jika TRUE, blok dieksekusi.
                                 **Apa itu if/else?**
                                   - Struktur *percabangan* dalam pemrograman.
                                   - "if (kondisi) { ... } else { ... }":
                                     jalankan blok IF jika kondisi TRUE; jika FALSE jalankan blok ELSE.
          $errors->all()       : Mengambil semua pesan error (array of string).
          @dd($errors->all())  : "die & dump" → HENTIKAN eksekusi & tampilkan isi error (untuk debug).
        ------------------------------------------------------------------------------- --}}
        @if ($errors->all())
            @dd($errors->all()) {{-- Hentikan halaman & tampilkan semua error (khusus DEBUG). --}}

            {{-- Kotak merah daftar error (baru tampil kalau @dd di atas dihapus/komentari) --}}
            <div class="alert alert-danger">
                <ul>
                    {{-- @foreach → loop semua pesan error & tampilkan sebagai <li> --}}
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li> {{-- <li> : list item berisi satu pesan error --}}
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ===================== FORM: SIMPAN ORDER OFFLINE =====================
          Tag     : <form>
          action  : route('orders.store') → helper Blade untuk URL dari nama route "orders.store".
          method  : "POST" (HTML hanya mendukung GET/POST → method lain pakai @method spoofing).
        ---------------------------------------------------------------------- --}}
        <form action="{{ route('orders.store') }}" method="POST">
            {{-- @csrf → Token proteksi CSRF WAJIB agar POST diterima Laravel --}}
            @csrf

            {{-- ===================== HIDDEN INPUT: type=offline =====================
              Tag   : <input type="hidden">
              name  : "type" → field yang dikirim ke server tapi tidak terlihat user.
              value : "offline" → menandai order offline.
            --------------------------------------------------------------------- --}}
            <input type="hidden" name="type" value="offline">

            {{-- ===================== BARIS TOMBOL (Batal & Simpan) =====================
              Tag   : <div>
              class : form-group mb-10 flex items-center justify-end flex-row
                      - form-group : kelas kustom (grup form).
                      - mb-10      : margin-bottom besar (jarak kebawah).
                      - flex       : aktifkan Flexbox (susun anak horizontal).
                      - items-center: vertikal rata tengah.
                      - justify-end : dorong anak ke kanan (rata kanan).
            ------------------------------------------------------------------------ --}}
            <div class="form-group mb-10 flex items-center justify-end flex-row">

                {{-- TOMBOL BATAL
                   Tag   : <a> → anchor/tautan.
                   href  : route('order.offline') → kembali ke daftar order offline.
                   class : btn btn-secondary → gaya tombol sekunder (abu-abu) dari tema kustom. --}}
                <a href="{{ route('order.offline') }}" class="btn btn-secondary">Batal</a>

                {{-- TOMBOL SIMPAN
                   Tag   : <button type="submit"> → submit form ke server.
                   class : btn btn-primary → gaya tombol utama (biru) dari tema kustom. --}}
                <button type="submit" class="btn btn-primary">Simpan Order</button>
            </div>

            {{-- ===================== GRID 12 KOLOM (5 kiri / 7 kanan) =====================
              Tag   : <div>
              class : grid grid-cols-12 gap-10
                      - grid          : aktifkan CSS Grid container.
                      - grid-cols-12  : bagi area menjadi 12 kolom.
                      - gap-10        : spasi antar kolom/baris grid (besar).
              Peran : Layout dua kolom besar: kiri (col-span-5), kanan (col-span-7).
            -------------------------------------------------------------------------- --}}
            <div class="grid grid-cols-12 gap-10">

                {{-- ===================== KOLOM KIRI (5 kolom) ===================== --}}
                <div class="space-y-5 col-span-5">
                    {{-- Catatan Tailwind:
                       - col-span-5 → elemen ini mengambil 5/12 lebar grid.
                       - space-y-5  → beri jarak vertikal 1.25rem antar anak langsung. --}}

                    {{-- ========== CARD: INFORMASI PELANGGAN ========== --}}
                    <div class="bg-white p-4 rounded-lg">
                        <h3 class="mb-4">Informasi Pelanggan</h3>

                        <div class="space-y-4 mt-4">

                            {{-- ===== FIELD: Nama Pelanggan ===== --}}
                            <div class="form-group">
                                <label for="new_customer_name">Nama</label>

                                {{-- INPUT TEKS NAMA
                                   id="new_customer_name" : kaitkan dengan <label for="..."> di atas.
                                   name="new_customer[name]" : akan menjadi array "new_customer" di server. --}}
                                <input type="text" id="new_customer_name" name="new_customer[name]"
                                    class="form-control input-field mt-0">
                            </div>

                            {{-- ===== FIELD: No. Telepon ===== --}}
                            <div class="form-group">
                                <label for="new_customer_phone">No. Telepon</label>
                                <input type="text" id="new_customer_phone" name="new_customer[phone]"
                                    class="form-control input-field mt-0">
                            </div>

                        </div>
                    </div>

                    {{-- ========== CARD: LOKASI PENGIRIMAN ========== --}}
                    <div class="bg-white p-4 rounded-lg">
                        <h3 class="mb-4">Lokasi Pengiriman</h3>
                        
                        <div class="space-y-4 mt-4">
                            {{-- ===== FIELD: Alamat (No rumah, dll) ===== --}}
                            <div class="form-group">
                                <label for="new_customer_address">Alamat (No rumah, dll)</label>
                                <textarea id="new_customer_address" name="new_customer[address]" class="form-control input-field mt-0" rows="2" placeholder="Masukkan alamat lengkap"></textarea>
                                @error('new_customer.address') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- ===== FIELD: Provinsi ===== --}}
                            <div class="form-group">
                                <label for="province">Provinsi</label>
                                <select id="province" name="province" class="form-control input-field mt-0" required>
                                    <option value="">-- Pilih Provinsi --</option>
                                </select>
                                @error('province') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- ===== FIELD: Kabupaten ===== --}}
                            <div class="form-group">
                                <label for="city">Kabupaten</label>
                                <input type="text" id="city" name="city" class="form-control input-field mt-0">
                                @error('city') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- ===== FIELD: Kecamatan ===== --}}
                            <div class="form-group">
                                <label for="district">Kecamatan</label>
                                <input type="text" id="district" name="district" class="form-control input-field mt-0">
                                @error('district') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- ===== FIELD: Kelurahan dan Kode Pos (dalam satu baris) ===== --}}
                            <div class="grid grid-cols-2 gap-3">
                                <div class="form-group">
                                    <label for="village">Kelurahan</label>
                                    <input type="text" id="village" name="village" class="form-control input-field mt-0">
                                    @error('village') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div class="form-group">
                                    <label for="postal_code">Kode Pos</label>
                                    <input type="text" id="postal_code" name="postal_code" class="form-control input-field mt-0">
                                    @error('postal_code') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ========== CARD: INFORMASI PEMBAYARAN ========== --}}
                    <div class="bg-white p-4 rounded-lg">
                        <h3 class="mb-4">Informasi Pembayaran</h3>

                        {{-- SELECT: Metode Pembayaran --}}
                        <div class="form-group">
                            <label for="payment_method">Metode Pembayaran</label>
                            <select name="payment_method" id="payment_method" class="form-control input-field mt-0">
                                <option value="cash">Tunai</option>
                                <option value="transfer">Transfer</option>
                                <option value="credit_card">Kartu Kredit</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- ===================== KOLOM KANAN (7 kolom) ===================== --}}
                <div class="space-y-5 col-span-7">
                    {{-- col-span-7 → ambil 7/12 lebar grid (lebih besar dari kiri). --}}

                    {{-- ========== CARD: ITEM PESANAN (dynamic) ========== --}}
                    <div class="bg-white p-4 rounded-lg">
                        <h3 class="mb-4">Item Pesanan</h3>

                        {{-- SECTION PENYIMPAN ITEM (append oleh JS) --}}
                        <div id="order-items-section" class="space-y-4">

                            {{-- ===== BLOK ITEM DEFAULT (INDEX 0) ===== --}}
                            <div class="order-item p-3 border border-slate-200 rounded-md">

                                {{-- BARIS TOMBOL HAPUS (ikon minus) --}}
                                <div class="flex items-center justify-end mb-3">
                                    <div class="col-span-1">
                                        {{-- BUTTON HAPUS ITEM
                                           class="btn-remove-item" → akan dipakai sebagai **selector** JS.
                                           px-4 → padding kiri-kanan 1rem (16px). --}}
                                        <button type="button"
                                            class="btn btn-secondary bg-red-200 btn-remove-item px-4">−</button>
                                    </div>
                                </div>

                                {{-- GRID KONTEN ITEM --}}
                                <div class="flex gap-3 flex-wrap items-center">

                                    {{-- ===== KOLOM: PILIH PRODUK JADI ===== --}}
                                    <div class="col-span-3 product-select">
                                        <div class="text-xs text-slate-600">Pilih Produk</div>
                                        <select name="items[0][product_id]"
                                            class="form-control input-field mt-0 product-select-field">
                                            <option value="">Pilih Produk</option>

                                            {{-- LOOP PRODUK (dari Controller) --}}
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                                    {{ $product->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- ===== KOLOM: FIELD CUSTOM (Bahan, Dimensi, Warna) ===== --}}
                                    <div class="col-span-5 custom-product-fields">
                                        <div class="flex flex-wrap gap-3">

                                            {{-- Pilih Bahan --}}
                                            <select name="items[0][material]"
                                                class="form-control input-field mt-0 material-select">
                                                <option value="">Pilih Bahan</option>
                                                <!-- Options akan diisi oleh updateColorDropdowns saat produk dipilih -->
                                            </select>

                                            {{-- Dimensi: Panjang/Lebar/Tinggi (cm) --}}
                                            <input type="number" name="items[0][length]" placeholder="Panjang (cm)"
                                                class="form-control input-field mt-0">
                                            <input type="number" name="items[0][width]" placeholder="Lebar (cm)"
                                                class="form-control input-field mt-0">
                                            <input type="number" name="items[0][height]" placeholder="Tinggi (cm)"
                                                class="form-control input-field mt-0">

                                            {{-- Warna Kayu --}}
                                            <select name="items[0][wood_color]" class="form-control input-field mt-0">
                                                <option value="">Pilih Warna Kayu</option>
                                                <!-- Options akan diisi oleh updateColorDropdowns saat produk dipilih -->
                                            </select>

                                            {{-- Warna Rotan --}}
                                            <select name="items[0][rattan_color]" class="form-control input-field mt-0">
                                                <option value="">Pilih Warna Rotan</option>
                                                <!-- Options akan diisi oleh updateColorDropdowns saat produk dipilih -->
                                            </select>
                                        </div>
                                    </div>

                                    {{-- ===== KOLOM: JUMLAH (Qty) ===== --}}
                                    <div class="">
                                        <div class="text-xs text-slate-600">Jumlah</div>
                                        <input type="number" name="items[0][quantity]" value="1" min="1"
                                            class="form-control input-field mt-0 quantity-field col-span-2">
                                    </div>

                                    {{-- ===== KOLOM: HARGA (unit×qty) ===== --}}
                                    <div class="">
                                        <div class="text-xs text-slate-600">Harga</div>
                                        {{-- readonly → tidak boleh diketik manual, diisi JS.
                                           class="price-field" → **APA ITU .price-field?**
                                              - Ini adalah *class CSS* yang sengaja dipakai sebagai
                                                **selector JS** untuk:
                                                (1) menaruh total harga tiap item,
                                                (2) mengumpulkan semua harga item saat menghitung subtotal/total. --}}
                                        <input type="number" name="items[0][price]"
                                            class="col-span-2 form-control input-field mt-0 price-field" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ===== TOMBOL TAMBAH ITEM ===== --}}
                        <button type="button" class="btn btn-sm btn-primary mt-4" id="btn-add-item">+ Tambah
                            Item</button>
                    </div>

                    {{-- ========== CARD: RINGKASAN PESANAN ==========
                       **Apa itu subtotal/total?**
                        - *Harga Total Barang*  : jumlah semua harga item (sebelum biaya lain seperti ongkir/ppn).
                        - *Ongkir*              : biaya pengiriman.
                        - *Total*                : nilai akhir yang harus dibayar (harga total barang + ongkir). --}}
                    <div class="bg-white p-4 rounded-lg">
                        <h3 class="mb-4">Ringkasan Pesanan</h3>

                        <div class="flex justify-between mb-2">
                          <span>Harga Total Barang:</span>
                          <span id="subtotal">Rp 0</span>
                        </div>
                        <input type="hidden" id="subtotal_value" name="subtotal" value="0">

                        <div class="flex justify-between mb-2">
                          <span>Ongkir:</span>
                          <span id="shipping">Rp 0</span>
                        </div>
                        <input type="hidden" id="shipping_value" name="shipping_estimate" value="0">

                        <div class="flex justify-between font-bold text-lg border-t pt-2 mt-2">
                          <span>Total:</span>
                          <span id="total">Rp 0</span>
                        </div>
                        <input type="hidden" id="total_value" name="total" value="0">
                    </div>

                </div>
            </div> {{-- /grid 12 kolom --}}
        </form> {{-- /form --}}
    </div> {{-- /container --}}

    {{-- ===================== @push('scripts') =====================
      Fungsi : Menitipkan JS ke stack "scripts" di layout (biasanya dirender menjelang </body>).
      Kenapa : Agar script hanya dimuat di halaman ini & tidak mengotori layout global.
    ------------------------------------------------------------- --}}
    @push('scripts')
<script>
// =================================================================
//  KONFIGURASI DASAR
// =================================================================

// Base URL API Laravel (relative ke domain yang sama)
const API_BASE = "{{ url('/api') }}";

// Daftar provinsi hanya untuk mengisi <select>, BUKAN untuk tarif ongkir
const PROVINCES = [
  'Aceh','Sumatera Utara','Sumatera Barat','Riau','Jambi','Sumatera Selatan',
  'Bengkulu','Lampung','Kepulauan Bangka Belitung','Kepulauan Riau',
  'DKI Jakarta','Jawa Barat','Jawa Tengah','DI Yogyakarta','Jawa Timur','Banten',
  'Bali','Nusa Tenggara Barat','Nusa Tenggara Timur',
  'Kalimantan Barat','Kalimantan Tengah','Kalimantan Selatan','Kalimantan Timur','Kalimantan Utara',
  'Sulawesi Utara','Sulawesi Tengah','Sulawesi Selatan','Sulawesi Tenggara','Gorontalo','Sulawesi Barat',
  'Maluku','Maluku Utara','Papua Barat','Papua'
];

const toRupiah = (n) => "Rp " + (parseFloat(n || 0)).toLocaleString("id-ID");

// Data produk dari controller (untuk mendapatkan extra_price warna)
const PRODUCTS_DATA = @json($productsData ?? []);

// =================================================================
//  FUNGSI HITUNG HARGA CUSTOM (SAMA DENGAN BACKEND)
// =================================================================
function computeCustomUnitPrice(item) {
  const material = item.material || '';
  const length = parseFloat(item.length) || 0;
  const width = parseFloat(item.width) || 0;
  const height = parseFloat(item.height) || 0;
  const woodColor = item.wood_color || '';
  const rattanColor = item.rattan_color || '';
  const productId = parseInt(item.product_id) || null;

  // Jika material atau dimensi belum lengkap → return 0
  if (!material || length <= 0 || width <= 0 || height <= 0) {
    return 0;
  }

  // PERBAIKAN: Ambil harga material dari product variants, bukan hardcoded
  let lengthPrice = 0;
  let widthPrice = 0;
  let heightPrice = 0;

  if (productId) {
    const product = PRODUCTS_DATA.find(p => p.id === productId);
    if (product && product.variants) {
      const variant = product.variants.find(v => v.name === material);
      if (variant) {
        lengthPrice = parseFloat(variant.length_price) || 0;
        widthPrice = parseFloat(variant.width_price) || 0;
        heightPrice = parseFloat(variant.height_price) || 0;
      }
    }
  }

  // Fallback ke hardcoded jika variant tidak ditemukan
  if (lengthPrice === 0 && widthPrice === 0 && heightPrice === 0) {
    const materialPrices = {
      'Kayu Jati': {
        length: 14000,
        width: 14000,
        height: 14000,
      },
      'Kayu Jati & Rotan': {
        length: 20000,
        width: 20000,
        height: 20000,
      },
    };
    const base = materialPrices[material];
    if (base) {
      lengthPrice = base.length;
      widthPrice = base.width;
      heightPrice = base.height;
    } else {
      return 0;
    }
  }

  // Harga dari dimensi (tanpa warna)
  const price = (length / 10) * lengthPrice
              + (width / 10) * widthPrice
              + (height / 10) * heightPrice;

  // Tambahan harga warna kayu dari PRODUCTS_DATA
  let woodExtra = 0;
  if (woodColor && productId) {
    const product = PRODUCTS_DATA.find(p => p.id === productId);
    if (product && product.wood_colors) {
      const color = product.wood_colors.find(c => c.name === woodColor);
      if (color) {
        woodExtra = parseFloat(color.extra_price) || 0;
      }
    }
  }

  // Tambahan harga warna rotan dari PRODUCTS_DATA
  let rattanExtra = 0;
  if (rattanColor && productId) {
    const product = PRODUCTS_DATA.find(p => p.id === productId);
    if (product && product.rattan_colors) {
      const color = product.rattan_colors.find(c => c.name === rattanColor);
      if (color) {
        rattanExtra = parseFloat(color.extra_price) || 0;
      }
    }
  }

  return Math.round(price + woodExtra + rattanExtra);
}

// =================================================================
//  FUNGSI UPDATE DROPDOWN WARNA BERDASARKAN PRODUK
// =================================================================
function updateColorDropdowns($item, productId) {
  const product = PRODUCTS_DATA.find(p => p.id === parseInt(productId));
  if (!product) {
    return;
  }

  // Update dropdown warna kayu
  const $woodSelect = $item.find('select[name*="[wood_color]"]');
  const currentWoodValue = $woodSelect.val();
  $woodSelect.empty().append('<option value="">Pilih Warna Kayu</option>');
  
  if (product.wood_colors && product.wood_colors.length > 0) {
    product.wood_colors.forEach(color => {
      const $option = $('<option></option>')
        .attr('value', color.name)
        .text(color.name)
        .data('extra', color.extra_price || 0);
      $woodSelect.append($option);
    });
    
    // Coba kembalikan nilai sebelumnya jika masih ada
    if (currentWoodValue && $woodSelect.find(`option[value="${currentWoodValue}"]`).length) {
      $woodSelect.val(currentWoodValue);
    }
  }

  // Update dropdown warna rotan
  const $rattanSelect = $item.find('select[name*="[rattan_color]"]');
  const currentRattanValue = $rattanSelect.val();
  $rattanSelect.empty().append('<option value="">Pilih Warna Rotan</option>');
  
  if (product.rattan_colors && product.rattan_colors.length > 0) {
    product.rattan_colors.forEach(color => {
      const $option = $('<option></option>')
        .attr('value', color.name)
        .text(color.name)
        .data('extra', color.extra_price || 0);
      $rattanSelect.append($option);
    });
    
    // Coba kembalikan nilai sebelumnya jika masih ada
    if (currentRattanValue && $rattanSelect.find(`option[value="${currentRattanValue}"]`).length) {
      $rattanSelect.val(currentRattanValue);
    }
  }

  // Update dropdown bahan dari variants produk
  const $materialSelect = $item.find('.material-select');
  const currentMaterialValue = $materialSelect.val();
  $materialSelect.empty();
  
  if (product.variants && product.variants.length > 0) {
    product.variants.forEach(variant => {
      const $option = $('<option></option>')
        .attr('value', variant.name)
        .text(variant.name)
        .data('length_price', variant.length_price || 0)
        .data('width_price', variant.width_price || 0)
        .data('height_price', variant.height_price || 0);
      $materialSelect.append($option);
    });
    
    // Set default material jika ada
    if (product.default_bahan) {
      $materialSelect.val(product.default_bahan);
    } else if (currentMaterialValue && $materialSelect.find(`option[value="${currentMaterialValue}"]`).length) {
      // Coba kembalikan nilai sebelumnya jika masih ada
      $materialSelect.val(currentMaterialValue);
    }
  } else {
    // Fallback: jika tidak ada variants, gunakan opsi default
    $materialSelect.append('<option value="Kayu Jati">Kayu Jati</option>');
    $materialSelect.append('<option value="Kayu Jati & Rotan">Kayu Jati & Rotan</option>');
  }

  // Set default dimensi jika ada
  if (product.default_length) {
    $item.find('input[name*="[length]"]').val(product.default_length);
  }
  if (product.default_width) {
    $item.find('input[name*="[width]"]').val(product.default_width);
  }
  if (product.default_height) {
    $item.find('input[name*="[height]"]').val(product.default_height);
  }
}

// =================================================================
//  FUNGSI HITUNG HARGA ITEM (PRODUK ATAU CUSTOM)
// =================================================================
function computeItemPrice($item) {
  const productId = $item.find('.product-select-field').val();
  const productPrice = parseFloat($item.find('.product-select-field option:selected').data('price')) || 0;
  const material = $item.find('.material-select').val() || '';
  const length = parseFloat($item.find('input[name*="[length]"]').val()) || 0;
  const width = parseFloat($item.find('input[name*="[width]"]').val()) || 0;
  const height = parseFloat($item.find('input[name*="[height]"]').val()) || 0;
  const woodColor = $item.find('select[name*="[wood_color]"]').val() || '';
  const rattanColor = $item.find('select[name*="[rattan_color]"]').val() || '';

  // Jika ada produk dan ada kustomisasi (material/dimensi), hitung custom price
  if (productId && (material || (length > 0 && width > 0 && height > 0))) {
    const customPrice = computeCustomUnitPrice({
      product_id: productId,
      material: material,
      length: length,
      width: width,
      height: height,
      wood_color: woodColor,
      rattan_color: rattanColor,
    });
    return customPrice;
  }

  // Jika tidak ada kustomisasi, gunakan harga produk
  return productPrice;
}

// =================================================================
//  1) HITUNG SUBTOTAL DARI FORM (HARGA PRODUK × QTY)
// =================================================================
function hitungSubtotalDariForm() {
  let subtotal = 0;
  document.querySelectorAll('.price-field').forEach(inp => {
    subtotal += parseFloat(inp.value) || 0;
  });

  const subLabel  = document.getElementById('subtotal');
  const subHidden = document.getElementById('subtotal_value');
  if (subLabel)  subLabel.textContent = toRupiah(subtotal);
  if (subHidden) subHidden.value = String(subtotal);

  return subtotal;
}

// =================================================================
//  2) KUMPULKAN DATA ITEM UNTUK ONGKIR (DIMENSI + QTY)
// =================================================================
function collectItemsForShipping() {
  const items = [];
  document.querySelectorAll('.order-item').forEach(item => {
    const length = parseFloat(item.querySelector('input[name*="[length]"]')?.value) || 0;
    const width  = parseFloat(item.querySelector('input[name*="[width]"]')?.value)  || 0;
    const height = parseFloat(item.querySelector('input[name*="[height]"]')?.value) || 0;
    const qty    = parseInt(item.querySelector('input[name*="[quantity]"]')?.value || '1', 10);

    if (length > 0 && width > 0 && height > 0 && qty > 0) {
      items.push({
        length_cm: length,
        width_cm:  width,
        height_cm: height,
        qty:       qty,
      });
    }
  });
  return items;
}

// =================================================================
//  3) CEK ALAMAT CUKUP DETAIL UNTUK HITUNG ONGKIR
// =================================================================
function hasEnoughAddressAdmin() {
  const provinsi = document.getElementById('province')?.value.trim() || '';
  const city     = document.getElementById('city')?.value.trim() || '';
  const postal   = document.getElementById('postal_code')?.value.trim() || '';

  if (!provinsi || !city || !/^[0-9]{4,6}$/.test(postal)) {
    return false;
  }
  return true;
}

// =================================================================
//  4) PANGGIL BACKEND /api/shipping/quote
// =================================================================
async function requestShippingQuoteAdmin({ destination, items }) {
  const url = `${API_BASE}/shipping/quote`;

  const body = {
    destination: {
      province:    destination.province,
      city:        destination.city,
      district:    destination.district || '',
      postal_code: destination.postal_code || '',
    },
    items,
    volume_divisor: 6000,   // P × L × T / 6000 → kg
    prefer: 'cheapest',
  };

  const res = await fetch(url, {
     method: "POST",
     headers: { "Content-Type": "application/json" },
     body: JSON.stringify(body),
  });

  if (!res.ok) {
    let msg = `Quote ongkir gagal: ${res.status}`;
    try {
      const errBody = await res.json();
      if (errBody?.message) msg = errBody.message;
    } catch (_) {}
    throw new Error(msg);
  }

  const data = await res.json();
  if (typeof data.price !== "number") {
    throw new Error("Format ongkir dari server tidak valid");
  }
  return data;
}

// =================================================================
//  5) UPDATE LABEL TOTAL (Subtotal + Ongkir)
// =================================================================
function updateTotalLabel(subtotal, ongkir) {
  const total = (parseFloat(subtotal) || 0) + (parseFloat(ongkir) || 0);
  const totalLabel = document.getElementById('total');
  const totalHidden = document.getElementById('total_value');
  if (totalLabel) totalLabel.textContent = toRupiah(total);
  if (totalHidden) totalHidden.value = String(total);
  return total;
}

// =================================================================
//  6) HITUNG SEMUA (SUBTOTAL + ONGKIR DI BACKEND)
// =================================================================
async function hitungOngkirDanTotal() {
  const subtotal = hitungSubtotalDariForm();

  const shippingLabel  = document.getElementById('shipping');
  const shippingHidden = document.getElementById('shipping_value');

  // alamat belum lengkap → ongkir 0
  if (!hasEnoughAddressAdmin()) {
    if (shippingLabel)  shippingLabel.textContent = "Rp 0";
    if (shippingHidden) shippingHidden.value = "0";
    updateTotalLabel(subtotal, 0);
    return;
  }

  const items = collectItemsForShipping();
  if (!items.length) {
    if (shippingLabel)  shippingLabel.textContent = "Rp 0";
    if (shippingHidden) shippingHidden.value = "0";
    updateTotalLabel(subtotal, 0);
    return;
  }

  const provinsi = document.getElementById('province')?.value.trim() || '';
  const city     = document.getElementById('city')?.value.trim() || '';
  const district = document.getElementById('district')?.value.trim() || '';
  const postal   = document.getElementById('postal_code')?.value.trim() || '';

  if (shippingLabel) shippingLabel.textContent = "Menghitung ongkir...";

  try {
    const quote = await requestShippingQuoteAdmin({
      destination: {
        province:    provinsi,
        city:        city,
        district:    district,
        postal_code: postal,
      },
      items,
    });

    const ongkir = Number(quote.price || 0);
    if (shippingHidden) shippingHidden.value = String(ongkir);
    if (shippingLabel)  shippingLabel.textContent = toRupiah(ongkir);

    updateTotalLabel(subtotal, ongkir);
  } catch (err) {
    console.error(err);
    if (shippingLabel)  shippingLabel.textContent = "Gagal menghitung ongkir";
    if (shippingHidden) shippingHidden.value = "0";
    updateTotalLabel(subtotal, 0);
  }
}

// =================================================================
//  7) RENDER LIST PROVINSI
// =================================================================
function renderProvinsiJikaPerlu() {
  const sel = document.getElementById('province');
  if (!sel) return;
  if (sel.options.length > 1) return;

  PROVINCES.forEach(p => {
    const op = document.createElement('option');
    op.value = p;
    op.textContent = p;
    sel.appendChild(op);
  });
}

// =================================================================
//  8) HANDLER JQUERY (DINAMIS TAMBAH/HAPUS ITEM + HARGA PRODUK)
// =================================================================
document.addEventListener('DOMContentLoaded', function() {
  if (typeof $ === 'undefined') return;

  let itemCounter = 1;

  // ==== PERUBAHAN ALAMAT → HITUNG ULANG ONGKIR & TOTAL ====
  $('#province, #city, #district, #postal_code').on('change input', function () {
    hitungOngkirDanTotal();
  });

  // Tambah item baru
  $('#btn-add-item').on('click', function() {
    const newItem = $(`
      <div class="order-item p-3 border border-slate-200 rounded-md">
        <div class="flex items-center justify-end mb-3">
          <div class="col-span-1">
            <button type="button" class="btn btn-secondary bg-red-200 btn-remove-item px-4">−</button>
          </div>
        </div>

        <div class="flex gap-3 flex-wrap items-center">
          <!-- Pilih Produk -->
          <div class="col-span-3 product-select">
            <div class="text-xs text-slate-600">Pilih Produk</div>
            <select name="items[${itemCounter}][product_id]" class="form-control input-field mt-0 product-select-field">
              <option value="">Pilih Produk</option>
              @foreach ($products as $product)
                <option value="{{ $product->id }}" data-price="{{ $product->price }}">{{ $product->title }}</option>
              @endforeach
            </select>
          </div>

          <!-- Field Custom (dimensi + warna, hanya untuk catatan + ongkir) -->
          <div class="col-span-5 custom-product-fields">
            <div class="flex flex-wrap gap-3">
              <select name="items[${itemCounter}][material]" class="form-control input-field mt-0 material-select">
                <option value="">Pilih Bahan</option>
                <!-- Options akan diisi oleh updateColorDropdowns saat produk dipilih -->
              </select>

              <input type="number" name="items[${itemCounter}][length]" placeholder="Panjang (cm)" class="form-control input-field mt-0">
              <input type="number" name="items[${itemCounter}][width]"  placeholder="Lebar (cm)"   class="form-control input-field mt-0">
              <input type="number" name="items[${itemCounter}][height]" placeholder="Tinggi (cm)"  class="form-control input-field mt-0">

              <select name="items[${itemCounter}][wood_color]" class="form-control input-field mt-0">
                <option value="">Pilih Warna Kayu</option>
                <!-- Options akan diisi oleh updateColorDropdowns saat produk dipilih -->
              </select>

              <select name="items[${itemCounter}][rattan_color]" class="form-control input-field mt-0">
                <option value="">Pilih Warna Rotan</option>
                <!-- Options akan diisi oleh updateColorDropdowns saat produk dipilih -->
              </select>
            </div>
          </div>

          <!-- Qty -->
          <div>
            <div class="text-xs text-slate-600">Jumlah</div>
            <input type="number" name="items[${itemCounter}][quantity]" value="1" min="1" class="form-control input-field mt-0 quantity-field col-span-2">
          </div>

          <!-- Harga -->
          <div>
            <div class="text-xs text-slate-600">Harga</div>
            <input type="number" name="items[${itemCounter}][price]" class="col-span-2 form-control input-field mt-0 price-field" readonly>
          </div>
        </div>
      </div>
    `);

    $('#order-items-section').append(newItem);
    
    // Pasang event handler untuk item baru
    const $newItem = newItem;
    
    // Event handler untuk update harga saat produk dipilih
    $newItem.find('.product-select-field').on('change', function() {
      const productId = $(this).val();
      if (productId) {
        updateColorDropdowns($newItem, productId);
        updateItemPrice($newItem);
      }
    });
    
    // Event handler untuk update harga saat material/dimensi/warna berubah
    $newItem.find('.material-select, input[name*="[length]"], input[name*="[width]"], input[name*="[height]"], select[name*="[wood_color]"], select[name*="[rattan_color]"]').on('change input', function() {
      updateItemPrice($newItem);
    });
    
    // Event handler untuk update harga saat quantity berubah
    $newItem.find('.quantity-field').on('change input', function() {
      updateItemPrice($newItem);
    });
    
    itemCounter++;
    hitungOngkirDanTotal();
  });

  // Hapus item
  $(document).on('click', '.btn-remove-item', function () {
    $(this).closest('.order-item').remove();
    hitungOngkirDanTotal();
  });

  // Fungsi untuk update harga item
  function updateItemPrice($item) {
    const unit = computeItemPrice($item);
    const qty = parseFloat($item.find('.quantity-field').val()) || 1;
    $item.find('.price-field').val(unit * qty);
  }

  // Produk dipilih/berubah → update dropdown warna dan hitung ulang harga
  $(document).on('change', '.product-select-field', function () {
    const $p = $(this).closest('.order-item');
    const productId = $(this).val();
    
    if (productId) {
      updateColorDropdowns($p, productId);
    }
    
    updateItemPrice($p);
    hitungOngkirDanTotal();
  });

  // Dimensi / material / warna berubah → hitung ulang harga dan ongkir
  $(document).on('change input', '.custom-product-fields input, .custom-product-fields select, .material-select, select[name*="[wood_color]"], select[name*="[rattan_color]"]', function () {
    const $p = $(this).closest('.order-item');
    updateItemPrice($p);
    hitungOngkirDanTotal();
  });

  // Qty berubah → recalc harga
  $(document).on('change input', '.quantity-field', function () {
    const $p = $(this).closest('.order-item');
    updateItemPrice($p);
    hitungOngkirDanTotal();
  });
});

// =================================================================
//  9) BOOT
// =================================================================
document.addEventListener('DOMContentLoaded', () => {
  renderProvinsiJikaPerlu();
  hitungOngkirDanTotal();
});
</script>
    @endpush
@endsection
