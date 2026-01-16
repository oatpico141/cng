<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Course Shared Users - แชร์คอร์สให้คนไข้คนอื่นใช้ร่วม
     */
    public function up(): void
    {
        Schema::create('course_shared_users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('course_purchase_id')->constrained('course_purchases')->onDelete('cascade');
            $table->foreignUuid('owner_patient_id')->constrained('patients'); // เจ้าของคอร์ส
            $table->foreignUuid('shared_patient_id')->constrained('patients'); // คนที่แชร์ให้
            $table->string('relationship')->nullable(); // ความสัมพันธ์: สามี/ภรรยา, บุตร, ฯลฯ
            $table->text('notes')->nullable(); // หมายเหตุ
            $table->boolean('is_active')->default(true);
            $table->integer('max_sessions')->nullable(); // จำกัดจำนวนครั้งที่ใช้ได้ (optional)
            $table->integer('used_sessions')->default(0); // จำนวนครั้งที่ใช้ไปแล้ว

            $table->foreignUuid('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            // Prevent duplicate sharing
            $table->unique(['course_purchase_id', 'shared_patient_id'], 'idx_course_shared_unique');
            $table->index(['owner_patient_id', 'is_active']);
            $table->index(['shared_patient_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_shared_users');
    }
};
