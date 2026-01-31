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
        Schema::table('treasury_transactions', function (Blueprint $table) {
            $table->foreignId('shift_id')->nullable()->after('user_id')->constrained()->onDelete('set null')->comment('ربط مع الوردية في حالة الغرامات');
            $table->date('date')->nullable()->after('transaction_date')->comment('نسخة من transaction_date للتوافق');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('treasury_transactions', function (Blueprint $table) {
            $table->dropForeign(['shift_id']);
            $table->dropColumn(['shift_id', 'date']);
        });
    }
};
