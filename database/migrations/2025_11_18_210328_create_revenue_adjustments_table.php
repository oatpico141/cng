<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('revenue_adjustments', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Reference
            $table->foreignUuid('invoice_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('refund_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignUuid('branch_id')->constrained()->onDelete('cascade');

            // Adjustment Details
            $table->enum('adjustment_type', ['refund', 'discount', 'correction'])->default('refund');
            $table->decimal('adjustment_amount', 10, 2); // Negative value for revenue reduction
            $table->date('effective_date'); // CRITICAL: Backdate to original invoice date
            $table->date('adjustment_date'); // Actual date when adjustment was made

            // Description
            $table->text('description')->nullable();
            $table->text('notes')->nullable();

            // Metadata
            $table->foreignUuid('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('effective_date');
            $table->index('adjustment_date');
            $table->index(['branch_id', 'effective_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('revenue_adjustments');
    }
};
