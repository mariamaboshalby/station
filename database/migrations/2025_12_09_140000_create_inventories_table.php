<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->date('inventory_date');
            $table->string('supplier')->nullable();
            $table->string('invoice_number')->nullable();
            $table->date('invoice_date')->nullable();
            $table->enum('type', ['daily', 'monthly']);
            $table->foreignId('tank_id')->constrained()->onDelete('cascade');
            $table->string('fuel_type');
            $table->decimal('opening_balance', 15, 2);
            $table->decimal('purchases', 15, 2)->default(0);
            $table->decimal('sales', 15, 2);
            $table->decimal('closing_balance', 15, 2);
            $table->decimal('actual_balance', 15, 2);
            $table->decimal('difference', 15, 2);
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
