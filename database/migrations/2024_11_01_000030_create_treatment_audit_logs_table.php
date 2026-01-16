<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Audit trail for treatment record modifications (compliance, medico-legal)
     */
    public function up(): void
    {
        Schema::create('treatment_audit_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('treatment_id')->constrained('treatments');
            $table->string('action', 50); // created, updated, deleted, cancelled
            $table->string('field_name', 100)->nullable(); // Which field was changed
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->jsonb('changes')->nullable(); // Full snapshot of changes
            $table->foreignUuid('performed_by')->constrained('users');
            $table->text('reason')->nullable(); // Why was this changed?
            $table->string('ip_address', 50)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamps();

            $table->index(['treatment_id', 'created_at']);
            $table->index(['performed_by', 'created_at']);
            $table->index(['action']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treatment_audit_logs');
    }
};
