<?php

namespace App\Http\Controllers;

use App\Models\StockItem;
use App\Models\StockTransaction;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
{
    /**
     * Display stock items list
     */
    public function index(Request $request)
    {
        $query = StockItem::with('branch');

        // Filter by branch
        if ($request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }

        // Filter by category
        if ($request->category) {
            $query->where('category', $request->category);
        }

        // Search
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('item_code', 'like', '%' . $request->search . '%');
            });
        }

        // Filter low stock
        if ($request->low_stock) {
            $query->whereRaw('quantity_on_hand <= minimum_quantity');
        }

        $stockItems = $query->orderBy('name')->paginate(20);
        $branches = Branch::all();
        $categories = StockItem::distinct()->pluck('category')->filter();

        // Stats
        $totalItems = StockItem::count();
        $lowStockCount = StockItem::whereRaw('quantity_on_hand <= minimum_quantity')->count();
        $totalValue = StockItem::selectRaw('SUM(quantity_on_hand * unit_cost) as total')->value('total') ?? 0;

        return view('stock.index', compact(
            'stockItems', 'branches', 'categories',
            'totalItems', 'lowStockCount', 'totalValue'
        ));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $branches = Branch::all();
        return view('stock.create', compact('branches'));
    }

    /**
     * Store new stock item
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_code' => 'required|string|unique:stock_items,item_code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string',
            'unit' => 'required|string',
            'branch_id' => 'required|exists:branches,id',
            'quantity_on_hand' => 'required|integer|min:0',
            'minimum_quantity' => 'required|integer|min:0',
            'maximum_quantity' => 'nullable|integer|min:0',
            'unit_cost' => 'required|numeric|min:0',
            'unit_price' => 'nullable|numeric|min:0',
            'supplier' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['is_active'] = true;

        $stockItem = StockItem::create($validated);

        // Create initial stock transaction if quantity > 0
        if ($validated['quantity_on_hand'] > 0) {
            StockTransaction::create([
                'transaction_number' => 'TXN-' . date('Ymd') . '-' . rand(1000, 9999),
                'stock_item_id' => $stockItem->id,
                'branch_id' => $validated['branch_id'],
                'transaction_type' => 'in',
                'quantity' => $validated['quantity_on_hand'],
                'quantity_before' => 0,
                'quantity_after' => $validated['quantity_on_hand'],
                'transaction_date' => now(),
                'description' => 'สต็อกเริ่มต้น',
                'unit_cost' => $validated['unit_cost'],
                'total_cost' => $validated['quantity_on_hand'] * $validated['unit_cost'],
                'created_by' => Auth::id(),
            ]);
        }

        return redirect()->route('stock.index')->with('success', 'เพิ่มสินค้าในคลังเรียบร้อยแล้ว');
    }

    /**
     * Show stock item details
     */
    public function show(StockItem $stock)
    {
        $stock->load(['branch', 'transactions' => function($q) {
            $q->orderBy('created_at', 'desc')->limit(50);
        }]);

        return view('stock.show', compact('stock'));
    }

    /**
     * Show edit form
     */
    public function edit(StockItem $stock)
    {
        $branches = Branch::all();
        return view('stock.edit', compact('stock', 'branches'));
    }

    /**
     * Update stock item
     */
    public function update(Request $request, StockItem $stock)
    {
        $validated = $request->validate([
            'item_code' => 'required|string|unique:stock_items,item_code,' . $stock->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string',
            'unit' => 'required|string',
            'branch_id' => 'required|exists:branches,id',
            'minimum_quantity' => 'required|integer|min:0',
            'maximum_quantity' => 'nullable|integer|min:0',
            'unit_cost' => 'required|numeric|min:0',
            'unit_price' => 'nullable|numeric|min:0',
            'supplier' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $stock->update($validated);

        return redirect()->route('stock.index')->with('success', 'อัปเดตข้อมูลสินค้าเรียบร้อยแล้ว');
    }

    /**
     * Delete stock item
     */
    public function destroy(StockItem $stock)
    {
        $stock->delete();
        return redirect()->route('stock.index')->with('success', 'ลบสินค้าเรียบร้อยแล้ว');
    }

    /**
     * Stock adjustment (in/out)
     */
    public function adjust(Request $request, StockItem $stock)
    {
        $validated = $request->validate([
            'transaction_type' => 'required|in:in,out,adjust',
            'quantity' => 'required|integer|min:1',
            'description' => 'required|string',
        ]);

        $quantityBefore = $stock->quantity_on_hand;

        if ($validated['transaction_type'] === 'in') {
            $quantityAfter = $quantityBefore + $validated['quantity'];
        } elseif ($validated['transaction_type'] === 'out') {
            if ($validated['quantity'] > $quantityBefore) {
                return back()->withErrors(['quantity' => 'จำนวนเบิกมากกว่าสต็อกคงเหลือ']);
            }
            $quantityAfter = $quantityBefore - $validated['quantity'];
        } else {
            $quantityAfter = $validated['quantity'];
        }

        // Create transaction
        StockTransaction::create([
            'transaction_number' => 'TXN-' . date('Ymd') . '-' . rand(1000, 9999),
            'stock_item_id' => $stock->id,
            'branch_id' => $stock->branch_id,
            'transaction_type' => $validated['transaction_type'],
            'quantity' => $validated['quantity'],
            'quantity_before' => $quantityBefore,
            'quantity_after' => $quantityAfter,
            'transaction_date' => now(),
            'description' => $validated['description'],
            'unit_cost' => $stock->unit_cost,
            'total_cost' => $validated['quantity'] * $stock->unit_cost,
            'created_by' => Auth::id(),
        ]);

        // Update stock
        $stock->update(['quantity_on_hand' => $quantityAfter]);

        return back()->with('success', 'ปรับปรุงสต็อกเรียบร้อยแล้ว');
    }

    /**
     * Stock transactions history
     */
    public function transactions(Request $request)
    {
        $query = StockTransaction::with(['stockItem', 'branch']);

        if ($request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->type) {
            $query->where('transaction_type', $request->type);
        }

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('transaction_date', [$request->start_date, $request->end_date]);
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(30);
        $branches = Branch::all();

        return view('stock.transactions', compact('transactions', 'branches'));
    }
}
