<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * PT replacement tracking (when original PT is on leave/absent)
     */
    public function up(): void
    {
        Schema::create('pt_replacements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('original_pt_id')->constrained('staff');
            $table->foreignUuid('replacement_pt_id')->constrained('staff');
            $table->foreignUuid('appointment_id')->nullable()->constrained('appointments');
            $table->foreignUuid('treatment_id')->nullable()->constrained('treatments');
            $table->foreignUuid('branch_id')->constrained('branches');
            $table->date('replacement_date');
            $table->string('reason', 100); // leave, sick, emergency, overbooked
            $table->text('notes')->nullable();

            // Commission handling
            $table->string('commission_handling', 50)->default('replacement'); // replacement, split, original
            $table->decimal('commission_split_percentage', 5, 2)->nullable();

            $table->foreignUuid('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['original_pt_id', 'replacement_date']);
            $table->index(['replacement_pt_id', 'replacement_date']);
            $table->index(['branch_id', 'replacement_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pt_replacements');
    }
};
