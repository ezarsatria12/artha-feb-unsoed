<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; // <--- TAMBAHAN PENTING
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\QnaController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. Redirect Halaman Utama
Route::get('/', function () {
    // Menggunakan Auth::check() agar lebih dikenali editor
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// 2. Rute untuk TAMU (Belum Login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// 3. Rute untuk MEMBER (Sudah Login)
Route::middleware('auth')->group(function () {

    // Dashboard & Logout
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Menu & Produk
    Route::resource('menu', ProductController::class);
    Route::resource('products', ProductController::class);

    // Pesanan (Orders)
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');

    // 4. Halaman Detail Pesanan (POST karena kirim data keranjang)
    // Route::match agar mendukung GET (untuk refresh/back) dan POST
    Route::match(['get', 'post'], '/orders/detail', [OrderController::class, 'detail'])->name('orders.detail');

    // 5. Proses Simpan
    Route::post('/orders/store', [OrderController::class, 'store'])->name('orders.store');
    // 6. Halaman Riwayat
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');

    // Fitur Lainnya
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}', [NotificationController::class, 'show'])->name('notifications.show');
    Route::get('/qna', [QnaController::class, 'index'])->name('qna.index');
    Route::get('/faq', [FaqController::class, 'index'])->name('faq.index');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
});
// 2. Resource Controller untuk Menu (menggunakan ProductController)
// Ini otomatis membuat semua route: index, create, store, edit, update, destroy
// URL: http://127.0.0.1:8000/menu

    // 3. Routes Manual untuk Orders (Pemesanan)
    // URL: http://127.0.0.1:8000/orders
    // 1. Halaman Pilih Menu
;
