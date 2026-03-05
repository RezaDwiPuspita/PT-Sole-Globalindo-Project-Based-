<?php

namespace App\Http\Controllers;
// ← namespace harus sesuai struktur folder (app/Http/Controllers)
//   supaya autoload PSR-4 Composer bisa menemukan controller ini.

use App\Models\User;                 // ← Eloquent model untuk tabel `users`
use Illuminate\Http\Request;         // ← Representasi HTTP request masuk (input form/body, query param, header, dll)
use Illuminate\Support\Facades\Auth; // ← Fasad Auth: akses user yang sedang login (Auth::user(), Auth::id(), dll)

class UserController extends Controller
{
    /**
     * updateProfile()
     * ---------------------------------------------------------------------------------
     * ROUTE (bukan bawaan resource): biasanya POST /profile atau PUT /profile
     *
     * TUJUAN:
     * - Meng-update profil user yang sedang login.
     * - Field yang diupdate: nama, no_hp, alamat.
     *
     * PARAM:
     * - Request $request
     *    -> berisi data yang dikirim user (misal dari form edit profil).
     *
     * FLOW:
     * 1. Ambil user yang lagi login via Auth::user()->id
     * 2. Validasi input request
     * 3. Simpan perubahan ke DB
     * 4. Redirect balik ke halaman profile dengan flash message success
     *
     * CATATAN PENTING:
     * - Saat ini kode pakai kolom 'nama', 'no_hp', 'alamat'. Tapi di model User sebelumnya
     *   kamu pakai 'name', 'phone', 'address'. Harus konsisten, kalau enggak akan error
     *   (kolom tidak ada di DB).
     *
     * RETURN:
     * - redirect()->route('profile')->with('success', '...')
     *   → redirect ke route bernama 'profile', bawa flash message agar bisa ditampilkan via session.
     */
    public function updateProfile(Request $request)
    {
        // Ambil user aktif dari database.
        // Auth::user() = user yang sudah login (instance User).
        // User::find(Auth::user()->id) = ambil data fresh dari DB berdasarkan ID user login.
        $user = User::find(Auth::user()->id);

        // Validasi request.
        // $request->validate([...]) akan:
        // - cek aturan
        // - kalau gagal → otomatis redirect back + flash error (kalau request via web)
        //   atau kirim 422 JSON (kalau request via API/AJAX accept: application/json).
        $request->validate([
            'nama'   => 'required',        // harus diisi (string bebas)
            'no_hp'  => 'required|numeric',// harus angka (kalau mau lebih ketat: regex hp)
            'alamat' => 'required',        // harus diisi
        ]);

        // Assign nilai baru ke model User.
        // $request->input('field') = ambil value dari body request,
        // sama saja seperti $request->field.
        //
        // Penting:
        // Pastikan kolom ini benar-benar ada di tabel users.
        // Kalau di DB kamu pakai 'name', 'phone', 'address', maka harus ganti ke itu.
        $user->nama   = $request->input('nama');
        $user->no_hp  = $request->input('no_hp');
        $user->alamat = $request->input('alamat');

        // Simpan perubahan ke database.
        // Bisa pakai $user->save() atau $user->update() tanpa argumen seperti ini
        // (keduanya pada akhirnya melakukan UPDATE).
        $user->update();

        // Redirect ke route bernama 'profile' + kirim flash message sukses.
        // Flash message (with('success', ...)) nanti biasanya kamu tampilkan pakai session('success')
        // di Blade.
        return redirect()->route('profile')->with('success', 'Profil berhasil diupdate');
    }

    /**
     * create()
     * ---------------------------------------------------------------------------------
     * ROUTE RESOURCE: GET /users/create
     *
     * TUJUAN:
     * - Menampilkan form untuk membuat user baru (admin create user).
     *
     * STATUS SAAT INI:
     * - Belum diisi (//).
     *
     * RETURN YANG UMUM:
     * - return view('admin.users.create');
     */
    public function create()
    {
        //
    }

    /**
     * store()
     * ---------------------------------------------------------------------------------
     * ROUTE RESOURCE: POST /users
     *
     * TUJUAN:
     * - Menyimpan user baru hasil input dari form create().
     *
     * PARAM:
     * - Request $request : seluruh data dari form pembuatan user.
     *
     * STATUS SAAT INI:
     * - Belum diisi.
     *
     * IMPLEMENTASI UMUM:
     * - Validasi request (name, email, password, role)
     * - Hash password (bcrypt)
     * - User::create([...])
     * - Redirect dengan pesan sukses
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * show()
     * ---------------------------------------------------------------------------------
     * ROUTE RESOURCE: GET /users/{user}
     *
     * PARAM:
     * - User $user
     *   → Route Model Binding otomatis: {user} di URL akan di-resolve menjadi 1 record User.
     *
     * TUJUAN:
     * - Menampilkan detail 1 user.
     *
     * STATUS SAAT INI:
     * - Belum diisi.
     *
     * IMPLEMENTASI UMUM:
     * - return view('admin.users.show', compact('user'));
     */
    public function show(User $user)
    {
        //
    }

    /**
     * edit()
     * ---------------------------------------------------------------------------------
     * ROUTE RESOURCE: GET /users/{user}/edit
     *
     * PARAM:
     * - User $user → data user yang mau diedit.
     *
     * TUJUAN:
     * - Menampilkan form edit profil user lain (misal, oleh admin).
     *
     * STATUS SAAT INI:
     * - Belum diisi.
     *
     * IMPLEMENTASI UMUM:
     * - return view('admin.users.edit', compact('user'));
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * update()
     * ---------------------------------------------------------------------------------
     * ROUTE RESOURCE: PUT/PATCH /users/{user}
     *
     * PARAM:
     * - Request $request  : data perubahan
     * - User $user        : user yang mau diupdate (sudah di-bind lewat route model binding)
     *
     * TUJUAN:
     * - Admin mengubah data user tertentu. (Ini beda dengan updateProfile(), yang update dirinya sendiri.)
     *
     * STATUS SAAT INI:
     * - Belum diisi.
     *
     * IMPLEMENTASI UMUM:
     * - Validasi request
     * - $user->update([...])
     * - redirect()->route('users.index')->with('success', 'User updated');
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * destroy()
     * ---------------------------------------------------------------------------------
     * ROUTE RESOURCE: DELETE /users/{id}
     *
     * PARAM:
     * - $id (bukan User $user)
     *   → Kamu manual ambil user pakai User::findOrFail($id)
     *   → Beda dari method lain yang pakai route model binding.
     *
     * TUJUAN:
     * - Menghapus user tertentu (biasanya hanya boleh oleh admin).
     *
     * FLOW:
     * 1. Ambil user target dengan findOrFail($id)
     * 2. Cek apakah yg login adalah admin
     *    - if Auth::user()->role !== 'admin' → tolak
     * 3. Cegah user menghapus dirinya sendiri (biar admin gak bunuh akun sendiri by accident)
     * 4. Jalankan $user->delete()
     * 5. Redirect balik dengan flash message
     *
     * CATATAN KEAMANAN:
     * - Pastikan kolom "role" memang ada di tabel users. Kalau di DB user kamu tidak punya kolom "role",
     *   ini akan error. Jadi struktur DB dan model User harus konsisten.
     */
    public function destroy($id)
    {
        // Cari user berdasarkan ID, atau 404 kalau tidak ketemu.
        $user = User::findOrFail($id);

        // Pastikan hanya admin yang boleh hapus user lain.
        // Auth::user() → user yang sedang login.
        // Auth::user()->role → field role di tabel users (contoh: 'admin', 'customer', dll).
        if (Auth::user()->role !== 'admin') {
            return redirect()
                ->route('admin.index')
                ->with('error', 'Anda tidak dapat menghapus akun karena bukan admin');
        }

        // Cegah admin menghapus dirinya sendiri (self-delete protection).
        // Bandingkan id user login dengan id akun target.
        if (Auth::user()->id === $user->id) {
            return redirect()
                ->route('admin.index')
                ->with('error', 'Anda tidak dapat menghapus akun anda sendiri');
        }

        // Hapus user dari database.
        $user->delete();

        // Kembali ke halaman sebelumnya dengan flash message sukses.
        return redirect()
            ->back()
            ->with('success', 'User berhasil dihapus');
    }
}
