<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * In-app notification system
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users'); // Recipient
            $table->string('notification_type', 50); // appointment_reminder, queue_update, commission_ready, low_stock
            $table->string('title', 255);
            $table->text('message');
            $table->jsonb('data')->nullable(); // Additional data (IDs, URLs, etc.)

            // Status
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->string('priority', 50)->default('normal'); // high, normal, low
            $table->string('channel', 50)->default('in_app'); // in_app, email, sms, line

            // Action
            $table->string('action_url', 500)->nullable(); // Where to go when clicked
            $table->string('action_text', 100)->nullable(); // Button text

            // Delivery tracking
            $table->boolean('is_sent')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('expires_at')->nullable(); // Auto-delete after this date

            $table->foreignUuid('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'is_read', 'created_at']);
            $table->index(['notification_type', 'created_at']);
            $table->index(['is_sent', 'sent_at']);
            $table->index(['expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
