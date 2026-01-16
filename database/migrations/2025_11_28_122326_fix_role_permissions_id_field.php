<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add UUID() as default for id column in role_permissions table
        DB::statement('ALTER TABLE role_permissions MODIFY id CHAR(36) NOT NULL DEFAULT (UUID())');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove default value from id column
        DB::statement('ALTER TABLE role_permissions MODIFY id CHAR(36) NOT NULL');
    }
};
