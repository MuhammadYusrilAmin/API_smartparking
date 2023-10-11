<?php

use App\Http\Controllers\API\DetailLokasiController;
use App\Http\Controllers\API\KendaraanController;
use App\Http\Controllers\API\ParkirController;
use App\Http\Controllers\API\SaldoController;
use App\Http\Controllers\API\TransaksiController;
use App\Http\Controllers\API\VoucherController;
use App\Http\Controllers\API\VoucherDetailController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);

Route::group(['middleware' => 'jwt.verify'], function ($router) {
    Route::get('show', [UserController::class, 'show']);
    Route::post('users', [UserController::class, 'UpdateUser']);
    Route::post('logout', [UserController::class, 'logout']);

    Route::prefix('detail_lokasi')->group(function () {
        Route::get('/', [DetailLokasiController::class, 'index'])->name('detail-lokasi');
        Route::get('/create', [DetailLokasiController::class, 'create'])->name('detail-lokasi-create');
        Route::get('/{id}', [DetailLokasiController::class, 'show'])->name('detail-lokasi-detail');
        Route::get('/{id}/edit', [DetailLokasiController::class, 'edit'])->name('detail-lokasi-edit');
        Route::post('/store', [DetailLokasiController::class, 'store'])->name('detail-lokasi-store');
        Route::put('/{id}/update', [DetailLokasiController::class, 'update'])->name('detail-lokasi-update');
        Route::delete('/{id}/destroy', [DetailLokasiController::class, 'destroy'])->name('detail-lokasi-hapus');
    });
e
    Route::prefix('kendaraan')->group(function () {
        Route::get('/', [KendaraanController::class, 'index'])->name('kendaraan');
        Route::get('/create', [KendaraanController::class, 'create'])->name('kendaraan-create');
        Route::get('/{id}', [KendaraanController::class, 'show'])->name('kendaraan-detail');
        Route::get('/{id}/edit', [KendaraanController::class, 'edit'])->name('kendaraan-edit');
        Route::post('/store', [KendaraanController::class, 'store'])->name('kendaraan-store');
        Route::put('/{id}/update', [KendaraanController::class, 'update'])->name('kendaraan-update');
        Route::delete('/{id}/destroy', [KendaraanController::class, 'destroy'])->name('kendaraan-hapus');
    });

    Route::prefix('parkir')->group(function () {
        Route::get('/', [ParkirController::class, 'index'])->name('parkir');
        Route::get('/create', [ParkirController::class, 'create'])->name('parkir-create');
        Route::get('/{id}', [ParkirController::class, 'show'])->name('parkir-detail');
        Route::get('/{id}/edit', [ParkirController::class, 'edit'])->name('parkir-edit');
        Route::post('/store', [ParkirController::class, 'store'])->name('parkir-store');
        Route::put('/{id}/update', [ParkirController::class, 'update'])->name('parkir-update');
        Route::delete('/{id}/destroy', [ParkirController::class, 'destroy'])->name('parkir-hapus');
    });

    Route::prefix('saldo')->group(function () {
        Route::get('/', [SaldoController::class, 'index'])->name('saldo');
        Route::get('/create', [SaldoController::class, 'create'])->name('saldo-create');
        Route::get('/{id}', [SaldoController::class, 'show'])->name('saldo-detail');
        Route::get('/{id}/edit', [SaldoController::class, 'edit'])->name('saldo-edit');
        Route::post('/store', [SaldoController::class, 'store'])->name('saldo-store');
        Route::put('/{id}/update', [SaldoController::class, 'update'])->name('saldo-update');
        Route::delete('/{id}/destroy', [SaldoController::class, 'destroy'])->name('saldo-hapus');
    });

    Route::prefix('transaksi')->group(function () {
        Route::get('/', [TransaksiController::class, 'index'])->name('transaksi');
        Route::get('/create', [TransaksiController::class, 'create'])->name('transaksi-create');
        Route::get('/{id}', [TransaksiController::class, 'show'])->name('transaksi-detail');
        Route::get('/{id}/edit', [TransaksiController::class, 'edit'])->name('transaksi-edit');
        Route::post('/store', [TransaksiController::class, 'store'])->name('transaksi-store');
        Route::put('/{id}/update', [TransaksiController::class, 'update'])->name('transaksi-update');
        Route::delete('/{id}/destroy', [TransaksiController::class, 'destroy'])->name('transaksi-hapus');
    });

    Route::prefix('voucher_detail')->group(function () {
        Route::get('/', [VoucherDetailController::class, 'index'])->name('voucher-detail');
        Route::get('/create', [VoucherDetailController::class, 'create'])->name('voucher-detail-create');
        Route::get('/{id}', [VoucherDetailController::class, 'show'])->name('voucher-detail-detail');
        Route::get('/{id}/edit', [VoucherDetailController::class, 'edit'])->name('voucher-detail-edit');
        Route::post('/store', [VoucherDetailController::class, 'store'])->name('voucher-detail-store');
        Route::put('/{id}/update', [VoucherDetailController::class, 'update'])->name('voucher-detail-update');
        Route::delete('/{id}/destroy', [VoucherDetailController::class, 'destroy'])->name('voucher-detail-hapus');
    });

    Route::prefix('voucher')->group(function () {
        Route::get('/', [VoucherController::class, 'index'])->name('voucher');
        Route::get('/create', [VoucherController::class, 'create'])->name('voucher-create');
        Route::get('/{id}', [VoucherController::class, 'show'])->name('voucher-detail');
        Route::get('/{id}/edit', [VoucherController::class, 'edit'])->name('voucher-edit');
        Route::post('/store', [VoucherController::class, 'store'])->name('voucher-store');
        Route::put('/{id}/update', [VoucherController::class, 'update'])->name('voucher-update');
        Route::delete('/{id}/destroy', [VoucherController::class, 'destroy'])->name('voucher-hapus');
    });
});
