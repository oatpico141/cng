<?php

namespace App\Http\Controllers;

use App\Models\CoursePurchase;
use App\Models\AuditLog;
use App\Models\Treatment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CoursePurchaseController extends Controller
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

    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Delete course purchase with Audit Log (Soft Delete)
     *
     * CRITICAL: STEP 1 = Create Audit Log, STEP 2 = Soft Delete
     */
    public function destroy(Request $request, $id)
    {
        // Validate reason
        $validated = $request->validate([
            'delete_reason' => 'required|string|min:5',
        ], [
            'delete_reason.required' => 'กรุณาระบุเหตุผลในการลบ',
            'delete_reason.min' => 'เหตุผลต้องมีอย่างน้อย 5 ตัวอักษร',
        ]);

        try {
            // Use DB Transaction for atomicity
            DB::beginTransaction();

            // Find course purchase
            $coursePurchase = CoursePurchase::with(['package', 'patient'])->findOrFail($id);

            // STEP 1: Create Audit Log BEFORE deletion (CRITICAL REQUIREMENT)
            AuditLog::create([
                'user_id' => Auth::id() ?? null,
                'action' => 'delete',
                'module' => 'course_purchases',
                'model_type' => 'App\Models\CoursePurchase',
                'model_id' => $coursePurchase->id,
                'old_values' => $coursePurchase->toArray(),
                'new_values' => null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'description' => 'ลบคอร์ส: ' . ($coursePurchase->package->name ?? 'ไม่มีชื่อ') .
                                ' (Patient: ' . ($coursePurchase->patient->name ?? 'ไม่ระบุ') . ')' .
                                ' | เหตุผล: ' . $validated['delete_reason'],
                'branch_id' => Auth::check() ? Auth::user()->branch_id : null,
            ]);

            // STEP 2: Soft Delete course purchase
            $coursePurchase->delete();

            // Commit transaction
            DB::commit();

            // Return JSON for AJAX requests
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'ลบคอร์สเรียบร้อยแล้ว (ข้อมูลถูกบันทึกใน Audit Log)'
                ]);
            }

            return redirect()
                ->back()
                ->with('success', 'ลบคอร์สเรียบร้อยแล้ว (ข้อมูลถูกบันทึกใน Audit Log)');

        } catch (\Exception $e) {
            // Rollback on error
            DB::rollBack();

            // Return JSON for AJAX requests
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่สามารถลบคอร์สได้: ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->with('error', 'ไม่สามารถลบคอร์สได้: ' . $e->getMessage());
        }
    }

    /**
     * Get course usage history with patient information (Task 2.13)
     */
    public function usageHistory($id)
    {
        try {
            $coursePurchase = CoursePurchase::with('patient')->findOrFail($id);

            // Query treatments/sessions where this course was used
            // CRITICAL: Include patient data to show "used by" information
            $usageHistory = Treatment::where('course_purchase_id', $id)
                ->with(['pt', 'patient', 'branch'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($treatment) use ($coursePurchase) {
                    // Determine who used this session
                    // If treatment patient = course owner → Owner used it
                    // If treatment patient ≠ course owner → Shared patient used it
                    $usedByPatient = $treatment->patient;
                    $courseOwner = $coursePurchase->patient;

                    $usedByPatientName = $usedByPatient ? $usedByPatient->name : 'ไม่ระบุ';

                    // Add indicator if shared patient used it
                    if ($usedByPatient && $courseOwner && $usedByPatient->id !== $courseOwner->id) {
                        $usedByPatientName .= ' (ผู้ใช้ร่วม)';
                    }

                    return [
                        'date' => \Carbon\Carbon::parse($treatment->created_at)->locale('th')->isoFormat('D MMM YYYY'),
                        'time' => \Carbon\Carbon::parse($treatment->created_at)->format('H:i') . ' น.',
                        'sessions' => 1, // Each treatment uses 1 session
                        'pt_name' => $treatment->pt->name ?? 'ไม่ระบุ',
                        'notes' => $treatment->diagnosis ?? $treatment->treatment_plan ?? '-',
                        'used_by_patient_name' => $usedByPatientName, // NEW FIELD (Task 2.13)
                    ];
                });

            // Calculate summary
            $totalUsed = $usageHistory->count();
            $totalSessions = $coursePurchase->total_sessions ?? 10;
            $remaining = $totalSessions - ($coursePurchase->used_sessions ?? 0);

            return response()->json([
                'success' => true,
                'usage_history' => $usageHistory,
                'summary' => [
                    'total_used' => $totalUsed,
                    'total_sessions' => $totalSessions,
                    'remaining' => $remaining,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
