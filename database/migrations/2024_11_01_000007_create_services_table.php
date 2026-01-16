<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ข้อ 5: Service/package selection on billing (one-time services)
     */
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 255);
            $table->string('code', 50)->unique();
            $table->text('description')->nullable();
            $table->string('category', 100); // treatment, consultation, assessment, package
            $table->decimal('default_price', 10, 2);
            $table->integer('default_duration_minutes')->nullable(); // For scheduling
            $table->boolean('is_active')->default(true);
            $table->boolean('is_package')->default(false); // True if this is a course package
            $table->integer('package_sessions')->nullable(); // Number of sessions in package
            $table->integer('package_validity_days')->nullable(); // Expiry period

            // Commission settings
            $table->decimal('default_commission_rate', 5, 2)->nullable(); // % for PT
            $table->decimal('default_df_rate', 5, 2)->nullable(); // % for Doctor Fee

            $table->foreignUuid('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['category', 'is_active']);
            $table->index(['is_package', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
