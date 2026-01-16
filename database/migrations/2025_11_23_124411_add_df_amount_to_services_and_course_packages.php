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
        // Add df_amount to services
        Schema::table('services', function (Blueprint $table) {
            $table->decimal('df_amount', 10, 2)->nullable()->after('default_df_rate')->comment('ค่ามือ PT (บาท)');
        });

        // Add df_amount to course_packages
        Schema::table('course_packages', function (Blueprint $table) {
            $table->decimal('df_amount', 10, 2)->nullable()->after('df_rate')->comment('ค่ามือ PT (บาท)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('df_amount');
        });

        Schema::table('course_packages', function (Blueprint $table) {
            $table->dropColumn('df_amount');
        });
    }
};
