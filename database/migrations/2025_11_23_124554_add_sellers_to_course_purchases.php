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
            // JSON array of seller user IDs for commission split
            $table->json('seller_ids')->nullable()->after('created_by')->comment('รายชื่อคนขาย (สำหรับแบ่งคอมมิชชั่น)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_purchases', function (Blueprint $table) {
            $table->dropColumn('seller_ids');
        });
    }
};
