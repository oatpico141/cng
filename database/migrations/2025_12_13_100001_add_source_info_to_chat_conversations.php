<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add source tracking to chat_conversations
     * - source_type: where the message came from (ad, organic, m.me link, etc.)
     * - ad_id: Facebook Ad ID if from ad
     * - ad_name: Ad name for display
     */
    public function up(): void
    {
        Schema::table('chat_conversations', function (Blueprint $table) {
            $table->string('source_type', 50)->default('organic')->after('status'); // ad, organic, m.me, referral
            $table->string('ad_id')->nullable()->after('source_type'); // Facebook Ad ID
            $table->string('ad_name')->nullable()->after('ad_id'); // Ad name for display
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_conversations', function (Blueprint $table) {
            $table->dropColumn(['source_type', 'ad_id', 'ad_name']);
        });
    }
};
