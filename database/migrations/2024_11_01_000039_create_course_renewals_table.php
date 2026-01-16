<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Course renewal/extension tracking (when patient extends expired course)
     */
    public function up(): void
    {
        Schema::create('course_renewals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('renewal_number', 50)->unique();
            $table->foreignUuid('course_purchase_id')->constrained('course_purchases');
            $table->foreignUuid('patient_id')->constrained('patients');
            $table->foreignUuid('branch_id')->constrained('branches');
            $table->date('renewal_date');
            $table->date('old_expiry_date');
            $table->date('new_expiry_date');
            $table->integer('extension_days'); // Days extended
            $table->decimal('renewal_fee', 10, 2)->default(0);
            $table->string('renewal_reason', 50); // extension, reactivation, upgrade
            $table->text('notes')->nullable();

            // Link to invoice if renewal was charged
            $table->foreignUuid('invoice_id')->nullable()->constrained('invoices');

            $table->foreignUuid('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['course_purchase_id', 'renewal_date']);
            $table->index(['patient_id', 'renewal_date']);
            $table->index(['branch_id', 'renewal_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_renewals');
    }
};
