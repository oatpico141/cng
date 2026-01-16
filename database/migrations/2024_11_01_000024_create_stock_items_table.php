<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Inventory management (supplies, consumables)
     */
    public function up(): void
    {
        Schema::create('stock_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('item_code', 50)->unique();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->string('category', 100); // medical_supplies, office_supplies, consumables
            $table->string('unit', 50); // pcs, box, bottle, pack
            $table->foreignUuid('branch_id')->constrained('branches');

            // Stock levels
            $table->integer('quantity_on_hand')->default(0);
            $table->integer('minimum_quantity')->default(0); // Reorder point
            $table->integer('maximum_quantity')->nullable();

            // Pricing
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->decimal('unit_price', 10, 2)->nullable(); // Selling price

            // Supplier info
            $table->string('supplier', 255)->nullable();
            $table->string('supplier_item_code', 100)->nullable();

            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_low_stock')->storedAs('quantity_on_hand <= minimum_quantity');

            $table->text('notes')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['branch_id', 'is_active']);
            $table->index(['category', 'is_active']);
            $table->index(['is_low_stock']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_items');
    }
};
