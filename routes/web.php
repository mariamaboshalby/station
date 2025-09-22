<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\TransactionController;

// الصفحة الرئيسية
Route::get('/', function () {
    return redirect()->route('shifts.index');
});

// الشيفتات
Route::prefix('shifts')->group(function () {
    Route::get('/', [ShiftController::class, 'index'])->name('shifts.index');
    Route::get('/create', [ShiftController::class, 'create'])->name('shifts.create');
    Route::post('/store', [ShiftController::class, 'store'])->name('shifts.store');
    Route::get('/{id}/close', [ShiftController::class, 'close'])->name('shifts.close');
    Route::get('/{id}/report', [ShiftController::class, 'report'])->name('shifts.report');
});

// العمليات
Route::prefix('transactions')->group(function () {
    Route::get('/', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/create', [TransactionController::class, 'create'])->name('transactions.create');
    Route::post('/store', [TransactionController::class, 'store'])->name('transactions.store');
});
// routes/web.php
use App\Http\Controllers\TankController;

Route::get('/tanks/create', [TankController::class, 'create'])->name('tanks.create');
Route::post('/tanks', [TankController::class, 'store'])->name('tanks.store');
