<?php

namespace App\Http\Controllers;

use App\Models\{Invoice, Patient, Treatment, StockTransaction, MaintenanceLog, Queue, Branch, Refund, Expense};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display reports dashboard
     */
    public function index()
    {
        return view('reports.index');
    }

    /**
     * P&L Report (ข้อ 16): Profit & Loss Statement
     * MUST support: รวมทุกสาขา และ แยกทีละสาขา
     */
    public function profitAndLoss(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        // Default to current month if not specified
        $startDate = $validated['start_date'] ?? now()->startOfMonth()->format('Y-m-d');
        $endDate = $validated['end_date'] ?? now()->endOfMonth()->format('Y-m-d');
        $branchId = $validated['branch_id'] ?? null;

        // Revenue Calculation (include soft deleted invoices, subtract refunds)
        $invoiceTotal = Invoice::withTrashed()
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->where('status', 'paid')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->sum('paid_amount');

        $refundTotal = Refund::whereBetween('refund_date', [$startDate, $endDate])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->sum('refund_amount');

        $totalRevenue = $invoiceTotal - $refundTotal;

        // Detailed Revenue Breakdown by Branch
        $revenueByBranch = Invoice::withTrashed()
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->where('status', 'paid')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->select('branch_id', DB::raw('SUM(paid_amount) as revenue'))
            ->groupBy('branch_id')
            ->get();

        $refundsByBranch = Refund::whereBetween('refund_date', [$startDate, $endDate])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->select('branch_id', DB::raw('SUM(refund_amount) as refund'))
            ->groupBy('branch_id')
            ->get();

        // Expense Calculation
        // 1. Stock Transaction Costs
        $stockExpenses = StockTransaction::whereBetween('transaction_date', [$startDate, $endDate])
            ->where('transaction_type', 'out') // เบิกจ่าย
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->sum('total_cost');

        // 2. Equipment Maintenance Costs
        $maintenanceExpenses = MaintenanceLog::whereBetween('maintenance_date', [$startDate, $endDate])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->sum('cost');

        // 3. Branch Expenses (from expenses table)
        $branchExpensesTotal = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->sum('amount');

        $totalExpenses = $stockExpenses + $maintenanceExpenses + $branchExpensesTotal;

        // Expenses by Branch
        $stockExpensesByBranch = StockTransaction::whereBetween('transaction_date', [$startDate, $endDate])
            ->where('transaction_type', 'out')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->select('branch_id', DB::raw('SUM(total_cost) as expense'))
            ->groupBy('branch_id')
            ->with('branch')
            ->get();

        $maintenanceExpensesByBranch = MaintenanceLog::whereBetween('maintenance_date', [$startDate, $endDate])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->select('branch_id', DB::raw('SUM(cost) as expense'))
            ->groupBy('branch_id')
            ->with('branch')
            ->get();

        // Branch expenses from expenses table
        $operatingExpensesByBranch = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->select('branch_id', DB::raw('SUM(amount) as expense'))
            ->groupBy('branch_id')
            ->get();

        // Net Profit/Loss
        $netProfit = $totalRevenue - $totalExpenses;

        // Branch-wise P&L
        $branches = Branch::all();
        $branchPL = [];

        foreach ($branches as $branch) {
            if ($branchId && $branch->id !== $branchId) continue;

            $branchInvoice = $revenueByBranch->firstWhere('branch_id', $branch->id)?->revenue ?? 0;
            $branchRefund = $refundsByBranch->firstWhere('branch_id', $branch->id)?->refund ?? 0;
            $branchRevenue = $branchInvoice - $branchRefund;
            $branchStockExp = $stockExpensesByBranch->firstWhere('branch_id', $branch->id)?->expense ?? 0;
            $branchMaintenanceExp = $maintenanceExpensesByBranch->firstWhere('branch_id', $branch->id)?->expense ?? 0;
            $branchOperatingExp = $operatingExpensesByBranch->firstWhere('branch_id', $branch->id)?->expense ?? 0;
            $branchExpenses = $branchStockExp + $branchMaintenanceExp + $branchOperatingExp;

            $branchPL[] = [
                'branch' => $branch,
                'revenue' => $branchRevenue,
                'expenses' => $branchExpenses,
                'operating_expenses' => $branchOperatingExp,
                'net_profit' => $branchRevenue - $branchExpenses,
            ];
        }

        // Revenue by Service Type from Invoice Items (if table exists)
        try {
            $revenueByType = DB::table('invoice_items')
                ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
                ->whereBetween('invoices.invoice_date', [$startDate, $endDate])
                ->where('invoices.status', 'paid')
                ->when($branchId, fn($q) => $q->where('invoices.branch_id', $branchId))
                ->select('invoice_items.item_type', DB::raw('SUM(invoice_items.total_price) as revenue'))
                ->groupBy('invoice_items.item_type')
                ->get();
        } catch (\Exception $e) {
            $revenueByType = collect([]);
        }

        // Top 5 Revenue Items (if table exists)
        try {
            $topItems = DB::table('invoice_items')
                ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
                ->whereBetween('invoices.invoice_date', [$startDate, $endDate])
                ->where('invoices.status', 'paid')
                ->when($branchId, fn($q) => $q->where('invoices.branch_id', $branchId))
                ->select('invoice_items.item_name', 'invoice_items.item_type', DB::raw('SUM(invoice_items.total_price) as revenue'), DB::raw('SUM(invoice_items.quantity) as qty'))
                ->groupBy('invoice_items.item_name', 'invoice_items.item_type')
                ->orderBy('revenue', 'desc')
                ->limit(5)
                ->get();
        } catch (\Exception $e) {
            $topItems = collect([]);
        }

        // Previous Period Comparison (same duration)
        $daysDiff = \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1;
        $prevStartDate = \Carbon\Carbon::parse($startDate)->subDays($daysDiff)->format('Y-m-d');
        $prevEndDate = \Carbon\Carbon::parse($startDate)->subDay()->format('Y-m-d');

        $prevRevenue = Invoice::withTrashed()
            ->whereBetween('invoice_date', [$prevStartDate, $prevEndDate])
            ->where('status', 'paid')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->sum('paid_amount') - Refund::whereBetween('refund_date', [$prevStartDate, $prevEndDate])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->sum('refund_amount');

        $prevExpenses = StockTransaction::whereBetween('transaction_date', [$prevStartDate, $prevEndDate])
            ->where('transaction_type', 'out')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->sum('total_cost') + MaintenanceLog::whereBetween('maintenance_date', [$prevStartDate, $prevEndDate])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->sum('cost') + Expense::whereBetween('expense_date', [$prevStartDate, $prevEndDate])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->sum('amount');

        $prevProfit = $prevRevenue - $prevExpenses;

        // Calculate % changes
        $revenueChange = $prevRevenue > 0 ? (($totalRevenue - $prevRevenue) / $prevRevenue) * 100 : 0;
        $expenseChange = $prevExpenses > 0 ? (($totalExpenses - $prevExpenses) / $prevExpenses) * 100 : 0;
        $profitChange = $prevProfit != 0 ? (($netProfit - $prevProfit) / abs($prevProfit)) * 100 : 0;

        return view('reports.pnl', compact(
            'startDate', 'endDate', 'branchId',
            'invoiceTotal', 'refundTotal', 'totalRevenue',
            'totalExpenses', 'netProfit',
            'stockExpenses', 'maintenanceExpenses', 'branchExpensesTotal',
            'branchPL', 'branches',
            'revenueByType', 'topItems',
            'prevRevenue', 'prevExpenses', 'prevProfit',
            'revenueChange', 'expenseChange', 'profitChange'
        ));
    }

    /**
     * Dashboard KPIs (ข้อ 24)
     * Display: ยอดขาย, ลูกค้าใหม่, Utilization Rate
     */
    public function dashboard(Request $request)
    {
        $period = $request->input('period', 'today'); // today, week, month
        $branchId = $request->input('branch_id');

        // Date range based on period
        switch ($period) {
            case 'week':
                $startDate = now()->startOfWeek();
                $endDate = now()->endOfWeek();
                break;
            case 'month':
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
                break;
            default: // today
                $startDate = today();
                $endDate = today();
        }

        // KPI 1: ยอดขาย (Total Sales)
        $totalSales = Invoice::whereBetween('invoice_date', [$startDate, $endDate])
            ->where('status', 'paid')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->sum('total_amount');

        // KPI 2: ลูกค้าใหม่ (New Patients)
        $newPatients = Patient::whereBetween('created_at', [$startDate, $endDate])
            ->when($branchId, fn($q) => $q->where('first_visit_branch_id', $branchId))
            ->count();

        // KPI 3: Utilization Rate (อัตราการใช้งาน PT)
        // Formula: (Total Treatments / Total Available Slots) × 100
        $totalTreatments = Treatment::whereBetween('created_at', [$startDate, $endDate])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->count();

        // Assume 8 hours/day, 6 patients/hour per PT (simplified)
        $totalPTs = \App\Models\User::whereHas('role', function($q) {
            $q->where('name', 'PT');
        })
        ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
        ->count();

        $workingDays = $startDate->diffInDays($endDate) + 1;
        $totalAvailableSlots = $totalPTs * 8 * 6 * $workingDays; // PTs × hours × patients/hour × days

        $utilizationRate = $totalAvailableSlots > 0
            ? ($totalTreatments / $totalAvailableSlots) * 100
            : 0;

        // Additional KPIs
        $totalInvoices = Invoice::whereBetween('invoice_date', [$startDate, $endDate])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->count();

        $totalPatients = Queue::whereBetween('queued_at', [$startDate, $endDate])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->distinct('patient_id')
            ->count('patient_id');

        $avgRevenuePerPatient = $totalPatients > 0 ? $totalSales / $totalPatients : 0;

        $branches = Branch::all();

        return view('reports.dashboard', compact(
            'period', 'branchId', 'startDate', 'endDate',
            'totalSales', 'newPatients', 'utilizationRate',
            'totalInvoices', 'totalPatients', 'avgRevenuePerPatient',
            'branches'
        ));
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
}
