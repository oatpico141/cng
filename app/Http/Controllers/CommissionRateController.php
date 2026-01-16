<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Treatment;
use App\Models\CoursePurchase;
use App\Models\Branch;
use App\Models\DfPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommissionRateController extends Controller
{
    /**
     * Display staff income summary
     */
    public function index(Request $request)
    {
        // ใช้ session branch เป็นค่า default ถ้าไม่ได้ระบุ branch_id ใน request
        $branchId = $request->branch_id ?? session('selected_branch_id');
        $month = $request->month ?? now()->format('Y-m');

        // Parse month
        $startDate = \Carbon\Carbon::parse($month . '-01')->startOfMonth();
        $endDate = \Carbon\Carbon::parse($month . '-01')->endOfMonth();

        // Get all staff (PT only - staff who receive DF/commission)
        $staffQuery = User::with(['role', 'branch'])
            ->whereHas('role', function ($q) {
                $q->where('name', 'PT');
            });

        if ($branchId) {
            $staffQuery->where('branch_id', $branchId);
        }

        $staffList = $staffQuery->get();
        $staffIds = $staffList->pluck('id')->toArray();

        // Pre-fetch all data to avoid N+1 queries
        $allTreatments = Treatment::whereIn('pt_id', $staffIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->groupBy('pt_id');

        $allDfPayments = DfPayment::whereIn('pt_id', $staffIds)
            ->whereBetween('payment_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get()
            ->groupBy('pt_id');

        // Get all course sales for all staff at once
        $allCourseSales = CoursePurchase::with('package')
            ->whereBetween('purchase_date', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->get();

        // Get total PT count once (for retroactive calculation)
        $totalPTs = User::whereHas('role', fn($q) => $q->where('name', 'PT'))->count();

        // Calculate income for each staff
        $staffSummary = [];

        foreach ($staffList as $staff) {
            // Count treatments from pre-fetched data
            $treatments = $allTreatments->get($staff->id, collect());
            $caseCount = $treatments->count();

            // Get DF from pre-fetched data
            $dfAmount = $allDfPayments->get($staff->id, collect())->sum('amount');

            // Filter course sales for this staff from pre-fetched data
            $courseSales = $allCourseSales->filter(function ($c) use ($staff) {
                $sellerIds = $c->seller_ids ?? [];
                return in_array($staff->id, $sellerIds);
            });

            $courseCount = $courseSales->count();
            $commissionAmount = $courseSales->sum(function ($c) use ($staff, $totalPTs) {
                // ตรวจสอบว่าลูกค้าจ่ายแบบไหน: ผ่อน หรือ เต็ม
                $isInstallment = $c->payment_type === 'installment';

                // เลือกค่าคอมตามประเภทการชำระ
                if ($isInstallment) {
                    $fullCommission = $c->package->commission_installment ?? $c->package->commission_rate ?? 0;
                } else {
                    $fullCommission = $c->package->commission_rate ?? 0;
                }

                // นับจำนวนคนขายจาก seller_ids
                $sellerIds = $c->seller_ids ?? [];
                $sellerCount = count($sellerIds);

                if ($sellerCount == 0) {
                    return 0; // ไม่มีคนขาย ไม่ได้คอม
                }

                // ตามกฎ purchase_pattern
                switch ($c->purchase_pattern) {
                    case 'buy_and_use':
                    case 'buy_for_later':
                        // หารตามจำนวนคนขาย
                        return $fullCommission / $sellerCount;
                    case 'retroactive':
                        // ต่อคอร์ส - หารเท่ากันทุก PT ในระบบ (use pre-fetched count)
                        return $totalPTs > 0 ? $fullCommission / $totalPTs : 0;
                    default:
                        return $fullCommission / $sellerCount;
                }
            });

            $salary = $staff->salary ?? 0;
            $totalPayout = $salary + $dfAmount + $commissionAmount;

            $staffSummary[] = [
                'user' => $staff,
                'salary' => $salary,
                'case_count' => $caseCount,
                'df_amount' => $dfAmount,
                'course_count' => $courseCount,
                'commission_amount' => $commissionAmount,
                'total_payout' => $totalPayout,
            ];
        }

        // Sort by total payout descending
        usort($staffSummary, function ($a, $b) {
            return $b['total_payout'] <=> $a['total_payout'];
        });

        // Calculate totals
        $totals = [
            'salary' => array_sum(array_column($staffSummary, 'salary')),
            'df_amount' => array_sum(array_column($staffSummary, 'df_amount')),
            'commission_amount' => array_sum(array_column($staffSummary, 'commission_amount')),
            'total_payout' => array_sum(array_column($staffSummary, 'total_payout')),
            'case_count' => array_sum(array_column($staffSummary, 'case_count')),
            'course_count' => array_sum(array_column($staffSummary, 'course_count')),
        ];

        $branches = Branch::all();

        return view('commission-rates.index', compact(
            'staffSummary',
            'branches',
            'branchId',
            'month',
            'totals',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Update staff salary
     */
    public function updateSalary(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'salary' => 'required|numeric|min:0',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $user->salary = $validated['salary'];
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'อัปเดตเงินเดือนเรียบร้อย',
            'salary' => $user->salary,
        ]);
    }

    /**
     * Show staff detail page with full history
     */
    public function staffDetail(Request $request, $userId)
    {
        $user = User::with(['role', 'branch'])->findOrFail($userId);
        $branches = Branch::all();

        // Date range filter
        $dateRange = $request->date_range ?? 'month';
        $customStart = $request->start_date;
        $customEnd = $request->end_date;

        switch ($dateRange) {
            case 'today':
                $startDate = today();
                $endDate = today()->endOfDay();
                break;
            case 'week':
                $startDate = now()->startOfWeek();
                $endDate = now()->endOfWeek();
                break;
            case 'month':
                $month = $request->month ?? now()->format('Y-m');
                $startDate = \Carbon\Carbon::parse($month . '-01')->startOfMonth()->startOfDay();
                $endDate = \Carbon\Carbon::parse($month . '-01')->endOfMonth()->endOfDay();
                break;
            case 'custom':
                $startDate = $customStart ? \Carbon\Carbon::parse($customStart) : now()->startOfMonth();
                $endDate = $customEnd ? \Carbon\Carbon::parse($customEnd)->endOfDay() : now()->endOfMonth();
                break;
            default:
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
        }

        // Get DF payments for this PT
        $dfPayments = DfPayment::with(['treatment.appointment.patient', 'treatment.patient', 'service', 'branch', 'coursePurchase.package'])
            ->where('pt_id', $userId)
            ->whereBetween('payment_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('payment_date', 'desc')
            ->get()
            ->map(function ($df) {
                // Get patient name from treatment (try appointment first, then direct patient relation)
                $patientName = $df->treatment?->appointment?->patient?->name
                    ?? $df->treatment?->patient?->name
                    ?? 'N/A';

                // Get service name
                $serviceName = $df->service?->name ?? 'N/A';

                // If course usage, append course name
                if ($df->source_type === 'course_usage' && $df->coursePurchase) {
                    $courseName = $df->coursePurchase->package?->name ?? 'คอร์ส';
                    $serviceName = $serviceName . ' (คอร์ส: ' . $courseName . ')';
                }

                return [
                    'id' => $df->id,
                    'date' => $df->payment_date,
                    'patient_name' => $patientName,
                    'service_name' => $serviceName,
                    'branch_name' => $df->branch?->name ?? 'N/A',
                    'df_amount' => $df->amount,
                    'source_type' => $df->source_type,
                    'notes' => $df->notes,
                ];
            });

        // Map to treatments for backward compatibility
        $treatments = $dfPayments;

        // Get course sales with commission detail (exclude cancelled courses)
        $courseSales = CoursePurchase::with(['patient', 'package', 'purchaseBranch'])
            ->whereJsonContains('seller_ids', $userId)
            ->whereBetween('purchase_date', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->orderBy('purchase_date', 'desc')
            ->get()
            ->map(function ($c) use ($userId) {
                $price = $c->package->price ?? 0;

                // ตรวจสอบว่าลูกค้าจ่ายแบบไหน: ผ่อน หรือ เต็ม
                $isInstallment = $c->payment_type === 'installment';

                // เลือกค่าคอมตามประเภทการชำระ
                if ($isInstallment) {
                    $commissionAmount = $c->package->commission_installment ?? $c->package->commission_rate ?? 0;
                } else {
                    $commissionAmount = $c->package->commission_rate ?? 0;
                }
                $fullCommission = $commissionAmount;

                $sellerIds = $c->seller_ids ?? [];
                $sellerCount = count($sellerIds);

                // Calculate this staff's commission share
                $staffCommission = 0;
                if ($sellerCount > 0) {
                    switch ($c->purchase_pattern) {
                        case 'buy_and_use':
                        case 'buy_for_later':
                            $staffCommission = $fullCommission / $sellerCount;
                            break;
                        case 'retroactive':
                            $totalPTs = \App\Models\User::whereHas('role', fn($q) => $q->where('name', 'PT'))->count();
                            $staffCommission = $totalPTs > 0 ? $fullCommission / $totalPTs : 0;
                            break;
                        default:
                            $staffCommission = $fullCommission / $sellerCount;
                    }
                }

                return [
                    'id' => $c->id,
                    'date' => $c->purchase_date,
                    'course_number' => $c->course_number,
                    'patient_name' => $c->patient->name ?? 'N/A',
                    'package_name' => $c->package->name ?? 'N/A',
                    'branch_name' => $c->purchaseBranch->name ?? 'N/A',
                    'price' => $price,
                    'commission_amount' => $commissionAmount,
                    'full_commission' => $fullCommission,
                    'seller_count' => $sellerCount,
                    'purchase_pattern' => $c->purchase_pattern,
                    'staff_commission' => $staffCommission,
                ];
            });

        // Calculate totals
        $totalDF = $dfPayments->sum('df_amount');
        $totalCommission = $courseSales->sum('staff_commission');
        $salary = $user->salary ?? 0;
        $totalPayout = $salary + $totalDF + $totalCommission;

        return view('commission-rates.detail', compact(
            'user',
            'branches',
            'treatments',
            'courseSales',
            'totalDF',
            'totalCommission',
            'salary',
            'totalPayout',
            'startDate',
            'endDate',
            'dateRange'
        ));
    }

    // Keep old methods for backward compatibility
    public function create()
    {
        return redirect()->route('commission-rates.index.temp');
    }

    public function store(Request $request)
    {
        return redirect()->route('commission-rates.index.temp');
    }

    public function show($id)
    {
        return $this->staffDetail(request(), $id);
    }

    public function edit($id)
    {
        return response()->json(['error' => 'Not implemented'], 404);
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('commission-rates.index.temp');
    }

    public function destroy($id)
    {
        return redirect()->route('commission-rates.index.temp');
    }

    /**
     * Store manual DF payment (for retroactive entries)
     */
    public function storeManualDf(Request $request)
    {
        $validated = $request->validate([
            'pt_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        // Get PT's branch
        $pt = User::find($validated['pt_id']);
        $branchId = $pt->branch_id ?? session('selected_branch_id');

        // Create manual DF payment
        $dfPayment = DfPayment::create([
            'pt_id' => $validated['pt_id'],
            'branch_id' => $branchId,
            'amount' => $validated['amount'],
            'source_type' => 'per_session', // Manual entries count as per_session
            'payment_date' => $validated['payment_date'],
            'notes' => ($validated['notes'] ?? '') . ' [เพิ่มค่ามือพิเศษโดย Admin]',
            'treatment_id' => null,
            'service_id' => null,
            'course_purchase_id' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'บันทึกค่ามือพิเศษสำเร็จ',
            'df_payment' => $dfPayment
        ]);
    }
}
