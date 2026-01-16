<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Core treatment records (linked to appointments/queues)
     */
    public function up(): void
    {
        Schema::create('treatments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('opd_id')->constrained('opd_records');
            $table->foreignUuid('patient_id')->constrained('patients');
            $table->foreignUuid('appointment_id')->nullable()->constrained('appointments');
            $table->foreignUuid('queue_id')->nullable()->constrained('queues');
            $table->foreignUuid('branch_id')->constrained('branches');
            $table->foreignUuid('pt_id')->nullable()->constrained('users'); // Treating PT
            $table->foreignUuid('service_id')->nullable()->constrained('services');

            // Treatment details
            $table->text('chief_complaint')->nullable();
            $table->jsonb('vital_signs')->nullable(); // BP, HR, temp, etc.
            $table->text('assessment')->nullable(); // PT assessment
            $table->text('diagnosis')->nullable();
            $table->text('treatment_plan')->nullable();
            $table->text('treatment_notes')->nullable();
            $table->text('home_program')->nullable(); // Exercise recommendations

            // Timing
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('duration_minutes')->nullable();

            // Billing link
            $table->foreignUuid('invoice_id')->nullable()->constrained('invoices');
            $table->uuid('course_purchase_id')->nullable(); // FK added later due to migration order
            $table->string('billing_status', 50)->default('pending'); // pending, billed, cancelled

            $table->foreignUuid('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['patient_id', 'created_at']);
            $table->index(['pt_id', 'created_at']);
            $table->index(['branch_id', 'created_at']);
            $table->index(['billing_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treatments');
    }
};
