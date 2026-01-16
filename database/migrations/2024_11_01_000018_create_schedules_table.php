<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * PT/Staff work schedules
     */
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('staff_id')->constrained('staff');
            $table->foreignUuid('branch_id')->constrained('branches');
            $table->date('schedule_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('schedule_type', 50)->default('regular'); // regular, overtime, on_call
            $table->string('status', 50)->default('scheduled'); // scheduled, completed, cancelled, no_show
            $table->boolean('is_available')->default(true); // For appointment booking

            // Break times
            $table->time('break_start')->nullable();
            $table->time('break_end')->nullable();

            // Recurring schedule
            $table->boolean('is_recurring')->default(false);
            $table->string('recurrence_pattern', 50)->nullable(); // daily, weekly, monthly
            $table->date('recurrence_end_date')->nullable();

            $table->text('notes')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['staff_id', 'schedule_date']);
            $table->index(['branch_id', 'schedule_date', 'is_available']);
            $table->index(['schedule_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
