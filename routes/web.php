<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\FuelController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TankController;
use App\Http\Controllers\UserController;
use App\Models\Shift;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// الصفحة الرئيسية -> توجيه حسب نوع المستخدم
Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->phone === '01111111111') {
            // الأدمن
            return redirect()->route('dashboard');
        } else {
            // اليوزر العادي
            return redirect()->route('home.buttons');
        }
    }
    // المستخدم غير مسجل الدخول
    return redirect()->route('login');
});

// ✅ لوحة التحكم (للأدمن فقط)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ✅ صفحة الأزرار (لليوزر العادي بعد تسجيل الدخول)
Route::get('/home-buttons', function () {
    $user = auth()->user();

    $openShift = Shift::where('user_id', $user->id)
        ->whereNull('end_time')
        ->first();

    return view('home-buttons', compact('openShift'));
})->middleware('auth')->name('home.buttons');

// ✅ كل الروتات اللي محتاجة تسجيل دخول
Route::middleware(['auth'])->group(function () {

    /** 🧍‍♂️ المستخدمين */
    Route::resource('users', UserController::class);

    /** ⛽ الشيفتات */
    Route::resource('shifts', ShiftController::class);
    Route::get('/shifts/{id}/close', [ShiftController::class, 'close'])->name('shifts.closeForm');
    Route::patch('/shifts/{id}/close', [ShiftController::class, 'closeStore'])->name('shifts.close');
    Route::get('/shifts/{shift}/report', [ShiftController::class, 'report'])->name('shifts.report');
    Route::get('/users/{id}/shifts', [ShiftController::class, 'userShifts'])->name('users.shifts');

    /** 🛢️ التانكات */
    Route::resource('tanks', TankController::class);
    Route::get('/tanks/{id}/add-capacity', [TankController::class, 'addCapacityForm'])->name('tanks.addCapacityForm');
    Route::post('/tanks/{id}/add-capacity', [TankController::class, 'addCapacity'])->name('tanks.addCapacity');
    Route::put('/tanks/{id}/updateAll', [TankController::class, 'updateAll'])->name('tanks.updateAll');

    /** 💰 العمليات (Transactions) */
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');

    /** 👥 العملاء */
    Route::get('/clients/search', [ClientController::class, 'search'])->name('clients.search');
    Route::resource('clients', ClientController::class);
    Route::get('/clients/{id}/transactions', [ClientController::class, 'transactions'])->name('clients.transactions');
    Route::get('/clients/{id}/add-payment', [ClientController::class, 'addPaymentForm'])->name('clients.addPaymentForm');
    Route::post('/clients/{id}/add-payment', [ClientController::class, 'addPayment'])->name('clients.addPayment');
});

require __DIR__ . '/auth.php';
