<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Table: chat_conversations - Represents a chat session
     */
    public function up(): void
    {
        Schema::create('chat_conversations', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Link to social identity
            $table->uuid('social_identity_id');
            $table->foreign('social_identity_id')
                  ->references('id')
                  ->on('social_identities')
                  ->cascadeOnDelete();

            // BranchScope compliance - links to existing branches table
            $table->uuid('branch_id');
            $table->foreign('branch_id')
                  ->references('id')
                  ->on('branches')
                  ->cascadeOnDelete();

            // Agent assignment - links to existing users table
            $table->uuid('current_agent_id')->nullable();
            $table->foreign('current_agent_id')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();

            // Conversation state
            $table->enum('status', ['open', 'pending', 'closed'])->default('open');
            $table->timestamp('last_interaction_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index('social_identity_id');
            $table->index('branch_id');
            $table->index('current_agent_id');
            $table->index('status');
            $table->index('last_interaction_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_conversations');
    }
};
