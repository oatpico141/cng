<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ข้อ 8: Refund calculation and tracking
     */
    public function up(): void
    {
        Schema::create('refunds', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('refund_number', 50)->unique();
            $table->foreignUuid('invoice_id')->constrained('invoices');
            $table->foreignUuid('patient_id')->constrained('patients');
            $table->foreignUuid('branch_id')->constrained('branches');
            $table->string('refund_type', 50); // full, partial, course_cancellation
            $table->decimal('refund_amount', 10, 2);
            $table->string('status', 50)->default('pending'); // pending, approved, rejected, completed
            $table->date('refund_date');

            // ข้อ 8: Refund calculation details
            $table->decimal('original_amount', 10, 2);
            $table->decimal('used_amount', 10, 2)->default(0);
            $table->decimal('penalty_amount', 10, 2)->default(0);
            $table->text('calculation_notes')->nullable();

            // Approval workflow
            $table->text('reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignUuid('approved_by')->nullable()->constrained('users');
            $table->text('rejection_reason')->nullable();

            $table->string('refund_method', 50)->nullable(); // cash, bank_transfer, credit_card_reversal
            $table->string('reference_number', 100)->nullable();

            $table->foreignUuid('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['invoice_id']);
            $table->index(['patient_id', 'status']);
            $table->index(['branch_id', 'refund_date']);
            $table->index(['status', 'refund_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};
