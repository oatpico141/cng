<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ข้อ 4: Auto-queue from appointment arrival + Timer (15 min warning)
     */
    public function up(): void
    {
        Schema::create('queues', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('appointment_id')->nullable()->constrained('appointments');
            $table->foreignUuid('patient_id')->nullable()->constrained('patients'); // ข้อ 2: Nullable for new patients without patient record
            $table->foreignUuid('branch_id')->constrained('branches');
            $table->foreignUuid('pt_id')->nullable()->constrained('users');
            $table->integer('queue_number'); // Daily queue number
            $table->string('status', 50)->default('waiting'); // waiting, in_treatment, completed, cancelled

            // Timer tracking
            $table->timestamp('queued_at')->nullable();
            $table->timestamp('called_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('waiting_time_minutes')->nullable(); // Auto-calculated

            // ข้อ 4: 15-minute warning flag
            $table->boolean('is_overtime')->default(false);
            $table->timestamp('overtime_warning_sent_at')->nullable();

            $table->text('notes')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['branch_id', 'created_at', 'status']);
            $table->index(['pt_id', 'status']);
            $table->index(['status', 'queued_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queues');
    }
};
