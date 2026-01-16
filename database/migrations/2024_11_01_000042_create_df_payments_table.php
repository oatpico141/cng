<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ข้อ 21: Doctor Fee (DF) payments - NO CLAWBACK (PT already did the work)
     */
    public function up(): void
    {
        Schema::create('df_payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('df_number', 50)->unique();
            $table->foreignUuid('pt_id')->constrained('users');
            $table->foreignUuid('treatment_id')->constrained('treatments');
            $table->foreignUuid('invoice_id')->nullable()->constrained('invoices');
            $table->foreignUuid('branch_id')->constrained('branches');
            $table->string('payment_type', 50); // treatment_df, session_df
            $table->decimal('base_amount', 10, 2); // Service price
            $table->decimal('df_rate', 5, 2); // Percentage
            $table->decimal('df_amount', 10, 2);
            $table->string('status', 50)->default('pending'); // pending, approved, paid
            $table->date('df_date');

            // ข้อ 21: CRITICAL - NO CLAWBACK for DF
            $table->boolean('is_clawback_eligible')->default(false); // Always false for DF

            // Payment tracking
            $table->timestamp('paid_at')->nullable();
            $table->foreignUuid('paid_by')->nullable()->constrained('users');
            $table->string('payment_reference', 100)->nullable();

            $table->text('notes')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['pt_id', 'df_date', 'status']);
            $table->index(['branch_id', 'df_date']);
            $table->index(['treatment_id']);
            $table->index(['status', 'df_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('df_payments');
    }
};
