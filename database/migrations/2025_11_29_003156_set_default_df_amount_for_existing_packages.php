<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Set default df_amount for existing course packages based on service's df_amount
     */
    public function up(): void
    {
        // Update course packages: set df_amount from linked service
        DB::statement("
            UPDATE course_packages cp
            INNER JOIN services s ON cp.service_id = s.id
            SET cp.df_amount = COALESCE(s.df_amount, s.default_df_rate, 0)
            WHERE cp.df_amount IS NULL
        ");

        // Also update services that don't have df_amount
        DB::statement("
            UPDATE services
            SET df_amount = COALESCE(default_df_rate, 0)
            WHERE df_amount IS NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback - keep the values
    }
};
