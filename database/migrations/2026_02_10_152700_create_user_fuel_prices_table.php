<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_fuel_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fuel_id')->constrained()->cascadeOnDelete();
            $table->decimal('price', 10, 2);
            $table->timestamps();

            // ضمان عدم تكرار المستخدم مع نفس نوع الوقود
            $table->unique(['user_id', 'fuel_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_fuel_prices');
    }
};
