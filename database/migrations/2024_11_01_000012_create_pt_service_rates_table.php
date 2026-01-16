<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ข้อ 20: PT-specific service rates (override default service price)
     */
    public function up(): void
    {
        Schema::create('pt_service_rates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('pt_id')->constrained('users');
            $table->foreignUuid('service_id')->constrained('services');
            $table->foreignUuid('branch_id')->nullable()->constrained('branches'); // Branch-specific rate
            $table->decimal('price', 10, 2); // PT-specific price for this service
            $table->decimal('commission_rate', 5, 2)->nullable(); // PT-specific commission %
            $table->decimal('df_rate', 5, 2)->nullable(); // PT-specific DF %
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();
            $table->boolean('is_active')->default(true);

            $table->foreignUuid('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['pt_id', 'service_id', 'branch_id'], 'idx_pt_service_branch_unique');
            $table->index(['pt_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pt_service_rates');
    }
};
