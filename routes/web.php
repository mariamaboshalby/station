<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientRefuelingController;
use App\Http\Controllers\FuelController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TankController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NozzleController;
use App\Http\Controllers\NozzleCalculationController;
use App\Http\Controllers\PumpController;
use App\Http\Controllers\BatchTransactionController;
use App\Models\Shift;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// الصفحة الرئيسية -> توجيه حسب نوع المستخدم
Route::get('/', function () {
    if (auth()->check()) {
        
        if (auth()->user()->phone === '01064093034') {
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
    Route::get('/tanks-report/{id}', [TankController::class, 'report'])->name('tanks.report');
    Route::get('/tanks/{id}/report/pdf', [TankController::class, 'reportPdf'])->name('tanks.report.pdf');
    Route::get('/tanks/{id}/report/excel', [TankController::class, 'reportExcel'])->name('tanks.report.excel');
    Route::get('/tanks/{id}/add-capacity', [TankController::class, 'addCapacityForm'])->name('tanks.addCapacityForm');
    Route::post('/tanks/{id}/add-capacity', [TankController::class, 'addCapacity'])->name('tanks.addCapacity');
    Route::put('/tanks/{id}/updateAll', [TankController::class, 'updateAll'])->name('tanks.updateAll');
    Route::post('/tanks/pumps/{id}/nozzles', [TankController::class, 'storeNozzle'])->name('tanks.storeNozzle');
    Route::delete('/tanks/nozzles/{id}', [TankController::class, 'destroyNozzle'])->name('tanks.destroyNozzle');
    
    /** 🔫 المسدسات */
    Route::patch('/nozzles/{id}/update-meter', [NozzleController::class, 'updateMeter'])->name('nozzles.updateMeter');
    Route::patch('/nozzles/{id}/update-name', [NozzleController::class, 'updateName'])->name('nozzles.updateName');
    
    /** 🔧 الطلمبات */
    Route::patch('/pumps/{id}/update-name', [PumpController::class, 'updateName'])->name('pumps.updateName');
    Route::get('/pumps/{id}/edit', [PumpController::class, 'edit'])->name('pumps.edit');
    Route::put('/pumps/{id}', [PumpController::class, 'update'])->name('pumps.update');
    /** 💰 العمليات (Transactions) */
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');

    /** 📦 الدفعات (Batch Transactions) */
    Route::get('/batch-transactions', [BatchTransactionController::class, 'index'])->name('batch-transactions.index');
    Route::get('/batch-transactions/create', [BatchTransactionController::class, 'create'])->name('batch-transactions.create');
    Route::post('/batch-transactions', [BatchTransactionController::class, 'store'])->name('batch-transactions.store');

    /** 👥 العملاء */
    Route::get('/clients/search', [ClientController::class, 'search'])->name('clients.search');
    Route::resource('clients', ClientController::class);
    Route::get('/clients/{id}/transactions', [ClientController::class, 'transactions'])->name('clients.transactions');
    Route::get('/clients/{id}/transactions/pdf', [ClientController::class, 'transactionsPdf'])->name('clients.transactions.pdf');
    Route::get('/clients/{id}/transactions/excel', [ClientController::class, 'transactionsExcel'])->name('clients.transactions.excel');
    Route::get('/clients/{id}/add-payment', [ClientController::class, 'addPaymentForm'])->name('clients.addPaymentForm');
    Route::post('/clients/{id}/add-payment', [ClientController::class, 'addPayment'])->name('clients.addPayment');
    Route::patch('/clients/{id}/toggle-status', [ClientController::class, 'toggleStatus'])->name('clients.toggleStatus');

    Route::delete('/client_refuelings/{id}', [ClientRefuelingController::class, 'destroy'])
        ->name('client_refuelings.destroy');

    /** 📊 التقارير المالية */
    Route::get('/reports', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/revenues', [App\Http\Controllers\ReportController::class, 'revenues'])->name('reports.revenues');
    Route::get('/reports/revenues/export', [App\Http\Controllers\ReportController::class, 'exportRevenues'])->name('reports.revenues.export');
    
    Route::get('/reports/expenses', [App\Http\Controllers\ReportController::class, 'expenses'])->name('reports.expenses');
    Route::get('/reports/expenses/export', [App\Http\Controllers\ReportController::class, 'exportExpenses'])->name('reports.expenses.export');
    
    Route::get('/reports/clients', [App\Http\Controllers\ReportController::class, 'clients'])->name('reports.clients');
    Route::get('/reports/clients/export', [App\Http\Controllers\ReportController::class, 'exportClients'])->name('reports.clients.export');
    
    Route::get('/reports/export', [App\Http\Controllers\ReportController::class, 'export'])->name('reports.export');
    
    /** 💸 المصروفات */
    Route::resource('expenses', App\Http\Controllers\ExpenseController::class);

    /** 🏦 الصندوق اليومي (الخزنة) */
    Route::get('/treasury', [\App\Http\Controllers\TreasuryController::class, 'index'])->name('treasury.index');
    Route::get('/treasury/export', [\App\Http\Controllers\TreasuryController::class, 'export'])->name('treasury.export');
    Route::post('/treasury', [\App\Http\Controllers\TreasuryController::class, 'store'])->name('treasury.store');
    Route::delete('/treasury/{id}', [\App\Http\Controllers\TreasuryController::class, 'destroy'])->name('treasury.destroy');

    /** 📦 الجرد */
    Route::get('/inventory', [\App\Http\Controllers\InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/inventory/create', [\App\Http\Controllers\InventoryController::class, 'create'])->name('inventory.create');
    Route::post('/inventory', [\App\Http\Controllers\InventoryController::class, 'store'])->name('inventory.store');
    Route::get('/inventory/report', [\App\Http\Controllers\InventoryController::class, 'report'])->name('inventory.report');
    Route::get('/inventory/export', [\App\Http\Controllers\InventoryController::class, 'export'])->name('inventory.export');
    
    /** 📊 الجرد الشهري التلقائي */
    Route::get('/inventory/monthly', [\App\Http\Controllers\InventoryController::class, 'monthlyIndex'])->name('inventory.monthly.index');
    Route::get('/inventory/monthly/detailed', [\App\Http\Controllers\InventoryController::class, 'monthlyDetailed'])->name('inventory.monthly.detailed');
    Route::get('/inventory/monthly/summary', [\App\Http\Controllers\InventoryController::class, 'monthlySummary'])->name('inventory.monthly.summary');
    
    /** 🔧 جرد الطلمبات */
    Route::get('/inventory/pumps', [\App\Http\Controllers\InventoryController::class, 'pumpIndex'])->name('inventory.pump.index');
    Route::get('/inventory/pumps/create', [\App\Http\Controllers\InventoryController::class, 'pumpCreate'])->name('inventory.pump.create');
    Route::post('/inventory/pumps', [\App\Http\Controllers\InventoryController::class, 'pumpStore'])->name('inventory.pump.store');
    Route::get('/inventory/pumps/report', [\App\Http\Controllers\InventoryController::class, 'pumpReport'])->name('inventory.pump.report');
});

require __DIR__ . '/auth.php';
