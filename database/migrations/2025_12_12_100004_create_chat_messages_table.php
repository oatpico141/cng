<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Table: chat_messages - Stores all chat messages
     */
    public function up(): void
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Link to conversation
            $table->uuid('conversation_id');
            $table->foreign('conversation_id')
                  ->references('id')
                  ->on('chat_conversations')
                  ->cascadeOnDelete();

            // Sender info
            $table->enum('sender_type', ['user', 'customer', 'bot', 'system']);
            $table->uuid('sender_id')->nullable(); // users.id if sender_type='user'

            // Message content
            $table->enum('message_type', [
                'text',
                'image',
                'video',
                'audio',
                'file',
                'sticker',
                'location',
                'slip',
                'template',
                'system'
            ])->default('text');

            $table->text('content')->nullable();
            $table->string('media_url', 500)->nullable();
            $table->json('meta_data')->nullable();

            // Read status
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();

            // Only created_at, no updated_at for messages
            $table->timestamp('created_at')->useCurrent();

            // Indexes
            $table->index('conversation_id');
            $table->index('created_at');
            $table->index('is_read');
            $table->index('sender_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
