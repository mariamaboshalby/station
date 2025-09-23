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
        Schema::create('tanks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fuel_id')->constrained()->cascadeOnDelete(); // كل تانك مرتبط بنوع وقود
            $table->string('name'); // اسم التانك (Tank A, Tank B)
            $table->decimal('capacity', 20, 2); // سعة التانك القصوى
            $table->decimal('current_level', 20, 2)->default(0); // الكمية الحالية في التانك
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tanks');
    }
};
