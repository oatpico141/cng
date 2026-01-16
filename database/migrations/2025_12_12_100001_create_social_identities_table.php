<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Table: social_identities - Links social IDs (LINE/Facebook) to patients
     */
    public function up(): void
    {
        Schema::create('social_identities', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Link to existing patients table (nullable for unlinked leads)
            $table->uuid('patient_id')->nullable();
            $table->foreign('patient_id')
                  ->references('id')
                  ->on('patients')
                  ->nullOnDelete();

            // Social provider info
            $table->enum('provider', ['line', 'facebook']);
            $table->string('provider_user_id', 255); // LINE UserID / FB PSID
            $table->string('profile_name', 255)->nullable();
            $table->string('avatar_url', 500)->nullable();
            $table->json('meta_data')->nullable();

            $table->timestamps();

            // Ensure unique social identity per provider
            $table->unique(['provider', 'provider_user_id'], 'social_provider_unique');
            $table->index('patient_id');
            $table->index('provider_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_identities');
    }
};
