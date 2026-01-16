<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ข้อ 9: Cross-branch course sharing permissions
     */
    public function up(): void
    {
        Schema::create('course_sharing', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('course_purchase_id')->constrained('course_purchases');
            $table->foreignUuid('from_branch_id')->constrained('branches'); // Purchase branch
            $table->foreignUuid('to_branch_id')->constrained('branches'); // Allowed usage branch
            $table->boolean('is_active')->default(true);
            $table->integer('max_sessions')->nullable(); // Limit sessions at this branch
            $table->integer('used_sessions')->default(0);
            $table->text('notes')->nullable();

            // Approval tracking
            $table->timestamp('approved_at')->nullable();
            $table->foreignUuid('approved_by')->nullable()->constrained('users');

            $table->foreignUuid('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['course_purchase_id', 'to_branch_id'], 'idx_course_branch_unique');
            $table->index(['course_purchase_id', 'is_active']);
            $table->index(['to_branch_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_sharing');
    }
};
