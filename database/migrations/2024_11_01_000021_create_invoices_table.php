<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ข้อ 5: Billing screen with service/package selection
     * ข้อ 7: Invoice generation (3 types: cash, installment, course)
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('invoice_number', 50)->unique();
            $table->foreignUuid('patient_id')->constrained('patients');
            $table->foreignUuid('opd_id')->nullable()->constrained('opd_records');
            $table->foreignUuid('branch_id')->constrained('branches');
            $table->string('invoice_type', 50); // cash, installment, course

            // Amounts
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('outstanding_amount', 10, 2)->default(0);

            // Status
            $table->string('status', 50)->default('pending'); // pending, partial, paid, cancelled, refunded
            $table->date('invoice_date');
            $table->date('due_date')->nullable();

            // Installment details (if applicable)
            $table->integer('installment_months')->nullable();
            $table->decimal('installment_amount', 10, 2)->nullable();
            $table->decimal('down_payment', 10, 2)->nullable();

            // References
            $table->text('notes')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['patient_id', 'status']);
            $table->index(['branch_id', 'invoice_date']);
            $table->index(['status', 'due_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
