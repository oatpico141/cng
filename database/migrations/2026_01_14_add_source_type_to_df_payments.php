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
        Schema::table('df_payments', function (Blueprint $table) {
            // Add source_type column after payment_type
            $table->string('source_type', 50)->nullable()->after('payment_type');
            // Possible values: 'course_usage', 'per_session'

            // Add service_id column which is also referenced in the code
            $table->foreignUuid('service_id')->nullable()->after('treatment_id')->constrained('services');

            // Add course_purchase_id column which is referenced in the model
            $table->foreignUuid('course_purchase_id')->nullable()->after('service_id')->constrained('course_purchases');

            // Add amount column (alias for df_amount) and payment_date (alias for df_date)
            $table->decimal('amount', 10, 2)->nullable()->after('course_purchase_id');
            $table->date('payment_date')->nullable()->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('df_payments', function (Blueprint $table) {
            $table->dropColumn('payment_date');
            $table->dropColumn('amount');
            $table->dropForeign(['course_purchase_id']);
            $table->dropColumn('course_purchase_id');
            $table->dropForeign(['service_id']);
            $table->dropColumn('service_id');
            $table->dropColumn('source_type');
        });
    }
};