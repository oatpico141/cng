<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Equipment maintenance tracking
     */
    public function up(): void
    {
        Schema::create('maintenance_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('maintenance_number', 50)->unique();
            $table->foreignUuid('equipment_id')->constrained('equipment');
            $table->foreignUuid('branch_id')->constrained('branches');
            $table->string('maintenance_type', 50); // preventive, corrective, emergency, inspection
            $table->date('maintenance_date');
            $table->text('description')->nullable();
            $table->text('work_performed')->nullable();
            $table->string('performed_by', 255)->nullable(); // Technician name
            $table->string('service_provider', 255)->nullable(); // External company
            $table->decimal('cost', 10, 2)->nullable();
            $table->string('status', 50)->default('completed'); // scheduled, in_progress, completed, cancelled
            $table->date('next_maintenance_date')->nullable();

            // Parts used
            $table->jsonb('parts_used')->nullable(); // [{part: "Belt", qty: 1, cost: 500}, ...]

            $table->text('notes')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['equipment_id', 'maintenance_date']);
            $table->index(['branch_id', 'maintenance_date']);
            $table->index(['status', 'maintenance_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_logs');
    }
};
