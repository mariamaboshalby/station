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

            // الشيفت اللي تمت فيه العملية
            $table->foreignId('shift_id')->constrained()->cascadeOnDelete();

            // الطلمبة
            $table->foreignId('pump_id')->constrained()->cascadeOnDelete();

            // العميل (للبيع الآجل فقط)
            $table->foreignId('client_id')->nullable()->constrained()->cascadeOnDelete();

            // مبيعات آجل
            $table->decimal('credit_liters', 10, 2)->default(0);
            $table->decimal('credit_amount', 20, 2)->default(0);

            // مبيعات نقدي
            $table->decimal('cash_liters', 10, 2)->default(0);
            $table->decimal('cash_amount', 20, 2)->default(0);

            // الإجمالي (نقدي + آجل)
            $table->decimal('total_amount', 20, 2)->default(0);

            // رصيد التانك بعد العملية
            $table->decimal('tank_level_after', 10, 2)->nullable();

            // صورة العداد
            $table->string('image')->nullable();

            // ملاحظات
            $table->text('notes')->nullable();

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

