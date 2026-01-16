<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ข้อ 11: PT Request/Change tracking (separate from appointments for history)
     */
    public function up(): void
    {
        Schema::create('pt_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('appointment_id')->constrained('appointments');
            $table->foreignUuid('patient_id')->constrained('patients');
            $table->foreignUuid('branch_id')->constrained('branches');
            $table->foreignUuid('original_pt_id')->nullable()->constrained('users');
            $table->foreignUuid('requested_pt_id')->constrained('users');
            $table->string('status', 50)->default('pending'); // pending, approved, rejected
            $table->text('reason')->nullable(); // Why patient wants this PT
            $table->text('rejection_reason')->nullable();

            $table->timestamp('requested_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->foreignUuid('processed_by')->nullable()->constrained('users'); // Admin who approved/rejected
            $table->foreignUuid('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['appointment_id', 'status']);
            $table->index(['requested_pt_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pt_requests');
    }
};
