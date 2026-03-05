/**
 * =============================================================================
 * FILE    : cart.js
 * LAYER   : Frontend (client-side logic untuk halaman Keranjang & Checkout)
 * DEPENDS : api.js (axios instance dgn Authorization header otomatis),
 *           config.js (IMAGE_BASE),
 *           Toastify (notifikasi),
 *           Bootstrap Modal (opsional),
 *           DOM elemen tertentu yang sudah ada di HTML.
 * =============================================================================
 */

import api from "./api.js";               // axios instance pre-config (baseURL, auth header via interceptor)
import { IMAGE_BASE } from "./config.js"; // base path untuk gambar produk dari backend

// ---------------------------------------------------------------------------
// STATE GLOBAL UNTUK ONGKIR
// ---------------------------------------------------------------------------
let cartShippingItems = [];   // daftar item untuk dikirim ke API /shipping/quote
let currentSubtotal   = 0;    // subtotal keranjang (tanpa ongkir)

// ============================================================================
// showErrorToast(message)
// ============================================================================
function showErrorToast(message) {
  if (!window.Toastify) {
    alert(message);
    return;
  }

  Toastify({
    text: message,
    duration: 3500,
    gravity: "top",
    position: "center",
    stopOnFocus: true,
    selector: document.body,
    style: {
      background: "#dc3545",
      color: "#ffffff",
      fontWeight: "700",
      textAlign: "center",
      padding: "12px 18px",
      borderRadius: "10px",
      boxShadow: "0 6px 18px rgba(220,53,69,.35)",
      maxWidth: "720px",
      width: "fit-content",
      whiteSpace: "pre-wrap",
      wordBreak: "break-word",
      lineHeight: "1.25",
      letterSpacing: ".2px",
      zIndex: 9999
    }
  }).showToast();
}

// ============================================================================
// UTIL ONGKIR USER
// ============================================================================

// Cek apakah data alamat cukup untuk minta quote ongkir
function hasEnoughAddressUser() {
  const prov = document.getElementById("provinsi")?.value.trim() || "";
  const kabField = document.getElementById("kabupaten") || document.getElementById("kota");
  const kab = kabField?.value.trim() || "";
  const kec = document.getElementById("kecamatan")?.value.trim() || "";
  const kodepos = document.getElementById("kodepos")?.value.trim() || "";

  if (!prov || !kab || !kec) return false;
  if (!/^[0-9]{4,6}$/.test(kodepos)) return false;

  return true;
}

// Minta ongkir ke backend (TIDAK menambah volume lagi di frontend)
async function requestShippingQuoteUser(destination, items) {
  const body = {
    destination: {
      province:    destination.province,
      city:        destination.city,
      district:    destination.district || "",
      postal_code: destination.postal_code || "",
    },
    items,
    volume_divisor: 6000,    // P × L × T / 6000 → kg (logika berat volumetrik ada di backend)
    prefer: "cheapest",
  };

  const res = await api.post("/shipping/quote", body);
  const data = res.data;

  if (typeof data.price !== "number") {
    throw new Error("Format response ongkir tidak valid");
  }

  return data; // { price, ... }
}

// Update tampilan ongkir + total pesanan
function updateOngkirDisplay(ongkir, cityId = null) {
  // label di modal (Ongkos Kirim)
  const ongkirLabel  = document.getElementById("ongkir-value");
  // hidden input untuk dikirim saat checkout
  const ongkirHidden = document.getElementById("ongkir-value-hidden");

  const safeOngkir = Number(ongkir) || 0;

  if (ongkirLabel) {
    ongkirLabel.textContent = "Rp " + safeOngkir.toLocaleString("id-ID");
  }
  if (ongkirHidden) {
    ongkirHidden.value = String(safeOngkir);
  }

  // PERBAIKAN: Simpan city_id ke hidden input (buat jika belum ada)
  if (cityId !== null) {
    let cityIdHidden = document.getElementById("city-id-hidden");
    if (!cityIdHidden) {
      cityIdHidden = document.createElement("input");
      cityIdHidden.type = "hidden";
      cityIdHidden.id = "city-id-hidden";
      cityIdHidden.name = "city_id";
      // Tambahkan ke form checkout atau body
      const checkoutForm = document.querySelector(".form-alamat") || document.body;
      checkoutForm.appendChild(cityIdHidden);
    }
    cityIdHidden.value = String(cityId);
  }

  // Hitung total pesanan = subtotal + ongkir
  const grandTotalEl = document.querySelector(".modal-footer .total-harga");
  if (grandTotalEl) {
    const grand = (Number(currentSubtotal) || 0) + safeOngkir;
    grandTotalEl.textContent = "Total: Rp " + grand.toLocaleString("id-ID");
  }
}

// Hitung ongkir + total pesanan berdasarkan state global & field alamat
async function recalcShippingFromAddress() {
  // Kalau tidak ada elemen ongkir, langsung keluar (mis. halaman keranjang tanpa checkout)
  const ongkirLabel = document.getElementById("ongkir-value");
  const ongkirHidden = document.getElementById("ongkir-value-hidden");
  const hasOngkirUi = ongkirLabel || ongkirHidden;

  if (!hasOngkirUi) return; // tidak perlu hitung ongkir kalau tidak ada UI-nya

  // Kalau alamat belum lengkap atau cart tidak punya item berdimensi → ongkir 0
  if (!hasEnoughAddressUser() || !cartShippingItems.length) {
    updateOngkirDisplay(0);
    return;
  }

  const provinsi  = document.getElementById("provinsi")?.value.trim() || "";
  const kabField  = document.getElementById("kabupaten") || document.getElementById("kota");
  const kabupaten = kabField ? kabField.value.trim() : "";
  const kecamatan = document.getElementById("kecamatan")?.value.trim() || "";
  const kodepos   = document.getElementById("kodepos")?.value.trim() || "";

  // Indikator kecil bahwa lagi menghitung ongkir
  if (ongkirLabel) {
    ongkirLabel.textContent = "Menghitung ongkir...";
  }

  try {
    const quote = await requestShippingQuoteUser(
      {
        province: provinsi,
        city: kabupaten,
        district: kecamatan,
        postal_code: kodepos,
      },
      cartShippingItems
    );

    const ongkir = Number(quote.price || 0);
    // PERBAIKAN: Simpan city_id dari response untuk digunakan saat checkout
    updateOngkirDisplay(ongkir, quote.city_id || null);
  } catch (err) {
    console.error("Gagal menghitung ongkir:", err);
    // kalau gagal, jangan bikin error ke user, cukup nol + tulis teks sederhana
    if (ongkirLabel) {
      ongkirLabel.textContent = "Rp 0";
    }
    updateOngkirDisplay(0);
  }
}

// Pasang listener di field alamat supaya ongkir otomatis dihitung ulang
function setupAddressFieldListenersForShipping() {
  const ids = ["provinsi", "kabupaten", "kota", "kecamatan", "kelurahan", "kodepos"];
  ids.forEach((id) => {
    const el = document.getElementById(id);
    if (!el) return;
    el.addEventListener("change", recalcShippingFromAddress);
    el.addEventListener("input", recalcShippingFromAddress);
  });
}

// ============================================================================
// fetchCart()
// ============================================================================
async function fetchCart() {
  try {
    const { data } = await api.get("/cart");
    const items = data?.items ?? [];

    const tableBody          = document.querySelector(".wrapper-produk .tabel-produk tbody");
    const modalBody          = document.querySelector("#keranjangModal .tabel-produk tbody");
    const totalElement       = document.getElementById("Total");
    const modalTotalElement  = document.querySelector(".modal-footer .total-harga");

    if (!tableBody || !modalBody || !totalElement || !modalTotalElement) return;

    tableBody.innerHTML = "";
    modalBody.innerHTML = "";

    let totalHarga = 0;
    const shippingItems = [];

    if (!items.length) {
      const emptyRow = `
        <tr>
          <td colspan="4" class="text-center">Keranjang Anda kosong</td>
        </tr>`;

      tableBody.innerHTML = emptyRow;
      modalBody.innerHTML = emptyRow;

      totalElement.textContent      = "Rp 0";
      modalTotalElement.textContent = "Total: Rp 0";

      currentSubtotal   = 0;
      cartShippingItems = [];

      updateOngkirDisplay(0);

      const ringkas = document.querySelector(".kartu-ringkasan");
      if (ringkas) ringkas.style.display = "none";

      return;
    }

    items.forEach((item) => {
      const isCustom = item.custom_product !== null;
      const product  = isCustom ? item.custom_product : item.product;

      const nama   = isCustom ? product.name : product.title;
      const harga  = parseFloat(item.price);
      const jumlah = item.quantity;

      const ukuran = isCustom
        ? `${product.length} x ${product.width} x ${product.height}`
        : `${item.length} x ${item.width} x ${item.height}` || "-";

      // PERBAIKAN: Gunakan field yang benar untuk warna
      // Untuk custom product: gunakan wood_color dan rattan_color dari custom_product
      // Untuk produk katalog: gunakan wood_color dan rattan_color dari item (sudah di-transform di backend)
      let woodColor = "";
      let rattanColor = "";
      
      if (isCustom) {
        // Custom product: ambil dari custom_product object
        woodColor = product?.wood_color || product?.color || "";
        rattanColor = product?.rattan_color || product?.rotan_color || "";
      } else {
        // Produk katalog: ambil dari item (sudah di-transform di backend)
        woodColor = item?.wood_color || item?.color || "";
        rattanColor = item?.rattan_color || item?.rotan_color || "";
      }
      
      // Debug log (bisa dihapus setelah fix)
      if (woodColor === "" || woodColor === null || woodColor === "0" || woodColor === 0) {
        console.log("Wood color is empty/null/0 for item:", item.id, "Raw values:", {
          wood_color: item?.wood_color,
          color: item?.color,
          isCustom: isCustom,
          product: product
        });
      }
      
      // Normalisasi: jika kosong, null, atau "0", tampilkan "-"
      if (!woodColor || woodColor === "0" || woodColor === 0 || woodColor === null) {
        woodColor = "-";
      }
      if (!rattanColor || rattanColor === "0" || rattanColor === 0 || rattanColor === null) {
        rattanColor = "-";
      }

      const bahan = isCustom
        ? product.material
        : (item.bahan || "-");

      const customDeskripsi = `${bahan}, Warna Kayu: ${woodColor}, Warna Rotan: ${rattanColor}`;

      const imgSrc = isCustom
        ? "images/custom-placeholder.png"
        : (IMAGE_BASE + product.display_image);

      const row = `
        <tr>
          <td class="produk-cell">
            <div class="produk-wrapper">
              <div class="gambar-wrapper">
                <img src="${imgSrc}" alt="Produk" class="gambar-produk" />
              </div>
              <div class="info-produk">
                <div class="nama-produk">${nama}</div>
                <div class="ukuran-produk">${ukuran}</div>
                ${customDeskripsi ? `<div class="custom-produk">${customDeskripsi}</div>` : ""}
              </div>
            </div>
          </td>
          <td class="harga-produk text-center align-middle">Rp ${harga.toLocaleString("id-ID")}</td>
          <td class="text-center align-middle">
            <div class="pengatur-jumlah">
              <button data-item-id="${item.id}" data-current="${jumlah}" onclick="updateQty(${item.id}, -1)">-</button>
              <input type="number" value="${jumlah}" min="1" onchange="manualQtyUpdate(${item.id}, this.value)" />
              <button data-item-id="${item.id}" data-current="${jumlah}" onclick="updateQty(${item.id}, 1)">+</button>
            </div>
          </td>
          <td class="text-center align-middle kolom-total">
            <button class="btn-hapus" onclick="confirmDelete(${item.id})" data-bs-toggle="modal" data-bs-target="#konfirmasiHapusModal">
              <i class="bi bi-trash"></i>
            </button>
            <div class="total-item">Rp ${(harga * jumlah).toLocaleString("id-ID")}</div>
          </td>
        </tr>
      `;

      tableBody.innerHTML += row;
      modalBody.innerHTML += row;

      totalHarga += harga * jumlah;

      // Siapkan data dimensi untuk ongkir
      let lengthCm, widthCm, heightCm;
      if (isCustom) {
        lengthCm = Number(product.length);
        widthCm  = Number(product.width);
        heightCm = Number(product.height);
      } else {
        lengthCm = Number(item.length);
        widthCm  = Number(item.width);
        heightCm = Number(item.height);
      }

      if (lengthCm > 0 && widthCm > 0 && heightCm > 0 && jumlah > 0) {
        shippingItems.push({
          length_cm: lengthCm,
          width_cm:  widthCm,
          height_cm: heightCm,
          qty:       jumlah,
        });
      }
    });

    const totalHidden = document.getElementById("total_harga");
    if (totalHidden) totalHidden.value = totalHarga;

    totalElement.textContent      = `Rp ${totalHarga.toLocaleString("id-ID")}`;
    modalTotalElement.textContent = `Total: Rp ${totalHarga.toLocaleString("id-ID")}`;

    // simpan ke state global untuk ongkir
    currentSubtotal   = totalHarga;
    cartShippingItems = shippingItems;

    // hitung ongkir berdasarkan alamat terkini
    await recalcShippingFromAddress();
  } catch (err) {
    console.error("Error fetching cart:", err);
  }
}

// ============================================================================
// updateQty(itemId, change)
// ============================================================================
async function updateQty(itemId, change) {
  try {
    const btn = document.querySelector(`[data-item-id="${itemId}"]`);
    const current = parseInt(btn?.getAttribute("data-current") || "1", 10);
    const newValue = Math.max(1, current + change);

    const res = await api.patch(`/cart/item/${itemId}`, { quantity: newValue });

    if (res.status === 200) fetchCart();
  } catch (err) {
    console.error("Failed to update quantity", err);
  }
}

// ============================================================================
// manualQtyUpdate(itemId, newQty)
// ============================================================================
async function manualQtyUpdate(itemId, newQty) {
  try {
    const qty = Math.max(1, parseInt(newQty || "1", 10));
    const res = await api.patch(`/cart/item/${itemId}`, { quantity: qty });
    if (res.status === 200) fetchCart();
  } catch (err) {
    console.error("Failed to update quantity", err);
  }
}

// ============================================================================
// confirmDelete(itemId)
// ============================================================================
function confirmDelete(itemId) {
  const btn = document.getElementById("konfirmasiHapus");
  if (btn) btn.onclick = () => deleteItem(itemId);
}

// ============================================================================
// deleteItem(itemId)
// ============================================================================
async function deleteItem(itemId) {
  try {
    await api.delete(`/cart/item/${itemId}`);
    fetchCart();
    window.location.reload();
  } catch (err) {
    console.error("Delete failed", err);
  }
}

window.updateQty = updateQty;
window.manualQtyUpdate = manualQtyUpdate;
window.confirmDelete = confirmDelete;

// ============================================================================
// setFieldError(input, message = "")
// ============================================================================
function setFieldError(input, message = "") {
  if (!input) return;

  input.classList.toggle("is-invalid", !!message);

  let err = input.parentElement.querySelector(".small-error");

  if (!err && message) {
    err = document.createElement("div");
    err.className = "small-error";
    input.parentElement.appendChild(err);
  }

  if (err) err.textContent = message;

  const clear = () => setFieldError(input, "");
  input.addEventListener("input", clear, { once: true });
  input.addEventListener("change", clear, { once: true });
}

// ============================================================================
// validateCheckoutFields()
// ============================================================================
function validateCheckoutFields() {
  const getEl = {
    alamat:     () => document.getElementById("alamat"),
    provinsi:   () => document.getElementById("provinsi"),
    kabupaten:  () =>
      document.getElementById("kabupaten") ||
      document.getElementById("kota"),
    kecamatan:  () => document.getElementById("kecamatan"),
    kelurahan:  () => document.getElementById("kelurahan"),
    kodepos:    () => document.getElementById("kodepos"),
    no_telepon: () => document.getElementById("no_telepon"),
  };

  const labels = {
    alamat:     "Alamat (No rumah, dll)",
    provinsi:   "Provinsi",
    kabupaten:  "Kabupaten",
    kecamatan:  "Kecamatan",
    kelurahan:  "Kelurahan",
    kodepos:    "Kode Pos",
    no_telepon: "No. Telepon",
  };

  let firstInvalid = null;
  const missing = [];

  for (const key of Object.keys(getEl)) {
    const el = getEl[key]();

    if (!el) {
      missing.push(labels[key]);
      if (!firstInvalid) firstInvalid = document.querySelector(".form-alamat");
      continue;
    }

    let val = (el.value ?? "").trim();

    if (key === "provinsi") {
      const isSelect   = el.tagName?.toLowerCase() === "select";
      const isDefault  =
        /pilih\s*provinsi/i.test(val) ||
        (isSelect && el.selectedIndex === 0);
      if (isDefault) val = "";
    }

    if (!val) {
      setFieldError(el, `${labels[key]} wajib diisi`);
      missing.push(labels[key]);
      if (!firstInvalid) firstInvalid = el;
    } else {
      setFieldError(el, "");
    }
  }

  const kp = getEl.kodepos();
  if (kp && kp.value.trim() && !/^[0-9]{4,6}$/.test(kp.value.trim())) {
    setFieldError(kp, "Kode Pos harus 4–6 digit angka");
    if (!firstInvalid) firstInvalid = kp;
  }

  const tel = getEl.no_telepon();
  if (tel && tel.value.trim() && !/^[0-9+\-\s()]{8,20}$/.test(tel.value.trim())) {
    setFieldError(tel, "Format No. Telepon tidak valid");
    if (!firstInvalid) firstInvalid = tel;
  }

  if (missing.length) {
    showErrorToast("Lengkapi data:\n" + missing.join(", "));
    firstInvalid?.focus();
    firstInvalid?.scrollIntoView({
      behavior: "smooth",
      block: "center"
    });

    return false;
  }

  return true;
}

// ============================================================================
// handleCheckout()
// ============================================================================
async function handleCheckout() {
  const tombol = document.querySelector(".tombol-pesan");
  const originalText = tombol?.textContent || "Pesan Sekarang";

  if (tombol) {
    tombol.textContent = "Memproses...";
    tombol.disabled = true;
  }

  if (!validateCheckoutFields()) {
    if (tombol) {
      tombol.textContent = originalText;
      tombol.disabled = false;
    }
    return;
  }

  const alamat    = document.getElementById("alamat")?.value.trim() ?? "";
  const noTelp    = document.getElementById("no_telepon")?.value.trim() ?? "";
  const provinsi  = document.getElementById("provinsi")?.value.trim() ?? "";
  const kabField  = document.getElementById("kabupaten") || document.getElementById("kota");
  const kabupaten = kabField ? kabField.value.trim() : "";
  const kecamatan = document.getElementById("kecamatan")?.value.trim() ?? "";
  const kelurahan = document.getElementById("kelurahan")?.value.trim() ?? "";
  const kodepos   = document.getElementById("kodepos")?.value.trim() ?? "";
  const cityIdHidden = document.getElementById("city-id-hidden");
  const cityId = cityIdHidden ? cityIdHidden.value.trim() : null;

  const fullAddress = `${alamat}, ${provinsi}, ${kabupaten}, ${kecamatan}, ${kelurahan}, ${kodepos}`;

  try {
    const ongkirVal = document.getElementById("ongkir-value-hidden")?.value ?? "0";
    const ongkir = parseFloat(ongkirVal);

    const res = await api.post("/checkout", {
      payment_method: "transfer",
      type: "online",
      address: fullAddress,
      phone: noTelp,
      ongkir: isNaN(ongkir) ? 0 : ongkir,
      // PERBAIKAN: Kirim data provinsi, kota, dan city_id untuk perhitungan detail ongkir
      province: provinsi,
      city: kabupaten,
      city_id: cityId ? parseInt(cityId, 10) : null,
    });

    if (res.status === 201) {
      window.location.href = "pembayaran.html";
    } else {
      showErrorToast("Gagal melakukan checkout. Silakan coba lagi.");
    }
  } catch (err) {
    console.error("Checkout error:", err);
    showErrorToast("Terjadi kesalahan saat memproses pesanan.");
  } finally {
    if (tombol) {
      tombol.textContent = originalText;
      tombol.disabled = false;
    }
  }
}

// ============================================================================
// isiDropdownProvinsiJikaPerlu()
// ============================================================================
function isiDropdownProvinsiJikaPerlu() {
  const selectProvinsi = document.getElementById("provinsi");

  if (
    !selectProvinsi ||
    selectProvinsi.tagName.toLowerCase() !== "select" ||
    selectProvinsi.options.length > 1
  ) {
    return;
  }

  const provinsiList = [
    "Aceh","Sumatera Utara","Sumatera Barat","Riau","Jambi","Sumatera Selatan",
    "Bengkulu","Lampung","Kepulauan Bangka Belitung","Kepulauan Riau","DKI Jakarta",
    "Jawa Barat","Jawa Tengah","DI Yogyakarta","Jawa Timur","Banten","Bali",
    "Nusa Tenggara Barat","Nusa Tenggara Timur","Kalimantan Barat","Kalimantan Tengah",
    "Kalimantan Selatan","Kalimantan Timur","Kalimantan Utara","Sulawesi Utara",
    "Sulawesi Tengah","Sulawesi Selatan","Sulawesi Tenggara","Gorontalo","Sulawesi Barat",
    "Maluku","Maluku Utara","Papua Barat","Papua"
  ];

  provinsiList.forEach((p) => {
    const opt = document.createElement("option");
    opt.value = p;
    opt.textContent = p;
    selectProvinsi.appendChild(opt);
  });
}

// ============================================================================
// DOMContentLoaded listener
// ============================================================================
document.addEventListener("DOMContentLoaded", () => {
  fetchCart();
  isiDropdownProvinsiJikaPerlu();
  setupAddressFieldListenersForShipping();

  const tombol = document.querySelector(".tombol-pesan");
  if (tombol) {
    tombol.addEventListener("click", (e) => {
      e.preventDefault();
      handleCheckout();
    });
  }
});
