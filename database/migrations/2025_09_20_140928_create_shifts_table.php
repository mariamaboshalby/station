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
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // بيانات فتح الشيفت
            $table->string('operation_type')->nullable(); // فتح شيفت / إغلاق شيفت
            $table->integer('meter_reading')->nullable(); // قراءة العداد عند بداية الشيفت
            $table->boolean('meter_match')->nullable();   // صورة العداد مطابقة ولا لا
            $table->string('meter_image')->nullable();    // صورة العداد عند الفتح

            // بيانات إغلاق الشيفت
            $table->integer('credit_sales')->nullable();     // مبيعات آجلة
            $table->integer('cash_sales')->nullable();       // مبيعات نقدية
            $table->integer('end_meter_reading')->nullable(); // قراءة العداد عند نهاية الشيفت
            $table->string('end_meter_image')->nullable();    // صورة العداد عند الإغلاق
            $table->text('notes')->nullable();                // ملاحظات

            // توقيتات
            $table->timestamp('start_time');
            $table->timestamp('end_time')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
