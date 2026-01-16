<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ข้อ 21: Commission tracking with CLAWBACK on cancellation
     */
    public function up(): void
    {
        Schema::create('commissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('commission_number', 50)->unique();
            $table->foreignUuid('pt_id')->constrained('users');
            $table->foreignUuid('invoice_id')->constrained('invoices');
            $table->foreignUuid('invoice_item_id')->nullable()->constrained('invoice_items');
            $table->foreignUuid('treatment_id')->nullable()->constrained('treatments');
            $table->foreignUuid('branch_id')->constrained('branches');
            $table->string('commission_type', 50); // service, package_sale, package_usage
            $table->decimal('base_amount', 10, 2); // Amount commission is calculated from
            $table->decimal('commission_rate', 5, 2); // Percentage
            $table->decimal('commission_amount', 10, 2);
            $table->string('status', 50)->default('pending'); // pending, approved, paid, clawed_back
            $table->date('commission_date');

            // ข้อ 21: CLAWBACK tracking
            $table->boolean('is_clawback_eligible')->default(true);
            $table->timestamp('clawed_back_at')->nullable();
            $table->foreignUuid('clawed_back_by')->nullable()->constrained('users');
            $table->text('clawback_reason')->nullable();
            $table->foreignUuid('clawback_refund_id')->nullable()->constrained('refunds');

            // Payment tracking
            $table->timestamp('paid_at')->nullable();
            $table->foreignUuid('paid_by')->nullable()->constrained('users');
            $table->string('payment_reference', 100)->nullable();

            $table->text('notes')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['pt_id', 'commission_date', 'status']);
            $table->index(['branch_id', 'commission_date']);
            $table->index(['invoice_id']);
            $table->index(['status', 'commission_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};
