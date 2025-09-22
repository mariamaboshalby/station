<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_id')->constrained()->cascadeOnDelete(); // العملية دي حصلت في أي شيفت
            $table->foreignId('nozzle_id')->constrained()->cascadeOnDelete(); // العملية تمت بأي مسدس
            $table->decimal('liters_dispensed', 20, 2); // عدد اللترات المباعة
            $table->decimal('total_price',20, 2); // إجمالي السعر (liters * price_per_liter)
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
