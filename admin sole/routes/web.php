<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoicePdfController;
use App\Http\Controllers\OrderAdminController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TestimonyController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| File ini mendefinisikan semua rute HTTP untuk aplikasi web (bukan API).
| Setiap rute berisi: METHOD, URI, penamaan (name), dan handler (Controller@method).
| Rute bisa dikelompokkan dengan middleware (auth/admin), prefix, dan name prefix.
*/

/* =========================================================================
|  HALAMAN AWAL (GUEST)
|  GET / → view('welcome')
|  Fungsi : Landing page default sebelum login.
|  Catatan: Ketika user SUDAH login, root (/) di bawah group 'auth' akan
|           diambil alih oleh DashboardController@index.
=========================================================================== */
Route::get('/', function () {
    return view('welcome');
});

/* =========================================================================
|  AUTH ROUTES (LARAVEL UI / BREEZE / FORTIFY)
|  Auth::routes()
|  Fungsi : Register semua rute otentikasi bawaan (login, register, logout,
|           verify email, password reset, dll), lengkap dengan name() default.
=========================================================================== */
Auth::routes();

/* =========================================================================
|  GRUP RUTE YANG WAJIB LOGIN
|  Middleware: ['auth']
|  Efek      : Semua rute di dalam blok ini hanya bisa diakses user terautentikasi.
=========================================================================== */
Route::middleware(['auth'])->group(function () {

    /* --------------------------------------------------------------
    |  GET /profile  → Route::view
    |  name('profile')
    |  Fungsi : Menampilkan halaman profil (view statis).
    --------------------------------------------------------------- */
    Route::view('/profile', 'profile')->name('profile');

    /* --------------------------------------------------------------
    |  GET /profile/edit  → Route::view
    |  name('edit-profile')
    |  Fungsi : Menampilkan halaman form edit profil (view statis).
    --------------------------------------------------------------- */
    Route::view('/profile/edit', 'profile-edit')->name('edit-profile');

    /* --------------------------------------------------------------
    |  POST /profile/edit  → UserController@updateProfile
    |  name('update.profile')
    |  Fungsi : Menerima submit form edit profil dan menyimpan perubahan.
    --------------------------------------------------------------- */
    Route::post('/profile/edit', [UserController::class, 'updateProfile'])->name('update.profile');

    /* --------------------------------------------------------------
    |  GET /  → DashboardController@index
    |  name('admin.index')
    |  Fungsi : Dashboard utama setelah login (menggantikan welcome).
    --------------------------------------------------------------- */
    Route::get('/', [DashboardController::class, 'index'])->name('admin.index');

    /* --------------------------------------------------------------
    |  GET /dasbhboard  → DashboardController@dashboard
    |  name('dashboard.owner')
    |  Fungsi : Dashboard khusus owner.
    |  Catatan: URI ada salah ketik ("dasbhboard"); dibiarkan sesuai kode.
    --------------------------------------------------------------- */
    Route::get('/dasbhboard', [DashboardController::class, 'dashboard'])->name('dashboard.owner');

    /* --------------------------------------------------------------
    |  RESOURCE products  → ProductController
    |  name auto: products.index/create/store/show/edit/update/destroy
    |  Fungsi : CRUD lengkap untuk produk.
    --------------------------------------------------------------- */
    Route::resource('products', ProductController::class);

    /* --------------------------------------------------------------
    |  POST /products/{product}/variants  → ProductController@storeVariant
    |  name('products.variants.store')
    |  Fungsi : Menambahkan varian untuk produk tertentu.
    --------------------------------------------------------------- */
    Route::post('/products/{product}/variants', [ProductController::class, 'storeVariant'])
        ->name('products.variants.store');

    /* --------------------------------------------------------------
    |  GET /order-offline  → OrderController@indexOffline
    |  name('order.offline')
    |  Fungsi : Menampilkan daftar order OFFLINE (list).
    --------------------------------------------------------------- */
    Route::get('/order-offline', [OrderController::class, 'indexOffline'])->name('order.offline');

    /* --------------------------------------------------------------
    |  GET /order-offline/create  → OrderController@create
    |  name('order.offline.create')
    |  Fungsi : Form pembuatan order OFFLINE baru.
    --------------------------------------------------------------- */
    Route::get('/order-offline/create', [OrderController::class, 'create'])->name('order.offline.create');

    /* --------------------------------------------------------------
    |  POST /order  → OrderController@store
    |  name('orders.store')
    |  Fungsi : Simpan data order OFFLINE baru dari form create.
    --------------------------------------------------------------- */
    Route::post('/order', [OrderController::class, 'store'])->name('orders.store');

    /* --------------------------------------------------------------
    |  GET /order-online  → OrderController@index
    |  name('order.online')
    |  Fungsi : Menampilkan daftar order ONLINE (list).
    --------------------------------------------------------------- */
    Route::get('/order-online', [OrderController::class, 'index'])->name('order.online');

    /* --------------------------------------------------------------
    |  GET /order/{id}  → OrderController@show
    |  name('orders.show')
    |  Fungsi : Menampilkan halaman detail satu order (online/offline).
    --------------------------------------------------------------- */
    Route::get('/order/{id}', [OrderController::class, 'show'])->name('orders.show');

    /* --------------------------------------------------------------
    |  GET /order/edit/{id}  → OrderController@show
    |  name('orders.edit')
    |  Fungsi : Menampilkan halaman "edit" (saat ini mengarah ke show yang sama).
    |  Catatan: Jika nanti punya view edit terpisah, arahkan ke method edit().
    --------------------------------------------------------------- */
    Route::get('/order/edit/{id}', [OrderController::class, 'show'])->name('orders.edit');

    /* --------------------------------------------------------------
    |  PUT /order/update-status/{id}  → OrderController@updateStatus
    |  name('orders.update-status')
    |  Fungsi : Memproses perubahan status order & payment_status.
    |  Dipakai: oleh form "Update Status" di halaman detail (show.blade.php).
    |  Penting: Walau tombol "Mark Completed/Cancel" di daftar sudah DIHAPUS,
    |           route ini TETAP dibutuhkan untuk update dari halaman detail.
    --------------------------------------------------------------- */
    Route::put('/order/update-status/{id}', [OrderController::class, 'updateStatus'])->name('orders.update-status');

    /* --------------------------------------------------------------
    |  DELETE /users/{id}  → UserController@destroy
    |  name('users.destroy')
    |  Fungsi : Menghapus user tertentu.
    --------------------------------------------------------------- */
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    /* --------------------------------------------------------------
    |  DELETE /orders/{id}  → OrderController@destroy
    |  name('orders.destroy')
    |  Fungsi : Menghapus order tertentu.
    --------------------------------------------------------------- */
    Route::delete('/orders/{id}', [OrderController::class, 'destroy'])->name('orders.destroy');

    /* --------------------------------------------------------------
    |  GET /orders/list  → OrderController@listAll
    |  name('orders.list')
    |  Fungsi : Menampilkan daftar semua pesanan (online & offline) dengan filter per minggu.
    --------------------------------------------------------------- */
    Route::get('/orders/list', [OrderController::class, 'listAll'])->name('orders.list');
});

/* =========================================================================
|  RUTE GUEST: HALAMAN LOGIN & REGISTER KUSTOM (opsional)
|  GET /login     → view('login')     name('login')
|  GET /register  → view('register')  name('register')
|  Fungsi : Menyajikan halaman auth kustom jika tidak pakai default Auth views.
=========================================================================== */
Route::view('/login', 'login')->name('login');
Route::view('/register', 'register')->name('register');

/* =========================================================================
|  GRUP RUTE ADMIN (SAAT INI KOSONG)
|  prefix('admin'), name('admin.'), middleware('admin')
|  Fungsi : Area khusus admin. Tambahkan rute admin di dalam blok ini.
|  Contoh : Route::get('/reports', [ReportController::class,'index'])->name('reports.index');
=========================================================================== */
Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
   
});
