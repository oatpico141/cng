<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Branch;
use App\Models\Appointment;
use App\Models\Queue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatientController extends Controller
{
    /**
     * Display a listing of patients with search and filter
     */
    public function index(Request $request)
    {
        // แสดงเฉพาะ "ลูกค้าจริง" (is_temporary = false) เท่านั้น
        $query = Patient::with('firstVisitBranch')
            ->where('is_temporary', false);

        // *** BRANCH FILTER - แยกข้อมูลตามสาขา ***
        // ถ้ามี branch_id ใน session ให้ filter ตาม branch นั้น
        // Admin ถ้าเลือกสาขาแล้วจะเห็นเฉพาะสาขานั้น ถ้าไม่เลือกจะเห็นทุกสาขา
        $selectedBranchId = session('selected_branch_id');
        $user = auth()->user();

        // ถ้าเลือกสาขาแล้ว ให้ filter ตามสาขานั้น (รวม admin ด้วย)
        if ($selectedBranchId) {
            $query->where('first_visit_branch_id', $selectedBranchId);
        } elseif ($user && $user->branch_id) {
            // ถ้าไม่ได้เลือก branch แต่ user มี branch_id ให้ใช้ branch ของ user
            $query->where('first_visit_branch_id', $user->branch_id);
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('phone', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Gender filter
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // Branch filter
        if ($request->filled('branch')) {
            $query->where('first_visit_branch_id', $request->branch);
        }

        // Customer Type Filter
        if ($request->filled('filter')) {
            if ($request->filter == 'course') {
                // แสดงเฉพาะลูกค้าที่มีคอร์ส
                $query->whereHas('coursePurchases', function($q) {
                    $q->where('status', 'active')
                      ->where('expiry_date', '>=', now())
                      ->where('remaining_sessions', '>', 0);
                });
            } elseif ($request->filter == 'normal') {
                // แสดงเฉพาะลูกค้าทั่วไป (ไม่มีคอร์ส)
                $query->whereDoesntHave('coursePurchases', function($q) {
                    $q->where('status', 'active')
                      ->where('expiry_date', '>=', now())
                      ->where('remaining_sessions', '>', 0);
                });
            }
        }

        // Age Range Filter
        if ($request->filled('age_range')) {
            $today = now();
            switch ($request->age_range) {
                case '0-20':
                    $query->whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, ?) BETWEEN 0 AND 20', [$today]);
                    break;
                case '21-40':
                    $query->whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, ?) BETWEEN 21 AND 40', [$today]);
                    break;
                case '41-60':
                    $query->whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, ?) BETWEEN 41 AND 60', [$today]);
                    break;
                case '60+':
                    $query->whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, ?) >= 60', [$today]);
                    break;
            }
        }

        // Sorting
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'hn_asc':
                    $query->orderBy('hn_number', 'asc');
                    break;
                case 'hn_desc':
                    $query->orderBy('hn_number', 'desc');
                    break;
                case 'name_asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                default:
                    $query->orderBy('hn_number', 'desc');
                    break;
            }
        } else {
            // Default: Order by HN descending (latest HN first)
            $query->orderBy('hn_number', 'desc');
        }

        // Paginate results
        $patients = $query->paginate(15)->withQueryString();

        // Get all branches for filter dropdown
        $branches = Branch::where('is_active', true)->get();

        return view('patients.index', compact('patients', 'branches'));
    }

    /**
     * Show the form for creating a new patient
     */
    public function create()
    {
        // Get all active branches for the dropdown
        $branches = Branch::where('is_active', true)->get();

        return view('patients.create', compact('branches'));
    }

    /**
     * Store a newly created patient in database
     */
    public function store(Request $request)
    {
        // Relaxed validation - allow incomplete data to be saved
        $validated = $request->validate([
            'prefix' => 'nullable|string|max:20',
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'first_name_en' => 'nullable|string|max:100',
            'last_name_en' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:15|unique:patients,phone',
            'email' => 'nullable|email|max:255|unique:patients,email',
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'blood_group' => 'nullable|string|max:10',
            'id_card' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'subdistrict' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'line_id' => 'nullable|string|max:100',
            'emergency_contact' => 'nullable|string|max:255',
            'chronic_diseases' => 'nullable|string',
            'drug_allergy' => 'nullable|string',
            'food_allergy' => 'nullable|string',
            'surgery_history' => 'nullable|string',
            'chief_complaint' => 'nullable|string',
            'insurance_type' => 'nullable|string|max:50',
            'insurance_number' => 'nullable|string|max:100',
            'booking_channel' => 'nullable|string|max:50',
            'photo' => 'nullable|image|max:2048',
        ], [
            'phone.unique' => 'เบอร์โทรศัพท์นี้ถูกใช้งานแล้ว',
            'email.unique' => 'อีเมลนี้ถูกใช้งานแล้ว',
            'birth_date.before' => 'วันเกิดต้องเป็นวันที่ผ่านมาแล้ว',
        ]);

        try {
            // Combine name fields
            $validated['name'] = trim(
                ($validated['prefix'] ?? '') . ' ' .
                ($validated['first_name'] ?? '') . ' ' .
                ($validated['last_name'] ?? '')
            );

            // Auto-assign branch from session or user's branch or default
            // Validate that the branch actually exists in database
            $branchId = session('selected_branch_id');
            if ($branchId && !Branch::find($branchId)) {
                $branchId = null; // Clear stale session value
                session()->forget('selected_branch_id');
            }

            if (!$branchId && auth()->user() && auth()->user()->branch_id) {
                $branchId = auth()->user()->branch_id;
                if (!Branch::find($branchId)) {
                    $branchId = null;
                }
            }

            if (!$branchId) {
                $branchId = Branch::first()?->id;
            }

            $validated['first_visit_branch_id'] = $branchId;

            // Also set branch_id for patient's default branch
            $validated['branch_id'] = $validated['first_visit_branch_id'];

            // Handle photo upload
            if ($request->hasFile('photo')) {
                $validated['photo'] = $request->file('photo')->store('patients', 'public');
            }

            // Create new patient
            $patient = Patient::create($validated);

            // Check if "Save and Queue" action
            if ($request->input('action') === 'save_and_queue') {
                // Create appointment for today (walk-in)
                $appointment = Appointment::create([
                    'patient_id' => $patient->id,
                    'branch_id' => $branchId,
                    'appointment_date' => today(),
                    'appointment_time' => now()->format('H:i:s'),
                    'status' => 'confirmed',
                    'purpose' => 'Walk-in',
                    'notes' => 'ลูกค้า Walk-in สร้างจากหน้าลงทะเบียน',
                ]);

                // Create queue entry
                $queueNumber = Queue::where('branch_id', $branchId)
                    ->whereDate('created_at', today())
                    ->count() + 1;

                Queue::create([
                    'appointment_id' => $appointment->id,
                    'patient_id' => $patient->id,
                    'branch_id' => $branchId,
                    'queue_number' => $queueNumber,
                    'status' => 'waiting',
                    'check_in_time' => now(),
                ]);

                return redirect()
                    ->route('queue.index')
                    ->with('success', "บันทึกลูกค้าสำเร็จ! หมายเลขคิว: {$queueNumber}");
            }

            return redirect()
                ->route('patients.show', $patient->id)
                ->with('success', 'บันทึกข้อมูลลูกค้าสำเร็จ!');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'ไม่สามารถบันทึกข้อมูลได้: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified patient
     */
    public function show($id)
    {
        $patient = Patient::with([
            'firstVisitBranch',
            'opdRecords',
            'appointments',
            'coursePurchases.package',
            'coursePurchases.sharedUsers.sharedPatient',
            'sharedCoursesReceived.coursePurchase.package',
            'sharedCoursesReceived.ownerPatient',
            'loyaltyPoints',
            'patientNotes.createdBy',
            'invoices'
        ])->findOrFail($id);

        // Get next appointment (today and future, ordered by closest date)
        $nextAppointment = $patient->appointments()
            ->where('appointment_date', '>=', now()->toDateString())
            ->where('status', '!=', 'cancelled')
            ->orderBy('appointment_date', 'asc')
            ->orderBy('appointment_time', 'asc')
            ->first();

        // Get course packages for purchase modal
        $coursePackages = \App\Models\CoursePackage::where('is_active', true)->orderBy('name')->get();

        // Get sales staff for seller selection
        $salesStaff = \App\Models\User::whereHas('role', function($q) {
            $q->whereIn('name', ['PT', 'Admin', 'Manager']);
        })->orderBy('name')->get();

        return view('patients.show', compact('patient', 'nextAppointment', 'coursePackages', 'salesStaff'));
    }

    /**
     * Show the form for editing the specified patient
     */
    public function edit($id)
    {
        $patient = Patient::findOrFail($id);
        $branches = Branch::where('is_active', true)->get();

        return view('patients.edit', compact('patient', 'branches'));
    }

    /**
     * Update the specified patient in database
     */
    public function update(Request $request, $id)
    {
        $patient = Patient::findOrFail($id);

        // Validation rules (phone can be same as current)
        // Note: first_visit_branch_id is NOT validated here - it should not be changed after creation
        $validated = $request->validate([
            'prefix' => 'nullable|string|max:20',
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'first_name_en' => 'nullable|string|max:100',
            'last_name_en' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:15|unique:patients,phone,' . $id,
            'email' => 'nullable|email|max:255|unique:patients,email,' . $id,
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'blood_group' => 'nullable|string|max:10',
            'id_card' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'subdistrict' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'line_id' => 'nullable|string|max:100',
            'emergency_contact' => 'nullable|string|max:255',
            'chronic_diseases' => 'nullable|string',
            'drug_allergy' => 'nullable|string',
            'food_allergy' => 'nullable|string',
            'surgery_history' => 'nullable|string',
            'chief_complaint' => 'nullable|string',
            'insurance_type' => 'nullable|string|max:50',
            'insurance_number' => 'nullable|string|max:100',
            'photo' => 'nullable|image|max:2048',
        ], [
            'phone.unique' => 'เบอร์โทรศัพท์นี้ถูกใช้งานแล้ว',
            'email.unique' => 'อีเมลนี้ถูกใช้งานแล้ว',
            'birth_date.before' => 'วันเกิดต้องเป็นวันที่ผ่านมาแล้ว',
        ]);

        try {
            // Combine name fields if first_name and last_name are provided
            if (!empty($validated['first_name']) || !empty($validated['last_name'])) {
                $validated['name'] = trim(
                    ($validated['prefix'] ?? $patient->prefix ?? '') . ' ' .
                    ($validated['first_name'] ?? $patient->first_name ?? '') . ' ' .
                    ($validated['last_name'] ?? $patient->last_name ?? '')
                );
            }

            // Handle photo upload
            if ($request->hasFile('photo')) {
                $validated['photo'] = $request->file('photo')->store('patients', 'public');
            }

            // Remove empty values to keep existing data
            $validated = array_filter($validated, function($value) {
                return $value !== null && $value !== '';
            });

            $patient->update($validated);

            return redirect()
                ->route('patients.show', $patient->id)
                ->with('success', 'อัปเดตข้อมูลลูกค้าสำเร็จ!');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'ไม่สามารถอัปเดตข้อมูลได้: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified patient (soft delete)
     */
    public function destroy($id)
    {
        try {
            $patient = Patient::findOrFail($id);
            $patient->delete(); // Soft delete

            return redirect()
                ->route('patients.index')
                ->with('success', 'ลบข้อมูลลูกค้าสำเร็จ!');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'ไม่สามารถลบข้อมูลได้: ' . $e->getMessage());
        }
    }

    /**
     * Search patients via Ajax (for quick search/autocomplete)
     */
    public function search(Request $request)
    {
        $query = $request->input('query');
        $phone = $request->input('phone');

        // Search by phone for appointment form
        if ($phone) {
            $patient = Patient::where('phone', $phone)->first();

            if ($patient) {
                return response()->json([
                    'patient' => [
                        'id' => $patient->id,
                        'name' => $patient->name,
                        'phone' => $patient->phone,
                        'email' => $patient->email,
                    ]
                ]);
            } else {
                return response()->json(['patient' => null]);
            }
        }

        // General search
        if (empty($query) || strlen($query) < 2) {
            return response()->json([]);
        }

        $patients = Patient::where(function($q) use ($query) {
                $q->where('phone', 'like', "%{$query}%")
                  ->orWhere('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get(['id', 'phone', 'name', 'email', 'date_of_birth']);

        return response()->json($patients);
    }

    /**
     * View patient treatment history
     */
    public function history($id)
    {
        $patient = Patient::with([
            'treatments' => function($query) {
                $query->with(['pt', 'service', 'branch'])
                      ->orderBy('created_at', 'desc');
            },
            'invoices' => function($query) {
                $query->with('branch')
                      ->orderBy('invoice_date', 'desc');
            }
        ])->findOrFail($id);

        return view('patients.history', compact('patient'));
    }

    /**
     * View patient courses purchased
     */
    public function courses($id)
    {
        $patient = Patient::with([
            'coursePurchases' => function($query) {
                $query->with(['package', 'purchaseBranch', 'usageLogs'])
                      ->orderBy('purchase_date', 'desc');
            }
        ])->findOrFail($id);

        return view('patients.courses', compact('patient'));
    }

    /**
     * Purchase course online (without appointment/queue)
     */
    public function purchaseCourseOnline(Request $request, $id)
    {
        $validated = $request->validate([
            'package_id' => 'required|exists:course_packages,id',
            'payment_type' => 'required|in:full,installment',
            'seller_ids' => 'required|array|min:1',
            'seller_ids.*' => 'exists:users,id',
            'payment_method' => 'required|in:cash,transfer,credit_card',
        ]);

        try {
            DB::beginTransaction();

            $patient = Patient::findOrFail($id);
            $package = \App\Models\CoursePackage::findOrFail($validated['package_id']);
            $branchId = auth()->user()->branch_id ?? $patient->first_visit_branch_id ?? 1;

            // Generate numbers
            $courseNumber = 'CRS-' . now()->format('Ymd') . '-' . rand(1000, 9999);
            $invoiceNumber = 'INV-' . now()->format('Ymd') . '-' . rand(1000, 9999);

            // Calculate amounts
            $installmentAmount = $validated['payment_type'] === 'installment' ? ceil($package->price / 3) : 0;
            $payAmount = $validated['payment_type'] === 'installment' ? $installmentAmount : $package->price;

            // Create Invoice
            $invoice = \App\Models\Invoice::create([
                'patient_id' => $patient->id,
                'branch_id' => $branchId,
                'invoice_number' => $invoiceNumber,
                'invoice_date' => now(),
                'due_date' => now()->addDays(30),
                'invoice_type' => 'course',
                'subtotal' => $payAmount,
                'discount_amount' => 0,
                'tax_amount' => 0,
                'total_amount' => $payAmount,
                'paid_amount' => $payAmount,
                'outstanding_amount' => 0,
                'status' => 'paid',
                'created_by' => auth()->id(),
            ]);

            // Create Invoice Item
            \App\Models\InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'item_type' => 'course_package',
                'item_id' => $package->id,
                'description' => $package->name . ($validated['payment_type'] === 'installment' ? ' (ผ่อนงวด 1/3)' : ''),
                'quantity' => 1,
                'unit_price' => $payAmount,
                'discount_amount' => 0,
                'total_amount' => $payAmount,
            ]);

            // Create Payment
            \App\Models\Payment::create([
                'payment_number' => 'PAY-' . now()->format('Ymd') . '-' . rand(1000, 9999),
                'invoice_id' => $invoice->id,
                'patient_id' => $patient->id,
                'branch_id' => $branchId,
                'amount' => $payAmount,
                'payment_method' => $validated['payment_method'],
                'payment_date' => now(),
                'status' => 'completed',
                'created_by' => auth()->id(),
            ]);

            // Create Course Purchase
            $coursePurchase = \App\Models\CoursePurchase::create([
                'course_number' => $courseNumber,
                'patient_id' => $patient->id,
                'package_id' => $package->id,
                'invoice_id' => $invoice->id,
                'purchase_branch_id' => $branchId,
                'purchase_pattern' => 'buy_for_later', // Online = ซื้อเก็บไว้ใช้ทีหลัง
                'purchase_date' => now(),
                'activation_date' => now(),
                'expiry_date' => now()->addDays($package->validity_days),
                'total_sessions' => $package->total_sessions,
                'used_sessions' => 0,
                'status' => 'active',
                'allow_branch_sharing' => true,
                'created_by' => auth()->id(),
                'seller_ids' => $validated['seller_ids'],
                'payment_type' => $validated['payment_type'],
                'installment_total' => $validated['payment_type'] === 'installment' ? 3 : 0,
                'installment_paid' => $validated['payment_type'] === 'installment' ? 1 : 0,
                'installment_amount' => $installmentAmount,
            ]);

            // Create Commission for sellers
            if (!empty($validated['seller_ids'])) {
                $commissionRate = $package->commission_rate ?? 10; // Default 10% if not set
                $baseAmount = $package->price;
                $totalCommission = $baseAmount * ($commissionRate / 100);

                $sellerCount = count($validated['seller_ids']);
                $splitAmount = $sellerCount > 0 ? round($totalCommission / $sellerCount, 2) : 0;

                foreach ($validated['seller_ids'] as $sellerId) {
                    $commissionNumber = 'COM-' . now()->format('Ymd') . '-' . rand(1000, 9999);

                    \App\Models\Commission::create([
                        'commission_number' => $commissionNumber,
                        'pt_id' => $sellerId,
                        'invoice_id' => $invoice->id,
                        'invoice_item_id' => null,
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
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'ซื้อคอร์สเรียบร้อยแล้ว',
                'course_number' => $courseNumber,
                'invoice_number' => $invoiceNumber,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(),
            ], 500);
        }
    }
}
