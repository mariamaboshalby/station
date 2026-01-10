<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('actual_balances', function (Blueprint $table) {
            $table->id();
            $table->date('balance_date'); // تاريخ الرصيد
            $table->foreignId('fuel_id')->constrained()->onDelete('cascade'); // نوع الوقود
            $table->decimal('actual_balance', 10, 2); // الرصيد الفعلي المدخل يدوياً
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // المستخدم الذي أدخل الرصيد
            $table->text('notes')->nullable(); // ملاحظات
            $table->timestamps();
            
            $table->unique(['balance_date', 'fuel_id']); // لا يوجد سجلين لنفس الوقود في نفس اليوم
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actual_balances');
    }
};
