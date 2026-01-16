<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Staff leave request management
     */
    public function up(): void
    {
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('leave_number', 50)->unique();
            $table->foreignUuid('staff_id')->constrained('staff');
            $table->foreignUuid('branch_id')->constrained('branches');
            $table->string('leave_type', 50); // sick, annual, personal, maternity, emergency
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('total_days');
            $table->string('status', 50)->default('pending'); // pending, approved, rejected, cancelled
            $table->text('reason')->nullable();

            // Approval workflow
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignUuid('approved_by')->nullable()->constrained('users');
            $table->text('approval_notes')->nullable();
            $table->text('rejection_reason')->nullable();

            // File attachments (medical certificate, etc.)
            $table->string('attachment_path', 500)->nullable();

            $table->foreignUuid('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['staff_id', 'status']);
            $table->index(['branch_id', 'start_date']);
            $table->index(['status', 'start_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
