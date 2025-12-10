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
        Schema::create('shift_nozzle_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_id')->constrained()->cascadeOnDelete();
            $table->foreignId('nozzle_id')->constrained()->cascadeOnDelete();
            $table->decimal('start_reading', 10, 2)->nullable(); // قراءة البداية
            $table->decimal('end_reading', 10, 2)->nullable();   // قراءة النهاية
            $table->decimal('liters_dispensed', 10, 2)->default(0); // اللترات المسحوبة
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift_nozzle_readings');
    }
};
