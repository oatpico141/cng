<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Staff profiles (extends users table with HR data)
     */
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->unique()->constrained('users');
            $table->foreignUuid('branch_id')->constrained('branches'); // Primary branch
            $table->string('employee_id', 50)->unique();
            $table->string('first_name', 255);
            $table->string('last_name', 255);
            $table->string('phone', 20);
            $table->string('email', 255)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender', 20)->nullable();
            $table->text('address')->nullable();

            // Employment details
            $table->string('position', 100); // PT, Receptionist, Manager, etc.
            $table->string('department', 100)->nullable();
            $table->date('hire_date');
            $table->date('termination_date')->nullable();
            $table->string('employment_status', 50)->default('active'); // active, on_leave, terminated
            $table->string('employment_type', 50); // full_time, part_time, contract

            // License info (for PTs)
            $table->string('license_number', 100)->nullable();
            $table->date('license_expiry')->nullable();
            $table->jsonb('certifications')->nullable(); // Additional certifications

            // Compensation
            $table->decimal('base_salary', 10, 2)->nullable();
            $table->string('salary_type', 50)->nullable(); // monthly, hourly, commission_only

            $table->text('notes')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['branch_id', 'employment_status']);
            $table->index(['position', 'employment_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
