<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Staff performance evaluations
     */
    public function up(): void
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('staff_id')->constrained('staff');
            $table->foreignUuid('evaluator_id')->constrained('users'); // Manager/supervisor
            $table->foreignUuid('branch_id')->constrained('branches');
            $table->string('evaluation_type', 50); // probation, annual, quarterly, special
            $table->date('evaluation_date');
            $table->string('evaluation_period', 100); // "Q1 2024", "Jan-Jun 2024"

            // Ratings (1-5 scale or custom)
            $table->jsonb('ratings')->nullable(); // {technical_skills: 4, customer_service: 5, ...}
            $table->decimal('overall_score', 3, 1)->nullable(); // Average score
            $table->string('overall_rating', 50)->nullable(); // excellent, good, satisfactory, needs_improvement

            // Detailed feedback
            $table->text('strengths')->nullable();
            $table->text('areas_for_improvement')->nullable();
            $table->text('goals')->nullable();
            $table->text('action_plan')->nullable();
            $table->text('evaluator_comments')->nullable();
            $table->text('staff_comments')->nullable(); // Self-assessment

            // Follow-up
            $table->date('next_evaluation_date')->nullable();
            $table->string('status', 50)->default('draft'); // draft, completed, acknowledged

            $table->foreignUuid('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['staff_id', 'evaluation_date']);
            $table->index(['evaluator_id', 'evaluation_date']);
            $table->index(['branch_id', 'evaluation_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
