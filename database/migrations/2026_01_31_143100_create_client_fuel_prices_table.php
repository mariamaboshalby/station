<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('client_fuel_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fuel_id')->constrained()->cascadeOnDelete();
            $table->decimal('price_per_liter', 10, 2);
            $table->timestamps();

            $table->unique(['client_id', 'fuel_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_fuel_prices');
    }
};
