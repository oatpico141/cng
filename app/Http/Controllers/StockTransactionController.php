<?php

namespace App\Http\Controllers;

use App\Models\{StockTransaction, StockItem};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockTransactionController extends Controller
{
    /**
     * Display stock transactions
     */
    public function index(Request $request)
    {
        $branchId = $request->input('branch_id');
        $stockItemId = $request->input('stock_item_id');

        $transactions = StockTransaction::with(['stockItem', 'branch'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($stockItemId, fn($q) => $q->where('stock_item_id', $stockItemId))
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('stock-transactions.index', compact('transactions'));
    }

    /**
     * Store stock transaction (ข้อ 29)
     * Handle: IN (รับเข้า) และ OUT (เบิกจ่าย)
     * OUT transactions = ค่าใช้จ่ายสาขา (for P&L Report)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'stock_item_id' => 'required|exists:stock_items,id',
            'branch_id' => 'required|exists:branches,id',
            'transaction_type' => 'required|in:in,out,transfer,adjustment',
            'quantity' => 'required|numeric|min:0',
            'unit_cost' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'transaction_date' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            $stockItem = StockItem::findOrFail($validated['stock_item_id']);

            // Check stock availability for OUT transactions
            if ($validated['transaction_type'] === 'out' && $stockItem->quantity_on_hand < $validated['quantity']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock. Available: ' . $stockItem->quantity_on_hand
                ], 400);
            }

            // Generate transaction number
            $transactionNumber = 'STK-' . strtoupper($validated['transaction_type']) . '-' . now()->format('Ymd') . '-' . rand(1000, 9999);

            // Calculate quantity changes
            $quantityBefore = $stockItem->quantity_on_hand;
            $quantityAfter = match($validated['transaction_type']) {
                'in' => $quantityBefore + $validated['quantity'],
                'out' => $quantityBefore - $validated['quantity'],
                'adjustment' => $validated['quantity'], // New quantity
                default => $quantityBefore,
            };

            // Calculate total cost (for OUT = expense)
            $totalCost = $validated['quantity'] * $validated['unit_cost'];

            // Create transaction
            $transaction = StockTransaction::create([
                'transaction_number' => $transactionNumber,
                'stock_item_id' => $validated['stock_item_id'],
                'branch_id' => $validated['branch_id'],
                'transaction_type' => $validated['transaction_type'],
                'quantity' => $validated['quantity'],
                'quantity_before' => $quantityBefore,
                'quantity_after' => $quantityAfter,
                'transaction_date' => $validated['transaction_date'],
                'description' => $validated['description'] ?? null,
                'unit_cost' => $validated['unit_cost'],
                'total_cost' => $totalCost, // บันทึกเป็นค่าใช้จ่ายสาขา
                'created_by' => auth()->id() ?? null,
            ]);

            // Update stock item quantity
            $stockItem->update(['quantity_on_hand' => $quantityAfter]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stock transaction recorded successfully',
                'transaction' => $transaction,
                'new_quantity' => $quantityAfter,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to record transaction: ' . $e->getMessage()
            ], 500);
        }
    }

    public function create()
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
