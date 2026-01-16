<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ข้อ 21: Commission calculation rates (configurable by service/PT/level)
     */
    public function up(): void
    {
        Schema::create('commission_rates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('rate_type', 50); // service_default, pt_override, package, special_promotion
            $table->foreignUuid('service_id')->nullable()->constrained('services');
            $table->foreignUuid('package_id')->nullable()->constrained('course_packages');
            $table->foreignUuid('pt_id')->nullable()->constrained('users'); // Specific PT override
            $table->foreignUuid('branch_id')->nullable()->constrained('branches'); // Branch-specific

            // Rate configuration
            $table->decimal('commission_percentage', 5, 2)->nullable(); // % commission
            $table->decimal('df_percentage', 5, 2)->nullable(); // % Doctor Fee
            $table->decimal('fixed_amount', 10, 2)->nullable(); // Fixed amount (instead of %)

            // Validity period
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();
            $table->boolean('is_active')->default(true);

            $table->text('notes')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['rate_type', 'is_active']);
            $table->index(['service_id', 'pt_id', 'effective_from']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commission_rates');
    }
};
