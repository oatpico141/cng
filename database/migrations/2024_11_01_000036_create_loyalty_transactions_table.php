<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ข้อ 13: Points earning and redemption history
     */
    public function up(): void
    {
        Schema::create('loyalty_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('transaction_number', 50)->unique();
            $table->foreignUuid('patient_id')->constrained('patients');
            $table->foreignUuid('branch_id')->constrained('branches');
            $table->string('transaction_type', 50); // earn, redeem, expire, adjust, bonus
            $table->integer('points');
            $table->integer('balance_before');
            $table->integer('balance_after');
            $table->date('transaction_date');

            // Earning details
            $table->foreignUuid('invoice_id')->nullable()->constrained('invoices');
            $table->decimal('spending_amount', 10, 2)->nullable();
            $table->decimal('points_rate', 5, 2)->nullable(); // Points per baht

            // Redemption details
            $table->foreignUuid('reward_id')->nullable()->constrained('loyalty_rewards');
            $table->decimal('discount_amount', 10, 2)->nullable();

            // Expiry tracking
            $table->date('expiry_date')->nullable(); // When these points expire
            $table->boolean('is_expired')->default(false);

            $table->text('description')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['patient_id', 'transaction_date'], 'idx_loyalty_tx_patient_date');
            $table->index(['branch_id', 'transaction_date'], 'idx_loyalty_tx_branch_date');
            $table->index(['transaction_type', 'transaction_date'], 'idx_loyalty_tx_type_date');
            $table->index(['expiry_date', 'is_expired'], 'idx_loyalty_tx_expiry');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_transactions');
    }
};
