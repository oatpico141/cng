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
        // Make opd_id nullable in treatments table
        Schema::table('treatments', function (Blueprint $table) {
            $table->uuid('opd_id')->nullable()->change();
        });

        // Make invoice_id nullable in course_purchases table
        Schema::table('course_purchases', function (Blueprint $table) {
            $table->uuid('invoice_id')->nullable()->change();
        });

        // Allow null expiry_date in course_purchases for testing
        Schema::table('course_purchases', function (Blueprint $table) {
            $table->date('expiry_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('treatments', function (Blueprint $table) {
            $table->uuid('opd_id')->nullable(false)->change();
        });

        Schema::table('course_purchases', function (Blueprint $table) {
            $table->uuid('invoice_id')->nullable(false)->change();
        });

        Schema::table('course_purchases', function (Blueprint $table) {
            $table->date('expiry_date')->nullable(false)->change();
        });
    }
};