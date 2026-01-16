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
        Schema::table('patients', function (Blueprint $table) {
            // is_temporary: true = ลูกค้าจอง (ยังไม่เคยรักษา), false = ลูกค้าจริง (เคยรักษาแล้ว)
            $table->boolean('is_temporary')->default(true)->after('id');

            // HN Number: จะถูก generate เมื่อ is_temporary เปลี่ยนเป็น false
            $table->string('hn_number', 20)->nullable()->unique()->after('is_temporary');

            // วันที่เปลี่ยนจาก temporary เป็น real
            $table->timestamp('converted_at')->nullable()->after('hn_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(['is_temporary', 'hn_number', 'converted_at']);
        });
    }
};
