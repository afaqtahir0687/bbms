<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::resource('allocations', \App\Http\Controllers\AllocationController::class);
    
    Route::post('allocations/{id}/upload', [\App\Http\Controllers\ProofController::class, 'store'])->name('proof.upload');
    Route::post('proof/{id}/verify', [\App\Http\Controllers\ProofController::class, 'verify'])->name('proof.verify');

    Route::resource('soft-bookings', \App\Http\Controllers\SoftBookingController::class)->only(['index', 'create', 'store']);
    Route::resource('master-data', \App\Http\Controllers\MasterDataController::class)->only(['index', 'create', 'store']);

    // Geography Routes
    Route::get('/geography', [\App\Http\Controllers\GeographyController::class, 'index'])->name('geography.index');
    Route::post('/geography/country', [\App\Http\Controllers\GeographyController::class, 'storeCountry'])->name('geography.storeCountry');
    Route::get('/geography/country/{id}', [\App\Http\Controllers\GeographyController::class, 'showCountry'])->name('geography.showCountry');
    Route::get('/geography/country/{id}/edit', [\App\Http\Controllers\GeographyController::class, 'editCountry'])->name('geography.editCountry');
    Route::put('/geography/country/{id}', [\App\Http\Controllers\GeographyController::class, 'updateCountry'])->name('geography.updateCountry');
    Route::delete('/geography/country/{id}', [\App\Http\Controllers\GeographyController::class, 'destroyCountry'])->name('geography.destroyCountry');

    Route::post('/geography/country/{id}/province', [\App\Http\Controllers\GeographyController::class, 'storeProvince'])->name('geography.storeProvince');
    Route::get('/geography/province/{id}', [\App\Http\Controllers\GeographyController::class, 'showProvince'])->name('geography.showProvince');
    Route::get('/geography/province/{id}/edit', [\App\Http\Controllers\GeographyController::class, 'editProvince'])->name('geography.editProvince');
    Route::put('/geography/province/{id}', [\App\Http\Controllers\GeographyController::class, 'updateProvince'])->name('geography.updateProvince');
    Route::delete('/geography/province/{id}', [\App\Http\Controllers\GeographyController::class, 'destroyProvince'])->name('geography.destroyProvince');

    Route::post('/geography/province/{id}/city', [\App\Http\Controllers\GeographyController::class, 'storeCity'])->name('geography.storeCity');
    Route::get('/geography/city/{id}', [\App\Http\Controllers\GeographyController::class, 'showCity'])->name('geography.showCity');
    Route::get('/geography/city/{id}/edit', [\App\Http\Controllers\GeographyController::class, 'editCity'])->name('geography.editCity');
    Route::put('/geography/city/{id}', [\App\Http\Controllers\GeographyController::class, 'updateCity'])->name('geography.updateCity');
    Route::delete('/geography/city/{id}', [\App\Http\Controllers\GeographyController::class, 'destroyCity'])->name('geography.destroyCity');

    Route::post('/geography/city/{id}/area', [\App\Http\Controllers\GeographyController::class, 'storeArea'])->name('geography.storeArea');
    Route::get('/geography/area/{id}', [\App\Http\Controllers\GeographyController::class, 'showArea'])->name('geography.showArea');
    Route::get('/geography/area/{id}/edit', [\App\Http\Controllers\GeographyController::class, 'editArea'])->name('geography.editArea');
    Route::put('/geography/area/{id}', [\App\Http\Controllers\GeographyController::class, 'updateArea'])->name('geography.updateArea');
    Route::delete('/geography/area/{id}', [\App\Http\Controllers\GeographyController::class, 'destroyArea'])->name('geography.destroyArea');
});
