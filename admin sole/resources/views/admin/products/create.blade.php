{{-- // kode ini untuk: memberitahu Blade bahwa view ini "turunan" dari layout admin utama --}}
{{-- // Tag: @extends (Blade Directive) --}}
{{-- //   - Fungsi: Memakai file layout sebagai kerangka (header/sidebar/footer). --}}
{{-- //   - Atribut: 'layouts.admin' → path view layout di resources/views/layouts/admin.blade.php --}}
@extends('layouts.admin')

{{-- // kode ini untuk: mengisi bagian "page" di layout dengan judul halaman --}}
{{-- // Tag: @section (Blade Directive) --}}
{{-- //   - Fungsi: Mengisi slot/section bernama "page" yang didefinisikan di layout. --}}
{{-- //   - Isi: "Tambah Produk" sebagai judul yang nanti dipakai di layout (misal di <title> atau header) --}}
@section('page')
    Tambah Produk
@endsection

{{-- // kode ini untuk: mengisi bagian "content" di layout dengan isi utama halaman --}}
@section('content')

    {{-- // =========================  WRAPPER HALAMAN  ========================= --}}
    {{-- // Tag: <div> (HTML), class="container" (utilitas CSS proyek) --}}
    {{-- //   - Fungsi: Pembungkus besar konten halaman agar lebar & spacing konsisten. --}}
    <div class="container">

        {{-- // =========================  F O R M   T A M B A H  ========================= --}}
        {{-- // Tag: <form> (HTML) --}}
        {{-- //   - Fungsi: Mengirim data produk baru ke server untuk disimpan. --}}
        {{-- //   - Atribut: --}}
        {{-- //       class="space-y-5"           → jarak vertikal antar blok input agar rapi --}}
        {{-- //       action="{{ route('products.store') }}" → URL tujuan submit (rute bernama "products.store") --}}
        {{-- //       method="POST"               → metode HTTP; HTML form hanya punya GET/POST (create = POST) --}}
        {{-- //       enctype="multipart/form-data" → WAJIB bila ada upload file (gambar) --}}
        <form class="space-y-5" action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">

            {{-- // Tag: @csrf (Blade) --}}
            {{-- //   - Fungsi: Token keamanan Laravel untuk mencegah CSRF (WAJIB pada form POST/PUT/DELETE) --}}
            @csrf

            {{-- // =========================  B A R I S  T O M B O L   A K S I  ========================= --}}
            <div class="form-group mb-10 flex items-center justify-end flex-row gap-3">
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Produk</button>
            </div>

            {{-- // =========================  G R I D   D U A   K O L O M  ========================= --}}
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
                            value="{{ old('title') }}"
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
                            value="{{ old('size') }}"
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
                            value="{{ old('price') }}"
                            min="0"
                            step="1"
                            required
                        >
                        @error('price') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- ---------- FIELD: GAMBAR UTAMA (UPLOAD) ---------- --}}
                    <div class="form-group">
                        <label for="display_image">Gambar Produk</label>
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
                        >{{ old('description') }}</textarea>
                        @error('description') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- ===================== KOLOM KANAN (UKURAN DETAIL & PILIHAN) ===================== --}}
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
                            value="{{ old('default_length') }}"
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
                            value="{{ old('default_width') }}"
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
                            value="{{ old('default_height') }}"
                            min="0"
                            step="0.01"
                        >
                        @error('default_height') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- ---------- FIELD: BAHAN/MATERIAL (DINAMIS) ---------- --}}
                    <div class="form-group">
                        <label>Bahan/Material</label>
                        <div id="materials-container" class="space-y-4">
                            <div class="material-row border border-gray-200 rounded-md p-4 space-y-3">
                                <div>
                                    <label class="text-sm text-gray-600">Nama Bahan</label>
                                    <input type="text" name="materials[0][name]" placeholder="Nama Bahan" 
                                        class="form-control input-field" required>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600">Harga per 10cm</label>
                                    <div class="grid grid-cols-3 gap-2 mt-2">
                                        <input type="number" name="materials[0][length_price]" placeholder="Harga Panjang" 
                                            class="form-control input-field" step="1" min="0">
                                        <input type="number" name="materials[0][width_price]" placeholder="Harga Lebar" 
                                            class="form-control input-field" step="1" min="0">
                                        <input type="number" name="materials[0][height_price]" placeholder="Harga Tinggi" 
                                            class="form-control input-field" step="1" min="0">
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-danger" onclick="removeMaterialRow(this)">Hapus</button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-secondary mt-2" onclick="addMaterialRow()">+ Tambah Bahan</button>
                        @error('materials.*') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- ---------- FIELD: BAHAN DEFAULT (SELECT) - untuk backward compatibility ---------- --}}
                    <div class="form-group">
                        <label for="default_bahan">Bahan Default</label>
                        <select id="default_bahan" name="default_bahan" class="form-control input-field">
                            <option value="">-- Pilih Bahan Default --</option>
                            <option value="Kayu Jati" {{ old('default_bahan')=='Kayu Jati' ? 'selected' : '' }}>Kayu Jati</option>
                            <option value="Kayu Jati & Rotan" {{ old('default_bahan')=='Kayu Jati & Rotan' ? 'selected' : '' }}>Kayu Jati & Rotan</option>
                        </select>
                        @error('default_bahan') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- ---------- FIELD: WARNA KAYU (SELECT + FIELD DINAMIS) ---------- --}}
                    <div class="form-group">
                        <label for="default_color">Warna Kayu</label>

                        {{-- dropdown warna kayu (isi di-generate dari field di bawah lewat JS) --}}
                        <select id="default_color" name="default_color" class="form-control input-field">
                            <option value="">-- Pilih Warna Kayu --</option>
                        </select>
                        @error('default_color') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror

                        {{-- baris-baris warna kayu dinamis --}}
                        <div id="wood_color_rows" class="space-y-2 mt-3">
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
                        </div>

                        {{-- hidden input agar warna kayu baru ikut terkirim ke server --}}
                        <div id="new_wood_hidden_container"></div>
                    </div>

                    {{-- ---------- FIELD: WARNA ROTAN (SELECT + FIELD DINAMIS) ---------- --}}
                    <div class="form-group">
                        <label for="default_rotan_color">Warna Rotan</label>

                        {{-- dropdown warna rotan (isi di-generate dari field di bawah lewat JS) --}}
                        <select id="default_rotan_color" name="default_rotan_color" class="form-control input-field">
                            <option value="">-- Pilih Warna Rotan --</option>
                        </select>
                        @error('default_rotan_color') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror

                        {{-- baris-baris warna rotan dinamis --}}
                        <div id="rattan_color_rows" class="space-y-2 mt-3">
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
                        </div>

                        {{-- hidden input agar warna rotan baru ikut terkirim ke server --}}
                        <div id="new_rattan_hidden_container"></div>
                    </div>

                </div>
            </div> {{-- // Selesai grid 2 kolom --}}
        </form> {{-- // Selesai form --}}
    </div> {{-- // Selesai kontainer --}}

    {{-- 
      // =========================  S C R I P T  A U T O  B I N D I N G  =========================
      // 1) Auto-format field "Ukuran" (#size) dari P/L/T
      // 2) Hitung Harga = base (material + dimensi) + extra warna kayu + extra warna rotan
      //    → extra diambil dari data-extra di <option> yang dibuat dari input admin
    --}}
    <script>
      (function () {
        const elSize      = document.getElementById('size');
        const elLen       = document.getElementById('default_length');
        const elWid       = document.getElementById('default_width');
        const elHei       = document.getElementById('default_height');
        const elBahan     = document.getElementById('default_bahan');
        const elPrice     = document.getElementById('price');

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

        // Export computePrice ke window agar bisa dipanggil dari script lain
        window.computePrice = function() {
          const bahan = elBahan.value || '';
          const P     = f(elLen.value);
          const L     = f(elWid.value);
          const T     = f(elHei.value);

          // Ambil harga material dari input admin (bukan hardcode)
          const materialPrices = getMaterialPrices(bahan);
          
          if (!materialPrices || !(P > 0 && L > 0 && T > 0)) {
            return;
          }

          // Hitung harga berdasarkan material yang dipilih
          const base = (P / 10) * materialPrices.length + (L / 10) * materialPrices.width + (T / 10) * materialPrices.height;

          // extra harga dari warna kayu & rotan (diambil dari data-extra pada option terpilih)
          const woodSelect   = document.getElementById('default_color');
          const rattanSelect = document.getElementById('default_rotan_color');

          const woodOpt   = woodSelect ? woodSelect.options[woodSelect.selectedIndex] : null;
          const rattanOpt = rattanSelect ? rattanSelect.options[rattanSelect.selectedIndex] : null;

          const extraWood   = woodOpt   ? f(woodOpt.dataset.extra)   : 0;
          const extraRattan = rattanOpt ? f(rattanOpt.dataset.extra) : 0;

          const total = Math.max(0, Math.round(base + extraWood + extraRattan));
          elPrice.value = total;
        };

        ['input', 'change'].forEach(evt => {
          elLen.addEventListener(evt, () => { updateSizeField(); computePrice(); });
          elWid.addEventListener(evt, () => { updateSizeField(); computePrice(); });
          elHei.addEventListener(evt, () => { updateSizeField(); computePrice(); });
        });

        if (elBahan) elBahan.addEventListener('change', computePrice);

        // bila warna berubah → hitung ulang
        document.getElementById('default_color')
          ?.addEventListener('change', computePrice);
        document.getElementById('default_rotan_color')
          ?.addEventListener('change', computePrice);

        updateSizeField();
        computePrice();
        setTimeout(() => { updateSizeField(); computePrice(); }, 0);
      })();
    </script>

    {{-- 
      // =========================  S C R I P T  W A R N A   D I N A M I S  =========================
      // Reusable untuk:
      //   - Warna Kayu  (select#default_color + #wood_color_rows)
      //   - Warna Rotan (select#default_rotan_color + #rattan_color_rows)
      //
      // Mekanisme:
      //   - Setiap baris (.color-row) punya:
      //       input.color-name   → nama warna
      //       input.color-extra  → harga tambahan
      //       .btn-add-row       → tambah baris baru (clone)
      //       .btn-remove-row    → hapus baris ini
      //   - Saat input berubah / baris ditambah / dihapus:
      //       * dropdown di-RESET (kecuali placeholder)
      //       * option baru dibuat dari semua baris yang namanya terisi
      //         dengan data-extra = harga tambahan
      //       * hidden input new_*_colors[][name|extra_price] dibuat supaya dikirim ke server
    --}}
    <script>
      (function () {

        function setupColorGroup(cfg) {
          const select     = document.getElementById(cfg.selectId);
          const rowsParent = document.getElementById(cfg.rowsId);
          const hiddenWrap = document.getElementById(cfg.hiddenId);

          if (!select || !rowsParent || !hiddenWrap) return;


          function rebuildFromRows() {
            // SIMPAN VALUE TERPILIH SEBELUM OPTION DIHAPUS
            const prevVal = select.value;

            // hapus semua option kecuali placeholder pertama
            while (select.options.length > 1) {
              select.remove(1);
            }
            // hapus semua hidden input lama
            hiddenWrap.innerHTML = '';

            const rows = rowsParent.querySelectorAll('.color-row');
            rows.forEach(row => {
              const nameInput  = row.querySelector('.color-name');
              const extraInput = row.querySelector('.color-extra');
              const name  = (nameInput?.value || '').trim();
              
              // BACA EXTRA PRICE DENGAN AMAN
              let extra = 0;
              if (extraInput) {
                // Baca dari .value (current input dari user)
                const currentValue = extraInput.value;
                // Jika kosong, coba baca dari defaultValue (nilai awal)
                const defaultValue = extraInput.defaultValue || '';
                const raw = (currentValue !== null && currentValue !== undefined && currentValue !== '') 
                  ? String(currentValue).trim() 
                  : (defaultValue !== null && defaultValue !== undefined && defaultValue !== '') 
                    ? String(defaultValue).trim() 
                    : '0';
                
                const parsed = parseFloat(raw);
                extra = (!isNaN(parsed) && isFinite(parsed) && parsed >= 0) ? parsed : 0;
              }

              if (!name) return;

              // buat option di dropdown
              const opt = document.createElement('option');
              opt.value         = name;
              opt.textContent   = name;
              opt.dataset.extra = String(extra);
              select.appendChild(opt);

              // buat hidden input untuk server dengan index eksplisit (seperti di form edit)
              const index = hiddenWrap.children.length;
              const nameHidden = document.createElement('input');
              nameHidden.type = 'hidden';
              nameHidden.name = `${cfg.hiddenName}[${index}][name]`;
              nameHidden.value = name;
              
              const extraHidden = document.createElement('input');
              extraHidden.type = 'hidden';
              extraHidden.name = `${cfg.hiddenName}[${index}][extra_price]`;
              extraHidden.value = String(extra);
              
              hiddenWrap.appendChild(nameHidden);
              hiddenWrap.appendChild(extraHidden);
            });

            // COBA KEMBALIKAN PILIHAN LAMA
            if (prevVal) {
              const stillExists = Array.from(select.options).some(o => o.value === prevVal);
              if (stillExists) {
                select.value = prevVal;
              } else {
                // kalau warna yang dulu dipilih sudah tidak ada → biarkan di placeholder
                select.value = '';
              }
            } else {
              // tidak ada prevVal → tetap di "-- Pilih Warna --"
              select.value = '';
            }

            // paksa trigger event change supaya computePrice() jalan
            select.dispatchEvent(new Event('change'));
          }

          
          // Event listener untuk rebuild dropdown (dengan debounce)
          let rebuildTimeout = null;
          rowsParent.addEventListener('input', function(e) {
            // Clear timeout sebelumnya
            clearTimeout(rebuildTimeout);
            rebuildTimeout = setTimeout(() => {
              rebuildFromRows();
            }, 300);
          });

          // Pastikan hidden input terisi saat form submit
          const form = document.querySelector('form[action*="products"]');
          if (form) {
            form.addEventListener('submit', function(e) {
              // Rebuild sekali lagi sebelum submit untuk memastikan hidden input terisi dengan nilai terbaru
              rebuildFromRows();
            });
          }

          // event: klik + / −
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

          // inisialisasi awal dengan delay untuk memastikan DOM sudah ter-render
          setTimeout(() => {
            rebuildFromRows();
          }, 100);
        }

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
      let materialIndex = 1;

      // Fungsi untuk update dropdown Bahan Default dari materials yang diinput
      function updateBahanDefault() {
        const bahanSelect = document.getElementById('default_bahan');
        if (!bahanSelect) return;

        // Simpan nilai yang sedang dipilih
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

        // Coba kembalikan nilai sebelumnya jika masih ada
        if (currentValue) {
          const stillExists = Array.from(bahanSelect.options).some(opt => opt.value === currentValue);
          if (stillExists) {
            bahanSelect.value = currentValue;
          }
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
        setTimeout(updateBahanDefault, 100);
      });
    </script>

@endsection
