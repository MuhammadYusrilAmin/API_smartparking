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
        Route::get('/{id}', [DetailLokasiController::class, 'show'])->name('detail-lokasi-detail');
    });

    Route::prefix('kendaraan')->group(function () {
        Route::get('/', [KendaraanController::class, 'index'])->name('kendaraan');
        Route::get('/is_active/{id}', [KendaraanController::class, 'is_active'])->name('kendaraan-is-active');
        Route::get('/is_nonactive/{id}', [KendaraanController::class, 'is_nonactive'])->name('kendaraan-is-nonactive');
        Route::post('/store', [KendaraanController::class, 'store'])->name('kendaraan-store');
        Route::put('/{id}/update', [KendaraanController::class, 'update'])->name('kendaraan-update');
        Route::delete('/{id}/destroy', [KendaraanController::class, 'destroy'])->name('kendaraan-hapus');
    });

    Route::prefix('parkir')->group(function () {
        Route::get('/', [ParkirController::class, 'index'])->name('parkir');
    });

    Route::prefix('saldo')->group(function () {
        Route::get('/', [SaldoController::class, 'index'])->name('saldo');
    });

    Route::prefix('transaksi')->group(function () {
        Route::get('/', [TransaksiController::class, 'index'])->name('transaksi');
        Route::get('/getParkirNotPay', [TransaksiController::class, 'getParkirNotPay'])->name('transaksi-getParkirNotPay');
        Route::post('/store/{id}', [TransaksiController::class, 'store'])->name('transaksi-store');
        Route::put('/{id}/update', [TransaksiController::class, 'update'])->name('transaksi-update');
    });
});
