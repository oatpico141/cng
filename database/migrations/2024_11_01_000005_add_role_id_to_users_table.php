<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Foundation: Add RBAC and multi-branch columns to users table
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Change id from bigIncrements to UUID
            $table->dropColumn('id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->uuid('id')->primary()->first();
            $table->string('username', 100)->unique()->after('id');
            $table->foreignUuid('role_id')->nullable()->constrained('roles')->after('password');
            $table->foreignUuid('branch_id')->nullable()->constrained('branches')->after('role_id');
            $table->boolean('is_active')->default(true)->after('email');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            $table->softDeletes();

            $table->index(['role_id', 'is_active']);
            $table->index(['branch_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropIndex(['branch_id', 'is_active']);
            $table->dropIndex(['role_id', 'is_active']);
            $table->dropColumn(['username', 'role_id', 'branch_id', 'is_active', 'last_login_at']);
            $table->dropColumn('id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->id()->first();
        });
    }
};
