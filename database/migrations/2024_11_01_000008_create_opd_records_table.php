<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ข้อ 2: Auto-create temporary OPD on booking
     * ข้อ 3: Cancellation logic (new patient: delete OPD, existing: keep OPD)
     */
    public function up(): void
    {
        Schema::create('opd_records', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('patient_id')->nullable()->constrained('patients'); // ข้อ 2: Nullable for temporary OPD
            $table->foreignUuid('branch_id')->constrained('branches');
            $table->string('opd_number', 50);
            $table->string('status', 50)->default('active'); // active, completed, cancelled
            $table->text('chief_complaint')->nullable();
            $table->boolean('is_temporary')->default(false); // ข้อ 2: Temp OPD from booking
            $table->foreignUuid('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['opd_number'], 'idx_opd_number_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opd_records');
    }
};
