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
        Schema::create('crm_calls', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('patient_id');
            $table->uuid('branch_id');
            $table->uuid('appointment_id')->nullable(); // สำหรับโทรยืนยันนัด
            $table->uuid('treatment_id')->nullable(); // สำหรับโทรติดตามอาการ
            $table->enum('call_type', ['confirmation', 'follow_up']); // confirmation=ยืนยันนัด, follow_up=ติดตามอาการ
            $table->date('scheduled_date'); // วันที่ต้องโทร
            $table->time('cutoff_time')->nullable(); // เวลาสรุปรายชื่อ (17:00)
            $table->enum('status', ['pending', 'called', 'no_answer', 'confirmed', 'cancelled', 'rescheduled'])->default('pending');
            $table->text('notes')->nullable(); // บันทึกการโทร/อาการ
            $table->string('patient_feedback')->nullable(); // ความคิดเห็นคนไข้
            $table->uuid('called_by')->nullable(); // พนักงานที่โทร
            $table->timestamp('called_at')->nullable(); // เวลาที่โทร
            $table->timestamps();

            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('set null');
            $table->foreign('called_by')->references('id')->on('users')->onDelete('set null');

            $table->index(['scheduled_date', 'call_type', 'status']);
            $table->index(['patient_id', 'call_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_calls');
    }
};
