<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Line items for each invoice (services/packages purchased)
     */
    public function up(): void
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('invoice_id')->constrained('invoices');
            $table->foreignUuid('service_id')->nullable()->constrained('services');
            $table->foreignUuid('package_id')->nullable()->constrained('course_packages');
            $table->foreignUuid('treatment_id')->nullable()->constrained('treatments');
            $table->string('item_type', 50); // service, package, other
            $table->string('description', 255);
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);

            // PT assignment for commission calculation
            $table->foreignUuid('pt_id')->nullable()->constrained('users');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['invoice_id']);
            $table->index(['service_id']);
            $table->index(['package_id']);
            $table->index(['pt_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
