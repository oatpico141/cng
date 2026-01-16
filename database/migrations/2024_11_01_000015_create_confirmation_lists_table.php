<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ข้อ 31: Auto-generate confirmation list on Day 1 (Receptionist reviews before calling)
     */
    public function up(): void
    {
        Schema::create('confirmation_lists', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('appointment_id')->constrained('appointments');
            $table->foreignUuid('patient_id')->constrained('patients');
            $table->foreignUuid('branch_id')->constrained('branches');
            $table->date('appointment_date');
            $table->time('appointment_time');

            // Confirmation tracking
            $table->string('confirmation_status', 50)->default('pending'); // pending, confirmed, declined, no_answer
            $table->timestamp('confirmed_at')->nullable();
            $table->foreignUuid('confirmed_by')->nullable()->constrained('users'); // Receptionist
            $table->text('confirmation_notes')->nullable();
            $table->integer('call_attempts')->default(0);
            $table->timestamp('last_call_attempt_at')->nullable();

            // Auto-generated flag
            $table->boolean('is_auto_generated')->default(true);
            $table->date('generated_date'); // When the list was generated

            $table->timestamps();
            $table->softDeletes();

            $table->index(['branch_id', 'appointment_date', 'confirmation_status'], 'idx_conf_branch_date_status');
            $table->index(['generated_date', 'confirmation_status'], 'idx_conf_gen_date_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('confirmation_lists');
    }
};
