<?php

use App\Http\Controllers\EidWishController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/eid', [EidWishController::class, 'create'])->name('eid.create');
Route::post('/eid', [EidWishController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('eid.store');
Route::get('/w/{code}', [EidWishController::class, 'show'])->name('eid.show');
Route::get('/w/{code}/audio', [EidWishController::class, 'audio'])->name('eid.audio');
Route::post('/w/{code}/facebook-share', [EidWishController::class, 'trackFacebookShare'])
    ->name('eid.facebook-share');
