<?php

use App\Http\Controllers\VCardController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::get('/card/create', [VCardController::class, 'create'])->name('vcard.create');
Route::post('/card/store', [VCardController::class, 'store'])->name('vcard.store');
Route::get('/card/{slug}', [VCardController::class, 'show'])->name('vcard.show');
Route::get('/card/{slug}/qr/download', [VCardController::class, 'downloadQr'])->name('vcard.qr.download');
