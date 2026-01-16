<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Stock movement tracking (in, out, transfer, adjustment)
     */
    public function up(): void
    {
        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('transaction_number', 50)->unique();
            $table->foreignUuid('stock_item_id')->constrained('stock_items');
            $table->foreignUuid('branch_id')->constrained('branches');
            $table->string('transaction_type', 50); // receive, issue, transfer_out, transfer_in, adjustment
            $table->integer('quantity');
            $table->integer('quantity_before');
            $table->integer('quantity_after');
            $table->date('transaction_date');
            $table->text('description')->nullable();

            // Transfer tracking
            $table->foreignUuid('from_branch_id')->nullable()->constrained('branches');
            $table->foreignUuid('to_branch_id')->nullable()->constrained('branches');

            // Reference
            $table->foreignUuid('treatment_id')->nullable()->constrained('treatments'); // If used in treatment
            $table->string('reference_number', 100)->nullable(); // PO number, DO number

            // Costs
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->decimal('total_cost', 10, 2)->nullable();

            $table->foreignUuid('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['stock_item_id', 'transaction_date']);
            $table->index(['branch_id', 'transaction_date']);
            $table->index(['transaction_type', 'transaction_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transactions');
    }
};
