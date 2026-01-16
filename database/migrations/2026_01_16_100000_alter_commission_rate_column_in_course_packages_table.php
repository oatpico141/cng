<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Change commission_rate from DECIMAL(5,2) to DECIMAL(10,2) to accommodate larger values
     */
    public function up(): void
    {
        Schema::table('course_packages', function (Blueprint $table) {
            $table->decimal('commission_rate', 10, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_packages', function (Blueprint $table) {
            $table->decimal('commission_rate', 5, 2)->nullable()->change();
        });
    }
};