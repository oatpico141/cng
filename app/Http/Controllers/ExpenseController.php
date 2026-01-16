<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Branch;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        // ใช้สาขาจาก session (ที่เลือกตอนล็อกอิน)
        $currentBranchId = session('selected_branch_id');
        $startDate = $request->start_date ?? now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? now()->format('Y-m-d');
        $category = $request->category;

        $query = Expense::with(['branch', 'creator'])
            ->where('branch_id', $currentBranchId) // กรองเฉพาะสาขาปัจจุบัน
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->orderBy('expense_date', 'desc')
            ->orderBy('created_at', 'desc');

        if ($category) {
            $query->where('category', $category);
        }

        $expenses = $query->get();

        // Summary by category
        $summaryByCategory = $expenses->groupBy('category')->map(function ($items) {
            return $items->sum('amount');
        });

        $totalExpenses = $expenses->sum('amount');
        $currentBranch = Branch::find($currentBranchId);
        $categories = Expense::CATEGORIES;

        return view('expenses.index', compact(
            'expenses',
            'categories',
            'startDate',
            'endDate',
            'category',
            'summaryByCategory',
            'totalExpenses',
            'currentBranch'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_date' => 'required|date',
            'category' => 'required|string',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string',
            'receipt_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        // ใช้สาขาจาก session
        $validated['branch_id'] = session('selected_branch_id');
        $validated['created_by'] = auth()->id();

        $expense = Expense::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'บันทึกรายจ่ายเรียบร้อย',
            'expense' => $expense->load('branch'),
        ]);
    }

    public function update(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);

        $validated = $request->validate([
            'expense_date' => 'required|date',
            'category' => 'required|string',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string',
            'receipt_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $expense->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'อัปเดตรายจ่ายเรียบร้อย',
            'expense' => $expense->load('branch'),
        ]);
    }

    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);
        $expense->delete();

        return response()->json([
            'success' => true,
            'message' => 'ลบรายจ่ายเรียบร้อย',
        ]);
    }
}
