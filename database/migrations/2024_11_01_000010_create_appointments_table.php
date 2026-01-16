<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ข้อ 1: Booking 2 Channels (Walk-in + Line Booking)
     * ข้อ 10: Calendar view with filtering by PT/Branch/Date
     * ข้อ 11: PT Request/Change mechanism
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('patient_id')->nullable()->constrained('patients'); // ข้อ 2: Nullable for new patients without patient record
            $table->foreignUuid('branch_id')->constrained('branches');
            $table->foreignUuid('pt_id')->nullable()->constrained('users'); // PT assigned
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->string('booking_channel', 50); // walk_in, line_booking
            $table->string('status', 50)->default('pending'); // pending, confirmed, arrived, completed, cancelled, no_show
            $table->text('notes')->nullable();

            // ข้อ 11: PT Request/Change tracking
            $table->foreignUuid('requested_pt_id')->nullable()->constrained('users');
            $table->text('pt_change_reason')->nullable();
            $table->timestamp('pt_changed_at')->nullable();
            $table->foreignUuid('pt_changed_by')->nullable()->constrained('users');

            // Cancellation tracking
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignUuid('cancelled_by')->nullable()->constrained('users');

            $table->foreignUuid('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['branch_id', 'appointment_date', 'status']);
            $table->index(['pt_id', 'appointment_date']);
            $table->index(['patient_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
