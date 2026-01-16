<?php

namespace App\Http\Controllers;

use App\Models\Treatment;
use App\Models\TreatmentAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TreatmentController extends Controller
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
     * Update treatment record with audit logging
     * CRITICAL: Audit log MUST be created BEFORE updating the record
     */
    public function update(Request $request, $id)
    {
        $treatment = Treatment::findOrFail($id);

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
            $originalData = $treatment->toArray();

            foreach ($request->except(['_token', '_method', 'reason']) as $field => $newValue) {
                if (array_key_exists($field, $originalData) && $originalData[$field] != $newValue) {
                    $changes[$field] = [
                        'old' => $originalData[$field],
                        'new' => $newValue
                    ];
                }
            }

            if (!empty($changes)) {
                TreatmentAuditLog::create([
                    'treatment_id' => $treatment->id,
                    'action' => 'updated',
                    'changes' => $changes,
                    'performed_by' => Auth::id(),
                    'reason' => $validated['reason'],
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }

            // STEP 2: Now update the treatment record
            $treatment->update($request->except(['_token', '_method', 'reason']));

            DB::commit();

            return redirect()
                ->back()
                ->with('success', 'แก้ไขประวัติการรักษาเรียบร้อย (บันทึกใน Audit Log แล้ว)');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'ไม่สามารถแก้ไขข้อมูลได้: ' . $e->getMessage());
        }
    }

    /**
     * Soft delete treatment record with audit logging
     * CRITICAL: Audit log MUST be created BEFORE deleting the record
     */
    public function destroy(Request $request, $id)
    {
        $treatment = Treatment::findOrFail($id);

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
            TreatmentAuditLog::create([
                'treatment_id' => $treatment->id,
                'action' => 'deleted',
                'changes' => [
                    'full_record' => $treatment->toArray(),
                ],
                'performed_by' => Auth::id(),
                'reason' => $validated['reason'],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // STEP 2: Now soft delete the treatment record
            $treatment->delete(); // This is a soft delete because Treatment model uses SoftDeletes

            DB::commit();

            return redirect()
                ->back()
                ->with('success', 'ลบประวัติการรักษาเรียบร้อย (ข้อมูลยังอยู่ในระบบและบันทึกใน Audit Log แล้ว)');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'ไม่สามารถลบข้อมูลได้: ' . $e->getMessage());
        }
    }
}
