<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Equipment/asset inventory management
     */
    public function up(): void
    {
        Schema::create('equipment', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('equipment_code', 50)->unique();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->string('category', 100); // treatment_equipment, office_equipment, furniture
            $table->foreignUuid('branch_id')->constrained('branches');
            $table->string('status', 50)->default('available'); // available, in_use, maintenance, retired
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->string('supplier', 255)->nullable();
            $table->string('serial_number', 100)->nullable();
            $table->string('warranty_number', 100)->nullable();
            $table->date('warranty_expiry')->nullable();

            // Maintenance tracking
            $table->date('last_maintenance_date')->nullable();
            $table->date('next_maintenance_date')->nullable();
            $table->integer('maintenance_interval_days')->nullable();

            // Depreciation
            $table->decimal('current_value', 10, 2)->nullable();
            $table->integer('useful_life_years')->nullable();

            $table->text('notes')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['branch_id', 'status']);
            $table->index(['category', 'status']);
            $table->index(['next_maintenance_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
