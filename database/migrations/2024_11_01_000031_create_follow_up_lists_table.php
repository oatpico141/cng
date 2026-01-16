<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ข้อ 32: Follow-up list for patients needing follow-up care
     */
    public function up(): void
    {
        Schema::create('follow_up_lists', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('patient_id')->constrained('patients');
            $table->foreignUuid('treatment_id')->nullable()->constrained('treatments');
            $table->foreignUuid('branch_id')->constrained('branches');
            $table->foreignUuid('pt_id')->constrained('users'); // PT who added to follow-up
            $table->date('follow_up_date'); // Recommended follow-up date
            $table->string('priority', 50)->default('normal'); // urgent, high, normal, low
            $table->text('notes')->nullable(); // Why follow-up needed
            $table->string('status', 50)->default('pending'); // pending, contacted, scheduled, completed, cancelled

            // Contact tracking
            $table->timestamp('contacted_at')->nullable();
            $table->foreignUuid('contacted_by')->nullable()->constrained('users');
            $table->text('contact_notes')->nullable();
            $table->foreignUuid('appointment_id')->nullable()->constrained('appointments'); // If scheduled

            $table->timestamp('completed_at')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['branch_id', 'follow_up_date', 'status']);
            $table->index(['pt_id', 'status']);
            $table->index(['priority', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('follow_up_lists');
    }
};
