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
        Schema::table('course_purchases', function (Blueprint $table) {
            $table->string('payment_type')->default('full')->after('status'); // full, installment
            $table->integer('installment_total')->default(0)->after('payment_type'); // จำนวนงวดทั้งหมด (3)
            $table->integer('installment_paid')->default(0)->after('installment_total'); // จำนวนงวดที่จ่ายแล้ว
            $table->decimal('installment_amount', 10, 2)->default(0)->after('installment_paid'); // ยอดต่องวด
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_purchases', function (Blueprint $table) {
            $table->dropColumn(['payment_type', 'installment_total', 'installment_paid', 'installment_amount']);
        });
    }
};
