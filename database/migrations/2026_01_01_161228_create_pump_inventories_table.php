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
        Schema::create('pump_inventories', function (Blueprint $table) {
            $table->id();
            $table->date('inventory_date');
            $table->foreignId('pump_id')->constrained()->onDelete('cascade');
            $table->foreignId('nozzle_id')->constrained()->onDelete('cascade');
            $table->foreignId('tank_id')->constrained()->onDelete('cascade');
            $table->string('fuel_type');
            $table->decimal('opening_reading', 10, 2);
            $table->decimal('closing_reading', 10, 2);
            $table->decimal('liters_dispensed', 10, 2);
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['inventory_date', 'pump_id']);
            $table->index(['inventory_date', 'nozzle_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pump_inventories');
    }
};
