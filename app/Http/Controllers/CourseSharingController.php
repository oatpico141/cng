<?php

namespace App\Http\Controllers;

use App\Models\CourseSharing;
use App\Models\CourseSharedUser;
use App\Models\CoursePurchase;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseSharingController extends Controller
{
    /**
     * Store a new shared user for a course purchase
     */
    public function store(Request $request)
    {
        // Validation
        $validated = $request->validate([
            'course_purchase_id' => 'required|exists:course_purchases,id',
            'shared_patient_phone' => 'required|string',
            'relationship' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'max_sessions' => 'nullable|integer|min:1',
        ], [
            'course_purchase_id.required' => 'กรุณาระบุคอร์สที่ต้องการแชร์',
            'course_purchase_id.exists' => 'ไม่พบคอร์สที่ระบุ',
            'shared_patient_phone.required' => 'กรุณากรอกเบอร์โทรศัพท์ของผู้ที่ต้องการแชร์',
            'max_sessions.min' => 'จำนวนครั้งต้องมากกว่า 0',
        ]);

        // Check if shared patient exists in system
        $sharedPatient = Patient::where('phone', $validated['shared_patient_phone'])->first();

        if (!$sharedPatient) {
            return redirect()
                ->back()
                ->with('error', 'ไม่พบคนไข้ที่มีเบอร์โทรศัพท์ ' . $validated['shared_patient_phone'] . ' ในระบบ');
        }

        // Get course purchase to verify owner
        $coursePurchase = CoursePurchase::findOrFail($validated['course_purchase_id']);

        // Check if trying to share with self
        if ($coursePurchase->patient_id === $sharedPatient->id) {
            return redirect()
                ->back()
                ->with('error', 'ไม่สามารถแชร์คอร์สให้ตัวเองได้');
        }

        // Check if already shared with this patient
        $existingShare = CourseSharedUser::where('course_purchase_id', $validated['course_purchase_id'])
            ->where('shared_patient_id', $sharedPatient->id)
            ->first();

        if ($existingShare) {
            return redirect()
                ->back()
                ->with('error', 'คอร์สนี้ถูกแชร์ให้ ' . $sharedPatient->name . ' แล้ว');
        }

        try {
            // Create shared user record
            CourseSharedUser::create([
                'course_purchase_id' => $validated['course_purchase_id'],
                'owner_patient_id' => $coursePurchase->patient_id,
                'shared_patient_id' => $sharedPatient->id,
                'relationship' => $validated['relationship'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'is_active' => true,
                'max_sessions' => $validated['max_sessions'] ?? null,
                'used_sessions' => 0,
                'created_by' => Auth::id() ?? null,
            ]);

            return redirect()
                ->back()
                ->with('success', 'แชร์คอร์สให้ ' . $sharedPatient->name . ' เรียบร้อยแล้ว');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'ไม่สามารถแชร์คอร์สได้: ' . $e->getMessage());
        }
    }

    /**
     * Remove shared user access
     */
    public function destroy($id)
    {
        try {
            $sharedUser = CourseSharedUser::findOrFail($id);
            $sharedPatientName = $sharedUser->sharedPatient->name;

            $sharedUser->delete(); // Soft delete

            return redirect()
                ->back()
                ->with('success', 'ยกเลิกการแชร์ให้ ' . $sharedPatientName . ' แล้ว');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'ไม่สามารถยกเลิกการแชร์ได้: ' . $e->getMessage());
        }
    }

    /**
     * Search patient by phone for sharing modal
     */
    public function searchPatient(Request $request)
    {
        $phone = $request->input('phone');

        if (empty($phone)) {
            return response()->json(['patient' => null]);
        }

        $patient = Patient::where('phone', $phone)->first();

        if ($patient) {
            return response()->json([
                'patient' => [
                    'id' => $patient->id,
                    'name' => $patient->name,
                    'phone' => $patient->phone,
                    'hn' => $patient->hn,
                ]
            ]);
        }

        return response()->json(['patient' => null]);
    }
}
