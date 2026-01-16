<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    /**
     * Update invoice record with audit logging
     * CRITICAL: Audit log MUST be created BEFORE updating the record
     */
    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        // Validate reason is required for updates
        $validated = $request->validate([
            'reason' => 'required|string|min:5',
            // Add other fields as needed
        ], [
            'reason.required' => 'กรุณาระบุเหตุผลในการแก้ไข',
            'reason.min' => 'เหตุผลต้องมีความยาวอย่างน้อย 5 ตัวอักษร',
        ]);

        try {
            DB::beginTransaction();

            // STEP 1: Create audit log BEFORE making any changes
            // This preserves the original data
            $changes = [];
            $originalData = $invoice->toArray();

            foreach ($request->except(['_token', '_method', 'reason']) as $field => $newValue) {
                if (array_key_exists($field, $originalData) && $originalData[$field] != $newValue) {
                    $changes[$field] = [
                        'old' => $originalData[$field],
                        'new' => $newValue
                    ];
                }
            }

            if (!empty($changes)) {
                AuditLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'update',
                    'module' => 'invoices',
                    'model_type' => Invoice::class,
                    'model_id' => $invoice->id,
                    'old_values' => $originalData,
                    'new_values' => $request->except(['_token', '_method', 'reason']),
                    'description' => 'แก้ไขใบเสร็จ: ' . $validated['reason'],
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                    'branch_id' => $invoice->branch_id,
                ]);
            }

            // STEP 2: Now update the invoice record
            $invoice->update($request->except(['_token', '_method', 'reason']));

            DB::commit();

            return redirect()
                ->back()
                ->with('success', 'แก้ไขใบเสร็จเรียบร้อย (บันทึกใน Audit Log แล้ว)');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'ไม่สามารถแก้ไขข้อมูลได้: ' . $e->getMessage());
        }
    }

    /**
     * Soft delete invoice record with audit logging
     * CRITICAL: Audit log MUST be created BEFORE deleting the record
     */
    public function destroy(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        // Validate reason is required for deletion
        $validated = $request->validate([
            'reason' => 'required|string|min:5',
        ], [
            'reason.required' => 'กรุณาระบุเหตุผลในการลบ',
            'reason.min' => 'เหตุผลต้องมีความยาวอย่างน้อย 5 ตัวอักษร',
        ]);

        try {
            DB::beginTransaction();

            // STEP 1: Create audit log BEFORE soft deleting
            // This preserves the complete record before deletion
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'delete',
                'module' => 'invoices',
                'model_type' => Invoice::class,
                'model_id' => $invoice->id,
                'old_values' => $invoice->toArray(),
                'new_values' => null,
                'description' => 'ลบใบเสร็จ: ' . $validated['reason'],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'branch_id' => $invoice->branch_id,
            ]);

            // STEP 2: Now soft delete the invoice record
            $invoice->delete(); // This is a soft delete because Invoice model uses SoftDeletes

            DB::commit();

            return redirect()
                ->back()
                ->with('success', 'ลบใบเสร็จเรียบร้อย (ข้อมูลยังอยู่ในระบบและบันทึกใน Audit Log แล้ว)');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'ไม่สามารถลบข้อมูลได้: ' . $e->getMessage());
        }
    }
}
