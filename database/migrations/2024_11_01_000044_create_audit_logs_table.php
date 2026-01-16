<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * System-wide audit trail for compliance and security
     */
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained('users');
            $table->string('action', 100); // login, logout, create, update, delete, view
            $table->string('module', 100); // patients, invoices, treatments, etc.
            $table->string('model_type', 100)->nullable(); // Model class name
            $table->uuid('model_id')->nullable(); // ID of affected record
            $table->jsonb('old_values')->nullable(); // Before changes
            $table->jsonb('new_values')->nullable(); // After changes
            $table->string('ip_address', 50)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->string('url', 500)->nullable();
            $table->string('method', 10)->nullable(); // GET, POST, PUT, DELETE
            $table->text('description')->nullable();
            $table->foreignUuid('branch_id')->nullable()->constrained('branches');
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['module', 'action', 'created_at']);
            $table->index(['model_type', 'model_id']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
