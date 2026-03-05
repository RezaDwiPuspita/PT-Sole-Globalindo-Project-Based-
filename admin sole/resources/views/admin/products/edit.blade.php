{{-- FILE: resources/views/admin/products/edit.blade.php --}}

{{-- // kode ini untuk: memberitahu Blade bahwa view ini "turunan" dari layout admin utama --}}
{{-- // Tag: @extends (Blade Directive) --}}
{{-- //   - Fungsi: Memakai file layout sebagai kerangka (header/sidebar/footer). --}}
{{-- //   - Atribut: 'layouts.admin' → path view layout di resources/views/layouts/admin.blade.php --}}
@extends('layouts.admin')

{{-- // kode ini untuk: mengisi bagian "page" di layout dengan judul halaman --}}
@section('page')
    Edit Product
@endsection

{{-- // kode ini untuk: mengisi bagian "content" di layout dengan isi utama halaman --}}
@section('content')

    {{-- =========================  WRAPPER HALAMAN  ========================= --}}
    <div class="container">

        {{-- =========================  F O R M   E D I T  ========================= --}}
        <form class="space-y-5"
              action="{{ route('products.update', $product->id) }}"
              method="POST"
              enctype="multipart/form-data"
              id="product-edit-form">

            @csrf
            @method('PUT')

            {{-- =========================  B A R I S  T O M B O L   A K S I  ========================= --}}
            <div class="form-group mb-10 flex items-center justify-end flex-row gap-3">
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Produk</button>
            </div>

            {{-- =========================  G R I D   D U A   K O L O M  ========================= --}}
            <div class="grid grid-cols-2 gap-10">

                {{-- ===================== KOLOM KIRI (DATA UTAMA) ===================== --}}
                <div class="space-y-5">

                    {{-- ---------- FIELD: JUDUL PRODUK ---------- --}}
                    <div class="form-group">
                        <label for="title">Judul Produk</label>
                        <input
                            type="text"
                            id="title"
                            name="title"
                            class="form-control input-field"
                            placeholder="Tulis Judul Produk"
                            value="{{ old('title', $product->title) }}"
                            required
                        >
                        @error('title') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- ---------- FIELD: UKURAN (SIZE) ---------- --}}
                    <div class="form-group">
                        <label for="size">Ukuran</label>
                        <input
                            type="text"
                            id="size"
                            name="size"
                            class="form-control input-field"
                            placeholder="P x L x T"
                            value="{{ old('size', $product->size) }}"
                            required
                        >
                        @error('size') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- ---------- FIELD: HARGA ---------- --}}
                    <div class="form-group">
                        <label for="price">Harga</label>
                        <input
                            type="number"
                            id="price"
                            name="price"
                            class="form-control input-field"
                            placeholder="Harga Produk"
                            value="{{ old('price', $product->price) }}"
                            min="0"
                            step="1"
                            required
                        >
                        @error('price') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- ---------- FIELD: GAMBAR UTAMA (UPLOAD + PREVIEW) ---------- --}}
                    <div class="form-group">
                        <label for="display_image">Gambar Produk</label>

                        @if ($product->display_image)
                            <div class="mb-2">
                                <img
                                    src="{{ asset('storage/' . $product->display_image) }}"
                                    alt="Display Image"
                                    width="150"
                                    class="rounded border"
                                >
                            </div>
                        @endif

                        <input
                            type="file"
                            id="display_image"
                            name="display_image"
                            class="form-control input-field-file"
                            accept="image/*"
                        >
                        @error('display_image') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- ---------- FIELD: DESKRIPSI ---------- --}}
                    <div class="form-group">
                        <label for="description">Deskripsi</label>
                        <textarea
                            id="description"
                            name="description"
                            class="form-control input-field"
                            rows="3"
                            placeholder="Tulis Deskripsi Produk"
                        >{{ old('description', $product->description) }}</textarea>
                        @error('description') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- ===================== KOLOM KANAN (UKURAN DETAIL & PILIHAN) ===================== --}}
                <div id="variant-section">
                    <div class="space-y-3">

                        {{-- ---------- FIELD: PANJANG DEFAULT (cm) ---------- --}}
                        <div class="form-group">
                            <label for="default_length"> Panjang (cm)</label>
                            <input
                                type="number"
                                id="default_length"
                                name="default_length"
                                class="form-control input-field"
                                placeholder="Panjang Produk"
                                value="{{ old('default_length', $product->default_length) }}"
                                min="0"
                                step="0.01"
                            >
                            @error('default_length') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- ---------- FIELD: LEBAR DEFAULT (cm) ---------- --}}
                        <div class="form-group">
                            <label for="default_width">Lebar (cm)</label>
                            <input
                                type="number"
                                id="default_width"
                                name="default_width"
                                class="form-control input-field"
                                placeholder="Lebar Produk"
                                value="{{ old('default_width', $product->default_width) }}"
                                min="0"
                                step="0.01"
                            >
                            @error('default_width') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- ---------- FIELD: TINGGI DEFAULT (cm) ---------- --}}
                        <div class="form-group">
                            <label for="default_height">Tinggi (cm)</label>
                            <input
                                type="number"
                                id="default_height"
                                name="default_height"
                                class="form-control input-field"
                                placeholder="Tinggi Produk"
                                value="{{ old('default_height', $product->default_height) }}"
                                min="0"
                                step="0.01"
                            >
                            @error('default_height') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- ---------- FIELD: BAHAN/MATERIAL (DINAMIS) ---------- --}}
                        <div class="form-group">
                            <label>Bahan/Material</label>
                            <div id="materials-container" class="space-y-4">
                                @forelse ($product->variants->where('type', 'material') as $index => $variant)
                                    <div class="material-row border border-gray-200 rounded-md p-4 space-y-3">
                                        <div>
                                            <label class="text-sm text-gray-600">Nama Bahan</label>
                                            <input type="text" name="materials[{{ $index }}][name]" 
                                                placeholder="Nama Bahan" 
                                                class="form-control input-field material-name-input" 
                                                value="{{ old("materials.$index.name", $variant->name) }}" required>
                                        </div>
                                        <div>
                                            <label class="text-sm text-gray-600">Harga per 10cm</label>
                                            <div class="grid grid-cols-3 gap-2 mt-2">
                                                <input type="number" name="materials[{{ $index }}][length_price]" 
                                                    placeholder="Harga Panjang" 
                                                    class="form-control input-field material-length-price" 
                                                    value="{{ old("materials.$index.length_price", $variant->length_price && $variant->length_price > 0 ? (int)round($variant->length_price) : '') }}" 
                                                    step="1" min="0">
                                                <input type="number" name="materials[{{ $index }}][width_price]" 
                                                    placeholder="Harga Lebar" 
                                                    class="form-control input-field material-width-price" 
                                                    value="{{ old("materials.$index.width_price", $variant->width_price && $variant->width_price > 0 ? (int)round($variant->width_price) : '') }}" 
                                                    step="1" min="0">
                                                <input type="number" name="materials[{{ $index }}][height_price]" 
                                                    placeholder="Harga Tinggi" 
                                                    class="form-control input-field material-height-price" 
                                                    value="{{ old("materials.$index.height_price", $variant->height_price && $variant->height_price > 0 ? (int)round($variant->height_price) : '') }}" 
                                                    step="1" min="0">
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="removeMaterialRow(this)">Hapus</button>
                                    </div>
                                @empty
                                    <div class="material-row border border-gray-200 rounded-md p-4 space-y-3">
                                        <div>
                                            <label class="text-sm text-gray-600">Nama Bahan</label>
                                            <input type="text" name="materials[0][name]" 
                                                placeholder="Nama Bahan" 
                                                class="form-control input-field" required>
                                        </div>
                                        <div>
                                            <label class="text-sm text-gray-600">Harga per 10cm</label>
                                            <div class="grid grid-cols-3 gap-2 mt-2">
                                                <input type="number" name="materials[0][length_price]" 
                                                    placeholder="Harga Panjang" 
                                                    class="form-control input-field" step="1" min="0">
                                                <input type="number" name="materials[0][width_price]" 
                                                    placeholder="Harga Lebar" 
                                                    class="form-control input-field" step="1" min="0">
                                                <input type="number" name="materials[0][height_price]" 
                                                    placeholder="Harga Tinggi" 
                                                    class="form-control input-field" step="1" min="0">
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="removeMaterialRow(this)">Hapus</button>
                                    </div>
                                @endforelse
                            </div>
                            <button type="button" class="btn btn-sm btn-secondary mt-2" onclick="addMaterialRow()">+ Tambah Bahan</button>
                            @error('materials.*') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- ---------- FIELD: BAHAN DEFAULT (SELECT) - akan diisi otomatis dari materials ---------- --}}
                        <div class="form-group">
                            <label for="default_bahan">Bahan Default</label>
                            <select id="default_bahan" name="default_bahan" class="form-control input-field">
                                <option value="">-- Pilih Bahan Default --</option>
                                {{-- Options akan diisi otomatis oleh JavaScript dari materials yang diinput --}}
                            </select>
                            @error('default_bahan') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- ---------- FIELD: WARNA KAYU (SELECT + FIELD DINAMIS) ---------- --}}
                        <div class="form-group">
                            <label for="default_color">Warna Kayu</label>

                            {{-- dropdown warna kayu, option akan diisi lewat JS --}}
                            <select
                                id="default_color"
                                name="default_color"
                                class="form-control input-field"
                                data-selected="{{ old('default_color', $product->default_color) }}"
                            >
                                <option value="">-- Pilih Warna Kayu --</option>
                            </select>
                            @error('default_color') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror

                            {{-- baris-baris warna kayu dinamis (prefill dari relasi woodColors) --}}
                            <div id="wood_color_rows" class="space-y-2 mt-3">
                                @forelse ($product->woodColors as $color)
                                    <div class="flex items-center gap-2 color-row">
                                        <input
                                            type="text"
                                            class="form-control input-field color-name"
                                            placeholder="Nama warna kayu"
                                            value="{{ $color->name }}"
                                        >
                                        <input
                                            type="number"
                                            class="form-control input-field color-extra"
                                            placeholder="+ Harga"
                                            min="0"
                                            step="1"
                                            value="{{ $color->pivot->extra_price ?? 0 }}"
                                            data-original-value="{{ $color->pivot->extra_price ?? 0 }}"
                                        >
                                        <button
                                            type="button"
                                            class="btn btn-secondary btn-add-row"
                                            title="Tambah baris warna kayu"
                                        >+</button>
                                        <button
                                            type="button"
                                            class="btn btn-danger btn-remove-row"
                                            title="Hapus baris ini"
                                        >−</button>
                                    </div>
                                @empty
                                    {{-- kalau belum ada, kasih 1 baris kosong --}}
                                    <div class="flex items-center gap-2 color-row">
                                        <input
                                            type="text"
                                            class="form-control input-field color-name"
                                            placeholder="Nama warna kayu"
                                        >
                                        <input
                                            type="number"
                                            class="form-control input-field color-extra"
                                            placeholder="+ Harga"
                                            min="0"
                                            step="1000"
                                        >
                                        <button
                                            type="button"
                                            class="btn btn-secondary btn-add-row"
                                            title="Tambah baris warna kayu"
                                        >+</button>
                                        <button
                                            type="button"
                                            class="btn btn-danger btn-remove-row"
                                            title="Hapus baris ini"
                                        >−</button>
                                    </div>
                                @endforelse
                            </div>

                            {{-- hidden input agar warna kayu ikut terkirim ke server --}}
                            <div id="new_wood_hidden_container"></div>
                        </div>

                        {{-- ---------- FIELD: WARNA ROTAN (SELECT + FIELD DINAMIS) ---------- --}}
                        <div class="form-group">
                            <label for="default_rotan_color">Warna Rotan</label>

                            <select
                                id="default_rotan_color"
                                name="default_rotan_color"
                                class="form-control input-field"
                                data-selected="{{ old('default_rotan_color', $product->default_rotan_color) }}"
                            >
                                <option value="">-- Pilih Warna Rotan --</option>
                            </select>
                            @error('default_rotan_color') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror

                            <div id="rattan_color_rows" class="space-y-2 mt-3">
                                @forelse ($product->rattanColors as $color)
                                    <div class="flex items-center gap-2 color-row">
                                        <input
                                            type="text"
                                            class="form-control input-field color-name"
                                            placeholder="Nama warna rotan"
                                            value="{{ $color->name }}"
                                        >
                                        <input
                                            type="number"
                                            class="form-control input-field color-extra"
                                            placeholder="+ Harga"
                                            min="0"
                                            step="1"
                                            value="{{ $color->pivot->extra_price ?? 0 }}"
                                            data-original-value="{{ $color->pivot->extra_price ?? 0 }}"
                                        >
                                        <button
                                            type="button"
                                            class="btn btn-secondary btn-add-row"
                                            title="Tambah baris warna rotan"
                                        >+</button>
                                        <button
                                            type="button"
                                            class="btn btn-danger btn-remove-row"
                                            title="Hapus baris ini"
                                        >−</button>
                                    </div>
                                @empty
                                    <div class="flex items-center gap-2 color-row">
                                        <input
                                            type="text"
                                            class="form-control input-field color-name"
                                            placeholder="Nama warna rotan"
                                        >
                                        <input
                                            type="number"
                                            class="form-control input-field color-extra"
                                            placeholder="+ Harga"
                                            min="0"
                                            step="1000"
                                        >
                                        <button
                                            type="button"
                                            class="btn btn-secondary btn-add-row"
                                            title="Tambah baris warna rotan"
                                        >+</button>
                                        <button
                                            type="button"
                                            class="btn btn-danger btn-remove-row"
                                            title="Hapus baris ini"
                                        >−</button>
                                    </div>
                                @endforelse
                            </div>

                            <div id="new_rattan_hidden_container"></div>
                        </div>

                    </div>
                </div>
            </div> {{-- end grid 2 kolom --}}
        </form>
    </div>

    {{-- =========================  SCRIPT AUTO SIZE & HARGA  ========================= --}}
    <script>
      (function () {
        const elSize  = document.getElementById('size');
        const elLen   = document.getElementById('default_length');
        const elWid   = document.getElementById('default_width');
        const elHei   = document.getElementById('default_height');
        const elBahan = document.getElementById('default_bahan');
        const elPrice = document.getElementById('price');

        function f(v) {
          const n = parseFloat(v);
          return Number.isFinite(n) ? n : 0;
        }

        // Fungsi untuk mendapatkan harga material dari input admin
        function getMaterialPrices(materialName) {
          if (!materialName) return null;

          // Cari material row yang sesuai dengan nama bahan
          const materialRows = document.querySelectorAll('#materials-container .material-row');
          for (const row of materialRows) {
            const nameInput = row.querySelector('input[name*="[name]"]');
            if (nameInput && nameInput.value.trim() === materialName) {
              const lengthPrice = f(row.querySelector('input[name*="[length_price]"]')?.value || 0);
              const widthPrice = f(row.querySelector('input[name*="[width_price]"]')?.value || 0);
              const heightPrice = f(row.querySelector('input[name*="[height_price]"]')?.value || 0);
              
              // Jika semua harga 0, return null
              if (lengthPrice === 0 && widthPrice === 0 && heightPrice === 0) {
                return null;
              }
              
              return {
                length: lengthPrice || 0,
                width: widthPrice || 0,
                height: heightPrice || 0,
              };
            }
          }
          return null;
        }

        function updateSizeField() {
          const P = Math.round(f(elLen.value));
          const L = Math.round(f(elWid.value));
          const T = Math.round(f(elHei.value));
          if (P > 0 && L > 0 && T > 0) {
            elSize.value = `${P}x${L}x${T} cm`;
          }
        }

        // Simpan harga awal dari database (untuk halaman edit)
        const initialPrice = elPrice.value ? parseFloat(elPrice.value) : null;
        let isInitialLoad = true; // Flag untuk menandai apakah ini adalah load pertama kali
        let userManuallyEditedPrice = false; // Flag untuk menandai apakah user sudah edit harga secara manual
        
        // Set flag setelah halaman selesai load
        setTimeout(() => {
          isInitialLoad = false;
        }, 1000);
        
        // Track apakah harga sudah pernah diubah oleh user secara manual
        elPrice.addEventListener('input', function() {
          if (!isInitialLoad) {
            userManuallyEditedPrice = true;
          }
        });
        
        // Jangan nolkan harga kalau data belum lengkap
        window.computePrice = function () {
          // Jangan hitung harga saat initial load (biarkan harga dari database tetap)
          if (isInitialLoad) {
            return;
          }
          
          // Jika user sudah edit harga secara manual, jangan overwrite
          if (userManuallyEditedPrice) {
            return;
          }
          
          const bahan = elBahan.value || '';
          const P     = f(elLen.value);
          const L     = f(elWid.value);
          const T     = f(elHei.value);

          // Ambil harga material dari input admin (bukan hardcode)
          const materialPrices = getMaterialPrices(bahan);

          // kalau belum lengkap, JANGAN utak-atik harga lama
          if (!materialPrices || !(P > 0 && L > 0 && T > 0)) {
            return;
          }

          // Hitung harga berdasarkan material yang dipilih
          const base = (P / 10) * materialPrices.length + (L / 10) * materialPrices.width + (T / 10) * materialPrices.height;

          const woodSelect   = document.getElementById('default_color');
          const rattanSelect = document.getElementById('default_rotan_color');

          let extraWood   = 0;
          let extraRattan = 0;

          if (woodSelect && woodSelect.selectedIndex > 0) {
            const woodOpt = woodSelect.options[woodSelect.selectedIndex];
            if (woodOpt && woodOpt.dataset.extra) {
              extraWood = f(woodOpt.dataset.extra);
            }
          }

          if (rattanSelect && rattanSelect.selectedIndex > 0) {
            const rattanOpt = rattanSelect.options[rattanSelect.selectedIndex];
            if (rattanOpt && rattanOpt.dataset.extra) {
              extraRattan = f(rattanOpt.dataset.extra);
            }
          }

          const total = Math.max(0, Math.round(base + extraWood + extraRattan));
          elPrice.value = total;
        };

        ['input', 'change'].forEach(evt => {
          elLen.addEventListener(evt, () => { 
            updateSizeField(); 
            // Reset flag saat user mengubah dimensi, sehingga harga akan di-update
            userManuallyEditedPrice = false;
            window.computePrice(); 
          });
          elWid.addEventListener(evt, () => { 
            updateSizeField(); 
            // Reset flag saat user mengubah dimensi, sehingga harga akan di-update
            userManuallyEditedPrice = false;
            window.computePrice(); 
          });
          elHei.addEventListener(evt, () => { 
            updateSizeField(); 
            // Reset flag saat user mengubah dimensi, sehingga harga akan di-update
            userManuallyEditedPrice = false;
            window.computePrice(); 
          });
        });

        if (elBahan) {
          elBahan.addEventListener('change', () => {
            // Reset flag saat user mengubah bahan, sehingga harga akan di-update
            userManuallyEditedPrice = false;
            window.computePrice();
          });
        }

        // Objek untuk menyimpan flag isRebuildingDropdown untuk setiap color group
        const rebuildingFlags = {
          'default_color': false,
          'default_rotan_color': false
        };
        
        document.getElementById('default_color')
          ?.addEventListener('change', () => {
            // Hanya update harga jika perubahan dropdown bukan dari rebuildFromRows()
            if (!rebuildingFlags['default_color']) {
              // Reset flag saat user mengubah warna kayu, sehingga harga akan di-update
              userManuallyEditedPrice = false;
              window.computePrice();
            }
          });
        document.getElementById('default_rotan_color')
          ?.addEventListener('change', () => {
            // Hanya update harga jika perubahan dropdown bukan dari rebuildFromRows()
            if (!rebuildingFlags['default_rotan_color']) {
              // Reset flag saat user mengubah warna rotan, sehingga harga akan di-update
              userManuallyEditedPrice = false;
              window.computePrice();
            }
          });

        // inisialisasi: cukup size saja, harga biarkan pakai nilai dari DB
        updateSizeField();
      })();
    </script>

    {{-- =========================  SCRIPT WARNA DINAMIS (KAYU & ROTAN)  ========================= --}}
    <script>
      (function () {

        // Objek untuk menyimpan flag isRebuildingDropdown untuk setiap color group
        const rebuildingFlags = {
          'default_color': false,
          'default_rotan_color': false
        };

        // Flag untuk menandai apakah ini adalah load pertama kali (untuk mencegah computePrice dipanggil saat initial load)
        // Variabel ini dideklarasikan di script block sebelumnya, tapi kita buat fallback di sini untuk akses dari setupColorGroup
        // Karena setupColorGroup berada di IIFE yang berbeda, kita perlu deklarasi lokal
        let isInitialLoad = true;
        
        // Set flag setelah halaman selesai load (sinkronkan dengan script block sebelumnya)
        setTimeout(() => {
          isInitialLoad = false;
        }, 1000);

        // Simpan referensi ke fungsi rebuildFromRows untuk setiap color group
        const colorGroupRebuilders = [];

        function setupColorGroup(cfg) {
          const select     = document.getElementById(cfg.selectId);
          const rowsParent = document.getElementById(cfg.rowsId);
          const hiddenWrap = document.getElementById(cfg.hiddenId);

          if (!select || !rowsParent || !hiddenWrap) return;

          const defaultSelected = select.dataset.selected || '';
          let rebuildTimeout = null;

          function rebuildFromRows() {
            // Set flag untuk mencegah computePrice() terpanggil saat rebuild
            rebuildingFlags[cfg.selectId] = true;
            
            const prevVal = select.value; // simpan pilihan sekarang (kalau sudah pilih)

            // hapus semua option kecuali placeholder pertama
            while (select.options.length > 1) {
              select.remove(1);
            }

            hiddenWrap.innerHTML = '';

            const rows = rowsParent.querySelectorAll('.color-row');
            let colorIndex = 0;
            
            // Pastikan semua nilai input ter-set dengan benar sebelum membaca
            rows.forEach(row => {
              const nameInput = row.querySelector('.color-name');
              const extraInput = row.querySelector('.color-extra');
              
              // Pastikan nama ter-set dari value atau atribut
              if (nameInput) {
                // Prioritas: baca dari .value (user input), lalu dari atribut value (Blade template)
                if (!nameInput.value || nameInput.value.trim() === '') {
                  const nameAttr = nameInput.getAttribute('value');
                  if (nameAttr && nameAttr.trim() !== '') {
                    nameInput.value = nameAttr;
                    nameInput.defaultValue = nameAttr;
                  }
                } else {
                  // Jika sudah ada value, pastikan defaultValue juga ter-set
                  nameInput.defaultValue = nameInput.value;
                }
              }
              
              // Pastikan extra_price ter-set dari value atau atribut
              if (extraInput) {
                // Prioritas: baca dari .value (user input), lalu dari atribut value atau data-original-value (Blade template)
                if (!extraInput.value || extraInput.value === '0' || extraInput.value === '') {
                  const attrValue = extraInput.getAttribute('value');
                  const originalValue = extraInput.getAttribute('data-original-value');
                  
                  if (attrValue !== null && attrValue !== undefined && attrValue !== '' && attrValue !== '0') {
                    extraInput.value = attrValue;
                    extraInput.defaultValue = attrValue;
                  } else if (originalValue !== null && originalValue !== undefined && originalValue !== '' && originalValue !== '0') {
                    extraInput.value = originalValue;
                    extraInput.defaultValue = originalValue;
                  }
                } else {
                  // Jika sudah ada value, pastikan defaultValue juga ter-set
                  extraInput.defaultValue = extraInput.value;
                }
              }
            });
            
            rows.forEach(row => {
              const nameInput  = row.querySelector('.color-name');
              const extraInput = row.querySelector('.color-extra');

              // BACA NAMA
              // Pastikan nilai ter-set dari atribut value jika .value masih kosong
              let name = '';
              if (nameInput) {
                // Baca dari .value terlebih dahulu (user input atau nilai yang sudah ter-set)
                name = (nameInput.value || '').trim();
                
                // Jika kosong, baca dari atribut value (nilai dari Blade template)
                if (!name) {
                  const nameAttr = nameInput.getAttribute('value');
                  if (nameAttr && nameAttr.trim() !== '') {
                    name = nameAttr.trim();
                    nameInput.value = name; // Set ke .value juga
                    nameInput.defaultValue = name; // Set juga defaultValue
                  }
                }
              }

              // BACA EXTRA PRICE DENGAN AMAN
              let extra = 0;
              if (extraInput) {
                // Prioritas 1: baca dari .value (current input dari user, termasuk saat user mengetik)
                // Ini adalah nilai yang paling penting karena mencerminkan perubahan user
                let raw = '';
                
                let currentValue = extraInput.value;
                
                // Jika kosong atau '0', coba baca dari atribut value atau data-original-value
                if (!currentValue || currentValue === '' || currentValue === '0') {
                  const attrValue = extraInput.getAttribute('value');
                  const originalValue = extraInput.getAttribute('data-original-value');
                  
                  if (attrValue !== null && attrValue !== undefined && attrValue !== '' && attrValue !== '0') {
                    raw = String(attrValue).trim();
                    extraInput.value = raw; // Set ke .value juga
                  } else if (originalValue !== null && originalValue !== undefined && originalValue !== '' && originalValue !== '0') {
                    raw = String(originalValue).trim();
                    extraInput.value = raw; // Set ke .value juga
                  } else {
                    raw = '0';
                  }
                } else {
                  // Jika user sudah mengubah nilai, gunakan nilai tersebut
                  raw = String(currentValue).trim();
                }
                
                const parsed = parseFloat(raw);
                // Pastikan extra_price selalu berupa angka valid, bukan NaN atau Infinity
                extra = (!isNaN(parsed) && isFinite(parsed) && parsed >= 0) ? parsed : 0;
              }

              if (!name) {
                console.log(`[${cfg.rowsId}] Skipping row ${colorIndex}: name is empty`);
                return;
              }

              console.log(`[${cfg.rowsId}] Adding option: name="${name}", extra="${extra}"`);
              
              const opt = document.createElement('option');
              opt.value        = name;
              opt.textContent  = name;
              opt.dataset.extra = String(extra);
              select.appendChild(opt);

              // Buat hidden inputs dengan INDEX EKSPLISIT agar PHP mem-parse sebagai pasangan
              const nameHidden = document.createElement('input');
              nameHidden.type = 'hidden';
              nameHidden.name = `${cfg.hiddenName}[${colorIndex}][name]`;
              nameHidden.value = name;
              
              const extraHidden = document.createElement('input');
              extraHidden.type = 'hidden';
              extraHidden.name = `${cfg.hiddenName}[${colorIndex}][extra_price]`;
              extraHidden.value = String(extra); // Pastikan string
              
              hiddenWrap.appendChild(nameHidden);
              hiddenWrap.appendChild(extraHidden);
              
              // Debug: log untuk memastikan hidden inputs ter-generate
              console.log(`[${cfg.rowsId}] Generated hidden input: ${nameHidden.name} = ${nameHidden.value}, ${extraHidden.name} = ${extraHidden.value}`);
              
              colorIndex++;
            });

            // logika pilih ulang - PASTIKAN pilihan tetap terkunci pada warna yang sama
            let nextVal = prevVal;

            const hasOption = (val) =>
              !!val && Array.from(select.options).some(o => o.value === val);

            // Hanya set defaultSelected jika tidak ada pilihan sebelumnya
            // Ini memastikan pilihan user tidak di-overwrite
            if (!nextVal && defaultSelected && hasOption(defaultSelected)) {
              nextVal = defaultSelected;
            }

            // Set nilai dropdown HANYA jika flag rebuildingFlags sudah di-set
            // Ini mencegah event change terpicu saat rebuild
            if (rebuildingFlags[cfg.selectId]) {
              if (hasOption(nextVal)) {
                select.value = nextVal;
              } else {
                select.value = '';
              }
              // JANGAN reset flag di sini, biarkan event listener yang reset
            } else {
              // Jika flag tidak di-set, berarti ini bukan dari rebuild yang dipicu oleh perubahan harga
              // Set nilai dropdown seperti biasa
              if (hasOption(nextVal)) {
                select.value = nextVal;
              } else {
                select.value = '';
              }
              // Reset flag setelah rebuild selesai
              setTimeout(() => {
                rebuildingFlags[cfg.selectId] = false;
              }, 100);
            }

            // DI SINI TIDAK ADA dispatchEvent('change') dan TIDAK memanggil computePrice()
            // supaya harga dari DB tidak diubah saat halaman pertama kali load
          }

          
          // Event listener untuk rebuild dropdown (dengan debounce)
          // Catatan: rebuildTimeout sudah dideklarasikan di awal fungsi setupColorGroup
          rowsParent.addEventListener('input', function(e) {
            // Skip jika ini adalah color-extra (ditangani oleh event listener khusus di bawah)
            if (e.target.classList.contains('color-extra')) {
              return;
            }
            
            // Clear timeout sebelumnya
            clearTimeout(rebuildTimeout);
            
            // Tunggu user selesai mengetik (500ms tanpa input baru) untuk rebuild dropdown
            rebuildTimeout = setTimeout(() => {
              rebuildFromRows();
            }, 500);
          });
          
          // Event listener khusus untuk color-extra: update dropdown dan harga produk saat harga warna berubah
          let colorExtraTimeout = null;
          rowsParent.addEventListener('input', function(e) {
            if (e.target.classList.contains('color-extra')) {
              // Clear timeout sebelumnya
              clearTimeout(colorExtraTimeout);
              
              // Tunggu user selesai mengetik (500ms) sebelum rebuild dropdown
              colorExtraTimeout = setTimeout(() => {
                // Simpan nilai dropdown yang sedang terpilih SEBELUM rebuild
                const currentSelectedValue = select.value;
                
                // Set flag rebuildingFlags SEBELUM rebuild untuk mencegah event change terpicu
                rebuildingFlags[cfg.selectId] = true;
                
                // Rebuild dropdown untuk update dataset.extra dengan harga baru
                // rebuildFromRows() akan otomatis mengembalikan pilihan ke prevVal (currentSelectedValue)
                rebuildFromRows();
                
                // Pastikan pilihan dropdown tetap terkunci pada warna yang sama
                // Ini dilakukan SETELAH rebuild untuk memastikan option sudah ter-update dengan harga baru
                if (currentSelectedValue) {
                  // Tunggu sedikit untuk memastikan rebuildFromRows() sudah selesai
                  setTimeout(() => {
                    const optionExists = Array.from(select.options).some(opt => opt.value === currentSelectedValue);
                    if (optionExists) {
                      // Set nilai dropdown tanpa memicu event change (karena flag sudah di-set)
                      // Ini memastikan pilihan tetap terkunci pada warna yang sama
                      select.value = currentSelectedValue;
                      
                      // Update harga produk berdasarkan warna yang sedang dipilih dengan harga baru
                      // Harga akan otomatis menyesuaikan dengan harga warna yang baru
                      if (window.computePrice && !isInitialLoad) {
                        // Panggil computePrice() untuk update harga
                        // computePrice() akan menangani userManuallyEditedPrice sendiri
                        window.computePrice();
                      }
                    }
                    
                    // Reset flag setelah semua operasi selesai
                    // Ini memungkinkan event change terpicu lagi untuk perubahan selanjutnya
                    rebuildingFlags[cfg.selectId] = false;
                  }, 100);
                } else {
                  // Jika tidak ada yang terpilih, reset flag
                  rebuildingFlags[cfg.selectId] = false;
                }
              }, 500);
            }
          });

          rowsParent.addEventListener('click', function (e) {
            const addBtn = e.target.closest('.btn-add-row');
            const delBtn = e.target.closest('.btn-remove-row');

            if (addBtn) {
              const firstRow = rowsParent.querySelector('.color-row');
              if (!firstRow) return;
              const clone = firstRow.cloneNode(true);
              // Kosongkan nilai input di clone dan hapus SEMUA atribut yang mungkin menyimpan nilai
              clone.querySelectorAll('input').forEach(inp => {
                inp.value = '';
                inp.defaultValue = '';
                // Hapus semua atribut yang mungkin menyimpan nilai
                inp.removeAttribute('value');
                inp.removeAttribute('data-original-value');
                // Pastikan input benar-benar kosong
                if (inp.classList.contains('color-extra')) {
                  inp.value = '';
                }
                if (inp.classList.contains('color-name')) {
                  inp.value = '';
                }
              });
              rowsParent.appendChild(clone);
              rebuildFromRows();
              return;
            }

            if (delBtn) {
              const row = delBtn.closest('.color-row');
              if (!row) return;
              row.remove();
              rebuildFromRows();
              return;
            }
          });

          // inisialisasi awal: kasih delay supaya value dari Blade sudah kebaca
          // Pastikan input sudah ter-render dengan benar sebelum membaca nilai
          function initializeValues() {
            // Baca nilai dari semua input color-name dan color-extra yang sudah ada
            rowsParent.querySelectorAll('.color-row').forEach(row => {
              const nameInput = row.querySelector('.color-name');
              const extraInput = row.querySelector('.color-extra');
              
              // Pastikan nama ter-set dari atribut value (dari Blade template)
              if (nameInput) {
                // Baca dari atribut value terlebih dahulu (nilai dari Blade)
                const nameAttr = nameInput.getAttribute('value');
                if (nameAttr && nameAttr.trim() !== '') {
                  nameInput.value = nameAttr;
                  nameInput.defaultValue = nameAttr;
                } else if (!nameInput.value || nameInput.value.trim() === '') {
                  // Jika atribut tidak ada, coba baca dari value yang sudah ter-set
                  const nameValue = nameInput.value || '';
                  if (nameValue) {
                    nameInput.value = nameValue;
                  }
                }
              }
              
              // Pastikan extra_price ter-set dari atribut value atau data-original-value
              if (extraInput) {
                // Baca dari atribut value terlebih dahulu (nilai dari Blade)
                const attrValue = extraInput.getAttribute('value');
                const originalValue = extraInput.getAttribute('data-original-value');
                
                // Set nilai ke input dari atribut value atau data-original-value
                if (attrValue !== null && attrValue !== undefined && attrValue !== '' && attrValue !== '0') {
                  extraInput.value = attrValue;
                  extraInput.defaultValue = attrValue;
                } else if (originalValue !== null && originalValue !== undefined && originalValue !== '' && originalValue !== '0') {
                  extraInput.value = originalValue;
                  extraInput.defaultValue = originalValue;
                } else if (!extraInput.value || extraInput.value === '0' || extraInput.value === '') {
                  // Jika semua kosong, set ke 0
                  extraInput.value = '0';
                  extraInput.defaultValue = '0';
                }
              }
            });
            
            // Rebuild setelah nilai ter-set dengan delay lebih lama
            setTimeout(() => {
              rebuildFromRows();
            }, 200);
          }

          // Tunggu DOM siap dan pastikan semua nilai ter-set dengan benar
          function initColorGroup() {
            console.log(`[${cfg.rowsId}] Initializing color group...`);
            
            // Pastikan semua input ter-render dengan nilai dari Blade
            const rows = rowsParent.querySelectorAll('.color-row');
            console.log(`[${cfg.rowsId}] Found ${rows.length} color rows`);
            
            // Set nilai dari atribut Blade ke .value input
            rows.forEach((row, index) => {
              const nameInput = row.querySelector('.color-name');
              const extraInput = row.querySelector('.color-extra');
              
              // Set nilai nama dari atribut value (dari Blade template)
              if (nameInput) {
                const nameAttr = nameInput.getAttribute('value');
                // SELALU set dari atribut jika ada, karena ini adalah nilai dari database
                if (nameAttr && nameAttr.trim() !== '') {
                  nameInput.value = nameAttr;
                  nameInput.defaultValue = nameAttr;
                  console.log(`[${cfg.rowsId}] Row ${index}: Set name to "${nameAttr}" from attribute`);
                }
              }
              
              // Set nilai harga dari atribut value atau data-original-value (dari Blade template)
              if (extraInput) {
                const attrValue = extraInput.getAttribute('value');
                const originalValue = extraInput.getAttribute('data-original-value');
                
                // SELALU set dari atribut jika ada, karena ini adalah nilai dari database
                if (attrValue !== null && attrValue !== undefined && attrValue !== '' && attrValue !== '0') {
                  extraInput.value = attrValue;
                  extraInput.defaultValue = attrValue;
                  console.log(`[${cfg.rowsId}] Row ${index}: Set price to "${attrValue}" from attrValue`);
                } else if (originalValue !== null && originalValue !== undefined && originalValue !== '' && originalValue !== '0') {
                  extraInput.value = originalValue;
                  extraInput.defaultValue = originalValue;
                  console.log(`[${cfg.rowsId}] Row ${index}: Set price to "${originalValue}" from originalValue`);
                }
              }
            });
            
            // Rebuild dropdown SETELAH nilai ter-set untuk populate dropdown dengan warna yang sudah ada
            console.log(`[${cfg.rowsId}] Calling rebuildFromRows() after setting values...`);
            rebuildFromRows();
          }
          
          // Tunggu DOM siap dan pastikan semua nilai ter-set dengan benar
          // Gunakan delay yang lebih lama untuk memastikan DOM sudah ter-render dengan benar
          if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
              setTimeout(() => {
                initColorGroup();
              }, 300);
            });
          } else {
            setTimeout(() => {
              initColorGroup();
            }, 300);
          }

          // Simpan referensi ke fungsi rebuildFromRows untuk color group ini
          colorGroupRebuilders.push({
            rowsParent: rowsParent,
            hiddenWrap: hiddenWrap,
            rebuildFromRows: rebuildFromRows
          });
        }
        
        // Pastikan hidden input terisi saat form submit (UNTUK SEMUA COLOR GROUPS)
        // Event listener ini dipasang SETELAH semua setupColorGroup dipanggil
        setTimeout(() => {
          const form = document.getElementById('product-edit-form');
          if (form && colorGroupRebuilders.length > 0) {
            // Pasang event listener submit yang akan memanggil rebuildFromRows untuk SEMUA color groups
            form.addEventListener('submit', function(e) {
              console.log('[Form Submit] Rebuilding hidden inputs for all color groups...');
              
              // Rebuild untuk SEMUA color groups sebelum submit
              colorGroupRebuilders.forEach((group, index) => {
                console.log(`[Form Submit] Processing color group ${index + 1}...`);
                
                // Pastikan semua nilai input ter-set dengan benar sebelum rebuild
                group.rowsParent.querySelectorAll('.color-row').forEach(row => {
                  const nameInput = row.querySelector('.color-name');
                  const extraInput = row.querySelector('.color-extra');
                  
                  // Pastikan nama ter-set dari value atau atribut
                  if (nameInput && (!nameInput.value || nameInput.value.trim() === '')) {
                    const nameAttr = nameInput.getAttribute('value');
                    if (nameAttr && nameAttr.trim() !== '') {
                      nameInput.value = nameAttr;
                    }
                  }
                  
                  // Pastikan extra_price ter-set dari value atau atribut
                  if (extraInput && (!extraInput.value || extraInput.value === '0' || extraInput.value === '')) {
                    const attrValue = extraInput.getAttribute('value');
                    const originalValue = extraInput.getAttribute('data-original-value');
                    
                    if (attrValue !== null && attrValue !== undefined && attrValue !== '' && attrValue !== '0') {
                      extraInput.value = attrValue;
                    } else if (originalValue !== null && originalValue !== undefined && originalValue !== '' && originalValue !== '0') {
                      extraInput.value = originalValue;
                    }
                  }
                });
                
                // Rebuild sekali lagi sebelum submit untuk memastikan hidden input terisi dengan nilai terbaru
                group.rebuildFromRows();
                
                // Debug: cek hidden inputs yang ter-generate
                if (group.hiddenWrap) {
                  const hiddenInputs = group.hiddenWrap.querySelectorAll('input[type="hidden"]');
                  console.log(`[Form Submit] Color group ${index + 1} generated ${hiddenInputs.length} hidden inputs`);
                  hiddenInputs.forEach((input, idx) => {
                    console.log(`  [${idx}] ${input.name} = ${input.value}`);
                  });
                }
              });
            });
          }
        }, 200); // Delay untuk memastikan semua setupColorGroup sudah dipanggil

        // setup untuk warna kayu
        setupColorGroup({
          selectId:  'default_color',
          rowsId:    'wood_color_rows',
          hiddenId:  'new_wood_hidden_container',
          hiddenName:'new_wood_colors'
        });

        // setup untuk warna rotan
        setupColorGroup({
          selectId:  'default_rotan_color',
          rowsId:    'rattan_color_rows',
          hiddenId:  'new_rattan_hidden_container',
          hiddenName:'new_rattan_colors'
        });


      })();
    </script>

    {{-- =========================  SCRIPT MATERIALS DINAMIS  ========================= --}}
    <script>
      let materialIndex = {{ max($product->variants->where('type', 'material')->count(), 1) }};

      // Simpan nilai default_bahan dari database (dari Blade template)
      const defaultBahanFromDB = @json(old('default_bahan', $product->default_bahan ?? ''));

      // Fungsi untuk update dropdown Bahan Default dari materials yang diinput
      function updateBahanDefault() {
        const bahanSelect = document.getElementById('default_bahan');
        if (!bahanSelect) return;

        // Simpan nilai yang sedang dipilih (jika ada)
        const currentValue = bahanSelect.value;

        // Hapus semua option kecuali placeholder pertama
        while (bahanSelect.options.length > 1) {
          bahanSelect.remove(1);
        }

        // Ambil semua materials yang sudah diinput
        const materialRows = document.querySelectorAll('#materials-container .material-row');
        materialRows.forEach(row => {
          const nameInput = row.querySelector('input[name*="[name]"]');
          if (nameInput && nameInput.value.trim()) {
            const materialName = nameInput.value.trim();
            const option = document.createElement('option');
            option.value = materialName;
            option.textContent = materialName;
            bahanSelect.appendChild(option);
          }
        });

        // Set nilai yang dipilih: prioritas currentValue (jika ada dan masih valid), lalu defaultBahanFromDB
        let valueToSet = '';
        if (currentValue) {
          // Jika ada nilai yang sedang dipilih, cek apakah masih ada di options
          const stillExists = Array.from(bahanSelect.options).some(opt => opt.value === currentValue);
          if (stillExists) {
            valueToSet = currentValue;
          }
        }
        
        // Jika tidak ada currentValue yang valid, gunakan defaultBahanFromDB
        if (!valueToSet && defaultBahanFromDB) {
          const exists = Array.from(bahanSelect.options).some(opt => opt.value === defaultBahanFromDB);
          if (exists) {
            valueToSet = defaultBahanFromDB;
          }
        }

        // Set nilai yang dipilih
        if (valueToSet) {
          bahanSelect.value = valueToSet;
        } else {
          // Jika tidak ada nilai yang valid, set ke placeholder
          bahanSelect.value = '';
        }

        // Trigger change event untuk update harga
        bahanSelect.dispatchEvent(new Event('change'));
      }

      function addMaterialRow() {
        const container = document.getElementById('materials-container');
        const row = document.createElement('div');
        row.className = 'material-row border border-gray-200 rounded-md p-4 space-y-3';
        row.innerHTML = `
          <div>
            <label class="text-sm text-gray-600">Nama Bahan</label>
            <input type="text" name="materials[${materialIndex}][name]" placeholder="Nama Bahan" 
              class="form-control input-field material-name-input" required>
          </div>
          <div>
            <label class="text-sm text-gray-600">Harga per 10cm</label>
            <div class="grid grid-cols-3 gap-2 mt-2">
              <input type="number" name="materials[${materialIndex}][length_price]" placeholder="Harga Panjang" 
                class="form-control input-field material-length-price" step="1" min="0">
              <input type="number" name="materials[${materialIndex}][width_price]" placeholder="Harga Lebar" 
                class="form-control input-field material-width-price" step="1" min="0">
              <input type="number" name="materials[${materialIndex}][height_price]" placeholder="Harga Tinggi" 
                class="form-control input-field material-height-price" step="1" min="0">
            </div>
          </div>
          <button type="button" class="btn btn-sm btn-danger" onclick="removeMaterialRow(this)">Hapus</button>
        `;
        container.appendChild(row);
        
        // Pasang event listener untuk update dropdown dan harga saat material berubah
        row.querySelector('.material-name-input').addEventListener('input', function() {
          updateBahanDefault();
        });
        row.querySelectorAll('.material-length-price, .material-width-price, .material-height-price').forEach(input => {
          input.addEventListener('input', function() {
            if (window.computePrice) window.computePrice();
          });
        });
        
        materialIndex++;
      }

      function removeMaterialRow(btn) {
        const row = btn.closest('.material-row');
        if (row) {
          row.remove();
          updateBahanDefault();
        }
      }

      // Update dropdown saat halaman dimuat
      document.addEventListener('DOMContentLoaded', function() {
        // Pasang event listener untuk semua material name input yang sudah ada
        document.querySelectorAll('#materials-container .material-row input[name*="[name]"]').forEach(input => {
          input.classList.add('material-name-input');
          input.addEventListener('input', updateBahanDefault);
        });
        
        // Pasang event listener untuk semua material price input yang sudah ada
        document.querySelectorAll('#materials-container .material-row input[name*="[length_price]"]').forEach(input => {
          input.classList.add('material-length-price');
          input.addEventListener('input', function() {
            if (window.computePrice) window.computePrice();
          });
        });
        document.querySelectorAll('#materials-container .material-row input[name*="[width_price]"]').forEach(input => {
          input.classList.add('material-width-price');
          input.addEventListener('input', function() {
            if (window.computePrice) window.computePrice();
          });
        });
        document.querySelectorAll('#materials-container .material-row input[name*="[height_price]"]').forEach(input => {
          input.classList.add('material-height-price');
          input.addEventListener('input', function() {
            if (window.computePrice) window.computePrice();
          });
        });
        
        // Update dropdown awal setelah semua event listener terpasang
        // Gunakan delay lebih lama untuk memastikan semua input sudah ter-render
        // Panggil beberapa kali untuk memastikan dropdown terisi dengan benar
        setTimeout(updateBahanDefault, 100);
        setTimeout(updateBahanDefault, 300);
        setTimeout(updateBahanDefault, 500);
      });
    </script>

@endsection
