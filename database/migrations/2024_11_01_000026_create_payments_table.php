<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Payment tracking (cash, credit card, installments)
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('payment_number', 50)->unique();
            $table->foreignUuid('invoice_id')->constrained('invoices');
            $table->foreignUuid('patient_id')->constrained('patients');
            $table->foreignUuid('branch_id')->constrained('branches');
            $table->decimal('amount', 10, 2);
            $table->string('payment_method', 50); // cash, credit_card, bank_transfer, installment
            $table->string('status', 50)->default('completed'); // pending, completed, failed, refunded
            $table->date('payment_date');

            // Payment details
            $table->string('reference_number', 100)->nullable(); // Transaction ID, approval code
            $table->string('card_type', 50)->nullable(); // Visa, Mastercard, etc.
            $table->string('card_last_4', 4)->nullable();
            $table->integer('installment_number')->nullable(); // Which installment is this?
            $table->integer('total_installments')->nullable();

            $table->text('notes')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['invoice_id']);
            $table->index(['patient_id', 'payment_date']);
            $table->index(['branch_id', 'payment_date']);
            $table->index(['status', 'payment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
