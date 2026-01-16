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
        Schema::table('course_packages', function (Blueprint $table) {
            // Add paid and bonus sessions columns
            $table->integer('paid_sessions')->default(0)->after('service_id');
            $table->integer('bonus_sessions')->default(0)->after('paid_sessions');
        });

        // Update existing records to set paid_sessions from total_sessions
        DB::table('course_packages')->update([
            'paid_sessions' => DB::raw('total_sessions')
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_packages', function (Blueprint $table) {
            $table->dropColumn(['paid_sessions', 'bonus_sessions']);
        });
    }
};
