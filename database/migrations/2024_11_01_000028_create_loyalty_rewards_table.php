<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ข้อ 13: Rewards catalog (what patients can redeem points for)
     */
    public function up(): void
    {
        Schema::create('loyalty_rewards', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reward_code', 50)->unique();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->string('reward_type', 50); // discount, service, product, voucher
            $table->integer('points_required');
            $table->decimal('discount_amount', 10, 2)->nullable();
            $table->decimal('discount_percentage', 5, 2)->nullable();
            $table->foreignUuid('service_id')->nullable()->constrained('services'); // If reward is a service

            // Availability
            $table->boolean('is_active')->default(true);
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();
            $table->integer('max_redemptions')->nullable(); // Total limit
            $table->integer('max_per_patient')->nullable(); // Per patient limit
            $table->integer('current_redemptions')->default(0);

            // Tier restrictions
            $table->string('minimum_tier', 50)->nullable(); // bronze, silver, gold, platinum
            $table->jsonb('allowed_branches')->nullable(); // Branch restrictions

            $table->text('terms_and_conditions')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'valid_from', 'valid_to']);
            $table->index(['reward_type', 'is_active']);
            $table->index(['points_required']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_rewards');
    }
};
