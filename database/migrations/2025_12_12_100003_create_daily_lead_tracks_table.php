<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Table: daily_lead_tracks - Tracks lead status per day for ROI analysis
     */
    public function up(): void
    {
        Schema::create('daily_lead_tracks', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Link to conversation
            $table->uuid('conversation_id');
            $table->foreign('conversation_id')
                  ->references('id')
                  ->on('chat_conversations')
                  ->cascadeOnDelete();

            // Daily tracking - one record per conversation per day
            $table->date('tracking_date');

            // Lead status progression
            $table->enum('status', [
                'new',        // First contact
                'contacted',  // Agent responded
                'interested', // Shows interest
                'booked',     // Appointment made
                'completed',  // Treatment done
                'no_show',    // Did not come
                'cancelled',  // Cancelled
                'lost'        // Unresponsive/lost
            ])->default('new');

            // Sales attribution - links to existing users table
            $table->uuid('sale_closed_by')->nullable();
            $table->foreign('sale_closed_by')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();

            // Marketing attribution
            $table->string('ad_source_id', 100)->nullable(); // e.g., "fb_ad_001", "line_oa"
            $table->json('utm_data')->nullable(); // UTM parameters

            // Notes
            $table->text('notes')->nullable();

            $table->timestamps();

            // One status per conversation per day
            $table->unique(['conversation_id', 'tracking_date'], 'daily_track_unique');

            // Indexes for reporting
            $table->index('tracking_date');
            $table->index('status');
            $table->index('ad_source_id');
            $table->index('sale_closed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_lead_tracks');
    }
};
