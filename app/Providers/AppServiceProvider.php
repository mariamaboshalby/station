<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View; // ✅ مهم
use App\Models\Transaction;
use App\Models\Client;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $totalCash = Transaction::with('pump.tank.fuel')
                ->get()
                ->sum(fn($t) => $t->cash_liters * $t->pump->tank->fuel->price_per_liter);

            $totalClientRest = Client::sum('rest');

            $capital = $totalCash + $totalClientRest;

            $view->with('capital', $capital);
        });
    }
}
