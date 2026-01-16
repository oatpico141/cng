<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ข้อ 6: Course purchase with 3 patterns (buy_and_use, buy_for_later, retroactive)
     * ข้อ 9: Course sharing between branches
     */
    public function up(): void
    {
        Schema::create('course_purchases', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('course_number', 50)->unique();
            $table->foreignUuid('patient_id')->constrained('patients');
            $table->foreignUuid('package_id')->constrained('course_packages');
            $table->foreignUuid('invoice_id')->constrained('invoices');
            $table->foreignUuid('purchase_branch_id')->constrained('branches'); // Where it was purchased

            // ข้อ 6: Purchase pattern
            $table->string('purchase_pattern', 50); // buy_and_use, buy_for_later, retroactive
            $table->date('purchase_date');
            $table->date('activation_date')->nullable(); // When course was activated
            $table->date('expiry_date'); // Calculated from package validity

            // Session tracking
            $table->integer('total_sessions');
            $table->integer('used_sessions')->default(0);
            $table->integer('remaining_sessions')->storedAs('total_sessions - used_sessions');

            // Status
            $table->string('status', 50)->default('active'); // active, expired, cancelled, completed

            // ข้อ 9: Sharing settings
            $table->boolean('allow_branch_sharing')->default(true);
            $table->jsonb('allowed_branches')->nullable(); // Specific branches if not all

            // Cancellation tracking
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignUuid('cancelled_by')->nullable()->constrained('users');

            $table->foreignUuid('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['patient_id', 'status']);
            $table->index(['purchase_branch_id', 'purchase_date']);
            $table->index(['status', 'expiry_date']);
            $table->index(['purchase_pattern']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_purchases');
    }
};
