<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SalamController;
use App\Http\Controllers\Api\ProdukController;
use App\Http\Controllers\Api\PelangganController;
use App\Http\Controllers\Api\PesanController;
use App\Http\Controllers\Api\DetilPesanController;
use App\Http\Controllers\Api\FakturController;
use App\Http\Controllers\Api\KuitansiController;
use App\Http\Controllers\Api\LaporanController;
use App\Http\Controllers\Api\AuthController;

// Route yang bisa diakses publik
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/salam', [SalamController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
// Endpoint untuk mengambil data produk
Route::get('/produk', [ProdukController::class, 'index']);

// Menampilkan detail produk berdasarkan ID
Route::get('/produk/{id}', [ProdukController::class, 'show']);

// Menambah produk baru
Route::post('/produk', [ProdukController::class, 'store']);

// Mengubah data produk berdasarkan ID
Route::put('/produk/{id}', [ProdukController::class, 'update']);

// Menghapus data produk
Route::delete('/produk/{id}', [ProdukController::class, 'destroy']);

Route::apiResource('/pelanggan', PelangganController::class); // Membuat route resource untuk Pelanggan
Route::apiResource('/pesan', PesanController::class); // Membuat route resource untuk Pesan

//membuat Route untuk detil pesan
Route::get('/detil-pesan', [DetilPesanController::class, 'index']);
Route::get('/detil-pesan/{id_pesan}', [DetilPesanController::class, 'show']);
Route::post('/detil-pesan', [DetilPesanController::class, 'store']);
// Update item tertentu dalam sebuah pesanan
Route::put('/detil-pesan/{id_pesan}/{id_produk}', [DetilPesanController::class, 'update']);
// Hapus item tertentu dari sebuah pesanan
Route::delete('/detil-pesan/{id_pesan}/{id_produk}', [DetilPesanController::class, 'destroy']);

// Membuat Route untuk Faktur
Route::apiResource('/faktur', FakturController::class);

// Membuat Route untuk Kuitansi
Route::apiResource('/kuitansi', KuitansiController::class);


});
// Route untuk laporan penjualan harian
Route::get('/laporan/penjualan', [LaporanController::class, 'penjualanHarian']);

// Laporan produk terlaris
Route::get('/laporan/produk-terlaris', [LaporanController::class, 'produkTerlaris']);
// Export laporan penjualan ke PDF
Route::get('/laporan/export-pdf', [LaporanController::class, 'exportPDF']);
