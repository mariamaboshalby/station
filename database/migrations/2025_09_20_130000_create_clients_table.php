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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pump_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->decimal('liters_drawn',10,2);
            $table->decimal('total_price',10,2);
            $table->decimal( 'amount_paid',10,2);
            $table->decimal('rest',10,2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
