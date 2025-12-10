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
       
        Schema::create('treasury_transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['income', 'expense']); // نوع المعاملة: إيراد / مصروف
            $table->string('category'); // التصنيف: بنزين، سولار، مرتبات، إلخ
            $table->decimal('amount', 15, 2); // المبلغ
            $table->date('transaction_date'); // تاريخ المعاملة
            $table->text('description')->nullable(); // وصف
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // القائم بالعملية
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treasury_transactions');
    }
};
