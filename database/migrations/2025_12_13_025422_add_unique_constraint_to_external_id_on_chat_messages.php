<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add unique constraint to external_id to prevent duplicate messages
     */
    public function up(): void
    {
        // First, remove any existing duplicates (keep the first one)
        DB::statement("
            DELETE t1 FROM chat_messages t1
            INNER JOIN chat_messages t2
            WHERE t1.id > t2.id
            AND t1.external_id = t2.external_id
            AND t1.external_id IS NOT NULL
        ");

        // Drop existing index if exists
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropIndex(['external_id']);
        });

        // Add unique index
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->unique('external_id', 'chat_messages_external_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropUnique('chat_messages_external_id_unique');
            $table->index('external_id');
        });
    }
};
