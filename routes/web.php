<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TankController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// لوحة التحكم
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// بروفايل
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
// ✅ كل الروتات اللي محتاجة لوجن
Route::middleware('auth')->group(function () {
    Route::resource('users', UserController::class);
    // Shifts
    Route::resource('shifts', ShiftController::class);
    Route::get('shifts/{shift}/close', [ShiftController::class, 'close'])->name('shifts.close');
    Route::get('shifts/{shift}/report', [ShiftController::class, 'report'])->name('shifts.report');

    // Transactions
    Route::resource('transactions', TransactionController::class);

    // Tanks
    Route::resource('tanks', TankController::class);




});

require __DIR__ . '/auth.php';
