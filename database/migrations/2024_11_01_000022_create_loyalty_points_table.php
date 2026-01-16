<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ข้อ 13: Loyalty points system (earn on spending, redeem for discounts/rewards)
     */
    public function up(): void
    {
        Schema::create('loyalty_points', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('patient_id')->unique()->constrained('patients');
            $table->integer('total_points_earned')->default(0);
            $table->integer('total_points_redeemed')->default(0);
            $table->integer('current_balance')->storedAs('total_points_earned - total_points_redeemed');
            $table->string('membership_tier', 50)->default('bronze'); // bronze, silver, gold, platinum
            $table->date('tier_start_date')->nullable();
            $table->integer('points_to_next_tier')->nullable();

            // Points expiry
            $table->integer('expiring_points')->default(0); // Points expiring soon
            $table->date('next_expiry_date')->nullable();

            // Lifetime stats
            $table->decimal('lifetime_spending', 10, 2)->default(0);
            $table->integer('total_visits')->default(0);
            $table->date('last_transaction_date')->nullable();

            $table->timestamps();

            $table->index(['membership_tier']);
            $table->index(['current_balance']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_points');
    }
};
