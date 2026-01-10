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
            $table->string('invoice_number')->nullable();
            $table->string('batch_number')->nullable();
            $table->foreignId('shift_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pump_id')->nullable()->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('nozzle_id')->nullable();
            $table->foreign('nozzle_id')->references('id')->on('nozzles')->onDelete('cascade');
            $table->foreignId('client_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('vehicle_number')->nullable();
            $table->decimal('credit_liters', 10, 2)->default(0);
            $table->decimal('cash_liters', 10, 2)->default(0);
            $table->decimal('total_amount', 20, 2)->default(0);
            $table->string('image')->nullable();
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

