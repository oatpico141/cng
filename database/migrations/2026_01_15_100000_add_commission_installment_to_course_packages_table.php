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
        Schema::table('course_packages', function (Blueprint $table) {
            $table->decimal('commission_installment', 10, 2)->nullable()->after('commission_rate')->comment('ค่าคอมมิชชั่นคนขาย กรณีผ่อน (บาท)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_packages', function (Blueprint $table) {
            $table->dropColumn('commission_installment');
        });
    }
};