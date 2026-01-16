<?php

namespace App\Http\Controllers;

use App\Models\{CrmCall, FollowUpList, ConfirmationList, Appointment, Treatment, Patient, Branch};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CrmController extends Controller
{
    /**
     * Display CRM dashboard with both call lists
     */
    public function index(Request $request)
    {
        $today = Carbon::today();
        $tomorrow = $today->copy()->addDay();
        $yesterday = $today->copy()->subDay();
        $branchId = $request->branch_id;
        $cutoffTime = $today->copy()->setTime(17, 0, 0);

        // Generate calls if after 17:00
        if (now()->gte($cutoffTime)) {
            $this->generateConfirmationCalls($tomorrow, $cutoffTime);
        }
        $this->generateFollowUpCalls($yesterday);

        // Get confirmation calls for today (appointments tomorrow)
        $confirmationQuery = CrmCall::with(['patient', 'appointment', 'branch', 'caller'])
            ->where('call_type', 'confirmation')
            ->whereDate('scheduled_date', $today)
            ->orderBy('status')
            ->orderBy('created_at');

        if ($branchId) {
            $confirmationQuery->where('branch_id', $branchId);
        }

        $confirmationCalls = $confirmationQuery->get();

        // Get follow-up calls for today (treatments yesterday)
        $followUpQuery = CrmCall::with(['patient', 'treatment.service', 'branch', 'caller'])
            ->where('call_type', 'follow_up')
            ->whereDate('scheduled_date', $today)
            ->orderBy('status')
            ->orderBy('created_at');

        if ($branchId) {
            $followUpQuery->where('branch_id', $branchId);
        }

        $followUpCalls = $followUpQuery->get();

        // Stats
        $stats = [
            'confirmation_pending' => $confirmationCalls->where('status', 'pending')->count(),
            'confirmation_done' => $confirmationCalls->where('status', '!=', 'pending')->count(),
            'followup_pending' => $followUpCalls->where('status', 'pending')->count(),
            'followup_done' => $followUpCalls->where('status', '!=', 'pending')->count(),
        ];

        $branches = Branch::all();

        return view('crm.index', compact('confirmationCalls', 'followUpCalls', 'stats', 'branches'));
    }

    /**
     * Generate confirmation calls for tomorrow's appointments
     */
    private function generateConfirmationCalls($appointmentDate, $cutoffTime)
    {
        $appointments = Appointment::with('patient')
            ->whereDate('appointment_date', $appointmentDate)
            ->where('created_at', '<=', $cutoffTime)
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->get();

        foreach ($appointments as $appointment) {
            $exists = CrmCall::where('appointment_id', $appointment->id)
                ->where('call_type', 'confirmation')
                ->exists();

            if (!$exists && $appointment->patient) {
                CrmCall::create([
                    'patient_id' => $appointment->patient_id,
                    'branch_id' => $appointment->branch_id,
                    'appointment_id' => $appointment->id,
                    'call_type' => 'confirmation',
                    'scheduled_date' => now()->toDateString(),
                    'cutoff_time' => '17:00:00',
                    'status' => 'pending',
                ]);
            }
        }
    }

    /**
     * Generate follow-up calls for yesterday's treatments
     */
    private function generateFollowUpCalls($treatmentDate)
    {
        $treatments = Treatment::with('patient', 'appointment')
            ->whereDate('completed_at', $treatmentDate)
            ->whereNotNull('completed_at')
            ->get();

        foreach ($treatments as $treatment) {
            $exists = CrmCall::where('treatment_id', $treatment->id)
                ->where('call_type', 'follow_up')
                ->exists();

            if (!$exists && $treatment->patient) {
                CrmCall::create([
                    'patient_id' => $treatment->patient_id,
                    'branch_id' => $treatment->branch_id ?? ($treatment->appointment->branch_id ?? null),
                    'treatment_id' => $treatment->id,
                    'call_type' => 'follow_up',
                    'scheduled_date' => now()->toDateString(),
                    'status' => 'pending',
                ]);
            }
        }
    }

    /**
     * Update call status
     */
    public function updateCall(Request $request, $id)
    {
        $call = CrmCall::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,called,no_answer,confirmed,cancelled,rescheduled',
            'notes' => 'nullable|string',
            'patient_feedback' => 'nullable|string',
        ]);

        $call->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? $call->notes,
            'patient_feedback' => $validated['patient_feedback'] ?? $call->patient_feedback,
            'called_by' => auth()->id(),
            'called_at' => now(),
        ]);

        // Update appointment status if confirmation call
        if ($call->call_type === 'confirmation' && $call->appointment) {
            $appointmentStatus = match($validated['status']) {
                'confirmed' => 'confirmed',
                'cancelled' => 'cancelled',
                'rescheduled' => 'rescheduled',
                default => $call->appointment->status,
            };
            $call->appointment->update(['status' => $appointmentStatus]);
        }

        return response()->json([
            'success' => true,
            'message' => 'บันทึกการโทรเรียบร้อย',
        ]);
    }

    /**
     * Refresh/regenerate calls
     */
    public function refreshCalls()
    {
        $today = Carbon::today();
        $tomorrow = $today->copy()->addDay();
        $yesterday = $today->copy()->subDay();
        $cutoffTime = $today->copy()->setTime(17, 0, 0);

        $this->generateConfirmationCalls($tomorrow, $cutoffTime);
        $this->generateFollowUpCalls($yesterday);

        return response()->json([
            'success' => true,
            'message' => 'รีเฟรชรายการโทรเรียบร้อย',
        ]);
    }

    /**
     * Follow-up List (ข้อ 30)
     * Auto-generate list of patients needing follow-up calls (treated yesterday)
     */
    public function followUpList(Request $request)
    {
        $branchId = $request->input('branch_id');
        $status = $request->input('status', 'pending');

        $followUps = FollowUpList::with(['patient', 'branch', 'pt', 'treatment'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($status, fn($q) => $q->where('status', $status))
            ->orderBy('follow_up_date', 'asc')
            ->orderBy('priority', 'desc')
            ->get();

        $branches = Branch::all();

        return view('crm.follow-up', compact('followUps', 'branches'));
    }

    /**
     * Auto-generate Follow-up List (ข้อ 30)
     * Create list from patients treated yesterday
     */
    public function generateFollowUpList(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
            'date' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            $targetDate = $validated['date'];
            $branchId = $validated['branch_id'] ?? null;

            // Find treatments from target date that need follow-up
            $treatments = Treatment::whereDate('completed_at', $targetDate)
                ->whereNotNull('completed_at')
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->with(['patient', 'pt'])
                ->get();

            $created = 0;
            $skipped = 0;

            foreach ($treatments as $treatment) {
                // Check if follow-up already exists
                $exists = FollowUpList::where('treatment_id', $treatment->id)->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                // Create follow-up record
                FollowUpList::create([
                    'patient_id' => $treatment->patient_id,
                    'treatment_id' => $treatment->id,
                    'branch_id' => $treatment->branch_id,
                    'pt_id' => $treatment->pt_id,
                    'follow_up_date' => now()->addDays(1), // Follow up tomorrow
                    'priority' => 'normal',
                    'notes' => 'Auto-generated follow-up from treatment on ' . $targetDate,
                    'status' => 'pending',
                    'created_by' => auth()->id() ?? null,
                ]);

                $created++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Follow-up list generated: {$created} created, {$skipped} skipped",
                'created' => $created,
                'skipped' => $skipped,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate follow-up list: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update follow-up status
     */
    public function updateFollowUp(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,contacted,scheduled,completed,cancelled',
            'contact_notes' => 'nullable|string',
            'appointment_id' => 'nullable|exists:appointments,id',
        ]);

        try {
            $followUp = FollowUpList::findOrFail($id);

            $updateData = [
                'status' => $validated['status'],
            ];

            if ($validated['status'] === 'contacted') {
                $updateData['contacted_at'] = now();
                $updateData['contacted_by'] = auth()->id();
                $updateData['contact_notes'] = $validated['contact_notes'] ?? null;
            }

            if ($validated['status'] === 'completed') {
                $updateData['completed_at'] = now();
            }

            if (!empty($validated['appointment_id'])) {
                $updateData['appointment_id'] = $validated['appointment_id'];
            }

            $followUp->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Follow-up updated successfully',
                'followUp' => $followUp,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update follow-up: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Confirmation List (ข้อ 31)
     * Auto-generate list of appointments for tomorrow that need confirmation calls
     */
    public function confirmationList(Request $request)
    {
        $branchId = $request->input('branch_id');
        $status = $request->input('status', 'pending');

        $confirmations = ConfirmationList::with(['appointment', 'patient', 'branch'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($status, fn($q) => $q->where('confirmation_status', $status))
            ->orderBy('appointment_date', 'asc')
            ->orderBy('appointment_time', 'asc')
            ->get();

        $branches = Branch::all();

        return view('crm.confirmation', compact('confirmations', 'branches'));
    }

    /**
     * Auto-generate Confirmation List (ข้อ 31)
     * Create list from appointments for tomorrow
     */
    public function generateConfirmationList(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
            'appointment_date' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            $appointmentDate = $validated['appointment_date'];
            $branchId = $validated['branch_id'] ?? null;

            // Find appointments for target date
            $appointments = Appointment::whereDate('appointment_date', $appointmentDate)
                ->where('status', '!=', 'cancelled')
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->with(['patient'])
                ->get();

            $created = 0;
            $skipped = 0;

            foreach ($appointments as $appointment) {
                // Skip appointments without patient (can't confirm without patient info)
                if (!$appointment->patient_id) {
                    $skipped++;
                    continue;
                }

                // Check if confirmation already exists
                $exists = ConfirmationList::where('appointment_id', $appointment->id)->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                // Create confirmation record
                ConfirmationList::create([
                    'appointment_id' => $appointment->id,
                    'patient_id' => $appointment->patient_id,
                    'branch_id' => $appointment->branch_id,
                    'appointment_date' => $appointment->appointment_date,
                    'appointment_time' => $appointment->appointment_time,
                    'confirmation_status' => 'pending',
                    'is_auto_generated' => true,
                    'generated_date' => today(),
                ]);

                $created++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Confirmation list generated: {$created} created, {$skipped} skipped",
                'created' => $created,
                'skipped' => $skipped,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate confirmation list: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update confirmation status
     */
    public function updateConfirmation(Request $request, $id)
    {
        $validated = $request->validate([
            'confirmation_status' => 'required|in:pending,confirmed,declined,no_answer',
            'confirmation_notes' => 'nullable|string',
        ]);

        try {
            $confirmation = ConfirmationList::findOrFail($id);

            $updateData = [
                'confirmation_status' => $validated['confirmation_status'],
                'call_attempts' => $confirmation->call_attempts + 1,
                'last_call_attempt_at' => now(),
            ];

            if ($validated['confirmation_status'] === 'confirmed') {
                $updateData['confirmed_at'] = now();
                $updateData['confirmed_by'] = auth()->id();
            }

            if (!empty($validated['confirmation_notes'])) {
                $updateData['confirmation_notes'] = $validated['confirmation_notes'];
            }

            $confirmation->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Confirmation updated successfully',
                'confirmation' => $confirmation,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update confirmation: ' . $e->getMessage()
            ], 500);
        }
    }
}
