<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Document storage (receipts, tax invoices, reports)
     */
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('document_type', 50); // receipt, tax_invoice, official_receipt, report
            $table->string('document_number', 50)->unique();
            $table->foreignUuid('invoice_id')->nullable()->constrained('invoices');
            $table->foreignUuid('payment_id')->nullable()->constrained('payments');
            $table->foreignUuid('patient_id')->nullable()->constrained('patients');
            $table->foreignUuid('branch_id')->constrained('branches');
            $table->string('file_path', 500)->nullable(); // PDF storage path
            $table->string('file_name', 255)->nullable();
            $table->integer('file_size')->nullable(); // bytes
            $table->date('document_date');
            $table->string('status', 50)->default('active'); // active, voided, cancelled
            $table->text('notes')->nullable();

            $table->foreignUuid('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['document_type', 'document_date']);
            $table->index(['invoice_id']);
            $table->index(['branch_id', 'document_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
