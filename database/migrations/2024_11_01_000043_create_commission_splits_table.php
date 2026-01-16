<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Commission splitting when multiple PTs work on same treatment (future feature)
     */
    public function up(): void
    {
        Schema::create('commission_splits', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('commission_id')->constrained('commissions');
            $table->foreignUuid('pt_id')->constrained('users');
            $table->decimal('split_percentage', 5, 2);
            $table->decimal('split_amount', 10, 2);
            $table->string('split_reason', 100)->nullable(); // primary_pt, assistant_pt, supervisor
            $table->string('status', 50)->default('pending'); // pending, approved, paid, clawed_back

            // Payment tracking
            $table->timestamp('paid_at')->nullable();
            $table->foreignUuid('paid_by')->nullable()->constrained('users');

            // Clawback tracking (inherits from parent commission)
            $table->timestamp('clawed_back_at')->nullable();
            $table->foreignUuid('clawed_back_by')->nullable()->constrained('users');

            $table->text('notes')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['commission_id']);
            $table->index(['pt_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commission_splits');
    }
};
