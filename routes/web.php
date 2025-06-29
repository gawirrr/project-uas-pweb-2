<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\Auth\LoginController;

// Halaman Utama (bisa nanti diarahkan ke dashboard)
Route::get('/', function () {
    return view('welcome');
});

// ==================== DASHBOARD ====================
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// ==================== MASTER DATA ==================
// Menggunakan Route::resource untuk Barang dan Kategori agar lebih ringkas
Route::resource('barang', BarangController::class);
Route::resource('kategori', KategoriController::class)->except(['show']); // Kategori biasanya tidak butuh halaman 'show'
Route::resource('supplier', SupplierController::class);


// ==================== TRANSAKSI ====================

// --- Penjualan ---
Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
Route::get('/penjualan/pos', [PenjualanController::class, 'create'])->name('penjualan.create'); // Ubah ke /pos
Route::post('/penjualan', [PenjualanController::class, 'store'])->name('penjualan.store');
Route::get('/penjualan/{penjualan}', [PenjualanController::class, 'show'])->name('penjualan.show');
// Endpoint API untuk pencarian produk oleh Javascript
Route::get('/api/search-products', [PenjualanController::class, 'searchProducts'])->name('penjualan.search_products');


// --- Pembelian ---
// Gunakan resource controller untuk pembelian juga
Route::resource('pembelian', PembelianController::class);
// Endpoint API untuk pencarian barang di form pembelian
Route::get('/api/search-all-products', [PembelianController::class, 'searchProducts'])->name('pembelian.search_products');


// ==================== LAPORAN ======================
// ==================== LAPORAN ======================
Route::get('/laporan', function () {
    // Arahkan ke view laporan yang sesuai
    return view('laporan.index'); 
})->name('laporan.index');

// ==================== AUTHENTICATION ==================== 
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');

// Proses login
Route::post('/login', [LoginController::class, 'login']);

// Proses logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Proteksi halaman dashboard agar hanya user yang sudah login yang bisa mengakses
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
