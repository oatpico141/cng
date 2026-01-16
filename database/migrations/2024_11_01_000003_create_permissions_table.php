<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Foundation: RBAC - Permissions
     */
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('module', 100); // patients, appointments, billing, reports, etc.
            $table->string('action', 100); // create, read, update, delete, approve, etc.
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['module', 'action'], 'idx_module_action_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
