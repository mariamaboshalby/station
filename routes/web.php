<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TankController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// الصفحة الرئيسية -> توجيه حسب المستخدم
Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->email === 'admin@admin.com') {
            return redirect()->route('dashboard'); // الأدمن يروح للداشبورد
        }
        return redirect()->route('home.buttons'); // اليوزر العادي يروح لصفحة الأزرار
    }
    return redirect()->route('login');
});

// لوحة التحكم (للأدمن)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ✅ صفحة الأزرار (لليوزر العادي بعد تسجيل الدخول)
Route::get('/home-buttons', function () {
    $user = auth()->user();
    $openShift = \App\Models\Shift::where('user_id', $user->id)
        ->whereNull('end_time')
        ->first();

    return view('home-buttons', compact('openShift'));
})->middleware('auth')->name('home.buttons');

// بروفايل
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ✅ كل الروتات اللي محتاجة لوجن
Route::middleware('auth')->group(function () {
    // Users
    Route::resource('users', UserController::class);

    // Shifts
    Route::resource('shifts', ShiftController::class);
    Route::get('/shifts/{id}/close', [ShiftController::class, 'close'])->name('shifts.closeForm');
    Route::patch('/shifts/{id}/close', [ShiftController::class, 'closeStore'])->name('shifts.close');

    Route::get('shifts/{shift}/report', [ShiftController::class, 'report'])->name('shifts.report');

    // عرض شيفتات موظف معين (باستخدام id)
    Route::get('/users/{id}/shifts', [ShiftController::class, 'userShifts'])
        ->name('users.shifts');

    // Transactions
    Route::resource('transactions', TransactionController::class);

    // Tanks
    Route::resource('tanks', TankController::class);
    // صفحة إدخال الكمية
    Route::get('/tanks/{id}/add-capacity', [TankController::class, 'addCapacityForm'])->name('tanks.addCapacityForm');
    // تنفيذ العملية
    Route::post('/tanks/{id}/add-capacity', [TankController::class, 'addCapacity'])->name('tanks.addCapacity');

    // Clients
    Route::resource('clients', ClientController::class);
});

require __DIR__ . '/auth.php';
