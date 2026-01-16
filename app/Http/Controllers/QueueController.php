<?php

namespace App\Http\Controllers;

use App\Models\{Queue, Treatment, OpdRecord, Appointment, CoursePurchase, CoursePackage, CourseUsageLog, Invoice, InvoiceItem, Patient, User, Service, Commission};
use App\Services\DfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QueueController extends Controller
{
    /**
     * Display today's appointment queue
     * Requirement ข้อ 4: Show queue with real-time status tracking
     */
    public function index()
    {
        // *** BRANCH FILTER - แยกข้อมูลตามสาขา ***
        $selectedBranchId = session('selected_branch_id');
        $user = auth()->user();
        $canViewAllBranches = $user && $user->role && in_array($user->role->name, ['Admin', 'Owner', 'Super Admin']);
        $filterBranchId = null;
        if (!$canViewAllBranches) {
            $filterBranchId = $selectedBranchId ?: ($user ? $user->branch_id : null);
        }

        // Fetch today's queues - filtered by branch
        $queuesQuery = Queue::with(['patient', 'appointment', 'pt', 'branch'])
            ->whereDate('queued_at', today())
            ->orderBy('queue_number');

        if ($filterBranchId) {
            $queuesQuery->where('branch_id', $filterBranchId);
        }
        $queues = $queuesQuery->get();

        // If no queues from Queue table, get from appointments
        if ($queues->isEmpty()) {
            $appointmentsQuery = \App\Models\Appointment::with(['patient', 'pt', 'branch'])
                ->whereDate('appointment_date', today())
                ->orderBy('appointment_time');

            if ($filterBranchId) {
                $appointmentsQuery->where('branch_id', $filterBranchId);
            }
            $appointments = $appointmentsQuery->get();
        } else {
            $appointments = collect();
        }

        // Stats
        $totalQueue = $queues->count() ?: $appointments->count();
        $waitingQueue = $queues->where('status', 'waiting')->count() ?: $appointments->where('status', 'pending')->count();
        $inTreatmentQueue = $queues->where('status', 'in_treatment')->count() ?: $appointments->where('status', 'confirmed')->count();
        $completedQueue = $queues->where('status', 'completed')->count() ?: $appointments->where('status', 'completed')->count();

        // Get staff for seller selection (PT, Admin, Manager)
        $salesStaff = User::whereHas('role', function($q) {
            $q->whereIn('name', ['PT', 'Admin', 'Manager']);
        })->orderBy('name')->get();

        return view('queue.index', compact(
            'queues',
            'appointments',
            'totalQueue',
            'waitingQueue',
            'inTreatmentQueue',
            'completedQueue',
            'salesStaff'
        ));
    }

    /**
     * TV Display for queue
     */
    public function display()
    {
        // Fetch today's queues
        $queues = Queue::with(['patient', 'appointment', 'pt', 'branch'])
            ->whereDate('queued_at', today())
            ->orderBy('queue_number')
            ->get();

        // If no queues from Queue table, get from appointments
        if ($queues->isEmpty()) {
            $appointments = \App\Models\Appointment::with(['patient', 'pt', 'branch'])
                ->whereDate('appointment_date', today())
                ->orderBy('appointment_time')
                ->get();
        } else {
            $appointments = collect();
        }

        return view('queue.display', compact('queues', 'appointments'));
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

    public function destroy($id)
    {
        //
    }

    /**
     * Start treatment for a queue item
     * Requirement ข้อ 4: Record start time and update status
     * Creates Treatment Record and links to OPD
     */
    public function startTreatment($id)
    {
        try {
            DB::beginTransaction();

            $queue = Queue::with(['patient', 'appointment'])->findOrFail($id);

            // *** CONVERT TEMPORARY PATIENT TO REAL PATIENT ***
            if ($queue->patient && $queue->patient->is_temporary) {
                // Generate HN Number with row-level locking to prevent duplicates
                $lastHN = Patient::whereNotNull('hn_number')
                    ->lockForUpdate()
                    ->orderByRaw('CAST(SUBSTRING(hn_number, 3) AS UNSIGNED) DESC')
                    ->first();

                if ($lastHN && preg_match('/HN(\d+)/', $lastHN->hn_number, $matches)) {
                    $nextNumber = intval($matches[1]) + 1;
                } else {
                    $nextNumber = 1;
                }

                $hnNumber = 'HN' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

                // Double-check HN doesn't already exist (safety check)
                if (Patient::where('hn_number', $hnNumber)->exists()) {
                    throw new \Exception('HN number collision detected. Please try again.');
                }

                // Update patient: convert from temporary to real
                $queue->patient->update([
                    'is_temporary' => false,
                    'hn_number' => $hnNumber,
                    'converted_at' => now(),
                ]);
            }

            // Get or create OPD Record
            $opdRecord = OpdRecord::where('patient_id', $queue->patient_id)
                ->where('branch_id', $queue->branch_id)
                ->where('status', 'active')
                ->first();

            if (!$opdRecord) {
                $opdRecord = OpdRecord::create([
                    'patient_id' => $queue->patient_id,
                    'branch_id' => $queue->branch_id,
                    'opd_number' => 'OPD-' . now()->format('Ymd') . '-' . rand(1000, 9999),
                    'status' => 'active',
                    'is_temporary' => false,
                    'created_by' => auth()->id() ?? null
                ]);
            }

            // Check if queue is in correct status
            if ($queue->status === 'in_treatment') {
                throw new \Exception('การรักษาสำหรับคิวนี้ถูกเริ่มแล้ว');
            } else if ($queue->status === 'completed' || $queue->status === 'cancelled') {
                throw new \Exception('สถานะคิวไม่ถูกต้อง ไม่สามารถเริ่มการรักษาได้');
            } else if ($queue->status !== 'waiting' && $queue->status !== 'called') {
                throw new \Exception('สถานะคิวไม่ถูกต้อง ไม่สามารถเริ่มการรักษาได้');
            }

            // Create Treatment Record
            $treatment = Treatment::create([
                'opd_id' => $opdRecord->id,
                'patient_id' => $queue->patient_id,
                'appointment_id' => $queue->appointment_id,
                'queue_id' => $queue->id,
                'branch_id' => $queue->branch_id,
                'pt_id' => $queue->pt_id,
                'started_at' => now(),
                'billing_status' => 'pending',
                'created_by' => auth()->id() ?? null
            ]);

            // Update queue status to in_treatment
            $queue->update([
                'status' => 'in_treatment',
                'started_at' => now(),
                'called_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Treatment started successfully',
                'started_at' => $queue->started_at->format('H:i:s'),
                'treatment_id' => $treatment->id,
                'opd_number' => $opdRecord->opd_number
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to start treatment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * End treatment for a queue item
     * Requirement ข้อ 4: Calculate duration and check for overtime (>15 minutes)
     * Updates Treatment Record completion
     */
    public function endTreatment(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $queue = Queue::findOrFail($id);

            // Calculate treatment duration
            $duration = now()->diffInMinutes($queue->started_at);

            // Update Treatment Record
            $treatment = Treatment::where('queue_id', $queue->id)->first();
            if ($treatment) {
                $treatment->update([
                    'completed_at' => now(),
                    'duration_minutes' => $duration,
                    'service_id' => $request->service_id,
                    'pt_id' => $request->pt_id,
                    'treatment_notes' => $request->treatment_notes,
                ]);
            }

            // Update queue status to completed
            $queue->update([
                'status' => 'completed',
                'completed_at' => now(),
                'waiting_time_minutes' => $duration,
                'is_overtime' => $duration > 15, // ข้อ 4: 15-minute threshold
            ]);

            // Handle course purchase if requested
            $coursePurchase = null;
            if ($request->buy_course && $request->package_id) {
                $coursePurchase = $this->createCoursePurchase($request, $queue->patient_id, $queue->branch_id);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Treatment ended successfully',
                'completed_at' => $queue->completed_at->format('H:i:s'),
                'duration' => $duration,
                'is_overtime' => $queue->is_overtime,
                'treatment_id' => $treatment?->id,
                'course_purchase_id' => $coursePurchase?->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to end treatment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Complete an appointment (when not using Queue table)
     * Creates treatment record and handles course purchase
     */
    public function completeAppointment(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $appointment = Appointment::with('patient')->findOrFail($id);

            // *** CONVERT TEMPORARY PATIENT TO REAL PATIENT ***
            if ($appointment->patient && $appointment->patient->is_temporary) {
                // Generate HN Number
                $lastHN = Patient::whereNotNull('hn_number')
                    ->orderByRaw('CAST(SUBSTRING(hn_number, 3) AS UNSIGNED) DESC')
                    ->first();

                if ($lastHN && preg_match('/HN(\d+)/', $lastHN->hn_number, $matches)) {
                    $nextNumber = intval($matches[1]) + 1;
                } else {
                    $nextNumber = 1;
                }

                $hnNumber = 'HN' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

                // Update patient: convert from temporary to real
                $appointment->patient->update([
                    'is_temporary' => false,
                    'hn_number' => $hnNumber,
                    'converted_at' => now(),
                ]);
            }

            // Calculate duration from when status changed to confirmed
            $startTime = $appointment->updated_at;
            $duration = now()->diffInMinutes($startTime);

            // Get or create OPD Record
            $opdRecord = OpdRecord::where('patient_id', $appointment->patient_id)
                ->where('branch_id', $appointment->branch_id)
                ->where('status', 'active')
                ->first();

            if (!$opdRecord) {
                $opdRecord = OpdRecord::create([
                    'patient_id' => $appointment->patient_id,
                    'branch_id' => $appointment->branch_id,
                    'opd_number' => 'OPD-' . now()->format('Ymd') . '-' . rand(1000, 9999),
                    'status' => 'active',
                    'is_temporary' => false,
                    'created_by' => auth()->id() ?? null
                ]);
            }

            // Create Treatment Record
            $treatment = Treatment::create([
                'opd_id' => $opdRecord->id,
                'patient_id' => $appointment->patient_id,
                'appointment_id' => $appointment->id,
                'branch_id' => $appointment->branch_id,
                'pt_id' => $request->pt_id,
                'service_id' => $request->service_id,
                'treatment_notes' => $request->treatment_notes,
                'started_at' => $startTime,
                'completed_at' => now(),
                'duration_minutes' => $duration,
                'billing_status' => 'pending',
                'created_by' => auth()->id() ?? null
            ]);

            // Update appointment status to completed
            $appointment->update([
                'status' => 'completed'
            ]);

            // Handle course purchase if requested
            $coursePurchase = null;
            if ($request->buy_course && $request->package_id) {
                $coursePurchase = $this->createCoursePurchase($request, $appointment->patient_id, $appointment->branch_id);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Treatment completed successfully',
                'duration' => $duration,
                'treatment_id' => $treatment->id,
                'opd_number' => $opdRecord->opd_number,
                'course_purchase_id' => $coursePurchase?->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete treatment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper: Create course purchase
     */
    private function createCoursePurchase(Request $request, $patientId, $branchId)
    {
        $package = CoursePackage::findOrFail($request->package_id);

        // Generate course number
        $courseNumber = 'CRS-' . now()->format('Ymd') . '-' . rand(1000, 9999);

        // Create course purchase
        $sellerIds = $request->seller_ids ?? [];
        if (is_string($sellerIds)) {
            $sellerIds = json_decode($sellerIds, true) ?? [];
        }

        $coursePurchase = CoursePurchase::create([
            'course_number' => $courseNumber,
            'patient_id' => $patientId,
            'package_id' => $package->id,
            'purchase_branch_id' => $branchId,
            'purchase_pattern' => $request->purchase_pattern ?? 'buy_and_use',
            'purchase_date' => now(),
            'activation_date' => $request->purchase_pattern === 'buy_for_later' ? null : now(),
            'expiry_date' => $request->purchase_pattern === 'buy_for_later' ? null : now()->addDays($package->validity_days),
            'total_sessions' => $package->total_sessions,
            'used_sessions' => 0,
            'status' => $request->purchase_pattern === 'buy_for_later' ? 'pending' : 'active',
            'allow_branch_sharing' => $request->allow_branch_sharing == '1',
            'created_by' => auth()->id() ?? null,
            'seller_ids' => $sellerIds
        ]);

        // Create invoice for course purchase
        $invoice = Invoice::create([
            'patient_id' => $patientId,
            'branch_id' => $branchId,
            'invoice_number' => 'INV-' . now()->format('Ymd') . '-' . rand(1000, 9999),
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'invoice_type' => 'course',
            'subtotal' => $package->price,
            'discount' => 0,
            'vat' => 0,
            'total' => $package->price,
            'status' => 'pending',
            'created_by' => auth()->id() ?? null
        ]);

        // Create invoice item
        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'item_type' => 'course_package',
            'item_id' => $package->id,
            'description' => $package->name,
            'quantity' => 1,
            'unit_price' => $package->price,
            'discount_amount' => 0,
            'total_amount' => $package->price
        ]);

        // Link invoice to course purchase
        $coursePurchase->update(['invoice_id' => $invoice->id]);

        return $coursePurchase;
    }

    /**
     * Finish treatment - stop timer and move to awaiting payment
     */
    public function finishTreatment(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $appointment = Appointment::with('patient')->findOrFail($id);

            // *** CONVERT TEMPORARY PATIENT TO REAL PATIENT ***
            if ($appointment->patient && $appointment->patient->is_temporary) {
                // Generate HN Number
                $lastHN = Patient::whereNotNull('hn_number')
                    ->orderByRaw('CAST(SUBSTRING(hn_number, 3) AS UNSIGNED) DESC')
                    ->first();

                if ($lastHN && preg_match('/HN(\d+)/', $lastHN->hn_number, $matches)) {
                    $nextNumber = intval($matches[1]) + 1;
                } else {
                    $nextNumber = 1;
                }

                $hnNumber = 'HN' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

                // Update patient: convert from temporary to real
                $appointment->patient->update([
                    'is_temporary' => false,
                    'hn_number' => $hnNumber,
                    'converted_at' => now(),
                ]);
            }

            // Calculate duration
            $startTime = $appointment->updated_at;
            $duration = now()->diffInMinutes($startTime);

            // Get or create OPD Record
            $opdRecord = OpdRecord::where('patient_id', $appointment->patient_id)
                ->where('branch_id', $appointment->branch_id)
                ->where('status', 'active')
                ->first();

            if (!$opdRecord) {
                $opdRecord = OpdRecord::create([
                    'patient_id' => $appointment->patient_id,
                    'branch_id' => $appointment->branch_id,
                    'opd_number' => 'OPD-' . now()->format('Ymd') . '-' . rand(1000, 9999),
                    'status' => 'active',
                    'is_temporary' => false,
                    'created_by' => auth()->id() ?? null
                ]);
            }

            // Create Treatment Record (without service/pt yet)
            $treatment = Treatment::create([
                'opd_id' => $opdRecord->id,
                'patient_id' => $appointment->patient_id,
                'appointment_id' => $appointment->id,
                'branch_id' => $appointment->branch_id,
                'started_at' => $startTime,
                'completed_at' => now(),
                'duration_minutes' => $duration,
                'billing_status' => 'pending',
                'created_by' => auth()->id() ?? null
            ]);

            // Update appointment status to awaiting_payment
            $appointment->update([
                'status' => 'awaiting_payment'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Treatment finished',
                'duration' => $duration,
                'treatment_id' => $treatment->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to finish treatment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process payment for completed treatment (Cart System)
     */
    public function processPayment(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            Log::info("=== PROCESS PAYMENT START === Appointment ID: {$id}");

            $appointment = Appointment::findOrFail($id);
            Log::info("Step 1: Appointment found - Patient: {$appointment->patient_id}");

            $treatment = Treatment::where('appointment_id', $id)->first();

            if (!$treatment) {
                Log::error("CRITICAL: Treatment record NOT FOUND for appointment {$id}");
                throw new \Exception('Treatment record not found');
            }

            Log::info("Step 2: Treatment found - ID: {$treatment->id}");

            // Update treatment with PT
            $treatment->update([
                'pt_id' => $request->pt_id,
            ]);
            Log::info("Step 3: Updated PT to {$request->pt_id}");

            $cartItems = $request->cart_items ?? [];
            $totalAmount = $request->total_amount ?? 0;
            $paymentMethod = $request->payment_method;
            $patientId = $appointment->patient_id;
            $branchId = $appointment->branch_id;

            // Validate cart items structure
            foreach ($cartItems as $index => $item) {
                if (!isset($item['type']) || !in_array($item['type'], ['service', 'course', 'use_course'])) {
                    throw new \Exception("รายการที่ {$index}: ประเภทไม่ถูกต้อง");
                }
                // ID can be numeric or UUID string
                if (!isset($item['id']) || (empty($item['id']) && $item['id'] !== 0)) {
                    throw new \Exception("รายการที่ {$index}: ID ไม่ถูกต้อง");
                }
                if (isset($item['price']) && (!is_numeric($item['price']) || $item['price'] < 0)) {
                    throw new \Exception("รายการที่ {$index}: ราคาไม่ถูกต้อง");
                }
            }

            // Create invoice if there are cart items
            $invoice = null;
            if (count($cartItems) > 0) {
                $invoice = Invoice::create([
                    'patient_id' => $patientId,
                    'branch_id' => $branchId,
                    'invoice_number' => 'INV-' . now()->format('Ymd') . '-' . rand(1000, 9999),
                    'invoice_date' => now(),
                    'due_date' => now(),
                    'invoice_type' => 'walk_in',
                    'subtotal' => $totalAmount,
                    'discount_amount' => 0,
                    'tax_amount' => 0,
                    'total_amount' => $totalAmount,
                    'paid_amount' => $totalAmount,
                    'outstanding_amount' => 0,
                    'status' => 'paid',
                    'notes' => 'Payment method: ' . $paymentMethod,
                    'created_by' => auth()->id() ?? null
                ]);
            }

            // VALIDATION: Check if any use_course has pending installment but no payment
            foreach ($cartItems as $item) {
                if (($item['type'] ?? '') === 'use_course') {
                    $coursePurchase = CoursePurchase::find($item['id'] ?? null);
                    if ($coursePurchase && $coursePurchase->hasPendingInstallment()) {
                        $itemPrice = $item['price'] ?? 0;
                        if ($itemPrice <= 0) {
                            throw new \Exception('ต้องชำระค่างวดผ่อน ฿' . number_format($coursePurchase->installment_amount, 0) . ' สำหรับ ' . ($coursePurchase->package->name ?? 'คอร์ส'));
                        }
                    }
                }
            }

            // Process each cart item
            $firstServiceId = null;
            $usedCoursePurchaseId = null; // Track which course was used
            $courseSessionsUsed = 1; // Track sessions used (default 1)
            $totalDfAmount = 0; // Track total DF for PT
            foreach ($cartItems as $item) {
                $itemType = $item['type'] ?? '';
                $itemId = $item['id'] ?? null;
                $itemPrice = $item['price'] ?? 0;
                $itemName = $item['name'] ?? '';

                if ($itemType === 'service') {
                    // Service item
                    $service = \App\Models\Service::find($itemId);
                    if ($service) {
                        if (!$firstServiceId) {
                            $firstServiceId = $service->id;
                        }

                        // Add DF amount for this service
                        $totalDfAmount += $service->df_amount ?? $service->default_df_rate ?? 0;

                        if ($invoice) {
                            InvoiceItem::create([
                                'invoice_id' => $invoice->id,
                                'item_type' => 'service',
                                'item_id' => $service->id,
                                'description' => $service->name,
                                'quantity' => 1,
                                'unit_price' => $service->default_price,
                                'discount_amount' => 0,
                                'total_amount' => $service->default_price
                            ]);
                        }
                    }
                } elseif ($itemType === 'course' || $itemType === 'course_retroactive') {
                    // Buy new course
                    $package = $itemId ? CoursePackage::find($itemId) : CoursePackage::where('name', $itemName)->first();
                    if ($package && $invoice) {
                        // Generate course number
                        $courseNumber = 'CRS-' . now()->format('Ymd') . '-' . rand(1000, 9999);

                        // Check if should use immediately
                        $useNow = $item['use_now'] ?? false;
                        $usedSessions = $useNow ? 1 : 0;

                        // Get installment info
                        $paymentType = $item['payment_type'] ?? 'full';
                        $installmentTotal = $item['installment_total'] ?? 0;
                        $installmentAmount = $item['installment_amount'] ?? 0;
                        $installmentPaid = $paymentType === 'installment' ? 1 : 0; // First installment paid today

                        // Get seller_ids from item
                        $itemSellerIds = $item['seller_ids'] ?? [];
                        if (is_string($itemSellerIds)) {
                            $itemSellerIds = json_decode($itemSellerIds, true) ?? [];
                        }

                        // Create course purchase with invoice_id
                        $coursePurchase = CoursePurchase::create([
                            'course_number' => $courseNumber,
                            'patient_id' => $patientId,
                            'package_id' => $package->id,
                            'invoice_id' => $invoice->id,
                            'purchase_branch_id' => $branchId,
                            'purchase_pattern' => $useNow ? 'buy_and_use' : 'buy_for_later',
                            'purchase_date' => now(),
                            'activation_date' => now(),
                            'expiry_date' => now()->addDays($package->validity_days),
                            'total_sessions' => $package->total_sessions,
                            'used_sessions' => $usedSessions,
                            'status' => 'active',
                            'allow_branch_sharing' => false,
                            'created_by' => auth()->id() ?? null,
                            'payment_type' => $paymentType,
                            'installment_total' => $installmentTotal,
                            'installment_paid' => $installmentPaid,
                            'installment_amount' => $installmentAmount,
                            'seller_ids' => $itemSellerIds
                        ]);

                        // If buying and using immediately, track this course for DF payment
                        if ($useNow) {
                            $usedCoursePurchaseId = $coursePurchase->id;
                            $courseSessionsUsed = 1; // buy_and_use always uses 1 session

                            // Create CourseUsageLog entry for buy_and_use
                            CourseUsageLog::create([
                                'course_purchase_id' => $coursePurchase->id,
                                'treatment_id' => $treatment->id,
                                'patient_id' => $coursePurchase->patient_id,
                                'branch_id' => $branchId,
                                'pt_id' => $treatment->pt_id,
                                'sessions_used' => 1,
                                'usage_date' => now()->toDateString(),
                                'status' => 'used',
                                'is_cross_branch' => false, // Same branch on first use
                                'purchase_branch_id' => $branchId,
                                'created_by' => auth()->id() ?? null,
                            ]);

                            // Add course DF amount to total
                            $totalDfAmount += $package->df_amount ?? 0;
                            Log::info("QueueController: buy_and_use - Set usedCoursePurchaseId = {$usedCoursePurchaseId}, added DF amount = " . ($package->df_amount ?? 0));
                        }

                        // Handle retroactive - refund previous treatment
                        $discountAmount = 0;
                        if ($itemType === 'course_retroactive' && isset($item['retroactive_treatment_id'])) {
                            $refundAmount = $item['refund_amount'] ?? 0;
                            $discountAmount = $refundAmount;

                            // Mark previous treatment as refunded/converted to course
                            $previousTreatment = Treatment::find($item['retroactive_treatment_id']);
                            if ($previousTreatment) {
                                $previousTreatment->update([
                                    'billing_status' => 'refunded',
                                    'treatment_notes' => ($previousTreatment->treatment_notes ?? '') . ' [คืนเงิน - ย้ายเข้าคอร์ส ' . $courseNumber . ']'
                                ]);
                            }
                        }

                        // Calculate amounts based on payment type
                        $unitPrice = $paymentType === 'installment' ? $installmentAmount : $package->price;
                        $description = $package->name;
                        if ($itemType === 'course_retroactive') {
                            $description .= ' (ตัดย้อนหลัง)';
                        }
                        if ($paymentType === 'installment') {
                            $description .= ' (ผ่อนงวด 1/' . $installmentTotal . ')';
                        }

                        $invoiceItem = InvoiceItem::create([
                            'invoice_id' => $invoice->id,
                            'item_type' => 'course_package',
                            'item_id' => $package->id,
                            'description' => $description,
                            'quantity' => 1,
                            'unit_price' => $unitPrice,
                            'discount_amount' => $discountAmount,
                            'total_amount' => $unitPrice - $discountAmount
                        ]);

                        // *** COMMISSION SPLITTING for processPayment ***
                        if (!empty($itemSellerIds)) {
                            $commissionRate = $package->commission_rate ?? 0;
                            $baseAmount = $package->price ?? 0;
                            $totalCommission = $baseAmount * ($commissionRate / 100);

                            $sellerCount = count($itemSellerIds);
                            $splitAmount = $sellerCount > 0 ? round($totalCommission / $sellerCount, 2) : 0;

                            Log::info("Commission Split (processPayment): Total {$totalCommission}, Sellers: {$sellerCount}, Each: {$splitAmount}");

                            foreach ($itemSellerIds as $sellerId) {
                                $commissionNumber = 'COM-' . now()->format('Ymd') . '-' . rand(1000, 9999);

                                Commission::create([
                                    'commission_number' => $commissionNumber,
                                    'pt_id' => $sellerId,
                                    'invoice_id' => $invoice->id,
                                    'invoice_item_id' => $invoiceItem->id,
                                    'branch_id' => $branchId,
                                    'commission_type' => 'package_sale',
                                    'base_amount' => $baseAmount,
                                    'commission_rate' => $commissionRate,
                                    'commission_amount' => $splitAmount,
                                    'status' => 'pending',
                                    'commission_date' => now()->toDateString(),
                                    'is_clawback_eligible' => true,
                                    'notes' => "ค่าคอมขายคอร์ส: {$package->name}" . ($sellerCount > 1 ? " (แบ่ง {$sellerCount} คน)" : ''),
                                    'created_by' => auth()->id() ?? null
                                ]);

                                Log::info("Commission created for seller {$sellerId}: {$splitAmount} THB");
                            }
                        }
                    }
                } elseif ($itemType === 'use_course') {
                    // Use existing course with row-level locking
                    $coursePurchase = CoursePurchase::with('package')->lockForUpdate()->find($itemId);
                    if ($coursePurchase) {
                        // Get sessions_used from cart item (default to 1 if not specified)
                        $sessionsUsed = $item['sessions_used'] ?? 1;
                        $courseSessionsUsed = $sessionsUsed; // Track for DF recording

                        // Validate: Check if course has enough remaining sessions
                        $remainingSessions = $coursePurchase->total_sessions - $coursePurchase->used_sessions;
                        if ($sessionsUsed > $remainingSessions) {
                            throw new \Exception("ไม่สามารถใช้ได้ เหลือเพียง {$remainingSessions} ครั้ง แต่พยายามใช้ {$sessionsUsed} ครั้ง");
                        }

                        // Validate: Check if course is not expired
                        if ($coursePurchase->expiry_date && $coursePurchase->expiry_date < now()->toDateString()) {
                            throw new \Exception("คอร์สหมดอายุแล้วเมื่อ {$coursePurchase->expiry_date}");
                        }

                        // Increment used_sessions by the amount specified
                        $coursePurchase->increment('used_sessions', $sessionsUsed);
                        $usedCoursePurchaseId = $coursePurchase->id; // Track this course

                        // Create CourseUsageLog entry
                        CourseUsageLog::create([
                            'course_purchase_id' => $coursePurchase->id,
                            'treatment_id' => $treatment->id,
                            'patient_id' => $coursePurchase->patient_id,
                            'branch_id' => $branchId,
                            'pt_id' => $treatment->pt_id,
                            'sessions_used' => $sessionsUsed,
                            'usage_date' => now()->toDateString(),
                            'status' => 'used',
                            'is_cross_branch' => $branchId != $coursePurchase->purchase_branch_id,
                            'purchase_branch_id' => $coursePurchase->purchase_branch_id,
                            'created_by' => auth()->id() ?? null,
                        ]);

                        // Add DF amount from course package (multiply by sessions used)
                        if ($coursePurchase->package) {
                            $dfPerSession = $coursePurchase->package->df_amount ?? 0;
                            $totalDfAmount += $dfPerSession * $sessionsUsed;
                            Log::info("QueueController: use_course - sessions={$sessionsUsed}, df_per_session={$dfPerSession}, total_df=" . ($dfPerSession * $sessionsUsed));
                        }

                        // Check if need to pay installment
                        if ($coursePurchase->hasPendingInstallment() && $invoice) {
                            $installmentNum = $coursePurchase->installment_paid + 1;
                            $coursePurchase->increment('installment_paid');

                            // Add installment payment to invoice
                            InvoiceItem::create([
                                'invoice_id' => $invoice->id,
                                'item_type' => 'course_installment',
                                'item_id' => $coursePurchase->package_id,
                                'description' => $coursePurchase->package->name . ' (ผ่อนงวด ' . $installmentNum . '/' . $coursePurchase->installment_total . ')',
                                'quantity' => 1,
                                'unit_price' => $coursePurchase->installment_amount,
                                'discount_amount' => 0,
                                'total_amount' => $coursePurchase->installment_amount
                            ]);
                        }

                        // Check if course is now depleted
                        if ($coursePurchase->used_sessions >= $coursePurchase->total_sessions) {
                            $coursePurchase->update(['status' => 'completed']);
                        }
                    }
                }
            }

            // Update treatment with first service, invoice, course info, and DF amount
            $treatmentUpdate = [
                'billing_status' => 'paid',
                'invoice_id' => $invoice?->id,
                'course_purchase_id' => $usedCoursePurchaseId,
                'df_amount' => $totalDfAmount
            ];
            if ($firstServiceId) {
                $treatmentUpdate['service_id'] = $firstServiceId;
            }
            $treatment->update($treatmentUpdate);
            Log::info("Step 4: Treatment updated - service_id: {$firstServiceId}, course_id: {$usedCoursePurchaseId}, df_amount: {$totalDfAmount}");

            // Record DF payment to df_payments table
            Log::info("Step 5: Recording DF Payment...");
            $dfPayment = null;

            // Only record DF if there's a PT assigned and amount > 0
            if ($treatment->pt_id && $totalDfAmount > 0) {
                if ($usedCoursePurchaseId) {
                    // ใช้คอร์ส
                    $dfPayment = DfService::recordDfForCourseUsage($treatment->id, $usedCoursePurchaseId, $courseSessionsUsed);
                    Log::info("Step 5a: Called DfService::recordDfForCourseUsage - sessions={$courseSessionsUsed}, Result: " . ($dfPayment ? $dfPayment->id : 'NULL'));
                } else {
                    // จ่ายรายครั้ง
                    $dfPayment = DfService::recordDfForTreatment($treatment->id);
                    Log::info("Step 5b: Called DfService::recordDfForTreatment - Result: " . ($dfPayment ? $dfPayment->id : 'NULL'));
                }

                // HARD CHECK: If DF should be recorded but wasn't, throw error to rollback
                if (!$dfPayment) {
                    Log::error("CRITICAL: DF Payment FAILED to record! Treatment: {$treatment->id}, Amount: {$totalDfAmount}");
                    throw new \Exception("Failed to record DF payment. DF Amount: {$totalDfAmount}");
                }

                // Double-check: Verify DF exists in database
                $verifyDf = \App\Models\DfPayment::find($dfPayment->id);
                if (!$verifyDf) {
                    Log::error("CRITICAL: DF Payment created but NOT FOUND in DB! ID: {$dfPayment->id}");
                    throw new \Exception("DF Payment verification failed");
                }

                Log::info("Step 5c: DF Payment VERIFIED - ID: {$dfPayment->id}, Amount: {$verifyDf->amount}");
            } else {
                Log::info("Step 5: Skipping DF record - PT: {$treatment->pt_id}, Amount: {$totalDfAmount}");
            }

            // Update appointment status to completed
            $appointment->update([
                'status' => 'completed'
            ]);

            DB::commit();
            Log::info("=== PROCESS PAYMENT COMPLETE === DF Payment ID: " . ($dfPayment ? $dfPayment->id : 'NONE (Amount was 0)'));

            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to process payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get invoice for appointment (for editing completed payment)
     */
    public function getAppointmentInvoice($appointmentId)
    {
        $treatment = Treatment::with(['service'])->where('appointment_id', $appointmentId)->first();

        if (!$treatment) {
            return response()->json([
                'success' => false,
                'message' => 'No treatment found'
            ]);
        }

        $invoice = null;
        $items = [];

        if ($treatment->invoice_id) {
            $invoice = Invoice::find($treatment->invoice_id);
            if ($invoice) {
                $items = InvoiceItem::where('invoice_id', $invoice->id)->get()->map(function($item) {
                    // Use package_id for courses, service_id for services
                    $itemId = $item->item_type === 'course_package' ? $item->package_id : $item->service_id;
                    return [
                        'id' => $item->id,
                        'item_type' => $item->item_type,
                        'item_id' => $itemId,
                        'description' => $item->description,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'discount_amount' => $item->discount_amount,
                        'total_amount' => $item->total_amount,
                    ];
                });
            }
        }

        // Get course purchase if used
        $coursePurchase = null;
        $sellerIds = [];
        $sellerNames = [];
        if ($treatment->course_purchase_id) {
            $coursePurchase = CoursePurchase::with('package')->find($treatment->course_purchase_id);
            if ($coursePurchase) {
                $sellerIds = $coursePurchase->seller_ids ?? [];
                // Get seller names
                if (!empty($sellerIds)) {
                    $sellerNames = User::whereIn('id', $sellerIds)->pluck('name')->toArray();
                }
            }
        }

        // Also check for courses purchased with this invoice
        $purchasedCourses = [];
        if ($invoice) {
            $purchasedCourses = CoursePurchase::with('package')
                ->where('invoice_id', $invoice->id)
                ->get()
                ->map(function ($cp) {
                    $cpSellerIds = $cp->seller_ids ?? [];
                    $cpSellerNames = [];
                    if (!empty($cpSellerIds)) {
                        $cpSellerNames = User::whereIn('id', $cpSellerIds)->pluck('name')->toArray();
                    }
                    return [
                        'id' => $cp->id,
                        'package_id' => $cp->package_id,
                        'package_name' => $cp->package->name ?? 'Unknown',
                        'price' => $cp->package->price ?? 0,
                        'total_sessions' => $cp->total_sessions,
                        'used_sessions' => $cp->used_sessions,
                        'seller_ids' => $cpSellerIds,
                        'seller_names' => $cpSellerNames,
                        'payment_type' => $cp->payment_type ?? 'full',
                        'installment_total' => $cp->installment_total ?? 0,
                        'installment_amount' => $cp->installment_amount ?? 0
                    ];
                });
        }

        return response()->json([
            'success' => true,
            'treatment' => [
                'id' => $treatment->id,
                'pt_id' => $treatment->pt_id,
                'service_id' => $treatment->service_id,
                'service_name' => $treatment->service->name ?? null,
                'service_price' => $treatment->service->default_price ?? 0,
                'course_purchase_id' => $treatment->course_purchase_id,
                'billing_status' => $treatment->billing_status
            ],
            'invoice' => $invoice,
            'items' => $items,
            'course_purchase' => $coursePurchase ? [
                'id' => $coursePurchase->id,
                'package_name' => $coursePurchase->package->name ?? 'Unknown',
                'seller_ids' => $sellerIds,
                'seller_names' => $sellerNames
            ] : null,
            'purchased_courses' => $purchasedCourses
        ]);
    }

    /**
     * Update existing payment (edit mode - full edit with DF Transfer)
     */
    public function updatePayment(Request $request, $appointmentId)
    {
        try {
            DB::beginTransaction();

            Log::info("=== UPDATE PAYMENT START === Appointment ID: {$appointmentId}");

            $treatment = Treatment::where('appointment_id', $appointmentId)->first();
            if (!$treatment) {
                throw new \Exception('Treatment not found');
            }

            $oldPtId = $treatment->pt_id;
            $newPtId = $request->pt_id;
            $oldServiceId = $treatment->service_id;

            // Calculate new DF amount based on service
            $newDfAmount = 0;
            $newServiceId = null;

            // Process cart items to get service
            if ($request->has('cart_items') && is_array($request->cart_items)) {
                foreach ($request->cart_items as $item) {
                    $itemType = $item['type'] ?? '';
                    $itemId = $item['id'] ?? null;

                    if ($itemType === 'service' && $itemId) {
                        $service = Service::find($itemId);
                        if ($service) {
                            $newServiceId = $service->id;
                            $newDfAmount = $service->df_amount ?? $service->default_df_rate ?? 0;
                        }
                    } elseif ($itemType === 'use_course' && $itemId) {
                        $coursePurchase = CoursePurchase::with('package')->find($itemId);
                        if ($coursePurchase && $coursePurchase->package) {
                            $newDfAmount = $coursePurchase->package->df_amount ?? 0;
                        }
                    }
                }
            }

            // If no new service from cart, keep old or calculate from existing
            if (!$newServiceId && $treatment->service_id) {
                $newServiceId = $treatment->service_id;
                $service = Service::find($treatment->service_id);
                if ($service) {
                    $newDfAmount = $service->df_amount ?? $service->default_df_rate ?? 0;
                }
            }

            // DF TRANSFER LOGIC
            if ($oldPtId != $newPtId || $treatment->df_amount != $newDfAmount) {
                Log::info("DF Transfer: PT {$oldPtId} -> {$newPtId}, Amount {$treatment->df_amount} -> {$newDfAmount}");

                // Find existing DF payment for this treatment
                $existingDf = \App\Models\DfPayment::where('treatment_id', $treatment->id)->first();

                if ($existingDf) {
                    if ($newPtId && $newDfAmount > 0) {
                        // Transfer DF to new PT with new amount
                        $existingDf->update([
                            'pt_id' => $newPtId,
                            'amount' => $newDfAmount,
                            'service_id' => $newServiceId,
                            'notes' => $existingDf->notes . ' [แก้ไข: ย้ายจาก PT ' . $oldPtId . ']'
                        ]);
                        Log::info("DF Payment {$existingDf->id} transferred to PT {$newPtId}");
                    } else {
                        // Delete DF if no PT or amount is 0
                        $existingDf->delete();
                        Log::info("DF Payment {$existingDf->id} deleted (no PT or amount 0)");
                    }
                } elseif ($newPtId && $newDfAmount > 0) {
                    // Create new DF payment if doesn't exist
                    $newDf = \App\Models\DfPayment::create([
                        'treatment_id' => $treatment->id,
                        'pt_id' => $newPtId,
                        'service_id' => $newServiceId,
                        'branch_id' => $treatment->branch_id,
                        'amount' => $newDfAmount,
                        'source_type' => $treatment->course_purchase_id ? 'course_usage' : 'per_session',
                        'payment_date' => now()->toDateString(),
                        'notes' => 'ค่ามือ (สร้างจากการแก้ไข)'
                    ]);
                    Log::info("New DF Payment created: {$newDf->id}");
                }
            }

            // Update treatment
            $treatment->update([
                'pt_id' => $newPtId,
                'service_id' => $newServiceId ?? $treatment->service_id,
                'df_amount' => $newDfAmount
            ]);

            // Update invoice total if service changed
            if ($oldServiceId != $newServiceId && $treatment->invoice_id) {
                $invoice = Invoice::find($treatment->invoice_id);
                if ($invoice && $newServiceId) {
                    $newService = Service::find($newServiceId);
                    if ($newService) {
                        // Update invoice item
                        $invoiceItem = InvoiceItem::where('invoice_id', $invoice->id)
                            ->where('item_type', 'service')
                            ->first();

                        if ($invoiceItem) {
                            $invoiceItem->update([
                                'item_id' => $newServiceId,
                                'description' => $newService->name,
                                'unit_price' => $newService->default_price,
                                'total_amount' => $newService->default_price
                            ]);
                        }

                        // Recalculate invoice total
                        $newTotal = InvoiceItem::where('invoice_id', $invoice->id)->sum('total_amount');
                        $invoice->update([
                            'subtotal' => $newTotal,
                            'total_amount' => $newTotal,
                            'paid_amount' => $newTotal
                        ]);

                        Log::info("Invoice {$invoice->id} updated: total = {$newTotal}");
                    }
                }
            }

            // Process courses - CREATE NEW or UPDATE SELLERS
            if ($request->has('cart_items')) {
                $invoice = $treatment->invoice_id ? Invoice::find($treatment->invoice_id) : null;
                $patientId = $treatment->patient_id;
                $branchId = $treatment->branch_id;

                // *** STEP 1: DELETE ALL OLD COMMISSIONS FOR THIS INVOICE ***
                if ($invoice) {
                    $deletedCount = Commission::where('invoice_id', $invoice->id)->delete();
                    Log::info("Deleted {$deletedCount} old commission records for invoice {$invoice->id}");
                }

                Log::info("Processing cart_items: " . json_encode($request->cart_items));

                foreach ($request->cart_items as $item) {
                    $itemType = $item['type'] ?? '';
                    $itemId = $item['id'] ?? null;
                    $sellerIdsFromItem = $item['seller_ids'] ?? [];

                    Log::info("Cart item - Type: {$itemType}, ID: {$itemId}, Sellers: " . json_encode($sellerIdsFromItem));

                    // *** CREATE NEW COURSE PURCHASE OR UPDATE EXISTING ***
                    if ($itemType === 'course' || $itemType === 'course_package') {
                        // Try to find package by ID first, then by name
                        $package = null;
                        if ($itemId) {
                            $package = CoursePackage::withoutGlobalScopes()->find($itemId);
                        }

                        // If no ID, try to find by name
                        $itemName = $item['name'] ?? '';
                        if (!$package && $itemName) {
                            $package = CoursePackage::withoutGlobalScopes()->where('name', $itemName)->first();
                        }

                        // Check if this course already exists for this invoice
                        $existingCourse = null;
                        if ($invoice && $package) {
                            $existingCourse = CoursePurchase::where('invoice_id', $invoice->id)
                                ->where('package_id', $package->id)
                                ->first();
                        }

                        Log::info("Course lookup - Package: " . ($package ? $package->name : 'NOT FOUND') . ", Existing: " . ($existingCourse ? 'YES' : 'NO'));

                        if ($package && !$existingCourse) {
                            Log::info("Creating NEW CoursePurchase for package: {$package->name}");

                            // If no invoice exists, create one
                            if (!$invoice) {
                                $invoice = Invoice::create([
                                    'patient_id' => $patientId,
                                    'branch_id' => $branchId,
                                    'invoice_number' => 'INV-' . now()->format('Ymd') . '-' . rand(1000, 9999),
                                    'invoice_date' => now(),
                                    'due_date' => now(),
                                    'invoice_type' => 'walk_in',
                                    'subtotal' => 0,
                                    'discount_amount' => 0,
                                    'tax_amount' => 0,
                                    'total_amount' => 0,
                                    'paid_amount' => 0,
                                    'outstanding_amount' => 0,
                                    'status' => 'paid',
                                    'notes' => 'Payment method: ' . ($request->payment_method ?? 'cash'),
                                    'created_by' => auth()->id() ?? null
                                ]);
                                $treatment->update(['invoice_id' => $invoice->id]);
                            }

                            // Generate course number
                            $courseNumber = 'CRS-' . now()->format('Ymd') . '-' . rand(1000, 9999);

                            // Get installment info
                            $paymentType = $item['payment_type'] ?? 'full';
                            $installmentTotal = $item['installment_total'] ?? 0;
                            $installmentAmount = $item['installment_amount'] ?? 0;
                            $installmentPaid = $paymentType === 'installment' ? 1 : 0;

                            // Get seller_ids from item
                            $itemSellerIds = $item['seller_ids'] ?? [];
                            if (is_string($itemSellerIds)) {
                                $itemSellerIds = json_decode($itemSellerIds, true) ?? [];
                            }

                            // Create course purchase
                            $coursePurchase = CoursePurchase::create([
                                'course_number' => $courseNumber,
                                'patient_id' => $patientId,
                                'package_id' => $package->id,
                                'invoice_id' => $invoice->id,
                                'purchase_branch_id' => $branchId,
                                'purchase_pattern' => 'buy_for_later',
                                'purchase_date' => now(),
                                'activation_date' => now(),
                                'expiry_date' => now()->addDays($package->validity_days),
                                'total_sessions' => $package->total_sessions,
                                'used_sessions' => 0,
                                'status' => 'active',
                                'allow_branch_sharing' => false,
                                'created_by' => auth()->id() ?? null,
                                'payment_type' => $paymentType,
                                'installment_total' => $installmentTotal,
                                'installment_paid' => $installmentPaid,
                                'installment_amount' => $installmentAmount,
                                'seller_ids' => $itemSellerIds
                            ]);

                            Log::info("CoursePurchase CREATED: {$coursePurchase->id}, sellers: " . json_encode($itemSellerIds));

                            // Calculate amounts based on payment type
                            $unitPrice = $paymentType === 'installment' ? $installmentAmount : $package->price;
                            $description = $package->name;
                            if ($paymentType === 'installment') {
                                $description .= ' (ผ่อนงวด 1/' . $installmentTotal . ')';
                            }

                            // Create invoice item
                            InvoiceItem::create([
                                'invoice_id' => $invoice->id,
                                'item_type' => 'course_package',
                                'package_id' => $package->id,
                                'description' => $description,
                                'quantity' => 1,
                                'unit_price' => $unitPrice,
                                'discount_amount' => 0,
                                'total_amount' => $unitPrice
                            ]);

                            // Recalculate invoice total
                            $newTotal = InvoiceItem::where('invoice_id', $invoice->id)->sum('total_amount');
                            $invoice->update([
                                'subtotal' => $newTotal,
                                'total_amount' => $newTotal,
                                'paid_amount' => $newTotal
                            ]);

                            Log::info("Invoice updated with course: total = {$newTotal}");

                            // *** COMMISSION SPLITTING LOGIC (All old commissions already deleted above) ***
                            if (!empty($itemSellerIds)) {
                                // Calculate total commission amount
                                $commissionRate = $package->commission_rate ?? 0;
                                $baseAmount = $package->price ?? 0;
                                $totalCommission = $baseAmount * ($commissionRate / 100);

                                // Split commission among sellers
                                $sellerCount = count($itemSellerIds);
                                $splitAmount = $sellerCount > 0 ? round($totalCommission / $sellerCount, 2) : 0;

                                Log::info("Commission Split (new course): Total {$totalCommission}, Sellers: {$sellerCount}, Each: {$splitAmount}");

                                // Get the invoice item we just created
                                $invoiceItem = InvoiceItem::where('invoice_id', $invoice->id)
                                    ->where('item_type', 'course_package')
                                    ->where('package_id', $package->id)
                                    ->latest()
                                    ->first();

                                // Create commission record for each seller
                                foreach ($itemSellerIds as $sellerId) {
                                    $commissionNumber = 'COM-' . now()->format('Ymd') . '-' . rand(1000, 9999);

                                    Commission::create([
                                        'commission_number' => $commissionNumber,
                                        'pt_id' => $sellerId,
                                        'invoice_id' => $invoice->id,
                                        'invoice_item_id' => $invoiceItem ? $invoiceItem->id : null,
                                        'branch_id' => $branchId,
                                        'commission_type' => 'package_sale',
                                        'base_amount' => $baseAmount,
                                        'commission_rate' => $commissionRate,
                                        'commission_amount' => $splitAmount,
                                        'status' => 'pending',
                                        'commission_date' => now()->toDateString(),
                                        'is_clawback_eligible' => true,
                                        'notes' => "ค่าคอมขายคอร์ส: {$package->name}" . ($sellerCount > 1 ? " (แบ่ง {$sellerCount} คน)" : ''),
                                        'created_by' => auth()->id() ?? null
                                    ]);

                                    Log::info("Commission created for seller {$sellerId}: {$splitAmount} THB");
                                }
                            }

                        } elseif ($existingCourse && isset($item['seller_ids'])) {
                            // Update existing course sellers AND recalculate commission split
                            $sellerIds = $item['seller_ids'];
                            if (is_string($sellerIds)) {
                                $sellerIds = json_decode($sellerIds, true) ?? [];
                            }
                            $existingCourse->update(['seller_ids' => $sellerIds]);
                            Log::info("Updated sellers for existing course: {$existingCourse->id}");

                            // *** RECALCULATE COMMISSION SPLIT FOR EXISTING COURSE (All old commissions already deleted above) ***
                            if (!empty($sellerIds)) {
                                $package = $existingCourse->package;
                                if ($package) {
                                    // Calculate and split
                                    $commissionRate = $package->commission_rate ?? 0;
                                    $baseAmount = $package->price ?? 0;
                                    $totalCommission = $baseAmount * ($commissionRate / 100);
                                    $sellerCount = count($sellerIds);
                                    $splitAmount = $sellerCount > 0 ? round($totalCommission / $sellerCount, 2) : 0;

                                    Log::info("Commission Split (existing course): Total {$totalCommission}, Sellers: {$sellerCount}, Each: {$splitAmount}");

                                    // Get invoice item
                                    $invoiceItem = InvoiceItem::where('invoice_id', $invoice->id)
                                        ->where('item_type', 'course_package')
                                        ->where('package_id', $package->id)
                                        ->first();

                                    // Create new commission records
                                    foreach ($sellerIds as $sellerId) {
                                        $commissionNumber = 'COM-' . now()->format('Ymd') . '-' . rand(1000, 9999);

                                        Commission::create([
                                            'commission_number' => $commissionNumber,
                                            'pt_id' => $sellerId,
                                            'invoice_id' => $invoice->id,
                                            'invoice_item_id' => $invoiceItem ? $invoiceItem->id : null,
                                            'branch_id' => $branchId,
                                            'commission_type' => 'package_sale',
                                            'base_amount' => $baseAmount,
                                            'commission_rate' => $commissionRate,
                                            'commission_amount' => $splitAmount,
                                            'status' => 'pending',
                                            'commission_date' => now()->toDateString(),
                                            'is_clawback_eligible' => true,
                                            'notes' => "ค่าคอมขายคอร์ส: {$package->name}" . ($sellerCount > 1 ? " (แบ่ง {$sellerCount} คน)" : ''),
                                            'created_by' => auth()->id() ?? null
                                        ]);

                                        Log::info("Commission created for seller {$sellerId}: {$splitAmount} THB");
                                    }
                                }
                            }
                        }
                    }
                }
            }

            // Update payment method
            if ($request->has('payment_method') && $treatment->invoice_id) {
                $invoice = Invoice::find($treatment->invoice_id);
                if ($invoice) {
                    $invoice->update(['notes' => 'Payment method: ' . $request->payment_method]);
                }
            }

            DB::commit();
            Log::info("=== UPDATE PAYMENT COMPLETE ===");

            return response()->json([
                'success' => true,
                'message' => 'อัพเดทข้อมูลสำเร็จ'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Update payment failed: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get patient's active courses
     */
    public function getPatientCourses($patientId)
    {
        $coursesRaw = CoursePurchase::with('package')
            ->where('patient_id', $patientId)
            ->where('status', 'active')
            ->whereRaw('used_sessions < total_sessions')
            ->where(function ($q) {
                // Also check expiry date - exclude expired courses
                $q->whereNull('expiry_date')
                    ->orWhere('expiry_date', '>=', now()->toDateString());
            })
            ->get();

        // Pre-fetch all seller names at once to avoid N+1
        $allSellerIds = [];
        foreach ($coursesRaw as $course) {
            $sellerIds = $course->seller_ids ?? [];
            $allSellerIds = array_merge($allSellerIds, $sellerIds);
        }
        $allSellers = User::whereIn('id', array_unique($allSellerIds))->pluck('name', 'id');

        $courses = $coursesRaw->map(function ($course) use ($allSellers) {
            // Get seller names from pre-fetched data
            $sellerIds = $course->seller_ids ?? [];
            $sellerNames = [];
            foreach ($sellerIds as $id) {
                if (isset($allSellers[$id])) {
                    $sellerNames[] = $allSellers[$id];
                }
            }

            return [
                'id' => $course->id,
                'name' => $course->package->name ?? 'Unknown',
                'remaining' => $course->total_sessions - $course->used_sessions,
                'expiry_date' => $course->expiry_date ? $course->expiry_date->format('d/m/Y') : null,
                'payment_type' => $course->payment_type ?? 'full',
                'installment_total' => $course->installment_total ?? 0,
                'installment_paid' => $course->installment_paid ?? 0,
                'installment_amount' => $course->installment_amount ?? 0,
                'package_name' => $course->package->name ?? 'Unknown',
                'total_sessions' => $course->total_sessions,
                'used_sessions' => $course->used_sessions,
                'seller_ids' => $sellerIds,
                'seller_names' => $sellerNames
            ];
        });

        // Get shared courses (also check expiry)
        $sharedCourses = \App\Models\CourseSharedUser::with(['coursePurchase.package', 'coursePurchase.patient'])
            ->where('shared_patient_id', $patientId)
            ->whereHas('coursePurchase', function ($q) {
                $q->where('status', 'active')
                    ->whereRaw('used_sessions < total_sessions')
                    ->where(function ($q2) {
                        $q2->whereNull('expiry_date')
                            ->orWhere('expiry_date', '>=', now()->toDateString());
                    });
            })
            ->get()
            ->map(function ($shared) {
                $course = $shared->coursePurchase;
                return [
                    'id' => $course->id,
                    'name' => $course->package->name ?? 'Unknown',
                    'remaining' => $course->total_sessions - $course->used_sessions,
                    'owner_name' => $course->patient->name ?? 'Unknown'
                ];
            });

        return response()->json([
            'courses' => $courses,
            'shared_courses' => $sharedCourses
        ]);
    }

    /**
     * Get treatment detail for an appointment
     */
    public function getTreatmentDetail($appointmentId)
    {
        $appointment = Appointment::with(['patient', 'pt'])->find($appointmentId);

        if (!$appointment) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบข้อมูลนัดหมาย'
            ]);
        }

        $treatment = Treatment::with(['service', 'pt'])
            ->where('appointment_id', $appointmentId)
            ->first();

        // Get invoice for this appointment
        $invoice = Invoice::with('items')
            ->where('patient_id', $appointment->patient_id)
            ->whereDate('invoice_date', $appointment->appointment_date)
            ->latest()
            ->first();

        return response()->json([
            'success' => true,
            'appointment' => [
                'id' => $appointment->id,
                'date' => $appointment->appointment_date,
                'time' => $appointment->appointment_time,
                'date_display' => \Carbon\Carbon::parse($appointment->appointment_date)->locale('th')->isoFormat('D MMM') . ' ' . (\Carbon\Carbon::parse($appointment->appointment_date)->year + 543),
                'time_display' => \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') . ' น.',
                'status' => $appointment->status,
                'purpose' => $appointment->purpose,
            ],
            'treatment' => $treatment ? [
                'id' => $treatment->id,
                'service_name' => $treatment->service->name ?? null,
                'pt_name' => $treatment->pt->name ?? $treatment->pt->username ?? null,
                'duration_minutes' => $treatment->duration_minutes,
                'billing_status' => $treatment->billing_status,
                'treatment_notes' => $treatment->treatment_notes,
                'started_at' => $treatment->started_at ? $treatment->started_at->format('H:i') : null,
                'completed_at' => $treatment->completed_at ? $treatment->completed_at->format('H:i') : null,
            ] : [
                'id' => null,
                'service_name' => $appointment->purpose ?? 'ไม่ระบุ',
                'pt_name' => $appointment->pt->name ?? $appointment->pt->username ?? 'ไม่ระบุ',
                'duration_minutes' => null,
                'billing_status' => null,
                'treatment_notes' => null,
                'started_at' => null,
                'completed_at' => null,
            ],
            'invoice' => $invoice ? [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'total_amount' => $invoice->total_amount,
                'status' => $invoice->status,
                'items' => $invoice->items->map(function ($item) {
                    return [
                        'description' => $item->description,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'total_amount' => $item->total_amount,
                    ];
                })
            ] : null
        ]);
    }

    /**
     * Get patient's last treatment for retroactive
     */
    public function getPatientLastTreatment($patientId)
    {
        $treatment = Treatment::with('service')
            ->where('patient_id', $patientId)
            ->where('billing_status', 'paid')
            ->latest()
            ->first();

        if (!$treatment || !$treatment->service) {
            return response()->json(['treatment' => null]);
        }

        return response()->json([
            'treatment' => [
                'id' => $treatment->id,
                'service_name' => $treatment->service->name,
                'price' => $treatment->service->default_price,
                'date' => $treatment->created_at->format('d/m/Y')
            ]
        ]);
    }

    /**
     * Get invoice detail by ID
     */
    public function getInvoiceDetail($invoiceId)
    {
        $invoice = Invoice::with(['items', 'patient', 'createdBy'])->find($invoiceId);

        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบข้อมูลใบเสร็จ'
            ]);
        }

        // Get related treatment info
        $treatment = Treatment::with(['service', 'pt'])
            ->where('patient_id', $invoice->patient_id)
            ->whereDate('created_at', $invoice->invoice_date)
            ->first();

        return response()->json([
            'success' => true,
            'invoice' => [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'date_display' => $invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->locale('th')->isoFormat('D MMM') . ' ' . (\Carbon\Carbon::parse($invoice->invoice_date)->year + 543) : '-',
                'time_display' => $invoice->created_at ? $invoice->created_at->format('H:i') . ' น.' : '-',
                'invoice_type' => $invoice->invoice_type,
                'type_display' => $invoice->invoice_type == 'course' ? 'คอร์ส' : ($invoice->invoice_type == 'refund' ? 'คืนเงิน' : 'บริการ'),
                'subtotal' => $invoice->subtotal,
                'discount_amount' => $invoice->discount_amount,
                'tax_amount' => $invoice->tax_amount,
                'total_amount' => $invoice->total_amount,
                'paid_amount' => $invoice->paid_amount,
                'status' => $invoice->status,
                'notes' => $invoice->notes,
                'patient_name' => $invoice->patient->name ?? 'ไม่ระบุ',
                'created_by_name' => $invoice->createdBy->name ?? $invoice->createdBy->username ?? 'ระบบ',
                'pt_name' => $treatment ? ($treatment->pt->name ?? $treatment->pt->username ?? 'ไม่ระบุ') : null,
                'service_name' => $treatment ? ($treatment->service->name ?? 'ไม่ระบุ') : null,
                'treatment_notes' => $treatment ? $treatment->treatment_notes : null,
                'duration_minutes' => $treatment ? $treatment->duration_minutes : null,
                'items' => $invoice->items->map(function ($item) {
                    return [
                        'description' => $item->description,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'total_amount' => $item->total_amount,
                    ];
                })
            ]
        ]);
    }

    /**
     * Cancel treatment - revert from in_treatment to waiting
     * If patient was newly converted, revert HN and temporary status
     */
    public function cancelTreatment($id)
    {
        try {
            DB::beginTransaction();

            $queue = Queue::with(['patient', 'appointment'])->findOrFail($id);

            // Check if patient was converted today (new customer)
            $patient = $queue->patient;
            $wasConvertedToday = $patient &&
                                 $patient->converted_at &&
                                 $patient->converted_at->isToday();

            // Delete treatment record
            Treatment::where('queue_id', $queue->id)->delete();

            // Delete OPD record if it was created today for this patient
            if ($wasConvertedToday) {
                OpdRecord::where('patient_id', $patient->id)
                    ->whereDate('created_at', today())
                    ->delete();

                // Revert patient to temporary status
                $patient->update([
                    'is_temporary' => true,
                    'hn_number' => null,
                    'converted_at' => null,
                ]);
            }

            // Update queue status back to waiting
            $queue->update([
                'status' => 'waiting',
                'started_at' => null,
                'called_at' => null,
            ]);

            // Update appointment status if exists
            if ($queue->appointment) {
                $queue->appointment->update([
                    'status' => 'confirmed'
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'ยกเลิกการรักษาสำเร็จ กลับไปสถานะรอคิว',
                'reverted_hn' => $wasConvertedToday
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
     * Cancel payment - revert from awaiting_payment to in_treatment (confirmed)
     * Delete OPD and Treatment record
     */
    public function cancelPayment($id)
    {
        try {
            DB::beginTransaction();

            $appointment = Appointment::with('patient')->findOrFail($id);

            // Check if patient was converted today (new customer)
            $patient = $appointment->patient;
            $wasConvertedToday = $patient &&
                                 $patient->converted_at &&
                                 $patient->converted_at->isToday();

            // Delete treatment record
            Treatment::where('appointment_id', $appointment->id)->delete();

            // Delete OPD record if it was created today for this patient
            if ($wasConvertedToday) {
                OpdRecord::where('patient_id', $patient->id)
                    ->whereDate('created_at', today())
                    ->delete();

                // Revert patient to temporary status
                $patient->update([
                    'is_temporary' => true,
                    'hn_number' => null,
                    'converted_at' => null,
                ]);
            }

            // Update appointment status back to confirmed (in_treatment)
            $appointment->update([
                'status' => 'confirmed'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'ยกเลิกการชำระเงินสำเร็จ กลับไปสถานะกำลังรักษา',
                'reverted_hn' => $wasConvertedToday
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
     * Revert complete - revert from completed to awaiting_payment
     * This will delete all payment records (Invoice, InvoiceItems, CoursePurchase)
     */
    public function revertComplete($id)
    {
        try {
            DB::beginTransaction();

            $appointment = Appointment::findOrFail($id);

            // Get treatment record
            $treatment = Treatment::where('appointment_id', $appointment->id)->first();

            if ($treatment) {
                // FIRST: Restore course session if this treatment used a course
                if ($treatment->course_purchase_id) {
                    $coursePurchase = CoursePurchase::find($treatment->course_purchase_id);
                    if ($coursePurchase && $coursePurchase->used_sessions > 0) {
                        $coursePurchase->decrement('used_sessions');

                        // If was completed, reactivate
                        if ($coursePurchase->status === 'completed') {
                            $coursePurchase->update(['status' => 'active']);
                        }
                    }
                }

                // THEN: Delete invoice linked to this treatment
                if ($treatment->invoice_id) {
                    $invoice = Invoice::find($treatment->invoice_id);
                    if ($invoice) {
                        // Delete invoice items first
                        InvoiceItem::where('invoice_id', $invoice->id)->delete();

                        // Delete ALL course purchases linked to this invoice
                        // This includes newly purchased courses
                        $coursePurchases = CoursePurchase::where('invoice_id', $invoice->id)->get();
                        foreach ($coursePurchases as $cp) {
                            $cp->delete();
                        }

                        // Delete the invoice
                        $invoice->delete();
                    }
                }

                // Reset treatment
                $treatment->update([
                    'billing_status' => 'pending',
                    'service_id' => null,
                    'invoice_id' => null,
                    'course_purchase_id' => null
                ]);
            }

            // Update appointment status back to awaiting_payment
            $appointment->update([
                'status' => 'awaiting_payment'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'ย้อนกลับสำเร็จ ลบข้อมูลการชำระเงินแล้ว'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'ไม่สามารถย้อนกลับได้: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Print receipt for completed appointment
     */
    public function printReceipt($id)
    {
        $appointment = Appointment::with(['patient', 'branch'])->findOrFail($id);
        $treatment = Treatment::where('appointment_id', $id)
            ->with(['service', 'pt'])
            ->first();

        // Get invoice for this appointment - find by patient_id and date
        // First try to find invoice created on the same date
        $invoice = Invoice::where('patient_id', $appointment->patient_id)
            ->whereDate('invoice_date', $appointment->appointment_date ?? now()->toDateString())
            ->with('items')
            ->latest()
            ->first();

        // If no invoice found, try to find any recent invoice for this patient
        if (!$invoice) {
            $invoice = Invoice::where('patient_id', $appointment->patient_id)
                ->with('items')
                ->latest()
                ->first();
        }

        return view('queue.receipt', compact('appointment', 'treatment', 'invoice'));
    }

    /**
     * Cancel queue entry completely - for waiting status
     * If temporary patient, force delete everything
     */
    public function cancelQueue($id)
    {
        try {
            DB::beginTransaction();

            $queue = Queue::with(['patient', 'appointment'])->findOrFail($id);

            // Check if patient is temporary (new customer who never started treatment)
            $patient = $queue->patient;
            $isTemporary = $patient && $patient->is_temporary;

            if ($isTemporary) {
                $patientId = $patient->id;

                // Delete all related data
                Treatment::where('patient_id', $patientId)->forceDelete();
                OpdRecord::where('patient_id', $patientId)->forceDelete();
                Queue::where('patient_id', $patientId)->forceDelete();
                Appointment::where('patient_id', $patientId)->forceDelete();

                // Force delete the patient
                $patient->forceDelete();
            } else {
                // Just delete/cancel the queue entry
                if ($queue->appointment) {
                    $queue->appointment->update([
                        'status' => 'cancelled'
                    ]);
                }
                $queue->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $isTemporary
                    ? 'ยกเลิกคิวและลบข้อมูลลูกค้าชั่วคราวสำเร็จ'
                    : 'ยกเลิกคิวสำเร็จ',
                'deleted_patient' => $isTemporary
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'ไม่สามารถยกเลิกได้: ' . $e->getMessage()
            ], 500);
        }
    }
}
