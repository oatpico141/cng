<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ข้อ 6: Course packages with special rules (3 purchase patterns)
     */
    public function up(): void
    {
        Schema::create('course_packages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 255);
            $table->string('code', 50)->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('total_sessions'); // Number of sessions included
            $table->integer('validity_days'); // Days until expiry
            $table->boolean('is_active')->default(true);

            // Linked to main service
            $table->foreignUuid('service_id')->nullable()->constrained('services');

            // Commission settings
            $table->decimal('commission_rate', 5, 2)->nullable(); // Commission % per package sale
            $table->decimal('per_session_commission_rate', 5, 2)->nullable(); // Commission % per session used
            $table->decimal('df_rate', 5, 2)->nullable(); // DF % when used

            // ข้อ 6: Purchase pattern flags
            $table->boolean('allow_buy_and_use')->default(true); // Pattern 1
            $table->boolean('allow_buy_for_later')->default(true); // Pattern 2
            $table->boolean('allow_retroactive')->default(true); // Pattern 3

            $table->foreignUuid('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_packages');
    }
};
