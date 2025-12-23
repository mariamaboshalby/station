<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Transaction;
use App\Models\Client;
use App\Models\Tank;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            // مجموع الكاش القادم من التفويلات
            $totalCash = Transaction::with('pump.tank.fuel')
                ->get()
                ->sum(fn($t) => $t->cash_liters * $t->pump->tank->fuel->price_per_liter);

            // إجمالي ديون العملاء
            $totalClientRest = Client::sum('rest');

            // تكلفة اللترات الموجودة حاليًا في التانكات (تكلفة الشراء)
            $totalTankValue = Tank::with('fuel')->get()
                ->sum(fn($tank) => $tank->current_level * $tank->fuel->price_for_owner);

            $capital = ($totalCash + $totalClientRest) - $totalTankValue;

            $view->with('capital', $capital);
        });

        // 🟢 تسجيل المراقبين (Observers)
        \App\Models\Pump::observe(\App\Observers\PumpObserver::class);
    }
}
