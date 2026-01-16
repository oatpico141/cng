<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Staff;
use App\Models\Patient;
use App\Models\OpdRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    /**
     * Display appointment calendar
     */
    public function index()
    {
        $branches = Branch::where('is_active', true)->get();
        // Use User model instead of Staff for PT selection
        $staff = User::whereHas('role', function($q) {
            $q->whereIn('name', ['PT', 'Admin', 'Manager']);
        })->get();

        // *** BRANCH FILTER - แยกข้อมูลตามสาขา ***
        $selectedBranchId = session('selected_branch_id');
        $user = auth()->user();
        $canViewAllBranches = $user && $user->role && in_array($user->role->name, ['Admin', 'Owner', 'Super Admin']);

        // Determine which branch to filter
        $filterBranchId = null;
        if (!$canViewAllBranches) {
            $filterBranchId = $selectedBranchId ?: ($user ? $user->branch_id : null);
        }

        // Get today's statistics - filtered by branch
        $today = today();
        $appointmentQuery = Appointment::whereDate('appointment_date', $today);
        if ($filterBranchId) {
            $appointmentQuery->where('branch_id', $filterBranchId);
        }
        $todayAppointments = $appointmentQuery->count();

        $confirmedQuery = Appointment::whereDate('appointment_date', $today)->where('status', 'confirmed');
        if ($filterBranchId) $confirmedQuery->where('branch_id', $filterBranchId);
        $confirmedAppointments = $confirmedQuery->count();

        $pendingQuery = Appointment::whereDate('appointment_date', $today)->where('status', 'pending');
        if ($filterBranchId) $pendingQuery->where('branch_id', $filterBranchId);
        $pendingAppointments = $pendingQuery->count();

        $cancelledQuery = Appointment::whereDate('appointment_date', $today)->whereIn('status', ['cancelled', 'no_show']);
        if ($filterBranchId) $cancelledQuery->where('branch_id', $filterBranchId);
        $cancelledAppointments = $cancelledQuery->count();

        // Get today's appointments list - filtered by branch
        $todayListQuery = Appointment::with(['patient', 'pt'])
            ->whereDate('appointment_date', $today)
            ->orderBy('appointment_time');
        if ($filterBranchId) {
            $todayListQuery->where('branch_id', $filterBranchId);
        }
        $todayAppointmentsList = $todayListQuery->get();

        // Get all appointments for calendar - filtered by branch
        $allAppointmentsQuery = Appointment::with(['patient', 'pt', 'branch'])
            ->where('appointment_date', '>=', now()->subMonths(1))
            ->where('appointment_date', '<=', now()->addMonths(2));
        if ($filterBranchId) {
            $allAppointmentsQuery->where('branch_id', $filterBranchId);
        }
        $allAppointments = $allAppointmentsQuery->get();

        return view('appointments.index', compact(
            'branches',
            'staff',
            'todayAppointments',
            'confirmedAppointments',
            'pendingAppointments',
            'cancelledAppointments',
            'todayAppointmentsList',
            'allAppointments'
        ));
    }

    /**
     * Feed API for FullCalendar
     */
    public function feed(Request $request)
    {
        // *** BRANCH FILTER - แยกข้อมูลตามสาขา ***
        $selectedBranchId = session('selected_branch_id');
        $user = auth()->user();
        $canViewAllBranches = $user && $user->role && in_array($user->role->name, ['Admin', 'Owner', 'Super Admin']);
        $filterBranchId = null;
        if (!$canViewAllBranches) {
            $filterBranchId = $selectedBranchId ?: ($user ? $user->branch_id : null);
        }

        $query = Appointment::with(['patient', 'branch', 'pt'])
            ->whereBetween('appointment_date', [
                $request->start ?? now()->subMonths(1),
                $request->end ?? now()->addMonths(2)
            ]);

        if ($filterBranchId) {
            $query->where('branch_id', $filterBranchId);
        }

        $appointments = $query->get();

        $events = $appointments->map(function($appointment) {
            $colors = [
                'pending' => 'primary',
                'confirmed' => 'success',
                'in_progress' => 'warning',
                'completed' => 'info',
                'cancelled' => 'danger',
                'no_show' => 'secondary'
            ];

            return [
                'id' => $appointment->id,
                'title' => $appointment->patient->name ?? 'Unknown',
                'start' => $appointment->appointment_date . 'T' . $appointment->appointment_time,
                'backgroundColor' => $colors[$appointment->status] ?? 'secondary',
                'extendedProps' => [
                    'patient_name' => $appointment->patient->name ?? 'Unknown',
                    'patient_phone' => $appointment->patient->phone ?? 'N/A',
                    'branch_name' => $appointment->branch->name ?? 'N/A',
                    'pt_name' => $appointment->pt ? $appointment->pt->name : null,
                    'time' => substr($appointment->appointment_time, 0, 5),
                    'status' => ucfirst($appointment->status),
                    'status_color' => $colors[$appointment->status] ?? 'secondary'
                ]
            ];
        });

        return response()->json($events);
    }

    /**
     * Store a new appointment
     * Requirement ข้อ 2: Create temporary OPD for new patients
     * Requirement ข้อ 3: Use existing OPD for returning patients
     */
    public function store(Request $request)
    {
        // Dynamic validation based on customer type
        $rules = [
            'customer_type' => 'nullable|in:existing,new',
            'branch_id' => 'required|exists:branches,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
            'pt_id' => 'nullable|exists:users,id',
            'booking_channel' => 'nullable|in:walk_in,phone,line,website,other,ads,facebook,referral,company',
            'notes' => 'nullable|string',
            'purpose' => 'required|in:FOLLOW_UP,PHYSICAL_THERAPY'
        ];

        // Add validation rules based on customer type
        if ($request->customer_type === 'new') {
            $rules['new_patient_name'] = 'required|string|max:255';
            $rules['new_patient_phone'] = 'required|string|max:20';
            $rules['new_lead_source'] = 'nullable|string';
            $rules['new_symptoms'] = 'nullable|string';
            $rules['new_custom_symptoms'] = 'nullable|string';
        } else {
            $rules['patient_id'] = 'required|exists:patients,id';
        }

        $validated = $request->validate($rules);

        try {
            DB::beginTransaction();

            $patient = null;
            $opdRecord = null;
            $isNewPatient = false;
            $patientId = null;

            // Handle new customer creation
            if ($request->customer_type === 'new') {
                $isNewPatient = true;

                // Parse name
                $fullName = $request->new_patient_name;
                $nameParts = explode(' ', $fullName, 2);
                $firstName = $nameParts[0] ?? $fullName;
                $lastName = $nameParts[1] ?? '';

                // Get symptom text
                $symptoms = $request->new_symptoms;
                if ($symptoms === 'อื่นๆ' && $request->new_custom_symptoms) {
                    $symptoms = $request->new_custom_symptoms;
                }

                // Check if patient with this phone already exists
                $existingPatient = Patient::where('phone', $request->new_patient_phone)->first();

                if ($existingPatient) {
                    $patient = $existingPatient;
                    $patientId = $existingPatient->id;
                } else {
                    // Create new temporary patient (Lead)
                    $patient = Patient::create([
                        'name' => $fullName,
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'phone' => $request->new_patient_phone,
                        'is_temporary' => true,
                        'first_visit_branch_id' => $request->branch_id,
                        'branch_id' => $request->branch_id,
                        'booking_channel' => $request->new_lead_source ?? $request->booking_channel,
                        'chief_complaint' => $symptoms,
                    ]);
                    $patientId = $patient->id;
                }
            } else {
                // Existing customer
                $patientId = $request->patient_id;
                $patient = Patient::find($request->patient_id);

                // ข้อ 3: Use existing OPD if available
                $opdRecord = OpdRecord::where('patient_id', $patient->id)
                    ->where('branch_id', $request->branch_id)
                    ->where('status', 'active')
                    ->first();

                if (!$opdRecord) {
                    // Create new OPD for existing patient
                    $opdRecord = OpdRecord::create([
                        'patient_id' => $patient->id,
                        'branch_id' => $request->branch_id,
                        'opd_number' => 'OPD-' . now()->format('Ymd') . '-' . rand(1000, 9999),
                        'status' => 'active',
                        'is_temporary' => false,
                        'created_by' => auth()->id() ?? null
                    ]);
                }
            }

            // Build notes with symptoms for new customers
            $notes = $request->notes;
            if ($request->customer_type === 'new') {
                $symptoms = $request->new_symptoms;
                if ($symptoms === 'อื่นๆ' && $request->new_custom_symptoms) {
                    $symptoms = $request->new_custom_symptoms;
                }

                $leadSource = $request->new_lead_source ?? $request->booking_channel;
                $noteParts = [];
                if ($leadSource) {
                    $noteParts[] = "ช่องทาง: {$leadSource}";
                }
                if ($symptoms) {
                    $noteParts[] = "อาการ: {$symptoms}";
                }
                if ($request->notes) {
                    $noteParts[] = $request->notes;
                }
                $notes = implode("\n", $noteParts);
            }

            // Create appointment
            $appointment = Appointment::create([
                'patient_id' => $patientId,
                'branch_id' => $request->branch_id,
                'pt_id' => $request->pt_id,
                'appointment_date' => $request->appointment_date,
                'appointment_time' => $request->appointment_time,
                'booking_channel' => $request->new_lead_source ?? $request->booking_channel ?? 'walk_in',
                'status' => 'pending',
                'notes' => $notes,
                'purpose' => $request->purpose,
                'created_by' => auth()->id() ?? null
            ]);

            // ข้อ 4: Create queue entry if appointment is for today
            $queueCreated = false;
            if ($request->appointment_date === today()->toDateString()) {
                // Get next queue number for today
                $lastQueue = \App\Models\Queue::whereDate('queued_at', today())->max('queue_number');
                $queueNumber = $lastQueue ? $lastQueue + 1 : 1;

                \App\Models\Queue::create([
                    'appointment_id' => $appointment->id,
                    'patient_id' => $patientId,
                    'branch_id' => $request->branch_id,
                    'pt_id' => $request->pt_id,
                    'queue_number' => $queueNumber,
                    'status' => 'waiting',
                    'queued_at' => now(),
                    'created_by' => auth()->id() ?? null
                ]);
                $queueCreated = true;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Appointment created successfully',
                'appointment' => $appointment,
                'opd_created' => true,
                'is_temporary_opd' => $isNewPatient,
                'queue_created' => $queueCreated
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create appointment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function create(Request $request)
    {
        $branches = Branch::where('is_active', true)->get();
        $staff = Staff::where('employment_status', 'active')
                     ->where('position', 'LIKE', '%PT%')
                     ->get();

        // Pre-load patient if patient_id is provided
        $preloadPatient = null;
        if ($request->has('patient_id')) {
            $preloadPatient = Patient::find($request->patient_id);
        }

        return view('appointments.index', compact('branches', 'staff', 'preloadPatient'));
    }

    public function show($id)
    {
        try {
            $appointment = Appointment::with(['patient', 'pt', 'branch'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'appointment' => $appointment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบนัดหมาย'
            ], 404);
        }
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        try {
            $appointment = Appointment::findOrFail($id);

            $validated = $request->validate([
                'appointment_date' => 'required|date',
                'appointment_time' => 'required',
                'pt_id' => 'nullable|exists:users,id',
                'purpose' => 'required|in:FOLLOW_UP,PHYSICAL_THERAPY',
                'status' => 'required|in:pending,confirmed,completed,cancelled,no_show',
                'notes' => 'nullable|string',
            ]);

            $appointment->update([
                'appointment_date' => $validated['appointment_date'],
                'appointment_time' => $validated['appointment_time'],
                'pt_id' => $validated['pt_id'],
                'purpose' => $validated['purpose'],
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Also update Queue if exists
            $queue = \App\Models\Queue::where('appointment_id', $id)->first();
            if ($queue) {
                $queue->pt_id = $validated['pt_id'];
                if ($validated['status'] === 'cancelled') {
                    $queue->status = 'cancelled';
                }
                $queue->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'อัปเดตนัดหมายสำเร็จ',
                'appointment' => $appointment
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่สามารถอัปเดตได้: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update appointment status
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $appointment = Appointment::with('patient')->findOrFail($id);

            // *** FORCE DELETE TEMPORARY PATIENT IF CANCELLED ***
            if ($request->status === 'cancelled' && $appointment->patient && $appointment->patient->is_temporary) {
                $patientId = $appointment->patient_id;

                // Delete related data first
                \App\Models\Queue::where('patient_id', $patientId)->forceDelete();
                Appointment::where('patient_id', $patientId)->forceDelete();

                // Force delete the patient
                $appointment->patient->forceDelete();

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'ยกเลิกนัดและลบข้อมูลลูกค้าชั่วคราวสำเร็จ',
                    'temporary_patient_deleted' => true
                ]);
            }

            $appointment->status = $request->status;
            $appointment->save();

            // Also update Queue status to keep in sync
            $queueStatus = match($request->status) {
                'pending' => 'waiting',
                'calling' => 'calling',
                'confirmed' => 'in_treatment',
                'awaiting_payment' => 'awaiting_payment',
                'completed' => 'completed',
                'cancelled' => 'cancelled',
                default => $request->status
            };

            \App\Models\Queue::where('appointment_id', $appointment->id)
                ->update(['status' => $queueStatus]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Quick store appointment from patient profile
     * Simplified version with minimal fields
     */
    public function quickStore(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
            'purpose' => 'required|in:FOLLOW_UP,PHYSICAL_THERAPY'
        ]);

        try {
            DB::beginTransaction();

            $patient = Patient::findOrFail($request->patient_id);

            // Get branch from patient or use default
            $branchId = $patient->branch_id ?? session('selected_branch_id') ?? Branch::first()->id;

            // Create appointment with minimal data
            $appointment = Appointment::create([
                'patient_id' => $request->patient_id,
                'branch_id' => $branchId,
                'pt_id' => null,
                'appointment_date' => $request->appointment_date,
                'appointment_time' => $request->appointment_time,
                'booking_channel' => 'walk_in',
                'status' => 'pending',
                'purpose' => $request->purpose,
                'notes' => 'Quick appointment from patient profile',
                'created_by' => auth()->id() ?? null
            ]);

            // Create queue if appointment is for today
            if ($request->appointment_date === today()->toDateString()) {
                $lastQueue = \App\Models\Queue::whereDate('queued_at', today())->max('queue_number');
                $queueNumber = $lastQueue ? $lastQueue + 1 : 1;

                \App\Models\Queue::create([
                    'appointment_id' => $appointment->id,
                    'patient_id' => $request->patient_id,
                    'branch_id' => $branchId,
                    'queue_number' => $queueNumber,
                    'status' => 'waiting',
                    'queued_at' => now(),
                    'created_by' => auth()->id() ?? null
                ]);
            }

            DB::commit();

            return redirect()
                ->route('patients.show', $request->patient_id)
                ->with('success', 'สร้างนัดหมายสำเร็จ');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'ไม่สามารถสร้างนัดหมายได้: ' . $e->getMessage());
        }
    }

    /**
     * Get daily summary with full appointment list
     * For Modal showing daily audit view
     */
    public function summary(Request $request)
    {
        $date = $request->date ?? today()->toDateString();

        // Get all appointments for this date
        $appointments = Appointment::with(['patient', 'pt', 'branch'])
            ->whereDate('appointment_date', $date)
            ->orderBy('appointment_time')
            ->get();

        // Calculate counts
        $total = $appointments->count();
        $completed = $appointments->where('status', 'completed')->count();
        $cancelled = $appointments->where('status', 'cancelled')->count();
        $noShow = $appointments->where('status', 'no_show')->count();
        $rescheduled = $appointments->where('status', 'rescheduled')->count();
        $pending = $appointments->whereIn('status', ['pending', 'confirmed'])->count();

        // Check for new patients (patient created on same day as appointment)
        $newPatients = 0;
        $coursePatients = 0;
        foreach ($appointments as $apt) {
            if ($apt->patient_id && $apt->patient) {
                // New patient: created_at is same date as appointment_date
                if ($apt->patient->created_at && $apt->patient->created_at->toDateString() === $date) {
                    $newPatients++;
                }

                // Course patient: has active course
                $hasActiveCourse = \App\Models\CoursePurchase::where('patient_id', $apt->patient_id)
                    ->where('status', 'active')
                    ->where('remaining_sessions', '>', 0)
                    ->exists();
                if ($hasActiveCourse) {
                    $coursePatients++;
                }
            }
        }

        // Format appointment list for frontend
        $appointmentList = $appointments->map(function($apt) {
            $isNewPatient = false;
            if ($apt->patient_id) {
                $firstApt = Appointment::where('patient_id', $apt->patient_id)
                    ->orderBy('created_at')
                    ->first();
                $isNewPatient = $firstApt && $firstApt->id === $apt->id;
            }

            return [
                'id' => $apt->id,
                'time' => substr($apt->appointment_time, 0, 5),
                'patient_id' => $apt->patient_id,
                'patient_name' => $apt->patient->name ?? 'ไม่ระบุ',
                'patient_phone' => $apt->patient->phone ?? '-',
                'status' => $apt->status,
                'status_text' => $this->getStatusText($apt->status),
                'status_color' => $this->getStatusColor($apt->status),
                'is_new_patient' => $isNewPatient,
                'pt_name' => $apt->pt ? $apt->pt->name : 'ไม่ระบุ',
                'branch_name' => $apt->branch->name ?? '-'
            ];
        });

        return response()->json([
            'success' => true,
            'date' => $date,
            'counts' => [
                'total' => $total,
                'completed' => $completed,
                'cancelled' => $cancelled,
                'no_show' => $noShow,
                'rescheduled' => $rescheduled,
                'pending' => $pending,
                'new_patients' => $newPatients,
                'course_patients' => $coursePatients
            ],
            'appointments' => $appointmentList
        ]);
    }

    /**
     * Get status text in Thai
     */
    private function getStatusText($status)
    {
        $texts = [
            'pending' => 'รอยืนยัน',
            'confirmed' => 'ยืนยันแล้ว',
            'in_progress' => 'กำลังรักษา',
            'completed' => 'เสร็จสิ้น',
            'cancelled' => 'ยกเลิก',
            'no_show' => 'ไม่มา'
        ];
        return $texts[$status] ?? $status;
    }

    /**
     * Get status color for badge
     */
    private function getStatusColor($status)
    {
        $colors = [
            'pending' => 'warning',
            'confirmed' => 'info',
            'in_progress' => 'primary',
            'completed' => 'success',
            'cancelled' => 'danger',
            'no_show' => 'secondary'
        ];
        return $colors[$status] ?? 'secondary';
    }

    /**
     * Cancel appointment - safer version that checks for treatments
     */
    public function cancel($id)
    {
        try {
            DB::beginTransaction();

            $appointment = Appointment::with('patient')->findOrFail($id);

            // Check if appointment has treatments - cannot cancel if already treated
            $hasTreatment = \App\Models\Treatment::where('appointment_id', $id)->exists();
            if ($hasTreatment) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่สามารถยกเลิกได้ เนื่องจากมีการรักษาแล้ว'
                ], 400);
            }

            // If temporary patient with no treatment, can delete everything
            if ($appointment->patient && $appointment->patient->is_temporary) {
                $patientId = $appointment->patient_id;

                // Delete queue entries
                \App\Models\Queue::where('patient_id', $patientId)->forceDelete();

                // Delete this appointment
                $appointment->forceDelete();

                // Check if patient has any other appointments
                $otherAppointments = Appointment::where('patient_id', $patientId)->count();
                if ($otherAppointments === 0) {
                    // Safe to delete patient
                    $appointment->patient->forceDelete();
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'ยกเลิกนัดและลบข้อมูลลูกค้าชั่วคราวสำเร็จ',
                    'temporary_patient_deleted' => true
                ]);
            }

            // For real patients, just update status to cancelled
            $appointment->update([
                'status' => 'cancelled',
                'cancellation_reason' => 'Cancelled by user',
                'cancelled_at' => now(),
                'cancelled_by' => auth()->id() ?? null
            ]);

            // Also cancel queue if exists
            \App\Models\Queue::where('appointment_id', $id)->update(['status' => 'cancelled']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'ยกเลิกนัดหมายสำเร็จ'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'ไม่สามารถยกเลิกได้: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel appointment
     * Requirement ข้อ 3: Delete temporary OPD for new patients, keep OPD for returning patients
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $appointment = Appointment::withTrashed()->with('patient')->findOrFail($id);
            $deletedPatient = false;

            // *** FORCE DELETE TEMPORARY PATIENT ***
            // ถ้าลูกค้ายังเป็น temporary (ยังไม่เคยรักษา) ให้ลบข้อมูลลูกค้าออกเลย
            if ($appointment->patient && $appointment->patient->is_temporary) {
                $patientId = $appointment->patient_id;

                // Delete related data first
                \App\Models\Queue::where('patient_id', $patientId)->forceDelete();
                Appointment::where('patient_id', $patientId)->forceDelete();

                // Force delete the patient
                $appointment->patient->forceDelete();
                $deletedPatient = true;

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'ยกเลิกนัดและลบข้อมูลลูกค้าชั่วคราวสำเร็จ',
                    'temporary_patient_deleted' => true
                ]);
            }

            // For real patients, just cancel the appointment
            // Delete related Queue first
            \App\Models\Queue::where('appointment_id', $id)->forceDelete();

            // Check if already deleted
            if ($appointment->trashed()) {
                // Already soft deleted, force delete it
                $appointment->forceDelete();
            } else {
                // Update appointment status to cancelled
                $appointment->update([
                    'status' => 'cancelled',
                    'cancellation_reason' => 'Cancelled by user',
                    'cancelled_at' => now(),
                    'cancelled_by' => auth()->id() ?? null
                ]);

                // Force delete appointment
                $appointment->forceDelete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'ยกเลิกนัดหมายสำเร็จ',
                'temporary_opd_deleted' => false
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel appointment: ' . $e->getMessage()
            ], 500);
        }
    }
}
