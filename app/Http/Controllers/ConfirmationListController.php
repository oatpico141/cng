<?php

namespace App\Http\Controllers;

use App\Models\{ConfirmationList, Appointment, Patient, Queue};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConfirmationListController extends Controller
{
    /**
     * Display confirmation list
     */
    public function index(Request $request)
    {
        $date = $request->input('date', today());
        $branchId = $request->input('branch_id');

        $confirmations = ConfirmationList::where('appointment_date', $date)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->with(['patient', 'appointment'])
            ->orderBy('appointment_time')
            ->get();

        return view('confirmation-lists.index', compact('confirmations', 'date', 'branchId'));
    }

    /**
     * Auto-generate Confirmation List (ข้อ 31)
     * สร้างลิสต์คนไข้ที่ต้องโทรคอนเฟิร์มนัด (สำหรับนัดพรุ่งนี้)
     */
    public function autoGenerate(Request $request)
    {
        try {
            DB::beginTransaction();

            $targetDate = $request->input('target_date', tomorrow());
            $branchId = $request->input('branch_id');

            // Find all appointments for tomorrow
            $appointments = Appointment::whereDate('appointment_date', $targetDate)
                ->whereIn('status', ['confirmed', 'pending']) // ยังไม่ cancelled
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->with(['patient'])
                ->get();

            $created = 0;

            foreach ($appointments as $appointment) {
                // Check if already exists
                $existing = ConfirmationList::where('appointment_id', $appointment->id)->first();

                if (!$existing) {
                    ConfirmationList::create([
                        'appointment_id' => $appointment->id,
                        'patient_id' => $appointment->patient_id,
                        'branch_id' => $appointment->branch_id,
                        'appointment_date' => $appointment->appointment_date,
                        'appointment_time' => $appointment->appointment_time,
                        'confirmation_status' => 'pending',
                        'call_attempts' => 0,
                        'is_auto_generated' => true,
                        'generated_date' => today(),
                    ]);
                    $created++;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Generated {$created} confirmation items",
                'created' => $created,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate confirmation list: ' . $e->getMessage()
            ], 500);
        }
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
        $confirmation = ConfirmationList::findOrFail($id);

        $validated = $request->validate([
            'confirmation_status' => 'required|in:pending,confirmed,cancelled,no_answer',
            'confirmation_notes' => 'nullable|string',
        ]);

        $updateData = [
            'confirmation_status' => $validated['confirmation_status'],
            'confirmation_notes' => $validated['confirmation_notes'] ?? null,
            'call_attempts' => $confirmation->call_attempts + 1,
            'last_call_attempt_at' => now(),
        ];

        if ($validated['confirmation_status'] === 'confirmed') {
            $updateData['confirmed_at'] = now();
            $updateData['confirmed_by'] = auth()->id();
        }

        $confirmation->update($updateData);

        return response()->json(['success' => true]);
    }

    /**
     * Action A: ยืนยันมาตามนัด (Confirm)
     * - อัปเดต confirmation_lists.status = confirmed
     * - นัดหมายยังคงเป็น scheduled (ปกติ)
     */
    public function confirmAttendance($id)
    {
        try {
            DB::beginTransaction();

            $confirmation = ConfirmationList::with('appointment')->findOrFail($id);

            // Update confirmation status
            $confirmation->update([
                'confirmation_status' => 'confirmed',
                'confirmed_at' => now(),
                'confirmed_by' => auth()->id(),
                'call_attempts' => $confirmation->call_attempts + 1,
                'last_call_attempt_at' => now(),
            ]);

            // Appointment stays as 'scheduled' or 'confirmed' - no change needed
            // When the day comes, system will pull to Queue automatically

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'ยืนยันการนัดหมายสำเร็จ',
                'status' => 'confirmed'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Action B: ขอยกเลิกนัด (Request Cancel)
     * - อัปเดต confirmation_lists.status = contacted_cancel
     * - อัปเดต appointments.status = cancelled ทันที
     */
    public function requestCancel($id, Request $request)
    {
        try {
            DB::beginTransaction();

            $confirmation = ConfirmationList::with(['appointment', 'patient'])->findOrFail($id);

            // *** FORCE DELETE TEMPORARY PATIENT ***
            if ($confirmation->patient && $confirmation->patient->is_temporary) {
                $patientId = $confirmation->patient_id;

                // Delete related data first
                Queue::where('patient_id', $patientId)->forceDelete();
                ConfirmationList::where('patient_id', $patientId)->forceDelete();
                Appointment::where('patient_id', $patientId)->forceDelete();

                // Force delete the patient
                $confirmation->patient->forceDelete();

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'ยกเลิกนัดและลบข้อมูลลูกค้าชั่วคราวสำเร็จ',
                    'status' => 'contacted_cancel',
                    'temporary_patient_deleted' => true
                ]);
            }

            // Update confirmation status
            $confirmation->update([
                'confirmation_status' => 'contacted_cancel',
                'confirmation_notes' => $request->notes ?? 'ลูกค้าขอยกเลิกนัด',
                'call_attempts' => $confirmation->call_attempts + 1,
                'last_call_attempt_at' => now(),
            ]);

            // Cancel the appointment
            if ($confirmation->appointment) {
                $confirmation->appointment->update([
                    'status' => 'cancelled',
                    'cancellation_reason' => $request->notes ?? 'ยกเลิกจากการโทรยืนยัน',
                    'cancelled_at' => now(),
                    'cancelled_by' => auth()->id(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'ยกเลิกนัดหมายสำเร็จ',
                'status' => 'contacted_cancel'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Action C: ขอเลื่อนนัด (Request Reschedule)
     * - อัปเดต confirmation_lists.status = contacted_reschedule
     * - อัปเดต appointments.appointment_date และ status = rescheduled
     */
    public function requestReschedule($id, Request $request)
    {
        try {
            $validated = $request->validate([
                'new_date' => 'required|date|after_or_equal:today',
                'new_time' => 'required',
                'notes' => 'nullable|string',
            ]);

            DB::beginTransaction();

            $confirmation = ConfirmationList::with('appointment')->findOrFail($id);

            // Update confirmation status
            $confirmation->update([
                'confirmation_status' => 'contacted_reschedule',
                'confirmation_notes' => $validated['notes'] ?? 'เลื่อนนัดเป็นวันที่ ' . $validated['new_date'],
                'call_attempts' => $confirmation->call_attempts + 1,
                'last_call_attempt_at' => now(),
            ]);

            // Reschedule the appointment
            if ($confirmation->appointment) {
                $confirmation->appointment->update([
                    'appointment_date' => $validated['new_date'],
                    'appointment_time' => $validated['new_time'],
                    'status' => 'rescheduled',
                    'notes' => ($confirmation->appointment->notes ? $confirmation->appointment->notes . "\n" : '') .
                               'เลื่อนจาก ' . $confirmation->appointment_date . ' ' . $confirmation->appointment_time,
                ]);

                // Also update confirmation list date/time
                $confirmation->update([
                    'appointment_date' => $validated['new_date'],
                    'appointment_time' => $validated['new_time'],
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'เลื่อนนัดหมายสำเร็จ',
                'status' => 'contacted_reschedule',
                'new_date' => $validated['new_date'],
                'new_time' => $validated['new_time']
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Action D: ไม่รับสาย (No Answer)
     * - อัปเดต confirmation_lists.status = no_answer
     * - เก็บ Log ไว้ว่าโทรแล้วแต่ไม่รับ
     */
    public function markNoAnswer($id)
    {
        try {
            $confirmation = ConfirmationList::findOrFail($id);

            $confirmation->update([
                'confirmation_status' => 'no_answer',
                'call_attempts' => $confirmation->call_attempts + 1,
                'last_call_attempt_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'บันทึกไม่รับสายสำเร็จ',
                'status' => 'no_answer',
                'call_attempts' => $confirmation->call_attempts
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        //
    }
}
