<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add branch_id to patients table
        Schema::table('patients', function (Blueprint $table) {
            $table->uuid('branch_id')->nullable()->after('first_visit_branch_id');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');

            // Set default branch_id from first_visit_branch_id for existing records
            // Will be handled in separate data migration
        });

        // Add branch_id to services table
        Schema::table('services', function (Blueprint $table) {
            $table->uuid('branch_id')->nullable()->after('created_by');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
        });

        // Add branch_id to course_packages table
        Schema::table('course_packages', function (Blueprint $table) {
            $table->uuid('branch_id')->nullable()->after('created_by');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });

        Schema::table('course_packages', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });
    }
};
