<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use chillerlan\QRCode\{QRCode, QROptions};
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    

    // Kembalikan URL file kode QR yang baru saja dibuat
    return asset('qrcodes/' . $fileName);
});
