<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Track every time a course session is used (for audit and reporting)
     */
    public function up(): void
    {
        Schema::create('course_usage_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('course_purchase_id')->constrained('course_purchases');
            $table->foreignUuid('treatment_id')->constrained('treatments');
            $table->foreignUuid('patient_id')->constrained('patients');
            $table->foreignUuid('branch_id')->constrained('branches'); // Where session was used
            $table->foreignUuid('pt_id')->constrained('users'); // PT who delivered session
            $table->integer('sessions_used')->default(1);
            $table->date('usage_date');
            $table->string('status', 50)->default('used'); // used, cancelled, refunded

            // Cross-branch usage tracking (ข้อ 9)
            $table->boolean('is_cross_branch')->default(false);
            $table->foreignUuid('purchase_branch_id')->nullable()->constrained('branches');

            // Cancellation tracking
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignUuid('cancelled_by')->nullable()->constrained('users');

            $table->foreignUuid('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['course_purchase_id', 'usage_date']);
            $table->index(['patient_id', 'usage_date']);
            $table->index(['branch_id', 'usage_date']);
            $table->index(['pt_id', 'usage_date']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_usage_logs');
    }
};
